<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2006 Ruslan R. Fazliev <rrf@rrf.ru>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazliev             |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2006           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: cc_caledon.php,v 1.6 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$avserr = array(
	"X" => "Exact match with 9-digit zip",
	"Y" => "Exact match with 5-digit zip",
	"A" => "Address match only",
	"W" => "9-digit zip match only",
	"Z" => "5-digit zip match only",
	"N" => "No address or zip match",
	"U" => "Address unavailable",
	"G" => "Non-US issuer does not participate",
	"R" => "Issuer system unavailable",
	"E" => "Not a mail/phone order (ineligible)",
	"S" => "Service not supported",
	"0" => "Transaction declined, AVS not processed"
);

$pp_term = $module_params["param01"];
$pp_oper = $module_params["param02"];

$post = "";
$post[] = "TERMID=".$pp_term;
$post[] = "TYPE=S";
$post[] = "OPERID=".$pp_oper;
$post[] = "CARD=".$userinfo["card_number"];
$post[] = "EXP=".$userinfo["card_expire"];
$post[] = "AMT=".(100*$cart["total_cost"]);
$post[] = "CVV2=".$userinfo["card_cvv2"];
$post[] = "AVS=".preg_replace("/[^\w]/","",strtoupper($userinfo["b_address"].$userinfo["b_zipcode"]));
$post[] = "REF=".$module_params["param03"].join("-",$secure_oid);

foreach($post as $k=>$v) {list($a,$b)=split("=",trim($v),2);$post[$k]=$a."=".urlencode($b);}

list($a,$return)=func_https_request("POST","https://lt3a.caledoncard.com:443/".join("&",$post));
parse_str($return,$ret);

$bill_output["code"] = ($ret["CODE"]==="0000") ? 1 : 2;

if($ret["TEXT"])
	$bill_output["billmes"] = $ret["TEXT"];

if($ret["AUTH"])
	$bill_output["billmes"].= " (AuthCode: ".$ret["AUTH"].")";

if($ret["WARNING"])
	$bill_output["billmes"].= " (Warning: ".$ret["WARNING"].")";

if($ret["UID"])
	$bill_output["billmes"].= " (UID: ".$ret["UID"].")";

if($ret["AVS"])
	$bill_output["avsmes"] = (empty($avserr[$ret["AVS"]]) ? "Code: ".$ret["AVS"] : $avserr[$ret["AVS"]]);

#print_r($bill_output);
#print_r($ret);
#exit;

?>
