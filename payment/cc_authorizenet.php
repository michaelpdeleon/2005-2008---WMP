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
# $Id: cc_authorizenet.php,v 1.51.2.3 2006/06/16 13:00:51 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','http');

if(empty($module_params["param07"]))
	$module_params["param07"] = 'A';

$transaction_type_name = func_check_cc_trans ('AuthorizeNet', $transaction_type, array("P" => "auth_capture", "C" => "auth_only", "R" => "credit", "X" => 'prior_auth_capture'));
if ($transaction_type_name == "credit" || $transaction_type_name == "prior_auth_capture") {
	if (preg_match("/Transaction ID: (\d+)/", $order['details'], $preg))
		$transaction_id = $preg[1];

	if (empty($transaction_id))
		$transaction_type_name = '';
}

$avserr = array(
	"A" => "Address (Street) matches, ZIP does not",
	"E" => "AVS error",
	"N" => "No Match on Address (Street) or ZIP",
	"P" => "AVS not applicable for this transaction",
	"R" => "Retry. System unavailable or timed out",
	"S" => "Service not supported by issuer",
	"U" => "Address information is unavailable",
	"W" => "9 digit ZIP matches, Address (Street) does not",
	"X" => "Exact AVS Match",
	"Y" => "Address (Street) and 5 digit ZIP match",
	"Z" => "5 digit ZIP matches, Address (Street) does not"
);

$cvverr = array(
	"M" => "Match",
	"N" => "No Match",
	"P" => "Not Processed",
	"S" => "Should have been present",
	"U" => "Issuer unable to process request"
);

$cavverr = array(
	"0" => "CAVV not validated because erroneous data was submitted",
	"1" => "CAVV failed validation",
	"2" => "CAVV passed validation",
	"3" => "CAVV validation could not be performed; issuer attempt incomplete",
	"4" => "CAVV validation could not be performed; issuer system error",
	"7" => "CAVV attempt - failed validation - issuer available (US issued card/non-US acquirer)",
	"8" => "CAVV attempt - passed validation - issuer available (US issued card/non-US acquirer)",
	"9" => "CAVV attempt - failed validation - issuer unavailable (US issued card/non-US acquirer)",
	"A" => "CAVV attempt - passed validation - issuer unavailable (US issued card/non-US acquirer)",
	"B" => "CAVV passed validation, information only, no liability shift"
);

$module_params["param01"] = text_decrypt($module_params["param01"]);
$module_params["param02"] = text_decrypt($module_params["param02"]);
if (is_null($module_params["param01"])) {
	x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt the field 'param01' for AuthorizeNet: AIM CC payment module", true);
}
if (is_null($module_params["param02"])) {
	x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt the field 'param02' for AuthorizeNet: AIM CC payment module", true);
}


$post[] = "x_login=".$module_params["param01"];
$post[] = "x_tran_key=".$module_params["param02"];
$post[] = "x_version=3.1";
$post[] = "x_test_request=".($module_params["testmode"] == "N" ? "FALSE" : "TRUE");
$post[] = "x_delim_data=True";
$post[] = "x_delim_char=,";
$post[] = "x_encap_char=|";

if($transaction_type_name == 'credit' || $transaction_type_name == "prior_auth_capture") {
	$post[] = "x_trans_id=".$transaction_id;
}

$post[] = "x_first_name=".$bill_firstname;
$post[] = "x_last_name=".$bill_lastname;
$post[] = "x_address=".$userinfo["b_address"];
$post[] = "x_company=".$userinfo["company"];
$post[] = "x_city=".$userinfo["b_city"];
$post[] = "x_state=".((!empty($userinfo["b_state"]) && $userinfo["b_state"]!="Other")? $userinfo["b_state"] : "Non US");
$post[] = "x_zip=".$userinfo["b_zipcode"];
$post[] = "x_country=".$userinfo["b_country"];

$post[] = "x_ship_to_first_name=".($userinfo["s_firstname"]?$userinfo["s_firstname"]:$userinfo["firstname"]);
$post[] = "x_ship_to_last_name=".($userinfo["s_lastname"]?$userinfo["s_lastname"]:$userinfo["lastname"]);
$post[] = "x_ship_to_address=".$userinfo["s_address"];
$post[] = "x_ship_to_company=".$userinfo["company"];
$post[] = "x_ship_to_city=".$userinfo["s_city"];
$post[] = "x_ship_to_state=".((!empty($userinfo["s_state"]) && $userinfo["s_state"]!="Other")? $userinfo["s_state"] : "Non US");
$post[] = "x_ship_to_zip=".$userinfo["s_zipcode"];
$post[] = "x_ship_to_country=".$userinfo["s_country"];

$post[] = "x_phone=".$userinfo["phone"];
$post[] = "x_fax=".$userinfo["fax"];
$post[] = "x_cust_id=".$userinfo["login"];
$post[] = "x_customer_ip=".$REMOTE_ADDR;
$post[] = "x_email=".$userinfo["email"];
$post[] = "x_email_customer=FALSE";
$post[] = "x_merchant_email=".$config["Company"]["orders_department"];
$post[] = "x_invoice_Num=".$module_params["param04"].join("-",$secure_oid);
$post[] = "x_description="."Order(s) #".join("-",$secure_oid)."; customer: ".$userinfo['login'];
$post[] = "x_amount=".price_format($cart["total_cost"]);
$post[] = "x_currency_code=".$module_params["param05"];
$post[] = "x_method=CC";
$post[] = "x_recurring_billing=".($is_rbilling?"YES":"NO");
$post[] = "x_type=".$transaction_type_name;
$post[] = "x_card_num=".$userinfo["card_number"];
$post[] = "x_exp_date=".$userinfo["card_expire"];
$post[] = "x_card_code=".$userinfo["card_cvv2"];
$post[] = "x_relay_response=False";
$post[] = "x_tax=".$cart['tax_cost'];
$post[] = "x_freight=".$cart['shipping_cost'];

if(isset($cmpi_result)) {
	$post[] = "x_authentication_indicator=".intval($cmpi_result['EciFlag']);
	$post[] = "x_cardholder_authentication_value=".$cmpi_result['Cavv'];
}

list($a,$return) = func_https_request("POST","https://secure.authorize.net:443/gateway/transact.dll",$post);
#list($a,$return) = func_https_request("POST","https://certification.authorize.net:443/gateway/transact.dll",$post);
$mass = split("\|,\|","|,".$return);

if(!empty($module_params["param06"])) {
	if(md5($module_params["param06"].text_decrypt($module_params["param01"]).$mass[7].price_format($cart["total_cost"])) != strtolower($mass[38])) {
		$mass = array();
		$mass[1] = 3;
		$mass[4] = "MD5 transaction signature is incorrect!";
		$mass[3] = 0;
		$mass[2] = 0;
	}
}

if($mass[1] == 1) {
	$bill_output['code'] = 1;
	$bill_output['billmes'] = " Approval Code: ".$mass[5]. (!empty($mass[7]) ? "; Transaction ID: ".$mass[7] : "");
} elseif($mass[1] == 4) {
	$bill_output['code'] = 3;
	$bill_output['billmes'] = $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";
} else {
	$bill_output['code'] = 2;
	$bill_output['billmes'] = ($mass[1]==2 ? "Declined" : "Error").": ";
	$bill_output['billmes'].= $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";
}

if(!empty($mass[6]))
	$bill_output['avsmes'] = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);

if(!empty($mass[39]))
	$bill_output['cvvmes'] = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);

if(!empty($mass[40]))
    $bill_output['cavvmes'] = (empty($cavverr[$mass[40]]) ? "Code: ".$mass[40] : $cavverr[$mass[40]]);

?>
