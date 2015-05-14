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
# $Id: process_order.php,v 1.11 2006/01/11 06:55:59 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('order');

x_session_register("orders_to_delete");

if ($REQUEST_METHOD == "POST") {
#
# Process POST request
#
	if (!empty($export_fmt)) {
		$search_data["orders"]["export_fmt"] = $export_fmt;
		x_session_save("search_data");
	}

	if ($mode == "update") {
	#
	# Update orders info (status)
	#
		$flag = 0;

		if (is_array($order_status) && is_array($order_status_old)) {
			foreach($order_status as $orderid=>$status) {
				if (is_numeric($orderid) && $status != $order_status_old[$orderid])
					func_change_order_status($orderid, $status);
					$flag = 1;
			}
        }
		if ($flag)
			$top_message["content"] = func_get_langvar_by_name("msg_adm_orders_upd");
		func_header_location("orders.php?mode=search");

	} # /if ($mode == "update")

	elseif ($mode == "delete" || $mode == "delete_all") {
	#
	# Delete the selected orders
	#
		if ($confirmed == "Y") {
		# Deleting is confirmed
			if ($mode == "delete_all") {
				include $xcart_dir."/include/orders_deleteall.php";
				$top_message["content"] = func_get_langvar_by_name("msg_adm_all_orders_del");
				func_header_location("orders.php?mode=search");
			}

			if (is_array($orders_to_delete)) {
				foreach ($orders_to_delete as $k=>$v) {
					# Delete order
					func_delete_order($k);
				}
				$orders_to_delete = "";

				#
				# Prepare the information message
				#
				$top_message["content"] = func_get_langvar_by_name("msg_adm_orders_del");
				func_header_location("orders.php?mode=search");
			}
		}
		else {
			$orders_to_delete = (!empty($orderids) ? $orderids : "");
			func_header_location("process_order.php?mode=$mode");
		}

	} # /if ($mode == "delete")

	elseif ($mode == "invoice" and !empty($orderids)) {
	#
	# Display invoices
	#
		$orders_to_delete = (!empty($orderids) ? $orderids : "");
		func_header_location("process_order.php?mode=invoice");
	}
	elseif ($mode == "label" and !empty($orderids)) {
	#
	# Display labels
	#
		$orders_to_delete = (!empty($orderids) ? $orderids : "");
		func_header_location("process_order.php?mode=label");

	}

	# Export selected order(s)
	elseif ($mode == "export" and !empty($orderids)) {
		include $xcart_dir."/include/orders_export.php";
	}

	$orders_to_delete = "";
	$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_orders_sel");
	$top_message["type"] = "W";

	func_header_location("orders.php?mode=search");

} # /if ($REQUEST_METHOD == "POST")



if ($mode == "invoice" || $mode == "label") {
#
# Display the printable version of order invoices
#
	if (is_array($orders_to_delete)) {
		$orderid = implode(",", array_keys($orders_to_delete));
		$orders_to_delete = "";
		x_session_save("orders_to_delete");
		include $xcart_dir."/include/history_order.php";
	}
}

if ($mode == "delete") {
#
# Prepare for deleting products
#
	if (is_array($orders_to_delete)) {

		$location[] = array(func_get_langvar_by_name("lbl_orders_management"), "search.php");
		$location[] = array(func_get_langvar_by_name("lbl_delete_orders"), "");
		$smarty->assign("location", $location);

		foreach ($orders_to_delete as $k=>$v) {
			$condition[] = "orderid='".addslashes($k)."'";
		}
		$search_condition = implode(" OR ", $condition);

		$orders = func_query("SELECT orderid, status, date, total FROM $sql_tbl[orders] WHERE $search_condition ORDER BY orderid");

		if (is_array($orders)) {
			foreach ($orders as $k=>$v) {
				$orders[$k]["date"] += $config["Appearance"]["timezone_offset"];
				if (!$single_mode)
					$orders[$k]["provider"] = func_query_first_cell("SELECT provider FROM $sql_tbl[order_details] WHERE orderid='$v[orderid]'");
			}

			$smarty->assign("orders", $orders);

			$smarty->assign("main","order_delete_confirmation");

			#
			# Show admin template because only admin can delete orders
			#
			@include $xcart_dir."/modules/gold_display.php";
			func_display("admin/home.tpl",$smarty);
			exit;
		}

	}

}
elseif ($mode == "delete_all") {
#
# Prepare the confirmation page for deleting all orders
#
	$location[] = array(func_get_langvar_by_name("lbl_orders_management"), "search.php");
	$location[] = array(func_get_langvar_by_name("lbl_delete_orders"), "");
	$smarty->assign("location", $location);

	$orders_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders]");
	$smarty->assign("orders_count", $orders_count);

	$smarty->assign("mode","delete_all");
	$smarty->assign("main","order_delete_confirmation");

	#
	# Show admin template because only admin can delete orders
	#
	@include $xcart_dir."/modules/gold_display.php";
	func_display("admin/home.tpl",$smarty);
	exit;

}

$orders_to_delete = "";

$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_orders_sel");
$top_message["type"] = "W";

func_header_location("orders.php?mode=search");

?>
