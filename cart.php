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
# $Id: cart.php,v 1.95.2.11 2006/07/29 07:27:41 max Exp $
#
# This script implements shopping cart facility
#

require "./auth.php";

if (!empty($active_modules['Wishlist'])) {
	if ($mode == 'add2wl' || $mode == "wishlist") {
    	require $xcart_dir."/include/remember_user.php";

	} elseif (!empty($login) && !empty($remember_data) && ($mode == 'add2wl' || $mode == "wishlist" || $mode == 'add')) {
    	require $xcart_dir."/include/remember_user.php";
	}
}

x_load('cart','user','order');

require $xcart_dir."/include/cart_process.php";
include $xcart_dir."/shipping/shipping.php";

x_session_register("cart");
x_session_register("intershipper_rates");
x_session_register("intershipper_recalc");
x_session_unregister("secure_oid");
x_session_register("anonymous_checkout");
x_session_register("payment_cc_fields");
x_session_register("current_carrier","UPS");
x_session_register("arb_account_used");
x_session_register("airborne_account");
x_session_register("order_secureid");
x_session_register("is_sns_action");

$intershipper_recalc = "Y";

#
# Check if the cart is empty
#
$func_is_cart_empty = func_is_cart_empty($cart);

#
# Stop list module: check transaction
#
if (!empty($active_modules["Stop_List"]) && !func_is_allowed_trans() && $func_is_cart_empty) {
	if($mode == "checkout" || $mode == "auth") {
		$top_message["content"] = func_get_langvar_by_name("txt_stop_list_customer_note");
		$top_message["type"] = "E";
		func_header_location("cart.php");
	}

	$smarty->assign("unallowed_transaction", "Y");
}


#
# Normalize cart content
#
if (!$func_is_cart_empty && $REQUEST_METHOD == "GET" && !in_array($mode, array("wishlist","wl2cart"))) {
	$cart_changed = func_cart_normalize($cart);
}

if (($mode == "checkout" || $mode == "auth") && !$func_is_cart_empty) {
	#
	# Calculate total number of checkout process steps
	#
	$total_checkout_steps = 2;
	$checkout_step_modifier["anonymous"] = 0;
	$checkout_step_modifier["payment_methods"] = 0;
	if ($login == "" && $anonymous_checkout) {
		$total_checkout_steps++;
		$checkout_step_modifier["anonymous"] = 1;
	}

	$payment_methods = check_payment_methods(@$user_account["membershipid"]);
	if (empty($payment_methods)) {
		$top_message['content'] = func_get_langvar_by_name("txt_no_payment_methods");
		$top_message['type'] = 'E';
		func_header_location("cart.php");
	}
	elseif (count($payment_methods) == 1) {
		$total_checkout_steps--;
		$checkout_step_modifier["payment_methods"] = 1;
	}
}
else {
	$anonymous_checkout = false;
}

if ($mode == "clear_cart") {
	#
	# Clear entire cart
	#
	if (!empty($active_modules["SnS_connector"]) && !empty($cart["products"])) {
		foreach ($cart["products"] as $p) {
			$is_sns_action['DeleteFromCart'][] = $p['productid'];
		}
	}

	$cart = "";
	func_header_location("cart.php");
}

if ($mode == "unset_gc" && $gcid) {
	#
	# Unset Gift Certificate
	#
	foreach ($cart["applied_giftcerts"] as $k=>$v) {
		if ($v["giftcert_id"] != $gcid)
			continue;

		$cart["total_cost"] = $cart["total_cost"] + $v["giftcert_cost"];
		db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE gcid='$gcid'");
		unset($cart["applied_giftcerts"][$k]);
	}

	$cart["applied_giftcerts"] = array_values($cart["applied_giftcerts"]);

	func_header_location("cart.php?mode=checkout".($paymentid ? "&paymentid=".$paymentid : ""));
}

$smarty->assign("register_script_name",(($config["General"]["use_https_login"] == "Y") ? $xcart_catalogs_secure["customer"]."/" : "")."cart.php");

#
# Register member if not registerred yet
# (not a newbie - do not show help messages)
#

if ($mode == "checkout") {
	$usertype = "C";
	$old_action = $action;
	$action = "cart";
	$smarty->assign("action", $action);
	$newbie = "Y";
	if (empty($login))
		include $xcart_dir."/include/register.php";

	if (!empty($auto_login)) {
		func_header_location("cart.php?mode=checkout&registered=");
	}

	$saved_userinfo = $userinfo;
	$action = $old_action;
	$smarty->assign("newbie", $newbie);
}

if (!empty($login))
	$userinfo = func_userinfo($login, $current_area, false, false, "H");

if ($mode == "add" && !empty($productid)) {
	#
	# Add product to the cart
	#
	$add_product = array();
	$add_product["productid"] = abs(intval($productid));
	$add_product["amount"] = abs(intval($amount));
	$add_product["product_options"] = $product_options;
	$add_product["price"] = abs(doubleval($price));

	#
	# Add to cart
	#
	$result = func_add_to_cart($cart, $add_product);

	if (!empty($result["redirect_to"]))
		func_header_location($result["redirect_to"]);

	$intershipper_recalc = "Y";

	#
	# Redirect
	#
	if ($config["General"]["redirect_to_cart"] == "Y") {
		if (!empty($active_modules["SnS_connector"]))
			$is_sns_action['AddToCart'][] = $productid;

		func_header_location("cart.php");

	} else {
		$products = func_products_in_cart($cart, (!empty($user_account["membershipid"]) ? $user_account["membershipid"] : ""));
		$cart = func_array_merge($cart, func_calculate($cart, $products, $login, $current_area, 0));

		if (!empty($active_modules["SnS_connector"]))
			func_generate_sns_action("AddToCart", $productid);

		func_save_customer_cart($login, $cart);
		if (!empty($HTTP_REFERER)) {
			$tmp = parse_url($HTTP_REFERER);
			if ($config["General"]["return_to_dynamic_part"] == "Y" && $is_hc == "Y" && (strpos($tmp["path"], ".html") !== false || substr($tmp["path"], -1) == "/")) {
				if(substr($tmp["path"], -1) == "/") {
					func_header_location("home.php");
				}
				elseif (strpos($HTTP_REFERER, "-c-") !== false) {
					func_header_location("home.php?cat=$cat&page=$page");
				}
				else {
					func_header_location("product.php?productid=".$add_product["productid"]);
				}
			}
			else {
				func_header_location($HTTP_REFERER);
			}
		}
		else {
			func_header_location("home.php?cat=$cat&page=$page");
		}
	}
}

if ($mode == "delete" && !empty($productindex)) {
	#
	# Delete product from the cart
	#
	if (!empty($cart['products']) && is_array($cart['products'])) {
		$productid = func_delete_from_cart($cart, $productindex);

		if (!empty($active_modules["SnS_connector"]))
			$is_sns_action['DeleteFromCart'][] = $productid;

		$intershipper_recalc = "Y";
	}
	func_header_location("cart.php");
}

if (empty($action)) $action = "";
$return_url = "";

#
# Update the cart
#
if ($action == "update" && !$func_is_cart_empty) {
	if (!empty($productindexes)) {
		# Update the quantity of products in cart
		$min_amount_warns = func_update_quantity_in_cart($cart, $productindexes);

		if (!empty($min_amount_warns) && !empty($cart['products'])) {
			$top_message['content'] = '';
			$min_amount_ids = array();
			foreach ($cart['products'] as $k => $v) {
				if (!isset($min_amount_warns[$v['cartid']])
				||  !isset($productindexes[$k])
				||   isset($min_amount_ids[$v['productid']])) {
					continue;
				}

				$product_name = func_query_first_cell("SELECT IF($sql_tbl[products_lng].product IS NULL OR $sql_tbl[products_lng].product = '', $sql_tbl[products].product, $sql_tbl[products_lng].product) as product FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' WHERE $sql_tbl[products].productid = '$v[productid]'");
				$top_message['content'] .= (empty($top_message['content']) ? "" : "<br />\n").func_get_langvar_by_name("lbl_cannot_buy_less_X", array("quantity" => $min_amount_warns[$v['cartid']], "product" => $product_name));
				$min_amount_ids[$v['productid']] = true;
			}

			if (!empty($top_message['content']))
				$top_message['type'] = "W";
		}

		if (!empty($active_modules["SnS_connector"]))
			$is_sns_action['CartChanged'][] = false;

		$intershipper_recalc = "Y";
	}

	#
	# Update shipping method
	#
	if ($config["Shipping"]["realtime_shipping"] == "Y" && !empty($active_modules["UPS_OnLine_Tools"]) && $config["Shipping"]["use_intershipper"] != "Y")
		$current_carrier = $selected_carrier;

	if (!empty($shippingid))
		$cart["shippingid"] = $shippingid;

	$airborne_account = $arb_account;

	if (!empty($mode))
		$url_args[] = "mode=".$mode;

	if (!empty($paymentid))
		$url_args[] = "paymentid=".$paymentid;

	$return_url = "cart.php".(!empty($url_args) ? "?".implode("&", $url_args) : "");
}

if (!$func_is_cart_empty) {
	#
	# Prepare cart for calculation
	#
	$products = func_products_in_cart($cart, (!empty($userinfo["membershipid"]) ? $userinfo["membershipid"] : ""));
	if (!empty($cart["products"]) && is_array($products) && count($products) != count($cart["products"])) {
		#
		# The products array in the cart isn't accords to the store
		#
		foreach ($products as $k=>$v)
			$prodids[] = $v["cartid"];

		if (is_array($prodids)) {
			foreach ($cart["products"] as $k=>$v) {
				if (in_array($v["cartid"], $prodids))
					$cart_prods[$k] = $v;
			}

			$cart["products"] = $cart_prods;
		}
		else {
			$cart = "";
		}

		func_header_location("cart.php?$QUERY_STRING");
	}

	if (!empty($active_modules["Subscriptions"])) {
		$in_cart = true;
		include $xcart_dir."/modules/Subscriptions/subscription.php";
	}

	if (empty($login) && $config["General"]["apply_default_country"] == "Y") {
		# Use the default address
		$userinfo["s_country"] = $config["General"]["default_country"];
		$userinfo["s_state"] = $config["General"]["default_state"];
		$userinfo["s_zipcode"] = $config["General"]["default_zipcode"];
		$userinfo["s_city"] = $config["General"]["default_city"];
		$userinfo["s_countryname"] = func_get_country($userinfo["s_country"]);
		$userinfo["s_statename"] = func_get_state($userinfo["s_state"], $userinfo["s_country"]);
	}

	#
	# Check if shipping cost is need to be calculated
	#
	$need_shipping = false;
	if ($config["Shipping"]["disable_shipping"] != "Y" && is_array($products)) {
		foreach ($products as $product) {
			if (!empty($active_modules["Egoods"]) &&  !empty($product["distribution"]))
				continue;

			$need_shipping = true;
			break;
		}
	}

	if ($need_shipping) {
		# Get the allowed shipping methods list
		$shipping = func_get_shipping_methods_list($cart, $products, $userinfo);

		# If current shipping is empty set it to default (first in shipping array)
		$shipping_matched = false;

		if (!empty($shipping) && is_array($shipping)) {
			foreach ($shipping as $shipping_method) {
				if (@$cart["shippingid"] == $shipping_method["shippingid"])
					$shipping_matched = true;
			}
		}

		if (!$shipping_matched && !empty($shipping))
			$cart["shippingid"] = $shipping[0]["shippingid"];

		if (!empty($shipping)) {
			foreach ($shipping as $shipping_method) {
				if (@$cart["shippingid"] == $shipping_method["shippingid"])
					$cart['shipping_warning'] = $shipping_method['warning'];
			}
		}

		$cart["delivery"] = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");

		$smarty->assign("shipping", $shipping);
		$smarty->assign("current_carrier", $current_carrier);
	}
	else {
		$cart["delivery"] = "";
		$cart["shippingid"] = 0;
	}

	$smarty->assign("need_shipping", $need_shipping);

	#
	# Discount coupons
	#
	if ($active_modules["Discount_Coupons"])
		include $xcart_dir."/modules/Discount_Coupons/discount_coupons.php";

	#
	# Calculate all prices
	#
	$cart = func_array_merge($cart, func_calculate($cart, $products, $login, $current_area, (!empty($paymentid) ? intval($paymentid) : 0)));

	if (func_is_cart_empty($cart)) {
		if (!empty($active_modules["SnS_connector"]))
			func_sns_exec_actions($is_sns_action);

		$cart = "";
		func_header_location($xcart_web_dir.DIR_CUSTOMER."/error_message.php?product_in_cart_expired");
	}
	else {
		$products = func_products_in_cart($cart, (!empty($userinfo["membershipid"])?$userinfo["membershipid"]:0));
	}

	$smarty->assign("cart",$cart);
}

if (!empty($active_modules["SnS_connector"]))
	func_sns_exec_actions($is_sns_action);

if ($return_url)
	func_header_location($return_url);

$smarty->assign("main","cart");


#
# Wishlist facility
#
if (!empty($active_modules["Wishlist"]) && $mode != "checkout") {
	@include $xcart_dir."/modules/Wishlist/wishlist.php";
}

if ($mode != "wishlist" || empty($active_modules['Wishlist'])) {
	if ($mode == "checkout")
		$location[] = array(func_get_langvar_by_name("lbl_checkout"), "");
	else
		$location[] = array(func_get_langvar_by_name("lbl_your_shopping_cart"), "");
}

#
# SHOPPING CART FEATURE
#

if ($mode == "checkout" && !empty($cart["products"]) && empty($shipping) && !empty($login) && $need_shipping && $config["Shipping"]["disable_shipping"] != "Y") {
	#
	# ERROR: No shipping methods selected
	#
	if (!empty($active_modules["Fast_Lane_Checkout"]))
		$no_shipping = true;
	else
		func_header_location("error_message.php?error_no_shipping");
}

if ($mode == "checkout" && !$func_is_cart_empty && $cart["subtotal"] < $config["General"]["minimal_order_amount"] && $config["General"]["minimal_order_amount"] > 0) {
	#
	# ERROR: Cart total must exceeds the minimum order total amount (defined in General settings)
	#
	func_header_location("error_message.php?error_min_order");
}

if ($mode == "checkout" && !$func_is_cart_empty && $config["General"]["maximum_order_amount"] > 0 && $cart["subtotal"] > $config["General"]["maximum_order_amount"]) {
	#
	# ERROR: Cart total must not exceeds the maximum order total amount (defined in General settings)
	#
	func_header_location("error_message.php?error_max_order");
}

if ($mode == "checkout" && !$func_is_cart_empty && $config["General"]["maximum_order_items"] > 0 && func_cart_count_items($cart) > $config["General"]["maximum_order_items"]) {
	#
	# ERROR: Cart total must not exceeds the maximum total quantity of products in an order (defined in General settings)
	#
	func_header_location("error_message.php?error_max_items");
}

if ($mode == "checkout" && empty($login) && !$func_is_cart_empty) {
	#
	# Start the anonymous checkout
	#
	$smarty->assign("main","anonymous_checkout");
	$smarty->assign("anonymous","Y");
	if (empty($userinfo) && !empty($saved_userinfo)) {
		$userinfo = $saved_userinfo;
	}

	$checkout_step = 1;
	$anonymous_checkout = true;

	$location[] = array(func_get_langvar_by_name("lbl_your_order"), "");

	#
	# For PayPal ExpressCheckout
	#
	if (test_active_bouncer() && $config['General']['disable_anonymous_checkout'] != 'Y') {
		# detect active PayPal Pro
		$tmp = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor='ps_paypal_pro.php' AND $sql_tbl[ccprocessors].paymentid=$sql_tbl[payment_methods].paymentid AND $sql_tbl[payment_methods].active='Y' ORDER BY $sql_tbl[payment_methods].protocol DESC LIMIT 1");
		$smarty->assign("paypal_express_active", $tmp);
		x_session_unregister('paypal_begin_express');
	}
}
elseif ($mode == "checkout" && empty($paymentid) && !$func_is_cart_empty && $cart["total_cost"] == 0) {
	#
	# Skip payment routine if cart total is 0
	#
	x_session_unregister('paypal_begin_express');
	func_header_location($current_location."/payment/payment_offline.php");
}
elseif ($mode == "checkout" && !empty($paymentid) && !$func_is_cart_empty) {
	#
	# Prepare the last step of checkout
	#

	# Check if paymentid isn't fake
	$is_egoods = ($config["Egoods"]["egoods_manual_cc_processing"] == "Y" ? func_esd_in_cart($cart) : false);
	$membershipid = $user_account["membershipid"];
	$paypal_pro_condition = "";

	$is_valid_paymentid = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[pmethod_memberships] ON $sql_tbl[pmethod_memberships].paymentid = $sql_tbl[payment_methods].paymentid WHERE $sql_tbl[payment_methods].paymentid='$paymentid'".(($is_egoods && $paymentid == 1) ? "" : " AND $sql_tbl[payment_methods].active='Y'")." AND ($sql_tbl[pmethod_memberships].membershipid IS NULL OR $sql_tbl[pmethod_memberships].membershipid = '$membershipid') ".$paypal_pro_condition);
	if (!$is_valid_paymentid)
		func_header_location("cart.php?mode=checkout&err=paymentid");

	$paypal_expressid = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid=$sql_tbl[ccprocessors].paymentid AND $sql_tbl[payment_methods].active='Y'");

	if (!empty($paypal_expressid) && $paypal_expressid == $paymentid) {

		if (!empty($active_modules['Fast_Lane_Checkout']) && empty($shipping) && $need_shipping && $config["Shipping"]["disable_shipping"] != "Y") {
			$top_message["content"] = func_get_langvar_by_name("msg_flc_select_shipping_err");
			$top_message["type"] = "E";
			func_header_location("cart.php?mode=checkout");
		}

		x_session_register('paypal_begin_express');
		if ($paypal_begin_express !== false) {
			$paypal_begin_express = true;
			func_header_location($current_location.'/payment/ps_paypal_pro.php?payment_id='.$paymentid.'&mode=express');
		}
	}

	# Generate uniq orderid which will identify order session
	$order_secureid = md5(uniqid(rand()));

	# Show payment details checkout page
	$payment_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='$paymentid'");
	if ($is_egoods && $paymentid != 1 && !empty($payment_cc_data)) {
		$paymentid = 1;
		$payment_cc_data = array();
	}

	# Generate payment script URL depending on HTTP/HTTPS settings
	if (empty($cart['shippingid'])) {
		$payment_data = func_query_first("SELECT $sql_tbl[payment_methods].*, $sql_tbl[payment_methods].payment_method as payment_method_orig, IFNULL(l1.value, $sql_tbl[payment_methods].payment_method) as payment_method, IFNULL(l2.value, $sql_tbl[payment_methods].payment_details) as payment_details FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[languages_alt] as l1 ON l1.name = CONCAT('payment_method_', $sql_tbl[payment_methods].paymentid) AND l1.code = '$shop_language' LEFT JOIN $sql_tbl[languages_alt] as l2 ON l2.name = CONCAT('payment_details_', $sql_tbl[payment_methods].paymentid) AND l2.code = '$shop_language' WHERE $sql_tbl[payment_methods].paymentid='$paymentid'");

	} else {
		$payment_data = func_query_first("SELECT $sql_tbl[payment_methods].*, $sql_tbl[payment_methods].payment_method as payment_method_orig, IFNULL(l1.value, $sql_tbl[payment_methods].payment_method) as payment_method, IFNULL(l2.value, $sql_tbl[payment_methods].payment_details) as payment_details FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[languages_alt] as l1 ON l1.name = CONCAT('payment_method_', $sql_tbl[payment_methods].paymentid) AND l1.code = '$shop_language' LEFT JOIN $sql_tbl[languages_alt] as l2 ON l2.name = CONCAT('payment_details_', $sql_tbl[payment_methods].paymentid) AND l2.code = '$shop_language' LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[shipping].shippingid = '$cart[shippingid]' WHERE $sql_tbl[payment_methods].paymentid='$paymentid' AND ($sql_tbl[payment_methods].is_cod != 'Y' || $sql_tbl[shipping].is_cod = 'Y')");
	}
	if (empty($payment_data)) {
		func_header_location("cart.php?mode=checkout");
	}

	$cart["paymentid"] = $paymentid;

	$payment_data["payment_script_url"] = ($payment_data["protocol"] == "https" ? $https_location : $http_location)."/payment/".$payment_data["payment_script"];

	if (!empty($payment_cc_fields)) {
		$userinfo = func_array_merge($userinfo, $payment_cc_fields);
	}

	if ($checkout_step_modifier["payment_methods"] == 1)
		$smarty->assign("ignore_payment_method_selection", 1);

	$checkout_step = 2 + $checkout_step_modifier["anonymous"] - $checkout_step_modifier["payment_methods"];

	if (x_session_is_registered('paypal_begin_express')) {
		$tmp = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor='ps_paypal_pro.php' AND $sql_tbl[ccprocessors].paymentid=$sql_tbl[payment_methods].paymentid AND $sql_tbl[payment_methods].paymentid='$paymentid' AND $sql_tbl[payment_methods].active='Y' ORDER BY $sql_tbl[payment_methods].protocol DESC LIMIT 1");
		$smarty->assign('paypal_express_active', $tmp);
	}

	if ($payment_data["processor_file"] == "ps_paypal_pro.php") {
		$payment_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
	}

	$payment_data['module_params'] = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid = '$payment_data[paymentid]'");
	$smarty->assign("payment_cc_data", $payment_cc_data);
	$smarty->assign("payment_data",$payment_data);
	$smarty->assign("userinfo",$userinfo);
	$smarty->assign("main","checkout");

	$location[] = array(func_get_langvar_by_name("lbl_payment_details"), "");
}
elseif ($mode == "checkout" && !$func_is_cart_empty) {
	#
	# Prepare the page for payment method selection
	#
	$payment_methods = check_payment_methods(@$user_account["membershipid"]);
	$force_change_shipping = (!empty($active_modules["Fast_Lane_Checkout"]) && (count($shipping) > 1 || ($need_shipping && empty($shipping))));
	if (count($payment_methods) == 1 && !$force_change_shipping) {
		# Skip payment method selection if only one method is available
		func_header_location("cart.php?paymentid=".$payment_methods[0]["paymentid"]."&mode=checkout");
	}

	if (!empty($payment_methods))
		$payment_methods[0]["is_default"] = 1;

	$checkout_step = 1 + $checkout_step_modifier["anonymous"] - $checkout_step_modifier["payment_methods"];

	$smarty->assign("payment_methods",$payment_methods);
	$smarty->assign("main","checkout");

	$location[] = array(func_get_langvar_by_name("lbl_payment_details"), "");

	x_session_unregister('paypal_begin_express');
}
elseif ($mode == "order_message") {
	#
	# Display the invoice page (order confirmation page)
	#
	$orders = array ();

	if (!empty($orderids)) {
		if (empty($login))
			func_header_location("error_message.php?access_denied&id=32");

		$_orderids = split (",",$orderids);

		foreach ($_orderids as $orderid) {
			$order_data = func_order_data($orderid);

			# Security check if current customer is not order's owner
			if (empty($order_data) || $order_data["order"]["login"] != $login) {
				unset($order_data);
				continue;
			}
			else {
				$order_data["products"] = func_translate_products($order_data["products"], $shop_language);
			}

			$orders[] = $order_data;
		}
	}

	if (empty($orders))
		func_header_location("error_message.php?access_denied&id=59");

	$smarty->assign("orders", $orders);

	if ($action == "print") {
		$smarty->assign("template", "customer/main/order_message.tpl");
		func_display("customer/preview.tpl",$smarty);
		exit;
	}

	$smarty->assign("orderids", $orderids);
	$smarty->assign("main","order_message");

	$location[] = array(func_get_langvar_by_name("lbl_order_processed"), "");
}
elseif ($mode == "auth" && !$func_is_cart_empty) {
	#
	# Display the authentication page
	#
	$smarty->assign("main","checkout");
	$checkout_step = 1;
}

require $xcart_dir."/include/categories.php";

if ($active_modules["Manufacturers"])
	include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

$giftcerts = (!empty($cart["giftcerts"]) ? $cart["giftcerts"] : array());

#
# Updare minicart
#
include "./minicart.php";

if (!empty($payment_cc_fields)) {
	$userinfo = func_array_merge($userinfo, $payment_cc_fields);
}

if (!empty($login) || $mode != "checkout") {
	$smarty->assign("userinfo", @$userinfo);
}

$smarty->assign("products", @$products);
$smarty->assign("giftcerts", $giftcerts);

if ($mode == "checkout" || $mode == "auth") {
	$smarty->assign("checkout_step", $checkout_step);
	$smarty->assign("total_checkout_steps", $total_checkout_steps);
}

func_save_customer_cart($login, $cart);

if (func_use_arb_account()) {
	$smarty->assign("use_airborne_account", true);
	$smarty->assign("airborne_account", $airborne_account);
}

$allow_cod = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] WHERE active = 'Y' AND is_cod = 'Y'") > 0;
$smarty->assign("allow_cod", $allow_cod);
$display_cod = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active = 'Y' AND is_cod = 'Y' AND shippingid = '$cart[shippingid]'") > 0;
$smarty->assign("display_cod", $display_cod);

x_session_save();

if (!empty($active_modules["Fast_Lane_Checkout"]))
	include $xcart_dir."/modules/Fast_Lane_Checkout/cart.php";

# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);

?>
