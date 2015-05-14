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
# $Id: func.order.php,v 1.34.2.13 2006/08/14 06:20:32 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart','crypt','mail','user');

#
# This function creates array with order data
#
function func_select_order($orderid) {
	global $sql_tbl, $config, $current_area, $active_modules, $shop_language;

	$o_date = "date+'".$config["Appearance"]["timezone_offset"]."' as date";
	$order = func_query_first("select *, $o_date from $sql_tbl[orders] where $sql_tbl[orders].orderid='$orderid'");

	if (empty($order))
		return false;

	$order['titleid'] = func_detect_title($order['title']);
	$order['b_titleid'] = func_detect_title($order['b_title']);
	$order['s_titleid'] = func_detect_title($order['s_title']);
	if ($current_area == 'C') {
		$order['title'] = func_get_title($order['titleid']);
		$order['b_title'] = func_get_title($order['b_titleid']);
		$order['s_title'] = func_get_title($order['s_titleid']);
		$tmp = func_get_languages_alt("payment_method_".$order['paymentid'], $shop_language);
		if (!empty($tmp)) {
			$order['payment_method_orig'] = $order['payment_method'];
			$order['payment_method'] = $tmp;
		}
	}

	$order["discounted_subtotal"] = $order["subtotal"] - $order["discount"] - $order["coupon_discount"];

	if ($order["giftcert_ids"]) {
		$order["applied_giftcerts"] = split("\*", $order["giftcert_ids"]);
		if ($order["applied_giftcerts"]) {
			$tmp = array();
			foreach ($order["applied_giftcerts"] as $k=>$v) {
				if (empty($v))
					continue;

				list($arr["giftcert_id"], $arr["giftcert_cost"]) = split(":", $v);
				$tmp[] = $arr;
			}

			$order["applied_giftcerts"] = $tmp;
		}
	}

	$shipping = func_query_first("select shipping from $sql_tbl[shipping] where shippingid='".$order["shippingid"]."'");

	$order["shipping"] = $shipping["shipping"];

	$order_details_crypt_type = func_get_crypt_type($order["details"]);
	$order["details"] = text_decrypt($order["details"]);
	if (is_null($order["details"])) {
		$order["details"] = func_get_langvar_by_name("err_data_corrupted");
		$order['details_corrupted'] = true;
		if ($order_details_crypt_type != 'C' || func_get_crypt_key("C") !== false) {
			x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt order details for the order ".$orderid, true);
		}

	}
	elseif ($order["details"] === false) {
		$order["details"] = func_get_langvar_by_name("txt_this_data_encrypted");
		$order['details_encrypted'] = true;
	}
	else {
		$order["details"] = stripslashes($order["details"]);
	}

	$order["notes"] = stripslashes($order["notes"]);
	$order["extra"] = @unserialize($order["extra"]);
	$extras = func_query("SELECT khash, value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid'");
	if (!empty($extras)) {
		foreach($extras as $v)
			$order["extra"][$v["khash"]] = $v["value"];
	}

	if ($current_area != "C" && !empty($active_modules["Stop_List"])) {
		if (func_ip_exist_slist($order["extra"]["ip"]))
			$order["blocked"] = "Y";
	}

	if ($order["taxes_applied"])
		$order["applied_taxes"] = unserialize($order["taxes_applied"]);

	if (preg_match("/NetBanx Reference: ([\w\d]+)/iSs", $order["details"], $preg)) {
		$order['netbanx_reference'] = $preg[1];
	}

	#
	# Assign the display_* vars for displaying in the invoice
	#
	if (@$order["extra"]["tax_info"]["display_taxed_order_totals"] == "Y" && !empty($order["extra"]["tax_info"]["taxed_subtotal"]))
		$order["display_subtotal"] = $order["extra"]["tax_info"]["taxed_subtotal"];
	else
		$order["display_subtotal"] = $order["subtotal"];

	if (@$order["extra"]["tax_info"]["display_taxed_order_totals"] == "Y" && !empty($order["extra"]["tax_info"]["taxed_discounted_subtotal"]))
		$order["display_discounted_subtotal"] = $order["extra"]["tax_info"]["taxed_discounted_subtotal"];
	else
		$order["display_discounted_subtotal"] = $order["discounted_subtotal"];

	if (@$order["extra"]["tax_info"]["display_taxed_order_totals"] == "Y" && !empty($order["extra"]["tax_info"]["taxed_shipping"]))
		$order["display_shipping_cost"] = $order["extra"]["tax_info"]["taxed_shipping"];
	else
		$order["display_shipping_cost"] = $order["shipping_cost"];

	list($order["b_address"], $order["b_address_2"]) = explode("\n", $order["b_address"]);
	$order["b_statename"] = func_get_state($order["b_state"], $order["b_country"]);
	$order["b_countryname"] = func_get_country($order["b_country"]);
	list($order["s_address"], $order["s_address_2"]) = explode("\n", $order["s_address"]);
	$order["s_statename"] = func_get_state($order["s_state"], $order["s_country"]);
	$order["s_countryname"] = func_get_country($order["s_country"]);

	if ($config["General"]["use_counties"] == "Y") {
		$order["b_countyname"] = func_get_county($order["b_county"]);
		$order["s_countyname"] = func_get_county($order["s_county"]);
	}

	return $order;
}

#
# This function returns data about specified order ($orderid)
#
function func_order_data($orderid) {
	global $sql_tbl, $config, $smarty, $active_modules, $current_area, $xcart_dir;
	global $xcart_catalogs;

	$join = "";
	$gc_add_date = ", add_date+'".$config["Appearance"]["timezone_offset"]."' as add_date";
	$fields = $gc_add_date;

	if (!empty($active_modules["Egoods"])) {
		$join .= " LEFT JOIN $sql_tbl[download_keys] ON $sql_tbl[order_details].itemid=$sql_tbl[download_keys].itemid AND $sql_tbl[download_keys].productid=$sql_tbl[order_details].productid";
		$fields .= ", $sql_tbl[download_keys].download_key, $sql_tbl[download_keys].expires";
	}

	$products = func_query("SELECT $sql_tbl[order_details].itemid, $sql_tbl[products].*, $sql_tbl[order_details].*, IF($sql_tbl[products].productid IS NOT NULL OR $sql_tbl[order_details].product = '', $sql_tbl[products].product, $sql_tbl[order_details].product) as product, IF($sql_tbl[products].productid IS NULL, 'Y', '') as is_deleted $fields FROM $sql_tbl[order_details] LEFT JOIN $sql_tbl[products] ON $sql_tbl[order_details].productid = $sql_tbl[products].productid $join WHERE $sql_tbl[order_details].orderid='$orderid'");

	if (!is_array($products))
		$products = array();

	#
	# If products are not present in products table, but they are present in
	# order_details, then create fake $products from order_details data
	#
	$is_returns = false;
	if (!empty($products) && !empty($active_modules['RMA'])) {
		foreach ($products as $k => $v) {
			$products[$k]['returns'] = func_query("SELECT * FROM $sql_tbl[returns] WHERE itemid = '$v[itemid]'");
			if (!empty($products[$k]['returns'])) {
				$is_returns = true;
				foreach (array('A','R','C') as $s) {
					$products[$k]['returns_sum_'.$s] = func_query_first_cell("SELECT SUM(amount) FROM $sql_tbl[returns] WHERE itemid = '$v[itemid]' AND status = '$s'");
				}
			}
		}
	}

	$giftcerts = func_query("SELECT * $gc_add_date FROM $sql_tbl[giftcerts] WHERE orderid = '$orderid'");
	if (!empty($giftcerts) && $config["General"]["use_counties"] == "Y") {
		foreach ($giftcerts as $k => $v) {
			if (!empty($v['recipient_county']))
				$giftcerts[$k]['recipient_countyname'] = func_get_county($v['recipient_county']);
		}
	}

	$order = func_select_order($orderid);
	if (!$order)
		return false;

	$order['is_returns'] = $is_returns;

	if ($current_area == "A" || ($current_area == "P" && !empty($active_modules['Simple_Mode']))) {
		if (strpos($order['details'], "{CardNumber}:") !== false && file_exists($xcart_dir."/payment/cmpi.php"))
			$order['is_cc_payment'] = "Y";
	}

	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_details], $sql_tbl[download_keys] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].itemid = $sql_tbl[download_keys].itemid")) {
		$order['is_egood'] = 'Y';
	}
	elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_details], $sql_tbl[products] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].productid = $sql_tbl[products].productid AND $sql_tbl[products].distribution != ''")) {
		$order['is_egood'] = 'E';
	}

	$userinfo = func_query_first("SELECT *, date+'".$config["Appearance"]["timezone_offset"]."' as date FROM $sql_tbl[orders] WHERE orderid = '$orderid'");
	if (isset($order["extra"]['additional_fields'])) {
		$userinfo['additional_fields'] = $order["extra"]['additional_fields'];
	}

	$userinfo['titleid'] = func_detect_title($userinfo['title']);
	$userinfo['b_titleid'] = func_detect_title($userinfo['b_title']);
	$userinfo['s_titleid'] = func_detect_title($userinfo['s_title']);
	if ($current_area == 'C') {
		$userinfo['title'] = func_get_title($userinfo['titleid']);
		$userinfo['b_title'] = func_get_title($userinfo['b_titleid']);
		$userinfo['s_title'] = func_get_title($userinfo['s_titleid']);
	}

	$userinfo = func_array_merge(func_userinfo($userinfo["login"], "C", false, false, array("C","H")), $userinfo);

	list($userinfo["b_address"], $userinfo["b_address_2"]) = split("[\n\r]+", $userinfo["b_address"]);
	list($userinfo["s_address"], $userinfo["s_address_2"]) = split("[\n\r]+", $userinfo["s_address"]);

	$userinfo["s_countryname"] = $userinfo["s_country_text"] = func_get_country($userinfo["s_country"]);
	$userinfo["s_statename"] = $userinfo["s_state_text"] = func_get_state($userinfo["s_state"], $userinfo["s_country"]);
	$userinfo["b_statename"] = func_get_state($userinfo["b_state"], $userinfo["b_country"]);
	$userinfo["b_countryname"] = func_get_country($userinfo["b_country"]);
	if ($config["General"]["use_counties"] == "Y") {
		$userinfo["b_countyname"] = func_get_county($userinfo["b_county"]);
		$userinfo["s_countyname"] = func_get_county($userinfo["s_county"]);
	}

	if (!$products)
		$products = array ();

	if (preg_match("/(free_ship|percent|absolute)(?:``)(.+)/S", $order["coupon"], $found)) {
		$order["coupon"] = $found[2];
		$order["coupon_type"] = $found[1];
	}

	$order["extra"]["tax_info"]["product_tax_name"] = "";
	$_product_taxes = array();

/* Start addition by Michael de Leon 09.21.06 */
foreach ($products as $k=>$v) {
if (!empty($active_modules['Extra_Fields']) && $v['is_deleted'] != 'Y') {
$products[$k]['extra_fields'] = $v['extra_fields'] = func_query("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid = '$v[productid]' AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby");
}
/* End addition by Michael de Leon 09.21.06 */
/* Deleted by Michael de Leon 09.21.06
	foreach ($products as $k=>$v) {
		if (!empty($active_modules['Extra_Fields']) && $v['is_deleted'] != 'Y') {
			$products[$k]['extra_fields'] = func_query("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid = '$v[productid]' AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby");
		}
*/
		$v['product_options_txt'] = $v['product_options'];
		if ($v["extra_data"]) {
			$v["extra_data"] = unserialize($v["extra_data"]);
			if (is_array(@$v["extra_data"]["display"])) {
				foreach ($v["extra_data"]["display"] as $i=>$j) {
					$v["display_".$i] = $j;
				}
			}
			if (is_array($v["extra_data"]["taxes"])) {
				foreach ($v["extra_data"]["taxes"] as $i=>$j) {
					if ($j["tax_value"] > 0)
						$_product_taxes[$i] = $j["tax_display_name"];
				}
			}
		}

		$v["original_price"] = $v["ordered_price"] = $v["price"];
		$v["price_deducted_tax"] = "Y";

		#
		# Get the original price (current price in the database)
		#
		if ($v['is_deleted'] != 'Y') {
			$v["original_price"] = func_query_first_cell("SELECT MIN($sql_tbl[pricing].price) FROM $sql_tbl[pricing] WHERE $sql_tbl[pricing].productid = '$v[productid]' AND $sql_tbl[pricing].membershipid IN ('$userinfo[membershipid]', 0) AND $sql_tbl[pricing].quantity <= '$v[amount]' AND $sql_tbl[pricing].variantid = 0");
			if (!empty($active_modules['Product_Options']) && $v['extra_data']['product_options']) {
				list($variant, $product_options) = func_get_product_options_data($v['productid'], $v['extra_data']['product_options'],$userinfo['membershipid']);

				if ($product_options === false) {
					unset($product_options);
				}
				else {
					if (empty($variant['price']))
						$variant['price'] = $v["original_price"];

					$v["original_price"] = $variant['price'];
					unset($variant['price']);
					if ($product_options) {
						foreach ($product_options as $o) {
							if ($o['modifier_type'] == '%')
								$v["original_price"] += $v["original_price"]*$o['price_modifier']/100;
							else
								$v["original_price"] += $o['price_modifier'];
						}
					}

					$v['product_options'] = $product_options;

					# Check current and saved product options set
					if (!empty($v['product_options_txt'])) {
						$flag_txt = true;

						# Check saved product options
						$count = 0;
						foreach ($v['product_options'] as $opt) {
							if (preg_match("/".preg_quote($opt['class'],"/").": ".preg_quote($opt['option_name'], "/")."/Sm", $v['product_options_txt']))
								$count++;
						}
						if ($count != count($v['product_options']))
							$flag_txt = false;

						# Check current product options set
						if ($flag_txt) {
							$count = 0;
							$tmp = explode("\n", $v['product_options_txt']);
							foreach ($tmp as $txt_row) {
								if (!preg_match("/^([^:]+): (.*)$/S", trim($txt_row), $match))
									continue;

								foreach ($v['product_options'] as $opt) {
									if ($match[1] == $opt['class'] && $match[2] == trim($opt['option_name'])) {
										$count++;
										break;
									}
								}
							}

							if ($count != count($tmp))
								$flag_txt = false;
						}

						# Force display saved product options set
						# if saved and current product options sets wasn't equal
						if(!$flag_txt)
							$v['force_product_options_txt'] = true;
					}

					if (!empty($variant)) {
						$v = func_array_merge($v, $variant);
					}
				}
			}
		}

		$products[$k] = $v;

	}

	if (count($_product_taxes) == 1) {
		$order["extra"]["tax_info"]["product_tax_name"] = array_pop($_product_taxes);
	}

	if ($order["coupon_type"] == "free_ship") {
		$order["shipping_cost"] = $order["coupon_discount"];
		$order["discounted_subtotal"] += $order["coupon_discount"];
	}

	return array(
		"order" => $order,
		"products" => $products,
		"userinfo" => $userinfo,
		"giftcerts" => $giftcerts);
}

#
# This function increments product rating
#
function func_increment_rating($productid) {
	global $sql_tbl;

	db_query("UPDATE $sql_tbl[products] SET rating=rating+1 WHERE productid='$productid'");
}

#
# Decrease number of products in stock and increase product rating
#
function func_decrease_quantity($products) {
	if (!empty($products) && is_array($products)) {
		foreach ($products as $product) {
			func_increment_rating($product["productid"]);
		}
	}

	func_update_quantity($products, false);
}

#
# This function creates order entry in orders table
#
function func_place_order($payment_method, $order_status, $order_details, $customer_notes, $extra = array(), $extras = array()) {
	global $cart, $userinfo, $discount_coupon, $mail_smarty, $config, $active_modules, $single_mode, $partner, $adv_campaignid, $partner_clickid;
	global $sql_tbl, $to_customer;
	global $wlid, $HTTP_COOKIE_VARS;
	global $xcart_dir, $REMOTE_ADDR, $PROXY_IP, $CLIENT_IP, $add_to_cart_time;
	global $arb_account_used, $arb_account;

	$mintime = 10;
	#
	# Lock place order process
	#
	func_lock("place_order");

	$userinfo['title'] = func_get_title($userinfo['titleid'], $config['default_admin_language']);
	$userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $config['default_admin_language']);
	$userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $config['default_admin_language']);

	$check_order = func_query_first("SELECT orderid FROM $sql_tbl[orders] WHERE login='".addslashes($userinfo["login"])."' AND '".time()."'-date<'$mintime'");
	if ($check_order) {
		func_unlock("place_order");
		return false;
	}

	if (($order_status != "I") && ($order_status != "Q")) {
		func_unlock("place_order");
		return false;
	}

	$userinfo["email"] = addslashes($userinfo["email"]);

	$orderids = array ();

	#
	# REMOTE_ADDR and PROXY_IP
	#
	$extras['ip'] = $CLIENT_IP;
	$extras['proxy_ip'] = $PROXY_IP;
	if (!empty($cart['shipping_warning'])) {
		$extras['shipping_warning'] = $cart['shipping_warning'];
	}

	if ($add_to_cart_time > 0) {
		$extras['add_to_cart_time'] = time() - $add_to_cart_time;
	}

	if (!empty($HTTP_COOKIE_VARS['personal_client_id'])) {
		$extras['personal_client_id'] = $HTTP_COOKIE_VARS['personal_client_id'];
	}

	#
	# Validate cart contents
	#
	if (!func_cart_is_valid($cart, $userinfo)) {
		# current state of cart is not valid and we cannot
		# re-calculate it now
		func_unlock("place_order");
		return false;
	}

	$products = $cart['products'];
	func_decrease_quantity($products);

	$giftcert_discount = $cart["giftcert_discount"];
	if ($cart["applied_giftcerts"]) {
		foreach ($cart["applied_giftcerts"] as $k=>$v) {
			$giftcert_str = join("*", array(@$giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));
			db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE gcid='$v[giftcert_id]'");
		}
	}

	$giftcert_id = @$cart["giftcert_id"];

	$extra = "";
	if (!empty($active_modules["Anti_Fraud"]) && defined("IS_AF_CHECK") && ($cart['total_cost'] > 0 || $config['Anti_Fraud']['check_zero_order'] == 'Y')) {
		include $xcart_dir."/modules/Anti_Fraud/anti_fraud.php";
	}

	#
	# Store Airborne account information into $order_details
	#
	x_session_register("arb_account_used");
	x_session_register("arb_account");
	if ($arb_account_used) {
		$_code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");
		if ($_code == "ARB")
			$order_details = func_get_langvar_by_name("lbl_arb_account").": ".$arb_account."\n".$order_details;
	}
	$extra['additional_fields'] = $userinfo['additional_fields'];

	foreach ($cart["orders"] as $current_order) {
		$_extra = $extra;
		$_extra["tax_info"] = array (
			"display_taxed_order_totals" => $config["Taxes"]["display_taxed_order_totals"],
			"display_cart_products_tax_rates" => $config["Taxes"]["display_cart_products_tax_rates"] == "Y",
			"taxed_subtotal" => $current_order["display_subtotal"],
			"taxed_discounted_subtotal" => $current_order["display_discounted_subtotal"],
			"taxed_shipping" => $current_order["display_shipping_cost"]);

		if (!empty($active_modules["Special_Offers"]))
			include $xcart_dir."/modules/Special_Offers/place_order_extra.php";

		if (!$single_mode) {
			$giftcert_discount = $current_order["giftcert_discount"];
			$giftcert_str = "";
			if ($current_order["applied_giftcerts"]) {
				foreach($current_order["applied_giftcerts"] as $k=>$v)
					$giftcert_str = join("*", array($giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));
			}
		}
		else
			$current_order['payment_surcharge'] = $cart['payment_surcharge'];

		$taxes_applied = addslashes(serialize($current_order["taxes"]));

		$discount_coupon = $current_order["coupon"];
		if (!empty($current_order["coupon"])) {
			$current_order["coupon"] = func_query_first_cell("SELECT coupon_type FROM $sql_tbl[discount_coupons] WHERE coupon='".addslashes($current_order["coupon"])."'")."``".$current_order["coupon"];
		}

		$save_info = $userinfo;
		$userinfo["b_address"] .= "\n".$userinfo["b_address_2"];
		$userinfo["s_address"] .= "\n".$userinfo["s_address_2"];

		#
		# Insert into orders
		#
		$insert_data = array (
			'login' => addslashes($userinfo['login']),
			'membershipid' => $userinfo['membershipid'],
			'membership' => addslashes($userinfo['membership']),
			'total' => $current_order['total_cost'],
			'giftcert_discount' => $giftcert_discount,
			'giftcert_ids' => @$giftcert_str,
			'subtotal' => $current_order['subtotal'],
			'shipping_cost' => $current_order['shipping_cost'],
			'shippingid' => $cart['shippingid'],
			'tax' => $current_order['tax_cost'],
			'taxes_applied' => $taxes_applied,
			'discount' => $current_order['discount'],
			'coupon' => addslashes(@$current_order['coupon']),
			'coupon_discount' => $current_order['coupon_discount'],
			'date' => time(),
			'status' => $order_status,
			'payment_method' => addslashes($payment_method),
			'paymentid' => $cart['paymentid'],
			'payment_surcharge' => $current_order['payment_surcharge'],
			'flag' => 'N',
			'details' => addslashes(text_crypt($order_details)),
			'customer_notes' => $customer_notes,
			'clickid' => $partner_clickid,
			'language' => $userinfo['language'],
			'extra' => addslashes(serialize($_extra)));

		# copy userinfo
		$_fields = array ('title','firstname','lastname','phone','fax','email','url','company','tax_number','tax_exempt');
		foreach ($_fields as $k) {
			if (!isset($userinfo[$k]))
				continue;

			$insert_data[$k] = addslashes($userinfo[$k]);
		}

		$_fields = array ('title','firstname','lastname','address','city','county','state','country','zipcode');
		foreach (array('b_','s_') as $p) {
			foreach ($_fields as $k) {
				$f = $p.$k;
				if (isset($userinfo[$f])) {
					$insert_data[$f] = addslashes($userinfo[$f]);
				}
			}
		}

		$orderid = func_array2insert('orders', $insert_data);
		unset($insert_data);

		if (!empty($extras) && is_array($extras)) {
			foreach ($extras as $k => $v) {
				if (strlen($v) > 0)
					db_query("INSERT INTO $sql_tbl[order_extras] (orderid, khash, value) VALUES ('$orderid', '".addslashes($k)."', '".addslashes($v)."')");
			}
		}

		$userinfo = $save_info;

		$orderids[] = $orderid;
		$order=func_select_order($orderid);

		#
		# Insert into order details
		#
		if (!empty($products) && is_array($products)) {

		foreach ($products as $pk => $product) {
			if (($single_mode) || ($product["provider"] == $current_order["provider"])) {
				$product["price"] = price_format($product["price"]);
				$product["extra_data"]["product_options"] = $product["options"];
				$product["extra_data"]["taxes"] = $product["taxes"];
				$product["extra_data"]["display"]["price"] = price_format($product["display_price"]);
				$product["extra_data"]["display"]["discounted_price"] = price_format($product["display_discounted_price"]);
				$product["extra_data"]["display"]["subtotal"] = price_format($product["display_subtotal"]);
				if (empty($product["product_orig"]))
					$product["product_orig"] = $product["product"];

				if(!empty($active_modules['Product_Options']))
					$product["product_options"] = func_serialize_options($product["options"]);

				$insert_data = array (
					'orderid' => $orderid,
					'productid' => $product['productid'],
					'product' => addslashes($product['product_orig']),
					'product_options' => addslashes($product['product_options']),
					'amount' => $product['amount'],
					'price' => $product['price'],
					'provider' => addslashes($product["provider"]),
					'extra_data' => addslashes(serialize($product["extra_data"])),
					'productcode' => addslashes($product['productcode']));


				$products[$pk]['itemid'] = func_array2insert('order_details', $insert_data);
				unset($insert_data);

				#
				# Insert into subscription_customers table (for subscription products)
				#
				if (!empty($active_modules["Subscriptions"]))
					include $xcart_dir."/modules/Subscriptions/subscriptions_cust.php";

				#
				# Check if this product is in Wish list
				#
				if (!empty($active_modules["Wishlist"]))
					include $xcart_dir."/modules/Wishlist/place_order.php";

				if (!empty($active_modules["Recommended_Products"])) {
					$rec_counter = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stats_customers_products] WHERE productid='$product[productid]' AND login='".addslashes($userinfo["login"])."'");

					if (!empty($rec_counter)) {
						db_query("UPDATE $sql_tbl[stats_customers_products] SET counter='".($rec_counter+1)."' WHERE productid='$product[productid]' AND login='".addslashes($userinfo["login"])."'");
					}
					else {
						db_query("INSERT INTO $sql_tbl[stats_customers_products] (productid, login, counter) VALUES ('$product[productid]', '".addslashes($userinfo["login"])."', '1')");
					}
				}
			}
		}

		}

		if (!empty($active_modules['XAffiliate'])) {
			#
			# Partner commission
			#
			if (!empty($partner))
				include $xcart_dir."/include/partner_commission.php";

			#
			# Save link order -> advertising campaign
			#
			if ($adv_campaignid)
				include $xcart_dir."/include/adv_campaign_commission.php";
		}

		if ((($single_mode) || (empty($current_order["provider"]))) && (!empty($cart["giftcerts"]))) {
			foreach($cart["giftcerts"] as $gk => $giftcert) {
				$gcid = substr(strtoupper(md5(uniqid(rand()))), 0, 16);

				#
				# status == Pending!
				#
				$insert_data = array (
					'gcid' => $gcid,
					'orderid' => $orderid,
					'purchaser' => addslashes($giftcert['purchaser']),
					'recipient' => addslashes($giftcert['recipient']),
					'send_via' => $giftcert['send_via'],
					'recipient_email' => @$giftcert['recipient_email'],
					'recipient_firstname' => addslashes(@$giftcert['recipient_firstname']),
					'recipient_lastname' => addslashes(@$giftcert['recipient_lastname']),
					'recipient_address' => addslashes(@$giftcert['recipient_address']),
					'recipient_city' => addslashes(@$giftcert['recipient_city']),
					'recipient_county' => @$giftcert['recipient_county'],
					'recipient_state' => @$giftcert['recipient_state'],
					'recipient_country' => @$giftcert['recipient_country'],
					'recipient_zipcode' => @$giftcert['recipient_zipcode'],
					'recipient_phone' => @$giftcert['recipient_phone'],
					'message' => addslashes($giftcert['message']),
					'amount' => $giftcert['amount'],
					'debit' => $giftcert['amount'],
					'status' => 'P',
					'add_date' => time());

				if ($giftcert['send_via'] == 'P') {
					$insert_data['tpl_file'] = $giftcert['tpl_file'];
				}

				func_array2insert('giftcerts', $insert_data);
				unset($insert_data);

				$cart["giftcerts"][$gk]['gcid'] = $gcid;

				#
				# Check if this giftcertificate is in Wish list
				#
				if (!empty($active_modules["Wishlist"])) {
					include $xcart_dir."/modules/Wishlist/place_order.php";
				}
			}
		}

		#
		# Mark discount coupons used
		#
		if ($discount_coupon) {
			$_per_user = func_query_first_cell("SELECT per_user FROM $sql_tbl[discount_coupons] WHERE coupon='$discount_coupon' LIMIT 1");
			if ($_per_user == "Y") {
				$_need_to_update = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons_login] WHERE coupon='$discount_coupon' AND login='".addslashes($userinfo["login"])."' LIMIT 1");
				if ($_need_to_update > 0)
					db_query("UPDATE $sql_tbl[discount_coupons_login] SET times_used=times_used+1 WHERE coupon='$discount_coupon' AND login='".addslashes($userinfo["login"])."'");
				else
					db_query("INSERT INTO $sql_tbl[discount_coupons_login] (coupon, login, times_used) VALUES ('$discount_coupon', '".addslashes($userinfo["login"])."', '1')");
			}
			else {
				db_query("UPDATE $sql_tbl[discount_coupons] SET times_used=times_used+1 WHERE coupon='$discount_coupon'");
				db_query("UPDATE $sql_tbl[discount_coupons] SET status='U' WHERE coupon='$discount_coupon' AND times_used=times");
			}
			$discount_coupon="";
		}

		#
		# Mail template processing
		#
		$admin_notify = (($order_status == "Q" && $config["Email_Note"]["enable_order_notif"] == "Y") || ($order_status == "I" && $config["Email_Note"]["enable_init_order_notif"] == "Y"));
		$customer_notify = (($order_status == "Q") || ($order_status == "I" && $config["Email_Note"]["enable_init_order_notif_customer"] == "Y"));

		$order_data = func_order_data($orderid);
		$mail_smarty->assign("products",$order_data["products"]);
		$mail_smarty->assign("giftcerts",$order_data["giftcerts"]);
		$mail_smarty->assign("order",$order_data["order"]);
		$mail_smarty->assign("userinfo",$order_data["userinfo"]);

		$prefix = ($order_status=="I"?"init_":"");

		if ($customer_notify) {
			#
			# Notify customer by email
			#
			$to_customer = ($userinfo['language']?$userinfo['language']:$config['default_customer_language']);
			$mail_smarty->assign("products", func_translate_products($order_data["products"], $to_customer));

			func_send_mail($userinfo["email"], "mail/".$prefix."order_customer_subj.tpl", "mail/".$prefix."order_customer.tpl", $config["Company"]["orders_department"], false);
		}

		if (!empty($order_data["order"]['payment_method_orig'])) {
			$order_data["order"]['payment_method'] = $order_data["order"]['payment_method_orig'];
			$mail_smarty->assign("order",$order_data["order"]);
		}

		$mail_smarty->assign("products",$order_data["products"]);
		if ($admin_notify) {
			#
			# Notify orders department by email
			#
			$mail_smarty->assign("show_order_details", "Y");
			func_send_mail($config["Company"]["orders_department"], "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification_admin.tpl", $userinfo["email"], true, true);
			$mail_smarty->assign("show_order_details", "");

			#
			# Notify provider (or providers) by email
			#
			if ((!$single_mode) && ($current_order["provider"]) && $config["Email_Note"]["send_notifications_to_provider"] == "Y") {
				$pr_result = func_query_first ("SELECT email, language FROM $sql_tbl[customers] WHERE login='$current_order[provider]'");
				$prov_email = $pr_result ["email"];
				if ($prov_email != $config["Company"]["orders_department"]) {
					$to_customer = $pr_result['language'];
					if (empty($to_customer))
						$to_customer = $config['default_admin_language'];

					func_send_mail($prov_email, "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification.tpl", $userinfo["email"], false);
				}
			}
			elseif ($config["Email_Note"]["send_notifications_to_provider"] == "Y" && !empty($products) && is_array($products)) {
				$providers = array();
				foreach($products as $product) {
					$pr_result = func_query_first("select email, language from $sql_tbl[customers] where login='$product[provider]'");
					if ($pr_result["email"])
						$providers[$product['provider']] = $pr_result;
				}

				if ($providers) {
					foreach ($providers as $prov_data) {
						if ($prov_data['email'] == $config["Company"]["orders_department"])
							continue;

						$to_customer = $prov_data['language'];
						if (empty($to_customer))
							$to_customer = $config['default_admin_language'];

						func_send_mail($prov_data['email'], "mail/".$prefix."order_notification_subj.tpl", "mail/order_notification.tpl", $userinfo["email"], false);
					}
				}
			}
		}

		if (!empty($active_modules['Survey']) && defined("AREA_TYPE") && constant("AREA_TYPE") == 'C') {
			func_check_surveys_events("OPL", $order_data);
		}

	}

	#
	# Send notifications to orders department and providers when product amount in stock is low
	#
	if ($config["General"]["unlimited_products"]!="Y") {
		foreach($order_data["products"] as $product) {
			if (!empty($product["distribution"]) && $active_modules["Egoods"])
				continue;

			if ($product['product_type'] == 'C' && !empty($active_modules['Product_Configurator']))
				continue;

			if ($active_modules['Product_Options'] && $product['extra_data']['product_options']) {
				$avail_now = func_get_options_amount($product['extra_data']['product_options'], $product['productid']);
			}
			else {
				$avail_now = func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid='".$product["productid"]."'");
			}

			if ($product['low_avail_limit'] >= $avail_now && $config['Email_Note']['eml_lowlimit_warning'] == 'Y') {
				#
				# Mail template processing
				#
				$product['avail'] = $avail_now;
				$mail_smarty->assign("product", $product);

				func_send_mail($config["Company"]["orders_department"], "mail/lowlimit_warning_notification_subj.tpl", "mail/lowlimit_warning_notification_admin.tpl", $config["Company"]["orders_department"], true);

				$pr_result = func_query_first ("SELECT email, language FROM $sql_tbl[customers] WHERE login='".$product["provider"]."'");
				if ((!$single_mode) && ($pr_result["email"]!=$config["Company"]["orders_department"]) && $config['Email_Note']['eml_lowlimit_warning_provider'] == 'Y') {
					$to_customer = $pr_result['language'];
					if (empty($to_customer))
						$to_customer = $config['default_admin_language'];

					func_send_mail($pr_result["email"], "mail/lowlimit_warning_notification_subj.tpl", "mail/lowlimit_warning_notification_admin.tpl", $config["Company"]["orders_department"], false);
				}
			}
		}
	}

	#
	# Release previously created lock
	#
	func_unlock("place_order");

	return $orderids;
}

#
# This function change order status in orders table
#
function func_change_order_status($orderids, $status, $advinfo="") {
	global $config, $mail_smarty, $active_modules, $current_area;
	global $sql_tbl;
	global $session_failed_transaction;

	if (!is_array($orderids)) $orderids = array($orderids);

	foreach ($orderids as $orderid) {
		$order_data = func_order_data($orderid);
		if (empty($order_data))
			continue;

		$order=$order_data["order"];

		if ($advinfo)
			$info = addslashes(text_crypt($order["details"]."\n--- Advanced info ---\n".$advinfo));

		db_query("update $sql_tbl[orders] set status='$status'".(($advinfo)? ", details='".$info."'" : "")." where orderid='$orderid'");

		$send_notification = false;

		if ($status == "P" && $order["status"] != "P") {
			$flag = true;
			if (in_array($order["status"], array('I','Q')) && !empty($active_modules["Anti_Fraud"]) && $config['Anti_Fraud']['anti_fraud_license'] && ($current_area != 'A' && $current_area != 'P') && !empty($order['extra']['Anti_Fraud'])) {
				$total_trust_score = $order['extra']['Anti_Fraud']['total_trust_score'];
				$available_request = $order['extra']['Anti_Fraud']['available_request'];
				$used_request = $order['extra']['Anti_Fraud']['used_request'];

				if ($total_trust_score > $config['Anti_Fraud']['anti_fraud_limit'] || ($available_request <= $used_request && $available_request > 0)) {
					$flag = false;
					db_query("UPDATE $sql_tbl[orders] SET status = 'Q' WHERE orderid = '$orderid'");
					$send_notification = true;
				}
			}

			if ($flag) {
				func_process_order($orderid);
			}
		}
		elseif ($status == "D" && $order["status"] != "D" && $order["status"] != "F") {
			func_decline_order($orderid, $status);
		}
		elseif ($status == "F" && $order["status"] != "F" && $order["status"] != "D") {
			func_decline_order($orderid, $status);
			if ($current_area == 'C')
				$session_failed_transaction++;
		}
		elseif ($status == "C" && $order["status"] != "C") {
			func_complete_order($orderid);

		} elseif ($status == "Q" && $order["status"] == "I" && $current_area != 'A' && $current_area != 'P') {
			$send_notification = true;
		}

		#
		# Decrease quantity in stock when "declined" or "failed" order is became "completed", "processed" or "queued"
		#
		if ($status != $order["status"] && strpos("DF",$order["status"])!==false && strpos("CPQI",$status)!==false) {
			func_update_quantity($order_data["products"],false);
		}

		if ($send_notification) {

			# Send notification to customer
			$to_customer = ($order_data['userinfo']['language'] ? $order_data['userinfo']['language'] : $config['default_customer_language']);
			$mail_smarty->assign("products", func_translate_products($order_data["products"], $to_customer));
			$mail_smarty->assign("giftcerts",$order_data["giftcerts"]);
			$mail_smarty->assign("order",$order_data["order"]);
			$mail_smarty->assign("userinfo",$order_data["userinfo"]);
			func_send_mail($order_data["userinfo"]['email'], "mail/order_customer_subj.tpl", "mail/order_customer.tpl", $config["Company"]["orders_department"], false);
		}
	}

	$op_message = "Login: $login\nIP: $REMOTE_ADDR\nOperation: change status of orders (".implode(',',$orderids).") to '$status'\n----";
	x_log_flag('log_orders_change_status', 'ORDERS', $op_message, true);
}

#
# This function performs activities nedded when order is processed
#
function func_process_order($orderids) {
	global $config, $mail_smarty, $active_modules;
	global $sql_tbl, $partner, $to_customer;
	global $single_mode;
	global $xcart_dir;
	global $current_area;

	if (empty($orderids))
		return false;

	if (!is_array($orderids))
		$orderids = array($orderids);

	foreach($orderids as $orderid) {

		if (empty($orderid))
			continue;

		$order_data = func_order_data($orderid);
		if (empty($order_data))
			continue;

		$order = $order_data["order"];
		$userinfo = $order_data["userinfo"];
		$products = $order_data["products"];
		$giftcerts = $order_data["giftcerts"];

		$mail_smarty->assign("customer",$userinfo);
		$mail_smarty->assign("products",$products);
		$mail_smarty->assign("giftcerts",$giftcerts);
		$mail_smarty->assign("order",$order);

		#
		# Order processing routine
		# Send gift certificates
		#
		if ($order["applied_giftcerts"]) {
			#
			# Search for enabled to applying GC
			#
			$flag = true;
			foreach ($order["applied_giftcerts"] as $k=>$v) {
				$res = func_query_first("SELECT gcid FROM $sql_tbl[giftcerts] WHERE gcid='$v[giftcert_id]' AND debit>='$v[giftcert_cost]'");
				if (!$res["gcid"]) {
					$flag = false;
					break;
				}
			}

			#
			# Decrease debit for applied GC
			#
			if (!$flag)
				return false;

			foreach ($order["applied_giftcerts"] as $k=>$v) {
				db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit-'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
				db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>0 AND gcid='$v[giftcert_id]'");
				db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE debit<=0 AND gcid='$v[giftcert_id]'");
			}
		}


		if ($giftcerts) {
			foreach ($giftcerts as $giftcert) {
				db_query("update $sql_tbl[giftcerts] set status='A' where gcid='$giftcert[gcid]'");
				if ($giftcert["send_via"] == "E")
					func_send_gc($userinfo["email"], $giftcert, $userinfo['login']);
			}
		}

		#
		# Send mail notifications
		#
		if ($config['Email_Note']['eml_order_p_notif_provider'] == 'Y') {
			$providers= func_query("select provider from $sql_tbl[order_details] where $sql_tbl[order_details].orderid='$orderid' group by provider");

			if (is_array($providers)) {
				foreach($providers as $provider) {
					$email_pro = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE login='$provider[provider]'");
					if (!empty($email_pro) && $email_pro != $config["Company"]["orders_department"]) {
						$to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE login='$provider[provider]'");
						if(empty($to_customer))
							$to_customer = $config['default_admin_language'];

						func_send_mail($email_pro, "mail/order_notification_subj.tpl", "mail/order_notification.tpl", $config["Company"]["orders_department"], false);
					}
				}
			}
		}

		$to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
		if (empty($to_customer))
			$to_customer = $config['default_customer_language'];

		if ($config['Email_Note']['eml_order_p_notif_customer'] == 'Y') {
			$mail_smarty->assign("products", func_translate_products($products, $to_customer));
			$_userinfo = $userinfo;
			$userinfo['title'] = func_get_title($userinfo['titleid'], $to_customer);
			$userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
			$userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);
			$mail_smarty->assign("customer",$userinfo);

			if ($current_area == 'C')
				$mail_body_template = "mail/order_customer.tpl";
			else
				$mail_body_template = "mail/order_customer_processed.tpl";
			
			func_send_mail($userinfo["email"], "mail/order_cust_processed_subj.tpl", $mail_body_template, $config["Company"]["orders_department"], false);

			$userinfo = $_userinfo;
			unset($_userinfo);
		}

		$mail_smarty->assign("products",$products);
		$mail_smarty->assign("show_order_details", "Y");
		if ($config['Email_Note']['eml_order_p_notif_admin'] == 'Y') {
			$to_customer = $config['default_admin_language'];
			func_send_mail($config["Company"]["orders_department"], "mail/order_notification_subj.tpl", "mail/order_notification_admin.tpl", $config["Company"]["orders_department"], true, true);
		}

		$mail_smarty->assign("show_order_details", "");

		#
		# Send E-goods download keys
		#
		if (!empty($active_modules["Egoods"]))
			include $xcart_dir."/modules/Egoods/send_keys.php";

		#
		# Update statistics for sold products
		#
		if ($active_modules["Advanced_Statistics"]) {
			include $xcart_dir."/modules/Advanced_Statistics/prod_sold.php";
		}

		if (!empty($active_modules["SnS_connector"])) {
			global $HTTP_COOKIE_VARS;

			$_old = $HTTP_COOKIE_VARS;
			$HTTP_COOKIE_VARS['personal_client_id'] = $order['extra']['personal_client_id'];
			func_generate_sns_action("Order", $orderid);
			$HTTP_COOKIE_VARS = $_old;
		}

		if (!empty($active_modules['Survey']) && !empty($userinfo)) {
			func_check_surveys_events("OPP", $order_data, $userinfo['login']);
			func_check_surveys_events("OPB", $order_data, $userinfo['login']);
		}

	}

}

#
# This function performs activities nedded when order is complete
#
function func_complete_order($orderid) {
	global $config, $mail_smarty, $active_modules;
	global $sql_tbl, $to_customer;
	global $xcart_dir;

	$order_data = func_order_data($orderid);
	if (empty($order_data))
		return false;

	$order = $order_data["order"];
	$userinfo = $order_data["userinfo"];
	$products = $order_data["products"];
	$giftcerts = $order_data["giftcerts"];

	$mail_smarty->assign("products",$products);
	$mail_smarty->assign("giftcerts",$giftcerts);
	$mail_smarty->assign("order",$order);

	if (!empty($active_modules["Special_Offers"])) {
		include $xcart_dir."/modules/Special_Offers/complete_order.php";
	}

	#
	# Send mail notifications
	#
	if ($config['Email_Note']['eml_order_c_notif_customer'] == 'Y') {
		$to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
		if(empty($to_customer))
			$to_customer = $config['default_customer_language'];
		$userinfo['title'] = func_get_title($userinfo['titleid'], $to_customer);
		$userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
		$userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);
		$mail_smarty->assign("customer",$userinfo);
		$mail_smarty->assign("products", func_translate_products($products, $to_customer));
		func_send_mail($userinfo["email"], "mail/order_cust_complete_subj.tpl", "mail/order_customer_complete.tpl", $config["Company"]["orders_department"], false);
	}

	if (!empty($active_modules['Survey']) && !empty($userinfo)) {
		func_check_surveys_events("OPC", $order_data, $userinfo['login']);
		func_check_surveys_events("OPB", $order_data, $userinfo['login']);
	}

}

#
# This function performs activities nedded when order is declined
# status may be assign (D)ecline or (F)ail
# (D)ecline order sent mail to customer, (F)ail - not
#
function func_decline_order($orderids, $status = "D") {
	global $config, $mail_smarty;
	global $sql_tbl, $to_customer;

	if (($status != "D") && ($status != "F")) return;

	if (!is_array($orderids))$orderids = array($orderids);

	foreach($orderids as $orderid) {
		#
		# Order decline routine
		#
		$order_data = func_order_data($orderid);
		if (empty($order_data))
			continue;

		$order = $order_data["order"];
		$userinfo = $order_data["userinfo"];
		$products = $order_data["products"];
		$giftcerts = $order_data["giftcerts"];

		# Send mail notifications
		if ($status == "D") {
			$mail_smarty->assign("customer",$userinfo);
			$mail_smarty->assign("products",$products);
			$mail_smarty->assign("giftcerts",$giftcerts);
			$mail_smarty->assign("order",$order);

			if ($config['Email_Note']['eml_order_d_notif_customer'] == 'Y') {
				$to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE login='$userinfo[login]'");
				if (empty($to_customer))
					$to_customer = $config['default_customer_language'];

				$mail_smarty->assign("products", func_translate_products($products, $to_customer));
				$userinfo['title'] = func_get_title($userinfo['titleid'], $to_customer);
				$userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
				$userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);
				$mail_smarty->assign("customer",$userinfo);
				func_send_mail($userinfo["email"], "mail/decline_notification_subj.tpl","mail/decline_notification.tpl", $config["Company"]["orders_department"], false);
			}
		}

		#
		# Discount restoring
		#
		$discount_coupon = $order["coupon"];
		if ($discount_coupon) {
			$_per_user = func_query_first_cell("SELECT per_user FROM $sql_tbl[discount_coupons] WHERE coupon='$discount_coupon' LIMIT 1");
			if ($_per_user == "Y") {
				db_query("UPDATE $sql_tbl[discount_coupons_login] SET times_used=IF(times_used>0, times_used-1, 0) WHERE coupon='$discount_coupon' AND login='".$userinfo["login"]."'");
			}
			else {
				db_query("UPDATE $sql_tbl[discount_coupons] SET status='A' WHERE coupon='$discount_coupon' and times_used=times");
				db_query("UPDATE $sql_tbl[discount_coupons] SET times_used=times_used-1 WHERE coupon='$discount_coupon'");
			}
			$discount_coupon="";
		}

		#
		# Increase debit for declined GC
		#
		if ($order["applied_giftcerts"]) {
			foreach ($order["applied_giftcerts"] as $k=>$v) {
				if ($order["status"]=="P" || $order["status"]=="C") {
					db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit+'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
				}

				db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>0 AND gcid='$v[giftcert_id]'");
			}
		}

		# Set GC's status to 'D'
		if ($giftcerts) {
			foreach($giftcerts as $giftcert) {
				db_query("UPDATE $sql_tbl[giftcerts] SET status='D' WHERE gcid='$giftcert[gcid]'");
			}
		}

		if ($config["General"]["unlimited_products"] != "Y") {
			func_update_quantity ($products);
		}
	}

	if (!empty($active_modules["SnS_connector"])) {
		global $HTTP_COOKIE_VARS;

		$_old = $HTTP_COOKIE_VARS;
		$HTTP_COOKIE_VARS['personal_client_id'] = $order['extra']['personal_client_id'];
		func_generate_sns_action("Order", $orderid);
		$HTTP_COOKIE_VARS = $_old;
	}
}

#
# This function sends GC emails (called from func_place_order
# and provider/order.php"
#
function func_send_gc($from_email, $giftcert, $from_login = '') {
	global $mail_smarty, $config, $to_customer, $sql_tbl;

	$giftcert["purchaser_email"] = $from_email;
	$mail_smarty->assign("giftcert", $giftcert);

	#
	# Send notifs to $orders_department & purchaser
	#
	if (@$config['Gift_Certificates']['eml_giftcert_notif_purchaser'] == 'Y' && (@$config['Gift_Certificates']['eml_giftcert_notif_admin'] != 'Y' || $config["Company"]["orders_department"] != $from_email)) {
		if (!empty($from_login)) {
			$to_customer = func_query_first_cell("SELECT language FROM $sql_tbl[customers] WHERE login = '$from_login'");
			if (empty($to_customer))
				$to_customer = $config['default_customer_language'];
		}

		func_send_mail($from_email, "mail/giftcert_notification_subj.tpl", "mail/giftcert_notification.tpl", $config["Company"]["orders_department"], false);
	}

	if (@$config['Gift_Certificates']['eml_giftcert_notif_admin'] == 'Y') {
		func_send_mail($config["Company"]["orders_department"], "mail/giftcert_notification_subj.tpl", "mail/giftcert_notification.tpl", $from_email, true);
	}

	#
	# Send GC to recipient
	#
	$to_customer = '';
	func_send_mail($giftcert["recipient_email"], "mail/giftcert_subj.tpl", "mail/giftcert.tpl", $from_email, false);
}

#
# Move products back to the inventory
#
function func_update_quantity($products,$increase=true) {
	global $config, $sql_tbl, $active_modules;

	$symbol = ($increase?"+":"-");
	if ($config["General"]["unlimited_products"] != "Y" && is_array($products)) {
		$ids = array();
		foreach ($products as $product) {
			if ($product['product_type'] == 'C' && !empty($active_modules['Product_Configurator']))
				continue;

			$variantid = "";
			if (!empty($active_modules['Product_Options']) && (!empty($product['extra_data']['product_options']) || !empty($product['options']))) {
				$options = (!empty($product['extra_data']['product_options'])?$product['extra_data']['product_options']:$product['options']);
				$variantid = func_get_variantid($options);
			}

			if (!empty($variantid)) {
				db_query("UPDATE $sql_tbl[variants] SET avail=avail$symbol'$product[amount]' WHERE variantid = '$variantid'");
			}
			else {
				$egoods_cond = $active_modules["Egoods"]?" AND distribution=''":"";
				db_query("UPDATE $sql_tbl[products] SET avail=avail$symbol'$product[amount]' WHERE productid='$product[productid]'".$egoods_cond);
			}

			$ids[$product['productid']] = true;
		}

		if (!empty($ids)) {
			func_build_quick_flags(array_keys($ids));
			func_build_quick_prices(array_keys($ids));
		}
	}
}

#
# This function removes orders and related info from the database
# $orders can be: 1) orderid; 2) orders array with orderid keys
function func_delete_order($orders, $update_quantity = true) {
	global $sql_tbl, $xcart_dir;

	$_orders = array();

	if (is_array($orders)) {
		foreach($orders as $order)
			if (!empty($order["orderid"]))
				$_orders[] = $order["orderid"];
	}
	elseif (is_numeric($orders)) {
		$_orders[] = $orders;
	}

	x_log_flag('log_orders_delete', 'ORDERS', "Login: $login\nIP: $REMOTE_ADDR\nOperation: delete orders (".implode(',',$_orders).")", true);
	#
	# Update quantity of products
	#
	if ($update_quantity) {
		foreach($_orders as $orderid) {
			$order_data = func_order_data($orderid);
			if (empty($order_data))
				continue;

			if (strpos("IQ",$order_data["order"]["status"]) !== false) {
				func_update_quantity($order_data["products"]);
			}
		}
	}

	#
	# Delete orders from the database
	#
	$xaff = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name='XAffiliate'") > 0);
	$xrma = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name='RMA'") > 0);
	if ($xaff && !isset($sql_tbl['partner_payment'])) {
		@include_once $xcart_dir."/modules/XAffiliate/config.php";
	}

	if ($xrma && !isset($sql_tbl['returns'])) {
		@include_once $xcart_dir."/modules/RMA/config.php";
	}

	db_query("LOCK TABLES $sql_tbl[orders] WRITE, $sql_tbl[order_details] WRITE, $sql_tbl[order_extras] WRITE, $sql_tbl[giftcerts] WRITE, $sql_tbl[subscription_customers] WRITE".(@$xaff?", $sql_tbl[partner_payment] WRITE, $sql_tbl[partner_product_commissions] WRITE, $sql_tbl[partner_adv_orders] WRITE":"").(@$xrma?", $sql_tbl[returns] WRITE":""));

	foreach($_orders as $orderid) {
		$itemids = func_query("SELECT itemid FROM $sql_tbl[order_details] WHERE orderid='$orderid'");
		if(!empty($itemids)) {
			foreach($itemids as $k => $v) {
				$itemids[$k] = $v['itemid'];
			}
		}

		db_query("DELETE FROM $sql_tbl[orders] WHERE orderid='$orderid'");
		db_query("DELETE FROM $sql_tbl[order_details] WHERE orderid='$orderid'");
		db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid='$orderid'");
		db_query("DELETE FROM $sql_tbl[giftcerts] WHERE orderid='$orderid'");
		if (@$xaff) {
			db_query("DELETE FROM $sql_tbl[partner_payment] WHERE orderid='$orderid'");
			db_query("DELETE FROM $sql_tbl[partner_product_commissions] WHERE orderid='$orderid'");
			db_query("DELETE FROM $sql_tbl[partner_adv_orders] WHERE orderid='$orderid'");
		}

		if (@$xrma && !empty($itemids)) {
			db_query("DELETE FROM $sql_tbl[returns] WHERE itemid IN ('".implode("','", $itemids)."')");
		}

		db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE orderid='$orderid'");
	}

	#
	# Check if no orders in the database
	#
	$total_orders = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders]");
	if ($total_orders == 0) {
		#
		# Clear Order ID counter (auto increment field in the xcart_orders table)
		#
		db_query("DELETE FROM $sql_tbl[orders]");
		db_query("DELETE FROM $sql_tbl[order_details]");
		if (@$xaff)
			db_query("DELETE FROM $sql_tbl[partner_payment]");

		db_query("DELETE FROM $sql_tbl[subscription_customers]");

	}

	db_query("UNLOCK TABLES");
}

function func_check_merchant_password($config_force = false) {
	global $merchant_password, $current_area, $active_modules, $config;

	return ($merchant_password && ($current_area == 'A' || ($current_area == 'P' && $active_modules["Simple_Mode"])) && ($config['Security']['blowfish_enabled'] == 'Y' || $config_force));
}

#
# This function recrypts data with the Blowfish method.
#
function func_data_recrypt() {
	global $sql_tbl;

	if (!func_check_merchant_password())
		return false;

	$orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE details NOT LIKE 'C%' AND details != ''");

	if (!$orders)
		return true;

	func_display_service_header("lbl_reencrypting_mkey");
	while ($order = db_fetch_array($orders)) {
		$details = text_decrypt($order['details']);
		$details = (is_string($details)) ? addslashes(func_crypt_order_details($details)) : "";

		func_array2update("orders", array("details" => $details), "orderid = '$order[orderid]'");
		func_flush(". ");
	}

	db_free_result($orders);

	return true;
}

#
# This function decrypts data Blowfish method -> Standart method.
#
function func_data_decrypt() {
	global $sql_tbl;

	if (!func_check_merchant_password(true))
		return false;

	$orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE details LIKE 'C%'");
	if (!$orders)
		return true;

	func_display_service_header("lbl_reencrypting_skey");
	while ($order = db_fetch_array($orders)) {
		$details = text_decrypt($order['details']);
		$details = is_string($details) ? addslashes(text_crypt($details)) : "";

		func_array2update("orders", array("details" => $details), "orderid = '$order[orderid]'");
		func_flush(". ");
	}

	db_free_result($orders);

	return true;
}

#
# This function recrypts Blowfish-crypted data with new password
# where:
#	old_password - old Merchant password
function func_change_mpassword_recrypt($old_password) {
	global $sql_tbl, $merchant_password;

	if (empty($old_password) || !func_check_merchant_password())
		return false;

	$orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE details != ''");
	if (!$orders)
		return true;

	$_merchant_password = $merchant_password;
	func_display_service_header("lbl_reencrypting_new_mkey");
	while ($order = db_fetch_array($orders)) {
		$merchant_password = $old_password;
		$details = text_decrypt($order['details']);
		$merchant_password = $_merchant_password;
		$details = is_string($details) ? addslashes(func_crypt_order_details($details)) : "";

		func_array2update("orders", array("details" => $details), "orderid = '$order[orderid]'");

		func_flush(". ");
	}

	db_free_result($orders);

	$merchant_password = $_merchant_password;

	return true;
}

#
# Encryption of the 'details' field of the orders table
#
function func_crypt_order_details($data) {
	if (func_check_merchant_password())
		return text_crypt($data, "C");

	return text_crypt($data);
}

#
# This function create file lock in temporaly directory
# It will return file descriptor, or false.
#
function func_lock($lockname, $ttl = 15, $cycle_limit = 0) {
	global $file_temp_dir, $_lock_hash;

	if (empty($lockname))
		return false;

	if (!empty($_lock_hash[$lockname]))
		return $_lock_hash[$lockname];

	$fname = $file_temp_dir.DIRECTORY_SEPARATOR.$lockname;

	# Generate current id
	$id = md5(uniqid(rand(0, func_microtime()*1000), true));
	$_lock_hash[$lockname] = $id;

	$file_id = false;
	$limit = $cycle_limit;
	while (($limit-- > 0 || $cycle_limit <= 0)) {
		if (!file_exists($fname)) {

			# Write locking data
			$fp = fopen($fname, "w");
			if ($fp) {
				fwrite($fp, $id.time());
				fclose($fp);
			}
		}

		$fp = fopen($fname, "r");
		if (!$fp)
			return false;

		$tmp = fread($fp, 43);
		fclose($fp);

		$file_id = substr($tmp, 0, 32);
		$file_time = substr($tmp, 32);

		if ($file_id == $id)
			break;

		if ($ttl > 0 && time() > $file_time+$ttl) {
			@unlink($fname);
			continue;
		}

		sleep(1);
	}

	return $file_id == $id ? $id : false;
}

#
# This function releases file lock which is previously created by func_lock
#
function func_unlock($lockname) {
	global $file_temp_dir, $_lock_hash;

	if (empty($lockname))
		return false;

	if (empty($_lock_hash[$lockname]))
		return false;

	$fname = $file_temp_dir.DIRECTORY_SEPARATOR.$lockname;
	if (!file_exists($fname))
		return false;

	$fp = fopen($fname, "r");
	if (!$fp)
		return false;

	$tmp = fread($fp, 43);
	fclose($fp);

	$file_id = substr($tmp, 0, 32);
	$file_time = substr($tmp, 32);

	if ($file_id == $_lock_hash[$lockname])
		@unlink($fname);

	func_unset($_lock_hash, $lockname);

	return true;
}

#
# Translate products names to local product names
#
function func_translate_products($products, $code) {
	global $sql_tbl;

	if (!is_array($products) || empty($products) || empty($code))
		return $products;

	$hash = array();
	foreach($products as $k => $p) {
		$hash[$p['productid']][] = $k;
	}

	if (empty($hash))
		return $products;

	foreach ($hash as $pid => $keys) {
		$local = func_query_first("SELECT product, descr, fulldescr FROM $sql_tbl[products_lng] WHERE productid = '$pid' AND code = '$code'");
		if (empty($local) || !is_array($local))
			continue;

		foreach ($local as $k => $v) {
			if (empty($v))
				unset($local[$k]);
		}

		foreach($keys as $k) {
			$products[$k] = func_array_merge($products[$k], $local);
		}
	}

	return $products;
}

#
# This function defines internal fields for storing sensitive information in order details
#
function func_order_details_fields($all=false) {
	global $store_cc, $store_ch, $store_cvv2;
	static $all_fields = array (
		"CC" => array (
			"card_name" => "{CardOwner}",
			"card_type" => "{CardType}",
			"card_number" => "{CardNumber}",
			"card_valid_from" => "{ValidFrom}",
			"card_expire" => "{ExpDate}",
			"card_issue_no" => "{IssueNumber}"
		),
		"CC_EXT" => array (
			"card_cvv2" => "CVV2"
		),
		"CH" => array (
			# ACH
			"check_name" => "{AccountOwner}",
			"check_ban" => "{BankAccount}",
			"check_brn" => "{BankNumber}",
			"check_number" => "{FractionNumber}",
			# Direct Debit
			"debit_name" => "{AccountOwner}",
			"debit_bank_account" => "{BankAccount}",
			"debit_bank_number" => "{BankNumber}",
			"debit_bank_name" => "{BankName}"
		)
	);

	$keys = array();
	if ($store_cc || $all) {
		$keys[] = "CC";
		if ($store_cvv2 || $all) $keys[] = "CC_EXT";
	}

	if ($store_ch || $all) $keys[] = "CH";

	$rval = array();
	foreach ($keys as $key) {
		$rval = func_array_merge($rval, $all_fields[$key]);
	}

	return $rval;
}

#
# Convert {CardName} => value of lbl_payment_CardName language variable
#
function func_order_details_fields_as_labels($force=false) {
	$rval = array();
	foreach (func_order_details_fields(true) as $field) {
		if (preg_match('!^\{(.*)\}$!S', $field, $sublabel))
			$rval[$field] = func_get_langvar_by_name('lbl_payment_'.$sublabel[1], NULL, false, $force);
	}

	return $rval;
}

#
# Remove sensitive information from order details
#
function func_order_remove_ccinfo($order_details, $save_4_digits) {
	static $find_re = array (
		1 => array ('/^\{(?:CardOwner|CardType|ExpDate)\}:.*$/mS', '/^CVV2:.*$/mS'),
		0 => array ('/^\{(?:CardOwner|CardType|CardNumber|ExpDate)\}:.*$/mS', '/^CVV2:.*$/mS'),
	);

	$save_4_digits = (int)((bool)$save_4_digits); # can use only 0 & 1

	$order_details = preg_replace($find_re[$save_4_digits], "", $order_details);

	if ($save_4_digits) {
		if (preg_match_all("/^(\{CardNumber\}:)(.*)$/mS", $order_details, $all_matches)) {
			foreach ($all_matches[2] as $matchn => $cardnum) {
				$cardnum = trim($cardnum);
				$order_details = str_replace(
					$all_matches[0][$matchn],
					$all_matches[1][$matchn]." ".str_repeat("*", strlen($cardnum)-4).substr($cardnum, -4),
					$order_details);
			}
		}
	}

	return $order_details;
}

#
# Replace all occurences of {Label} by corresponding language variable
#
function func_order_details_translate($order_details, $force=false) {
	static $labels = array();
	global $shop_language;

	if (empty($labels[$shop_language])) {
		$labels[$shop_language] = func_order_details_fields_as_labels($force);
	}

	$order_details = str_replace(
		array_keys($labels[$shop_language]),
		array_values($labels[$shop_language]),
		$order_details);

	return $order_details;
}

?>
