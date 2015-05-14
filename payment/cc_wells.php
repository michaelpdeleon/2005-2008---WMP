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
# $Id: cc_wells.php,v 1.17.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["IOC_merchant_order_id"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$IOC_merchant_order_id."'");

	if(!$IOC_response_code)
	{
		$bill_output["code"] = 1;
	}
	else
	{
		$bill_output["code"] = 2;
		$error=array(
			"-1" => "Authorization system not responding. Order accepted in Faith mode.",
			"1" => "Authorization system not responding. Please retry transaction.",
			"2" => "Authorization declined. Please retry with different credit card.",
			"3" => "No response from issuing institution. Order not accepted. Please retry.",
			"4" => "Authorization declined. Invalid credit card. Please retry with different credit card.",
			"5" => "Authorization declined. Invalid amount. Please retry.",
			"6" => "Authorization declined. Expired credit card. Please retry with different credit card.",
			"7" => "Authorization declined. Invalid transaction. Please retry with different credit card.",
			"8" => "Received unexpected reply. Order not accepted. Please retry.",
			"9" => "Authorization declined. Duplicate transaction.",
			"10" => "Other issue. Order not accepted. Please retry.",
			"11" => "We're sorry, but we are unable to process your request"
		);
		if (empty($HTTP_GET_VARS ["IOC_reject_description"]))
			$bill_output["billmes"] = empty($error[$response]) ? "Response code: ".$IOC_response_code : $error[$response];
		else 
			$bill_output["billmes"] = $HTTP_GET_VARS ["IOC_reject_description"]." (".$IOC_response_code.")";
	}

	$avserr = array(
        "0" => "No data",
        "1" => "No match",
        "2" => "Address match only",
        "3" => "Zip code match only",
        "4" => "Exact match"
	);

	$cvverr = array(
        "M" => "CVV Matched.",
        "N" => "CVV No Match.",
        "P" => "Not Processed.",
        "S" => "CVV is on the card, but the shopper has indicated that CVV is not present.",
        "U" => "Issuer is not VISA certified for CVV and has not provided Visa encryption keys or both."
	);

	if(isset($IOC_avs_result))
        $bill_output["avsmes"] = empty($avserr[$IOC_avs_result]) ? "AVS Response code: ".$IOC_avs_result : $avserr[$IOC_avs_result];
	if(!empty($Ecom_payment_card_verification_rc))
        $bill_output["cvvmes"] = empty($cvverr[$Ecom_payment_card_verification_rc]) ? "CVV Response code: ".$Ecom_payment_card_verification_rc : $cvverr[$Ecom_payment_card_verification_rc];

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$id = $module_params ["param01"];
	$cvv = $module_params ["param02"];
	$expiry_month = substr($userinfo["card_expire"],0,2);
	$expiry_year = substr($userinfo["card_expire"],2,2);;
	$ordr = $module_params ["param03"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://cart.wellsfargoestore.com/payment.mart" method=POST name=process>
	<input type=hidden name=ecom_billto_online_email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=ecom_billto_postal_city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=ecom_billto_postal_countrycode value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=ecom_billto_postal_name_first value="<?php echo htmlspecialchars($bill_firstname); ?>">
 	<input type=hidden name=ecom_billto_postal_name_last value="<?php echo htmlspecialchars($bill_lastname); ?>">
	<input type=hidden name=ecom_billto_postal_postalcode value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=ecom_billto_postal_stateprov value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=ecom_billto_postal_street_line1 value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=ecom_billto_telecom_phone_number value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">

	<input type=hidden name=ecom_shipto_online_email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=ecom_shipto_postal_city value="<?php echo htmlspecialchars($userinfo["s_city"]); ?>">
	<input type=hidden name=ecom_shipto_postal_countrycode value="<?php echo htmlspecialchars($userinfo["s_country"]); ?>">
	<input type=hidden name=ecom_shipto_postal_name_first value="<?php echo htmlspecialchars($userinfo["s_firstname"]); ?>">
 	<input type=hidden name=ecom_shipto_postal_name_last value="<?php echo htmlspecialchars($userinfo["s_lastname"]); ?>">
	<input type=hidden name=ecom_shipto_postal_postalcode value="<?php echo htmlspecialchars($userinfo["s_zipcode"]); ?>">
	<input type=hidden name=ecom_shipto_postal_stateprov value="<?php echo htmlspecialchars($userinfo["s_state"]); ?>">
	<input type=hidden name=ecom_shipto_postal_street_line1 value="<?php echo htmlspecialchars($userinfo["s_address"]); ?>">
	<input type=hidden name=ecom_shipto_telecom_phone_number value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">

	<input type=hidden name=ioc_merchant_id value="<?php echo htmlspecialchars($id); ?>">
	<input type=hidden name=ioc_merchant_order_id value="<?php echo htmlspecialchars($ordr); ?>">
	<input type=hidden name=ioc_order_shopper_id value="<?php echo htmlspecialchars($cart["login"]); ?>">
	<input type=hidden name=ioc_order_transaction_type value="CC">
	<input type=hidden name=ioc_shopper_ip_address value="<?php echo htmlspecialchars($REMOTE_ADDR); ?>">
	<input type=hidden name=ioc_order_total_amount value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
	<input type=hidden name=ioc_order_tax_amount value="<?php echo htmlspecialchars($cart["tax_cost"]); ?>">
	<input type=hidden name=ioc_order_ship_amount value="<?php echo htmlspecialchars($cart["shipping_cost"]); ?>">
	<input type=hidden name=ioc_auto_settle_flag value="Y">
	<input type=hidden name=ioc_transaction_type value="E">
	<input type=hidden name=ioc_shipto_same_as_billto value="0">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>Wells Fargo</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
