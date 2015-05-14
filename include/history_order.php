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
# $Id: history_order.php,v 1.32.2.1 2006/07/17 08:28:21 max Exp $
#
# Collect infos about ordered products
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('order');

if (empty($mode)) $mode = "";

if ($mode == "invoice" or $mode == "label") {
	header("Content-Type: text/html");
	header("Content-Disposition: inline; filename=invoice.txt");

	$orders = explode(",", $orderid);

	if ($orders) {
		$orders_data = array();
		foreach ($orders as $orderid) {
			$order_data = func_order_data($orderid);
			if (empty($order_data))
				continue;

			#
			# Security check if order owned by another customer
			#
			if ($current_area == 'C' && $order_data["userinfo"]["login"] != $login) {
				func_header_location("error_message.php?access_denied&id=34");
			}

			$order = $order_data["order"];
			$customer = $order_data["userinfo"];
			$order_language = ($current_area == 'C' ? (empty($userinfo['language']) ? $config['default_customer_language'] : $userinfo['language']) : $shop_language);
			$products = func_translate_products($order_data["products"], $order_language);
			$giftcerts = $order_data["giftcerts"];
			$orders_data[] = array ("order" => $order, "customer" => $customer, "products" => $products, "giftcerts" => $giftcerts);
		}

		$smarty->assign("orders_data", $orders_data);

		$_tmp_smarty_debug = $smarty->debugging;
		$smarty->debugging = false;

		if ($mode == "invoice") {
			if ($current_area == "A" || ($current_area == "P" && !empty($active_modules["Simple_Mode"])))
				$smarty->assign("show_order_details", "Y");
			func_display("main/order_invoice_print.tpl",$smarty);
		} elseif ($mode == "label")
			func_display("main/order_labels_print.tpl",$smarty);

		$smarty->debugging = $_tmp_smarty_debug;
	}

	exit;
} else {
	$order_data = func_order_data($orderid);
	if (empty($order_data))
		return false;

	#
	# Security check if order owned by another customer
	#
	if ($current_area == 'C' && $order_data["userinfo"]["login"] != $login) {
		func_header_location("error_message.php?access_denied&id=35");
	}

	$smarty->assign("order_details_fields_labels", func_order_details_fields_as_labels());
	$smarty->assign("order", $order_data["order"]);
	$smarty->assign("customer", $order_data["userinfo"]);
	$order_language = ($current_area == 'C' ? (empty($userinfo['language']) ? $config['default_customer_language'] : $userinfo['language']) : $shop_language);
	$order_data["products"] = func_translate_products($order_data["products"], $order_language);
	$smarty->assign("products", $order_data["products"]);
	$smarty->assign("giftcerts", $order_data["giftcerts"]);
	if ($order_data) {
		$owner_condition = "";
		if ($current_area == "C")
			$owner_condition = " AND $sql_tbl[orders].login='".$login."'";
		elseif ($current_area == "P" && !$single_mode ) {
			$owner_condition = " AND $sql_tbl[order_details].provider='".$login."'";
		}
		# find next
		$tmp = func_query_first("SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].orderid>'".$orderid."' AND $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid $owner_condition ORDER BY $sql_tbl[orders].orderid ASC LIMIT 1");
		if (!empty($tmp["orderid"]))
			$smarty->assign("orderid_next", $tmp["orderid"]);
		# find prev
		$tmp = func_query_first("SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].orderid<'".$orderid."' AND $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid $owner_condition ORDER BY $sql_tbl[orders].orderid DESC LIMIT 1");
		if (!empty($tmp["orderid"]))
			$smarty->assign("orderid_prev", $tmp["orderid"]);
	}
}

$location[] = array(func_get_langvar_by_name("lbl_orders_management"), "orders.php");
$location[] = array(func_get_langvar_by_name("lbl_order_details_label"), "");

if(!empty($active_modules['RMA'])) {
    include $xcart_dir."/modules/RMA/add_returns.php";
}

if(!empty($active_modules['Anti_Fraud'])) {
    include $xcart_dir."/modules/Anti_Fraud/order.php";
}

?>
