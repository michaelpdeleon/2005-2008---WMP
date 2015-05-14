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
# $Id: cc_directone.php,v 1.4 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$summary_codes = array(
	"0" => "Transaction successful",
	"1" => "Transaction declined",
	"2" => "Declined with some action required by you.",
	"3" => "System Error"
);

$an_login = $module_params["param01"];
$an_password = $module_params["param02"];
$an_prefix = $module_params["param04"];

$post = array();
$post[] = "vendor_name=".$an_login;
$post[] = "vendor_password=".$an_password;
$post[] = "card_number=".$userinfo["card_number"];
$post[] = "card_type=".$userinfo["card_type"];
$post[] = "card_expiry=".$userinfo["card_expire"];
$post[] = "card_expiry_month=".substr($userinfo["card_expire"], 0, 2);
$post[] = "card_expiry_year=".substr($userinfo["card_expire"], 2);
$post[] = "card_holder=".$userinfo["card_name"];
$post[] = "payment_amount=".$cart["total_cost"];
$post[] = "payment_date=".date("m/d/Y");
$post[] = "payment_reference=".$an_prefix.implode("-",$secure_oid);
$post[] = "remote_ip=".$SERVER_ADDR;

$gateway_host = "https://vault.safepay.com.au:443/cgi-bin/direct_".($module_params["testmode"] == 'Y'?"test":"process").".pl";
list($a,$return) = func_https_request("POST",$gateway_host,$post);
$return = func_parse_str($return, "\n");
if(!isset($summary_codes[$return['summary_code']])) {
	$return['summary_code'] = 3;
}
if($return['summary_code'] == 0 && $return['payment_reference'] == $an_prefix.implode("-",$secure_oid)) {
	$bill_output['code'] = 1;
} else {
	$bill_output['code'] = 2;
}
$bill_output['billmes'] .= "Reponse: ".$summary_codes[$return['summary_code']];

if(!empty($return['payment_number'])) {
	$bill_output['billmes'] .= "; Payment number: ".$return['payment_number'];
}
if(!empty($return['bank_reference'])) {
	$bill_output['billmes'] .= "; Bank reference: ".$return['bank_reference'];
}
if(!empty($return['response_text'])) {
	$bill_output['billmes'] .= "; Response comment: ".$return['response_text']." (code: ".$return['response_code'].")";
}
?>
