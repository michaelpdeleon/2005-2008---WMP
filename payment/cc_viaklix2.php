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
# $Id: cc_viaklix2.php,v 1.15.2.3 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && !empty($HTTP_POST_VARS["ssl_result_message"]) && isset($HTTP_POST_VARS["ssl_result"])) {
	require "./auth.php";

	$results = array (
		"APPROVAL" => "Approved", 
		"APPROVED" => "Approved",
		"ACCEPTED" => "Frequency Approval", 
		"BAL.: 99999999.99" => "Debit Card Balance Inquiry Response",  
		"PICK UP CARD" => "Pick up card",  
		"AMOUNT ERROR" => "Tran Amount Error",  
		"APPL TYPE ERROR" => "Call for Assistance",  
		"DECLINED" => "Do Not Honor",  
		"DECLINED-HELP 9999" => "System Error", 
		"EXCEEDS BAL." => "Req. exceeds balance", 
		"EXPIRED CARD" => "Expired Card",  
		"INVALID CARD" => "Invalid Card",  
		"INCORRECT PIN" => "Invalid PIN",  
		"INVALID TERM ID" => "Invalid Terminal ID",  
		"INVLD TERM ID 1" => "Invalid Merchant Number",  
		"INVLD TERM ID 2" => "Invalid SE Number",  
		"INVLD VOID DATA" => "Invalid Data",  
		"MUST SETTLE MMDD" => "Must settle POS Device, open batch is more than 7 days old.",  
		"ON FILE" => "Cardholder not found",  
		"RECORD NOT FOUND" => "Record not on Host",  
		"FOUND SERV NOT ALLOWED" => "Invalid request",  
		"SEQ ERR PLS CALL" => "Call for Assistance", 
		"CALL AUTH." => "Refer to Issuer", 
		"CENTER CALL REF.; 999999" => "Refer to Issuer", 
		"DECLINE CVV2" => "Do Not Honor; Declined due to CVV2 mismatch \ failure" 
	);

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

	$bill_output["sessid"] = $XCARTSESSID;
	
	$bill_output["code"] = (($ssl_result == 0) ? 1 : 2);
	
	if ($ssl_result_message)
		$bill_output["billmes"] = isset($results[$ssl_result_message]) ? $results[$ssl_result_message] : "unknown result code";

	$bill_output["billmes"] .= " (TransId: ".$ssl_txn_id.")";
	if ($ssl_avs_response)
		$bill_output["avsmes"] = "AVS Code: ".$avserr[$ssl_avs_response];
    if ($ssl_cvv2_response)
		$bill_output["cvvmes"] = "CVV Code: ".$cvverr[$ssl_cvv2_response];
	
	require($xcart_dir."/payment/payment_ccend.php");

} else {

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$ssl_merchant_id = $module_params["param01"];
	$ssl_user_id = $module_params["param07"];
	$ssl_pin = $module_params["param02"];
	$vk_prefix = $module_params["param04"];
	$vk_cvv = $module_params["param05"];
	$vk_avs = $module_params["param06"];

	$ssl_invoice_number = join("-",$secure_oid);
	$post = array(
		"ssl_invoice_number" => $ssl_invoice_number,
		"ssl_merchant_id" => $ssl_merchant_id,
		"ssl_user_id" => $ssl_user_id,
		"ssl_pin" => $ssl_pin,
    	"ssl_customer_code" => $userinfo["login"],
    	"ssl_salestax" => $cart["tax_cost"],
		"ssl_description" => $vk_prefix.join("-", $secure_oid),
		"ssl_test_mode" => $module_params["testmode"] != "N" ? "TRUE" : "",
		"ssl_receipt_link_url" => $current_location."/payment/cc_viaklix2.php",
		"ssl_receipt_link_method" => "POST",
		"ssl_amount" => $cart["total_cost"],
		"ssl_transaction_type" => "SALE",
		"ssl_card_number" => $userinfo["card_number"],
		"ssl_exp_date" => $userinfo["card_expire"],

		"ssl_company" => $userinfo["company"],
		"ssl_first_name" => $bill_firstname,
		"ssl_last_name" => $bill_lastname,
		"ssl_address1" => $userinfo["b_address"],	
		"ssl_address2" => $userinfo["b_address_2"],
		"ssl_city" => $userinfo["b_city"],
		"ssl_state" => $userinfo["b_state"] ? $userinfo["b_state"] : "n/a",
		"ssl_zip" => $userinfo["b_zipcode"],
		"ssl_country" => $userinfo["b_country"],
		"ssl_phone" => $userinfo["phone"], 
		"ssl_email" => $userinfo["email"],

		"ssl_ship_to_company" => $userinfo["company"],
		"ssl_ship_to_first_name" => $userinfo["s_firstname"],
		"ssl_ship_to_last_name" => $userinfo["s_lastname"],
		"ssl_ship_to_address" => $userinfo["s_address"],
		"ssl_ship_to_city" => $userinfo["s_city"],
		"ssl_ship_to_state" => $userinfo["s_state"] ? $userinfo["s_state"] : "n/a",
		"ssl_ship_to_country" => $userinfo["s_country"],
		"ssl_ship_to_zip" => $userinfo["s_zipcode"],
	);

	if ($vk_avs == "Y") {
		$post["ssl_avs_address"] = substr($userinfo["b_address"], 0, 30);
		$post["ssl_avs_zip"] = $userinfo["b_zipcode"];
	}

	if ($vk_cvv == "Y" && !empty($card_cvv2)) {
		$post["ssl_cvv2"] = "present";
		$post["ssl_cvv2cvc2"] = $card_cvv2; 
	}

	func_create_payment_form("https://www2.viaklix.com/process.asp", $post, "viaKlix");
}
exit;

?>
