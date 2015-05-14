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
# $Id: cc_prip.php,v 1.12 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$mid = $module_params["param01"];

$post = array();
$post[] = "MerchantID=".$mid;
$post[] = "RegKey=".$module_params["param02"];
$post[] = "Amount=".$cart["total_cost"];
$post[] = "REFID=".$module_params["param03"].join("-",$secure_oid);
$post[] = "AccountNo=".$userinfo["card_number"];
$post[] = "CCMonth=".substr($userinfo["card_expire"],0,2);
$post[] = "CCYear=".substr($userinfo["card_expire"],2,2);
$post[] = "NameonAccount=".$userinfo["card_name"];
$post[] = "AVSADDR=".$userinfo["b_address"];
$post[] = "AVSZIP=".$userinfo["b_zipcode"];
$post[] = "CVV2=".$userinfo["card_cvv2"];
$post[] = "CCRURL=Unix";

list($a,$return) = func_https_request("POST","https://webservices.primerchants.com:443/billing/TransactionCentral/processCC.asp",$post);

#.... TransID=2000104&REFNO=xc0&Auth=012345&AVSCode=&Notes=&User1=&User2=&User3=&User4= ....

$avserr = array(
	"X" => "Exact match - 9 digit zip",
	"Y" => "Exact match - 5 digit zip",
	"A" => "Address match only",
	"W" => "9-digit zip match only",
	"Z" => "5-digit zip match only",
	"N" => "No address or zip match",
	"U" => "Address unavailable",
	"G" => "Non-U.S. Issuer",
	"R" => "Issuer system unavailable"
);

$cvverr = array (
	'M' => 'Match',
	'N' => 'No Match',
	'U' => 'Issuer Not Identified'
);

if(preg_match("/Auth=(.*)&/U",$return,$ret)) {
	if($ret[1] != "Declined") {
		$bill_output["code"] = 1;
		$bill_output["billmes"].= "AuthCode: ".$ret[1];
	} else {
		$bill_output["code"] = 2;
		preg_match("/Notes=(.*)&/U",$return,$out);
		$bill_output["billmes"] = $ret[1].": ".$out[1];
	}

	if(preg_match("/TransID=(.*)&/U",$return,$out))
		$bill_output["billmes"].= " (TransID: ".$out[1].")";

	if(preg_match("/AVSCode=(.*)&/U",$return,$out))
		$bill_output["avsmes"] = empty($avserr[$out[1]]) ? "AVS Code: ".$out[1] : $avserr[$out[1]];

	if(preg_match("/CVV2ResponseMsg=([^&]*)/U",$return,$out))
		$bill_output["cvvmes"] = empty($cvverr[$out[1]]) ? "CVV Code: ".$out[1] : $cvverr[$out[1]];

} else {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = strip_tags($return);
}

?>
