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
# $Id: wishlist.php,v 1.72.2.1 2006/07/25 14:25:32 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('cart','mail','product');

x_session_register("wlid_eventid");

if (!empty($login) && $REQUEST_METHOD == 'GET' && $mode != 'checkout') {
	$counts = func_query("SELECT COUNT(wishlistid) as count, wishlistid, productid, options, event_id, object FROM $sql_tbl[wishlist] WHERE login = '$login' GROUP BY productid, options, event_id, object HAVING count > 1");
	if (!empty($counts)) {
		foreach ($counts as $c) {
			$c = func_array_map("addslashes", $c);
			$sum = func_query_first_cell("SELECT SUM(amount) FROM $sql_tbl[wishlist] WHERE login = '$login' AND productid = '$c[productid]' AND options = '$c[options]' AND event_id = '$c[event_id]' AND object = '$c[object]'");
			db_query("DELETE FROM $sql_tbl[wishlist] WHERE login = '$login' AND productid = '$c[productid]' AND options = '$c[options]' AND event_id = '$c[event_id]' AND object = '$c[object]' AND wishlistid != '$c[wishlistid]'");
			db_query("UPDATE $sql_tbl[wishlist] SET amount = '$sum' WHERE wishlistid = '$c[wishlistid]'");
		}

		func_header_location("cart.php?".$QUERY_STRING);
	}
}

if ($mode == "addgc2wl") {
	#
	# Add Gift Certificate to the wish list
	#
	if (!empty($gcindex)) {
		db_query("UPDATE $sql_tbl[wishlist] SET object='".addslashes(serialize($giftcert))."' WHERE wishlistid='$gcindex'");
		$eventid = func_query_first_cell("SELECT event_id FROM $sql_tbl[wishlist] WHERE wishlistid='$gcindex'");
		if ($eventid > 0 && @$active_modules["Gift_Registry"])
			func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
		else
			func_header_location("cart.php?mode=wishlist");
	}
	else {
		db_query("insert into $sql_tbl[wishlist] (login, amount, options, object) values ('$login', '1', '', '".addslashes(serialize($giftcert))."')");
	}

	func_header_location("cart.php?mode=wishlist");
}
elseif ($action == "update_quantity") {
	#
	# Update quantity for product
	#
	if (!empty($wlitem) && isset($quantity)) {
		$eventid = intval($eventid);
		if ($quantity > 0) {
			db_query("UPDATE $sql_tbl[wishlist] SET amount='$quantity' WHERE wishlistid='$wlitem'");
		} else {
			db_query("DELETE FROM $sql_tbl[wishlist] WHERE login='$login' AND wishlistid='$wlitem' AND event_id='$eventid'");
		}

		if ($eventid == 0)
			func_header_location("cart.php?mode=wishlist");
		else
			func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
	}
}
elseif ($mode == "add2wl" && $productid) {
	#
	# Add product to the wish list
	#
	$_options = '';
	if ($active_modules["Product_Options"]) {
		if (is_array($product_options)) {
			if (!func_check_product_options ($productid, $product_options)) {
				if (!empty($active_modules["Product_Configurator"]) && $added_product["product_type"] == "C")
					func_header_location("pconf.php?productid=$productid&err=options");
				else
					func_header_location("product.php?productid=$productid&err=options");
			}

			$product_options = func_array_map('stripslashes', $product_options);
		}
		else {
			$product_options = func_get_default_options($productid, $amount, @$user_account['membershipid']);
		}

		if (!is_array($product_options)) {
			unset($product_options);
		}
		else {
			$_options = addslashes(serialize($product_options));
		}
	}

	$added_product = func_select_product($productid, @$user_account["membershipid"], false, true);

	$oamount = 0;
	$wlid = false;
	$object = "";
	if ($added_product["product_type"] == "C") {
		x_session_register("configurations");
		$object = addslashes(serialize($configurations[$productid]));
	} else {
		if (empty($added_product['distribution']) || empty($active_modules['Egoods'])) {
			$oamount = func_query_first_cell("SELECT amount FROM $sql_tbl[wishlist] WHERE login='$login' AND productid='$productid' AND options='$_options' AND event_id='0'");
		}
		$wlid = func_query_first_cell("SELECT wishlistid FROM $sql_tbl[wishlist] WHERE login='$login' AND productid='$productid' AND options='$_options' AND event_id='0'");
	}

	#
	# Add to or update the wish list
	#
	if (!empty($wlid)) {
		func_array2update("wishlist", array("amount" => $amount+$oamount), "wishlistid='$wlid'");
	} else {
		func_array2insert("wishlist",
			array(
				"login" => $login,
				"productid" => $productid,
				"amount" => $amount,
				"options" => $_options,
				"object" => $object
			)
		);
	}

	if (!empty($active_modules['SnS_connector']))
		func_generate_sns_action("AddToWishList");

	func_header_location("cart.php?mode=wishlist");

}
elseif ($mode == "wl2cart" && ($wlitem || ($fwlitem && (!empty($wlid) || !empty($eventid))))) {
	#
	# Add to cart product from wish list
	#
	if (!empty($eventid)) {
		$wishlistid = $fwlitem;
		$login_cond = "event_id='$eventid' AND wishlistid='$fwlitem'";
		$wlid = func_query_first_cell("SELECT login FROM $sql_tbl[wishlist] WHERE $login_cond");
		$wlid = md5($wlid);
		$wlid_eventid = $eventid;
	}
	else {
		if ($wlitem) {
			$wishlistid = $wlitem;
			$login_cond = "login='$login' AND wishlistid='$wlitem'";
		}
		else {
			$wishlistid = $fwlitem;
			$login_cond = "MD5(login)='$wlid' AND wishlistid='$fwlitem'";
		}
	}

	if (!empty($wlid)) {
		$giftreg = array("wlid"=>$wlid, "eventid"=>$eventid);
		$friends_wihlists[] = $giftreg;
	}

	$wlproduct = func_query_first("SELECT wishlistid, productid, amount-amount_purchased as amount, options, object FROM $sql_tbl[wishlist] WHERE $login_cond AND (productid='0' OR amount-amount_purchased>0)");

	if ($wlproduct) {
		$wlproduct["wishlistid"] = $wishlistid;
		if ($wlproduct["productid"] == 0) {
			#
			# Add gift certificate to the cart
			#
			$giftcert = unserialize($wlproduct["object"]);
			if (!isset($cart["giftcerts"]))
				$cart["giftcerts"] = array();
			$cart["giftcerts"][] = func_array_merge($giftcert, array("wishlistid"=>$wishlistid));
		}
		else {
			#
			# Add product to the cart
			#
			$cartid = func_generate_cartid($cart["products"]);
			$wlproduct["cartid"] = $cartid;
			$wlproduct["options"] = unserialize($wlproduct["options"]);
			if(empty($wlproduct["options"]) || !is_array($wlproduct["options"]))
				$wlproduct["options"] = "";
			if (!empty($eventid))
				$wlproduct["event_id"] = $eventid;

			if (!isset($cart["products"]))
				$cart["products"] = array();

			$tmp = func_query_first("SELECT avail, distribution FROM $sql_tbl[products] WHERE productid='$wlproduct[productid]'");

			$valid_options = true;
			$variantid = false;

			if (!empty($active_modules['Product_Options']) && !empty($wlproduct["options"])) {

				# Check product options and get variant id
				if (!func_check_product_options($wlproduct['productid'], $wlproduct["options"])) {
					$valid_options = false;

				} elseif ($variantid = func_get_variantid($wlproduct["options"], $wlproduct['productid'])) {
					$tmp['avail'] = func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid = '$variantid'");
				}
			}

			$product_amount = $tmp['avail'];
			$is_esd = (!empty($tmp['distribution']) && !empty($active_modules['Egoods']));

			$configurable_product = (func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid='$wlproduct[productid]'") == "C");
			if (
				(empty($active_modules['Product_Options']) || $valid_options) &&
				($product_amount > 0 || $config["General"]["unlimited_products"] == "Y" || $configurable_product)
			) {
				if ($config["General"]["unlimited_products"] != "Y")
					$wlproduct["amount"] = ($wlproduct["amount"] > $product_amount ? $product_amount : $wlproduct["amount"]);

				if (($configurable_product && $wlproduct["amount"] == 0) || $is_esd)
					$wlproduct["amount"] = 1;

				$cart["products"][] = array(
					"cartid" => $cartid,
					"productid" => $wlproduct["productid"],
					"amount" => $wlproduct["amount"],
					"options" => $wlproduct["options"],
					"free_price" => price_format($wlproduct["price"]),
					"wishlistid" => $wishlistid,
					"variantid" => $variantid
				);

				if ($configurable_product) {
					$productindex = $index_in_cart = count($cart["products"])-1;
					$productid = $wlproduct["productid"];
					$added_product = func_select_product($productid, @$user_account["membershipid"]);
					$amount = $wlproduct["amount"];
					$mode = "add";
					$configurations[$productid] = unserialize($wlproduct["object"]);
					include $xcart_dir."/modules/Product_Configurator/pconf_customer_cart.php";

					list($variant, $product_options_result) = func_get_product_options_data($wlproduct["productid"], $wlproduct["options"], @$user_account["membershipid"]);
					$wlproduct["options_surcharge"] = 0;
					if ($product_options_result) {
						foreach ($product_options_result as $o) {
							$wlproduct["options_surcharge"] += ($o['modifier_type'] == '%'?($cart["products"][$index_in_cart]["pconf_data"]["price"]*$o['price_modifier']/100):$o['price_modifier']);
						}
					}

					$wlproduct["options_surcharge"] = price_format($wlproduct["options_surcharge"]);

					$cart["products"][$index_in_cart]["options_surcharge"] = $wlproduct["options_surcharge"];
					$cart["products"][$index_in_cart]["free_price"] = $cart["products"][$index_in_cart]["pconf_data"]["price"];
				}
			}
		}
	}

	func_header_location("cart.php");
}
elseif ($mode == "wldelete" && $wlitem) {
	#
	# Delete from wish list
	#
	$eventid = intval($eventid);
	db_query("DELETE FROM $sql_tbl[wishlist] WHERE login='$login' AND wishlistid='$wlitem' AND event_id='$eventid'");
	if ($eventid > 0)
		func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
	else
		func_header_location("cart.php?mode=wishlist");
}
elseif ($mode == "wlclear") {
	#
	# Clear wish list
	#
	db_query("DELETE FROM $sql_tbl[wishlist] WHERE login='$login' AND event_id='0'");
	func_header_location("cart.php?mode=wishlist");
}
elseif ($mode == "wishlist" || (!empty($login) && $mode == "send_friend"  && $action == "entire_list") || ($mode == "friend_wl" && !empty($wlid))) {

	if ($mode == "friend_wl" && !empty($wlid) && !empty($wlid_eventid))
		func_header_location("giftregs.php?eventid=$wlid_eventid");

	#
	# Obtain wishlist from database
	#
	if ($mode == "send_friend" && empty ($friend_email))
		func_header_location("cart.php?mode=wishlist&sendall2friend=failed");

	$ids_redirect = array();

	if ($mode == "friend_wl") {
		$wl_raw = func_query("select $sql_tbl[wishlist].*, $sql_tbl[products].forsale from $sql_tbl[wishlist], $sql_tbl[products] where MD5($sql_tbl[wishlist].login)='$wlid' AND $sql_tbl[wishlist].event_id='0' AND $sql_tbl[wishlist].productid = $sql_tbl[products].productid");
		$smarty->assign("giftregistry","giftregistry");
	}
	else {
		$wl_raw = func_query("select $sql_tbl[wishlist].*, $sql_tbl[products].forsale from $sql_tbl[wishlist], $sql_tbl[products] where $sql_tbl[wishlist].login='$login' AND $sql_tbl[wishlist].event_id='0' AND $sql_tbl[wishlist].productid = $sql_tbl[products].productid");
		$smarty->assign("allow_edit", "Y");
	}

	foreach ($wl_raw as $index=>$wl_product) {
		if ($wl_product['forsale'] != 'Y' && $wl_product['forsale'] != 'H') {
			$ids_redirect[$wl_product['wishlistid']] = $wl_product['productid'];
			unset($wl_raw[$index]);
			break;
		}

		$wl_raw[$index]["options"] = unserialize($wl_product["options"]);
		if (!empty($wl_raw[$index]["options"]) && !empty($active_modules['Product_Options'])) {
			$wl_raw[$index]["variantid"] = func_get_variantid($wl_raw[$index]["options"], $wl_product['productid']);
		}

		$wl_product["amount_requested"] = $wl_product["amount"];
		if ($wl_product["amount"] > $wl_product["amount_purchased"] && $mode == "friend_wl")
			$wl_raw[$index]["amount"] = $wl_product["amount"] - $wl_product["amount_purchased"];
	}

	$wl_products = func_products_from_scratch($wl_raw, $user_account["membershipid"], true );

	if ($active_modules["Subscriptions"]) {
		if (!function_exists("SubscriptionProducts"))
			@include $xcart_dir."/modules/Subscriptions/subscription.php";

		$wl_products = SubscriptionProducts($wl_products);
	}

	if ($active_modules["Product_Configurator"]) {
		include $xcart_dir."/modules/Product_Configurator/pconf_customer_wishlist.php";
	}

	if (!empty($active_modules["Gift_Certificates"])) {
		$wl_raw = func_query("select wishlistid, amount, amount_purchased, object from $sql_tbl[wishlist] where login='$login' AND event_id='0' AND productid=0");
		if (is_array($wl_raw)) {
			foreach ($wl_raw as $k=>$v) {
				$object = unserialize($v["object"]);
				$wl_giftcerts[] = func_array_merge($v, $object);
			}

			if (!empty($wl_giftcerts))
				$smarty->assign("wl_giftcerts", $wl_giftcerts);
		}
	}

	if (@$active_modules["Gift_Registry"]) {
		@include $xcart_dir."/modules/Gift_Registry/giftreg_wishlist.php";
	}

	if (!empty($ids_redirect) && is_array($ids_redirect)) {
		db_query("DELETE FROM $sql_tbl[wishlist] WHERE wishlistid IN ('".implode("','", array_keys($ids_redirect))."')");
		foreach($ids_redirect as $k => $id) {
			$ids_redirect[$k] = func_query_first_cell("SELECT IF($sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' WHERE $sql_tbl[products].productid = '$id'");
		}

		$top_message['content'] = func_get_langvar_by_name("txt_wishlist_disabled_products", array("product_list" => "<br />&nbsp;&nbsp;&nbsp;".implode("<br />&nbsp;&nbsp;&nbsp;", $ids_redirect)));
		$top_message['type'] = 'W';
		func_header_location("cart.php?".$QUERY_STRING);
	}

	if ($mode == "send_friend") {
		$mail_smarty->assign("wlid", md5($login));
		$mail_smarty->assign("wl_products", $wl_products);
		$mail_smarty->assign("userinfo", $userinfo);
		func_send_mail ($friend_email, "mail/wishlist_sendall2friend_subj.tpl", "mail/wishlist_sendall2friend.tpl", $userinfo["email"], false);
		func_header_location("cart.php?mode=wishlist&sendall2friend=success");
	}
	else {
		$location[] = array(func_get_langvar_by_name("lbl_wish_list"), "");

		if (!empty($wl_products))
			$smarty->assign("wl_products",$wl_products);
		$smarty->assign("main","wishlist");
	}
}
elseif (!empty($login) && ($mode == "send_friend") && (!empty ($friend_email))) {
	$product = func_select_product($productid, $user_account['membershipid']);
	$mail_smarty->assign ("product", $product);
	$mail_smarty->assign ("userinfo", $userinfo);
	func_send_mail ($friend_email, "mail/wishlist_send2friend_subj.tpl", "mail/wishlist_send2friend.tpl", $userinfo["email"], false);
	func_header_location("cart.php?mode=wishlist&send2friend=success");
}
elseif (!empty($login) && ($mode == 'send_friend') && (empty($friend_email))) {
	func_header_location("cart.php?mode=wishlist&send2friend=failed");
}

?>
