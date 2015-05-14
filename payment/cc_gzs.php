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
# $Id: cc_gzs.php,v 1.15 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$curr = $module_params["param02"];

if(is_visa($userinfo["card_number"]))$userinfo["card_type"]="VISA";
if(is_mc($userinfo["card_number"]))$userinfo["card_type"]="MasterCard";
if(is_amex($userinfo["card_number"]))$userinfo["card_type"]="AMEX";
if(is_diners($userinfo["card_number"]))$userinfo["card_type"]="DINERS";
if(is_jcb($userinfo["card_number"]))$userinfo["card_type"]="JCB";

$post = "";
$post[] = "MerchantID=".$module_params["param01"];
$post[] = "TransID=".$module_params["param04"].join("-",$secure_oid);
$post[] = "Amount=".(($curr=="JPY" || $curr=="LUF" || $curr=="SEK") ? 1 : 100 )*$cart["total_cost"];
$post[] = "Currency=".$curr;
$post[] = "CCNr=".$userinfo["card_number"];
$post[] = "CCexpiry=".(2000+substr($userinfo["card_expire"],2,2)).substr($userinfo["card_expire"],0,2);
$post[] = "CCCVC=".$userinfo["card_cvv2"];
$post[] = "CCBrand=".$userinfo["card_type"];
$post[] = "OrderDesc=".$config['Company']['company_name'];

list($a,$return)=func_https_request("POST","https://".$module_params["param01"].":".$module_params["param05"]."@txms.gzs.de:51384/direct.aspx",$post);
$return = "&".$return."&";

#PayID=ae53e18447c0414ca6d1ba024bad3e6b&TransID=1982&Status=FAILED&XID=1cff093dd5194fc5bf26e4230a5d3ad0&Code=-7&Description=declined
if(preg_match("/&Status=AUTHORIZED&/U",$return,$out))
{
	$bill_output["code"] = 1;
}
else
{
	$bill_output["code"] = 2;
	preg_match("/&Description=(.*)&/U",$return,$out);
	if(!empty($out[1]))
		$bill_output["billmes"] = $out[1];
}

$nextpost = array();
preg_match("/&PayID=(.*)&/U",$return,$out);
if(!empty($out[1]))
{
	$nextpost[] = "PayID=".$out[1];
	$bill_output["billmes"].= " (PayID=".$out[1].")";
}

preg_match("/&XID=(.*)&/U",$return,$out);
if(!empty($out[1]))
{
	$nextpost[] = "XID=".$out[1];
	$bill_output["billmes"].= " (XID=".$out[1].")";
}

if(!empty($nextpost))
{
	list($a,$return)=func_https_request("POST","https://".$module_params["param01"].":".$module_params["param05"]."@txms.gzs.de:51384/confirm.aspx",$nextpost);
	$bill_output["billmes"].=" (".$return.")";
}

$bill_output["cvvmes"].= "Not support";
$bill_output["avsmes"] = "Not support";

#print_r($return);print "<hr />";print_r($bill_output);
#exit;
?>
