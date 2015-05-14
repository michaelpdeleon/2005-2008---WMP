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
# $Id: payment_giftcert.php,v 1.41 2006/03/03 07:49:09 max Exp $
#
# Gift certificate processing payment module
#

require "../include/payment_method.php";

x_load('cart','order','payment');

#
# Checking GC if it's already applied to order
#

if (empty($gcid)) {
	$top_message = array(
		'content' => func_get_langvar_by_name("err_filling_form"),
		'type' => 'E'
	);
	func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=fields&paymentid=".$paymentid);
}

$gc_applied = false;
if ($cart["applied_giftcerts"]) {
	foreach ($cart["applied_giftcerts"] as $k => $v) {
		if ($v["giftcert_id"] == $gcid) {
			$gc_applied = true;
			break;
		}
	}
}

if ($gc_applied) {
	func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=gc_used&paymentid=".$paymentid);
}

#
# Unblock GC after $config["Gift_Certificates"]["gc_blocking_period"] minutes of blocking
#
$gc_blocking_period = $config["Gift_Certificates"]["gc_blocking_period"] * 60;

db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE gcid='$gcid' AND status='B' AND block_date+'$gc_blocking_period' < '".time()."'");

$gc = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid' AND status='A' AND debit > '0'");
if (empty($gc)) {
	#
	# Non existing Gift certificate
	#
	func_header_location($xcart_catalogs['customer']."/error_message.php?error_giftcert_notfound");
}

#
# Gift certificate exists
#
$cart["applied_giftcerts"][] = array(
	"giftcert_id" => $gcid,
	"giftcert_cost" => $gc["debit"]
);

db_query("UPDATE $sql_tbl[giftcerts] SET status='B', block_date='".time()."' WHERE gcid='$gcid'");

if ($gc["debit"] < $cart["total_cost"]) {
	#
	# Not enough money
	#
	if (empty($active_modules['Fast_Lane_Chekout'])) {
		$top_message = array(
			"content" => func_get_langvar_by_name("txt_gc_not_enough_money")
		);
	}
	func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=gc_not_enough_money");
}

#
# Process order
#
include $xcart_dir."/include/payment_wait.php";

$cart["applied_giftcerts"][count($cart["applied_giftcerts"])-1]["giftcert_cost"] = $cart["total_cost"];
$cart["giftcert_discount"] += $cart["total_cost"];
$cart["total_cost"] = 0;
if ($cart["orders"]) {
	foreach($cart["orders"] as $k => $v)
		$cart["orders"][$k]["total_cost"] = $cart["total_cost"];
}

$products = func_products_in_cart($cart, (!empty($userinfo["membership"])?$userinfo["membership"]:""));
$cart = func_array_merge ($cart, func_calculate($cart, $products, $login, $login_type));

$customer_notes = $Customer_Notes;

$orderids = func_place_order(stripslashes($payment_method), "I", "", $customer_notes);
if (is_null($orderids) || $orderids === false) {
	func_header_location($xcart_catalogs['customer'].'/error_message.php?product_in_cart_expired');
}

func_change_order_status($orderids,"P");
$_orderids = func_get_urlencoded_orderids ($orderids);

#
# Remove all from cart
#
$cart = "";

if (!empty($active_modules['SnS_connector'])) {
	func_generate_sns_action("CartChanged");
}

func_header_location($xcart_catalogs['customer']."/cart.php?mode=order_message&orderids=$_orderids");

?>
