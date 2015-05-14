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
# $Id: order.php,v 1.60 2006/01/11 06:55:57 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('mail','order');

if ($mode == "update") {
	#
	# Update orders info (status)
	#
	if (is_array($order_status) && is_array($order_status_old)) {
		foreach ($order_status as $orderid=>$status) {
			if (is_numeric($orderid) && $status != $order_status_old[$orderid])
				func_change_order_status($orderid, $status);
		}

		func_header_location("orders.php".(empty($qrystring)?"":"?$qrystring"));
	}
}
elseif ($mode == 'prolong_ttl' && $orderid && !empty($active_modules["Egoods"])) {
	#
	# Prolong TTL
	#
	$itemids = func_query("SELECT $sql_tbl[order_details].itemid FROM $sql_tbl[order_details], $sql_tbl[download_keys] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].itemid = $sql_tbl[download_keys].itemid");
	if ($itemids) {
		foreach ($itemids as $v)
			db_query("UPDATE $sql_tbl[download_keys] SET expires = '".(time()+$config["Egoods"]["download_key_ttl"]*3600)."' WHERE itemid = '$v[itemid]'");
	}

	$pids = func_query("SELECT $sql_tbl[order_details].itemid, $sql_tbl[order_details].productid, $sql_tbl[products].distribution FROM $sql_tbl[order_details], $sql_tbl[products] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].productid = $sql_tbl[products].productid AND $sql_tbl[products].distribution != ''");
	if ($pids) {
		$keys = array();
		foreach ($pids as $v) {
			if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[download_keys] WHERE itemid = '$v[itemid]'"))
				continue;

			$keys[$v['itemid']]['download_key'] = keygen($v["productid"], $config["Egoods"]["download_key_ttl"], $v['itemid']);
			$keys[$v['itemid']]['distribution_filename'] = basename($v['distribution']);

		}

		if (!empty($keys)) {
			$order = func_order_data($orderid);
			if (!empty($order)) {
				foreach ($order['products'] as $k => $v) {
					if (isset($keys[$v['itemid']])) {
						$order['products'][$k] = func_array_merge($v,$keys[$v['itemid']]);
					}
				}

				$mail_smarty->assign("products", $order['products']);
				$mail_smarty->assign("order", $order['order']);
				$mail_smarty->assign("userinfo", $order['userinfo']);
				func_send_mail($order['userinfo']["email"], "mail/egoods_download_keys_subj.tpl", "mail/egoods_download_keys.tpl", $config["Company"]["orders_department"], false);
			}
		}
	}

	func_header_location("order.php?orderid=".$orderid);
}
elseif ($mode == 'send_ip' && $orderid) {
	#
	# Send customer IP address to Anti Fraud server
	#
	list($a, $result) = func_send_ip_to_af($orderid, $reason);
	if ($result == "1") {
		$top_message["content"] = func_get_langvar_by_name("msg_antifraud_ip_added");
		$top_message["type"] = "I";
	}
	else {
		$top_message["content"] = func_get_langvar_by_name("txt_antifraud_service_generror");
		$top_message["type"] = "E";
	}

	func_header_location("order.php?orderid=".$orderid);
}

$order_ids = explode(",", $orderid);
if (!is_array($order_ids)) $order_ids[] = $orderid;

foreach ($order_ids as $oid) {
	if (!is_numeric($oid))
		func_header_location("error_message.php?access_denied&id=8");
}

$smarty->assign("show_order_details", "Y");

#
# Collect infos about ordered products
#
require $xcart_dir."/include/history_order.php";

$order = $order_data["order"];
$userinfo = $order_data["userinfo"];
$products = $order_data["products"];
$giftcerts = $order_data["giftcerts"];

$smarty->assign("orderid", $orderid);

if ($mode == "status_change") {
	#
	# Update order
	#
	$query_data = array (
		"tracking" => $tracking,
		"customer_notes" => $customer_notes,
		"notes" => $notes
	);
	if (isset($HTTP_POST_VARS['details'])) {
		$query_data['details'] = func_crypt_order_details($details);
	}

	func_array2update("orders", $query_data, "orderid = '$orderid'");

	func_change_order_status($orderid, $status);

	$top_message = array(
		"content" => func_get_langvar_by_name("txt_order_has_been_changed")
	);
	func_header_location("order.php?orderid=".$orderid);
}

#
# Delete order
#
if ($mode=="delete") {
	func_delete_order($orderid);
	func_header_location("orders.php?".$query_string);
}

$smarty->assign("main","history_order");

if (!empty($active_modules["Advanced_Order_Management"]) && $mode == "edit") {
	include $xcart_dir."/modules/Advanced_Order_Management/order_edit.php";
}
elseif (!empty($active_modules["Anti_Fraud"]) && $mode == "anti_fraud") {
	if ($order['extra']) {
		$userinfo = $order_data["userinfo"];
		$extra = $order['extra'];
		$extras['ip'] = $extra['ip'];
		$extras['proxy_ip'] = $extra['proxy_ip'];
		include $xcart_dir."/modules/Anti_Fraud/anti_fraud.php";
		db_query("UPDATE $sql_tbl[orders] SET extra = '".addslashes(serialize($extra))."' WHERE orderid = '$orderid'");
	}

	func_header_location("order.php?orderid=".$orderid);
}
elseif (!empty($active_modules["Stop_List"]) && $mode == "block_ip") {
	func_add_ip_to_slist($order['extra']['ip']);
	$top_message["content"] = func_get_langvar_by_name("msg_stoplist_ip_added");
	$top_message["type"] = "I";
	func_header_location("order.php?orderid=".$orderid);
}

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
