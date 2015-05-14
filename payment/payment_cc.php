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
# $Id: payment_cc.php,v 1.84.2.2 2006/06/16 09:26:15 max Exp $
#
# CC processing payment module
#

require "../include/payment_method.php";

x_load('cart','crypt','order','payment','tests');

x_session_unregister('logged_paymentid');


if ($REQUEST_METHOD != "POST") {
	func_header_location($current_location.DIR_CUSTOMER."/cart.php?mode=checkout");
}


include $xcart_dir."/include/payment_wait.php";

#
# Check require fields
#
$is_egoods = false;
if ($config['Egoods']['egoods_manual_cc_processing'] == 'Y') {
	$is_egoods = func_esd_in_cart($cart);
}

x_session_register("payment_cc_fields");
$payment_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='$paymentid'");
if ($is_egoods && $paymentid != 1 && !empty($payment_cc_data)) {
	$paymentid = 1;
	$payment_cc_data = array();
}

if ($paymentid == 1 && !empty($config['card_types'])) {
	foreach($config['card_types'] as $v) {
		if ($v['code'] != $card_type)
			continue;

		if(empty($v['cvv2'])) {
			unset($card_cvv2);
		}
		break;
	}
}

if (($paymentid == 1 || @$payment_cc_data['type'] == 'C')
&&  @$payment_cc_data['disable_ccinfo'] != 'Y'
&& (@$payment_cc_data['processor'] == 'ps_paypal.php' && isset($card_name) && isset($card_type) && isset($card_number))) {
	if(empty($card_name)
	|| empty($card_type)
	|| empty($card_number)
	|| empty($card_expire)
	|| (empty($card_cvv2) && isset($card_cvv2))) {
		$payment_cc_fields = array("card_name" => $card_name, "card_type" => $card_type, "card_number" => $card_number, "card_expire" => $card_expire, "card_cvv2" => $card_cvv2);
		$top_message['content'] = func_get_langvar_by_name("err_filling_form");
		$top_message['anchor'] = "ccinfo";
		$top_message['type'] = 'E';

		func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=fields&paymentid=".$paymentid);
	}
}

#
# Make order details
#
$_order_details_rval = array();
foreach (func_order_details_fields() as $_details_field => $_field_label) {
	if (isset($GLOBALS[$_details_field])) {
		$_order_details_rval[] = $_field_label.": ".stripslashes($GLOBALS[$_details_field]);
	}
}

$order_details = implode("\n", $_order_details_rval);

$customer_notes = $Customer_Notes;

#
# Only logged users can submit orders
#
$is_valid_paymentid = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[pmethod_memberships] ON $sql_tbl[pmethod_memberships].paymentid = $sql_tbl[payment_methods].paymentid WHERE $sql_tbl[payment_methods].paymentid='$paymentid'".(($is_egoods && $paymentid == 1)?"":" AND $sql_tbl[payment_methods].active='Y'")." AND ($sql_tbl[pmethod_memberships].membershipid IS NULL OR  $sql_tbl[pmethod_memberships].membershipid = '$userinfo[membershipid]') ");

if (!$is_valid_paymentid)
	func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=paymentid");

$is_paypal_pro = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] WHERE paymentid='".$paymentid."' AND processor_file='ps_paypal_pro.php'");
if ($is_paypal_pro) {
	$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='ps_paypal_pro.php'");
}
else {
	$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where paymentid='".$paymentid."'");
}

if (!empty($module_params["processor"])) {
	x_session_register('logged_paymentid');
	$logged_paymentid = $paymentid;
	#
	# Get active processor's data and module parameters
	#
	$duplicate = true;
	x_session_register("secure_oid");
	x_session_register("secure_oid_cost");

	# Put order in table
	if(empty($secure_oid) || ($secure_oid_cost != $cart["total_cost"])) {
		$orderids = func_place_order(stripslashes($payment_method)." (".$module_params["module_name"].(get_cc_in_testmode($module_params)?", in test mode":"").")", "I", $order_details, $customer_notes);
		if (is_null($orderids) || $orderids===false) {
			func_header_location($xcart_catalogs['customer'].'/error_message.php?product_in_cart_expired');
		}
		$secure_oid = $orderids;
		$secure_oid_cost = $cart["total_cost"];
		$duplicate = false;
	}
	else {
		$orderids = $secure_oid;
	}

	x_session_save();

	# Set CVV2 info line...
	$a = strlen($userinfo["card_cvv2"]);
	$bill_output = "";
	$bill_output["cvvmes"] = (($a)?($a." digit(s)"):("not set"))." / ";

	if ($module_params['cmpi'] == 'Y' && file_exists($xcart_dir."/payment/cmpi.php") && $config['CMPI']['cmpi_enabled'] == 'Y' && in_array($card_type, array("VISA", "MC", "JCB"))) {
		require $xcart_dir."/payment/cmpi.php";
	}

	require $xcart_dir."/payment/".basename($module_params["processor"]);
	require $xcart_dir."/payment/payment_ccend.php";

	exit;
}
else {
	#
	# Manual processing
	#

	$orderids = func_place_order(stripslashes($payment_method)." (manual processing)","Q",$order_details, $customer_notes);
	if (is_null($orderids) || $orderids===false) {
		func_header_location($xcart_catalogs['customer'].'/error_message.php?product_in_cart_expired');
	}
	$_orderids = func_get_urlencoded_orderids ($orderids);
	$cart = "";
	x_session_save();

	#
	# If successful - Store CC number in database
	#
	if ($store_cc) {
		$query_data = array(
			"card_name" => $card_name,
			"card_type" => $card_type,
			"card_number" => addslashes(text_crypt($card_number)),
			"card_expire" => $card_expire
		);

		if ($store_cvv2) {
			$query_data['card_cvv2'] = addslashes(text_crypt($card_cvv2));
		}

		func_array2update("customers", $query_data, "login='$login' AND usertype='$login_type'");
		$query_data = array();
	}

	if (!empty($active_modules['SnS_connector'])) {
		func_generate_sns_action("CartChanged");
	}

	func_header_location($xcart_catalogs['customer']."/cart.php?mode=order_message&orderids=$_orderids");
}

?>
