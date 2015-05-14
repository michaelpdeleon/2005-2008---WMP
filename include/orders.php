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
# $Id: orders.php,v 1.76.2.1 2006/06/27 05:29:42 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('export');

$location[] = array(func_get_langvar_by_name("lbl_orders_management"), "orders.php");
$smarty->assign("location", $location);

$do_export = in_array($mode, array("export","export_found", "export_all"));

$advanced_options = array("orderid1", "orderid2", "total_max", "payment_method", "shipping_method", "status", "provider", "features", "product_substring", "productcode", "productid", "price_max", "customer", "address_type", "phone", "email");


if ($REQUEST_METHOD == "GET") {
	#
	# Quick orders search
	#
	$go_search = false;
	if (!empty($date) && in_array($date, array("M","W","D"))) {
		$search_data["orders"]["date_period"] = $date;
		$go_search = true;
	}

	if (!empty($status) && in_array($status, array("P","C","D","F","Q","B"))) {
		$search_data["orders"]["status"] = $status;
		$go_search = true;
	}

	if ($go_search)
		func_header_location("orders.php?mode=search");
}

if ($REQUEST_METHOD == "POST" && !$do_export) {
	#
	# Update the session $search_data variable from $posted_data
	#
	if (!empty($posted_data)) {

		$need_advanced_options = false;
		foreach ($posted_data as $k=>$v) {
			if (!is_array($v) && !is_numeric($v))
				$posted_data[$k] = stripslashes($v);

			if (is_array($v)) {
				$tmp = array();
				foreach ($v as $k1=>$v1) {
					$tmp[$v1] = 1;
				}

				$posted_data[$k] = $tmp;
			}

			if (in_array($k, $advanced_options) && !empty($v))
				$need_advanced_options = true;
		}

		if (!$need_advanced_options)
			$need_advanced_options = (doubleval($posted_data["price_min"]) != 0 || doubleval($posted_data["total_min"]) != 0);

		$posted_data["need_advanced_options"] = $need_advanced_options;

		if ($StartMonth) {
			$posted_data["start_date"] = mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
			$posted_data["end_date"] = mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
		}

		if (empty($search_data["orders"]["sort_field"])) {
			$posted_data["sort_field"] = "orderid";
			$posted_data["sort_direction"] = 1;
		}
		else {
			$posted_data["sort_field"] = $search_data["orders"]["sort_field"];
			$posted_data["sort_direction"] = $search_data["orders"]["sort_direction"];
		}

		$search_data["orders"] = $posted_data;

	}

	func_header_location("orders.php?mode=search");
}
elseif ($REQUEST_METHOD == "POST" && $do_export) {
	#
	# Export all orders
	#
	include $xcart_dir."/include/orders_export.php";
}

if ($mode == "search") {
	#
	# Perform search and display results
	#

	$data = array();

	$flag_save = false;

	#
	# Prepare the search data
	#
	if (!empty($sort) && in_array($sort, array("orderid","status","customer","date","provider", "total"))) {
		# Store the sorting type in the session
		$search_data["orders"]["sort_field"] = $sort;
		$search_data["orders"]["sort_direction"] = abs(intval($search_data["orders"]["sort_direction"]) - 1);
		$flag_save = true;
	}

	if (!empty($page) && $search_data["orders"]["page"] != intval($page)) {
		# Store the current page number in the session
		$search_data["orders"]["page"] = $page;
		$flag_save = true;
	}

	if ($flag_save)
		x_session_save("search_data");

	if (is_array($search_data["orders"])) {
		$data = $search_data["orders"];
		foreach ($data as $k=>$v) {
			if (!is_array($v) && !is_numeric($v))
				$data[$k] = addslashes($v);
		}
	}

	$search_condition = "";
	$search_in_order_details = false;
	$search_in_products = false;
	$search_from = array($sql_tbl["orders"]);
	$search_links = array();

	# Search by orderid
	if (!empty($data["orderid1"]))
		$search_condition .= " AND $sql_tbl[orders].orderid>='".intval($data["orderid1"])."'";

	if (!empty($data["orderid2"]))
		$search_condition .= " AND $sql_tbl[orders].orderid<='".intval($data["orderid2"])."'";

	# Search by order total
	if (!empty($data["total_min"]) && doubleval($data["total_min"]) != 0)
		$search_condition .= " AND $sql_tbl[orders].total>='".doubleval($data["total_min"])."'";

	if (!empty($data["total_max"]))
		$search_condition .= " AND $sql_tbl[orders].total<='".doubleval($data["total_max"])."'";

	# Search by payment method
	if (!empty($data["payment_method"]))
		$search_condition .= " AND $sql_tbl[orders].payment_method LIKE '".$data["payment_method"]."%'";

	# Search by shipping method
	if (!empty($data["shipping_method"]))
		$search_condition .= " AND $sql_tbl[orders].shippingid='".intval($data["shipping_method"])."'";

	# Search by order status
	if (!empty($data["status"]))
		$search_condition .= " AND $sql_tbl[orders].status='".$data["status"]."'";

	#
	# Exact search by provider (for provider area and $single_mode = false)
	#
	if (!empty($data["provider_login"])) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].provider='".$data["provider_login"]."'";
	}

	# Search by provider
	if (!empty($data["provider"])) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].provider LIKE '%".$data["provider"]."%'";
	}

	#
	# Search by date condition
	#
	if (!empty($data["date_period"])) {
		if ($data["date_period"] == "C") {
			# ...orders within specified period
			$start_date = $data["start_date"] - $config["Appearance"]["timezone_offset"];
			$end_date = $data["end_date"] - $config["Appearance"]["timezone_offset"];
		}
		else {
			# ...orders within this month
			$end_date = time() + $config["Appearance"]["timezone_offset"];
			if ($data["date_period"] == "M") {
				$start_date = mktime(0,0,0,date("n",$end_date),1,date("Y",$end_date));
			}
			elseif ($data["date_period"] == "D") {
				$start_date = mktime(0,0,0,date("n",$end_date),date("j",$end_date),date("Y",$end_date));
			}
			elseif ($data["date_period"] == "W") {
				$first_weekday = $end_date - (date("w",$end_date) * 86400);
				$start_date = mktime(0,0,0,date("n",$first_weekday),date("j",$first_weekday),date("Y",$first_weekday));
			}

			$start_date -= $config["Appearance"]["timezone_offset"];
			$end_date = time();
		}

		$search_condition .= " AND $sql_tbl[orders].date>='".($start_date)."'";
		$search_condition .= " AND $sql_tbl[orders].date<='".($end_date)."'";
	}

	#
	# Exact search by customer login (for customers area)
	#
	if (!empty($data["customer_login"]))
		$search_condition .= " AND $sql_tbl[orders].login='".$data["customer_login"]."'";

	#
	# Search by custtomer
	#
	if (!empty($data["customer"]) && (!empty($data['by_username']) || !empty($data['by_firstname']) || !empty($data['by_lastname']))) {
		$condition = array();	
		if (!empty($data['by_username']))
			$condition[] = "$sql_tbl[orders].login LIKE '%".$data["customer"]."%'";
		if (!empty($data['by_firstname']))
			$condition[] = "$sql_tbl[orders].firstname LIKE '%".$data["customer"]."%'";
		if (!empty($data['by_lastname']))
			$condition[] = "$sql_tbl[orders].lastname LIKE '%".$data["customer"]."%'";
		if (preg_match("/^(.+)\s+(.+)$/", $data["customer"], $found) && !empty($data["by_firstname"]) && !empty($data["by_lastname"]))
			$condition[] = "$sql_tbl[orders].firstname LIKE '%".trim($found[1])."%' AND $sql_tbl[orders].lastname LIKE '%".trim($found[2])."%'";

		if (!empty($condition))
			$search_condition .= " AND (".implode(" OR ", $condition).")";
	}

	if (!empty($data["address_type"])) {
		#
		# Search by address...
		#
		if (!empty($data["city"]))
			$address_condition .= " AND $sql_tbl[orders].PREFIX_city LIKE '%".$data["city"]."%'";

		if (!empty($data["state"]))
			$address_condition .= " AND $sql_tbl[orders].PREFIX_state='".$data["state"]."'";

		if (!empty($data["country"]))
			$address_condition .= " AND $sql_tbl[orders].PREFIX_country='".$data["country"]."'";

		if (!empty($data["zipcode"]))
			$address_condition .= " AND $sql_tbl[orders].PREFIX_zipcode LIKE '%".$data["zipcode"]."%'";

		if ($data["address_type"] == "B" || $data["address_type"] == "Both")
			$search_condition .= preg_replace("/AND ".$sql_tbl["orders"]."\.PREFIX_(city|state|country|zipcode)/", "AND ".$sql_tbl["orders"].".b_\\1", $address_condition);

		if ($data["address_type"] == "S" || $data["address_type"] == "Both")
			$search_condition .= preg_replace("/AND ".$sql_tbl["orders"]."\.PREFIX_(city|state|country|zipcode)/", "AND ".$sql_tbl["orders"].".s_\\1", $address_condition);
	}

	# Search by e-mail pattern
	if (!empty($data["email"]))
		$search_condition .= " AND $sql_tbl[orders].email LIKE '%".$data["email"]."%'";

	# Search by phone/fax pattern
	if (!empty($data["phone"]))
		$search_condition .= " AND ($sql_tbl[orders].phone LIKE '%".$data["phone"]."%' OR $sql_tbl[orders].fax LIKE '%".$data["phone"]."%')";

	#
	# Search by special features
	#
	if (!empty($data["features"])) {
		# Search for orders that payed by Gift Certificates
		if (!empty($data["features"]["gc_applied"]))
			$search_condition .= " AND $sql_tbl[orders].giftcert_discount>0";

		# Search for orders with global discount applied
		if (!empty($data["features"]["discount_applied"]))
			$search_condition .= " AND $sql_tbl[orders].discount>0";

		# Sea4rch for orders with discount coupon applied
		if (!empty($data["features"]["coupon_applied"]))
			$search_condition .= " AND $sql_tbl[orders].coupon!=''";

		# Search for orders with free shipping (shipping cost = 0)
		if (!empty($data["features"]["free_ship"]))
			$search_condition .= " AND $sql_tbl[orders].shipping_cost=0";

		# Search for orders with free taxes
		if (!empty($data["features"]["free_tax"]))
			$search_condition .= " AND $sql_tbl[orders].tax=0 ";

		# Search for orders with notes assigned
		if (!empty($data["features"]["notes"]))
			$search_condition .= " AND $sql_tbl[orders].notes!=''";

		# Search for orders with Gift Certificates ordered
		if (!empty($data["features"]["gc_ordered"])) {
			$search_from[] = $sql_tbl["giftcerts"];
			$search_links[] = "$sql_tbl[orders].orderid=$sql_tbl[giftcerts].orderid";
		}
	}

	#
	# Search by ordered products
	#
	if (!empty($data["product_substring"])) {

		$search_in_order_details = true;
		$condition = array();

		# Search by product title
		if (!empty($data["by_title"])) {
			$search_in_products = true;
			$condition[] = "$sql_tbl[products].product LIKE '%".$data["product_substring"]."%'";
		}

		# Search by product options
		if (!empty($data["by_options"])) {
			$search_in_order_details = true;
			$condition[] = "$sql_tbl[order_details].product_options LIKE '%".$data["product_substring"]."%'";
		}

		if (!empty($condition) && is_array($condition)) {
			$search_condition .= " AND (".implode(" OR ", $condition).")";
		}
	}

	# Search by product code (SKU)
	if (!empty($data["productcode"])) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].productcode LIKE '%".$data["productcode"]."%'";
	}

	# Search by product ID
	if (!empty($data["productid"])) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].productid='".$data["productid"]."'";
	}

	#
	# Search by product price range
	#
	if (!empty($data["price_min"]) && doubleval($data["price_min"]) != 0) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].price>='".$data["price_min"]."'";
	}

	if (!empty($data["price_max"])) {
		$search_in_order_details = true;
		$search_condition .= " AND $sql_tbl[order_details].price<='".$data["price_max"]."'";
	}

	$sort_string = "$sql_tbl[orders].orderid DESC";

	if (!empty($data["sort_field"])) {
		# Sort the search results...

		$direction = ($data["sort_direction"] ? "DESC" : "ASC");
		switch ($data["sort_field"]) {
			case "orderid":
				$sort_string = "$sql_tbl[orders].orderid $direction";
				break;
			case "status":
				$sort_string = "$sql_tbl[orders].status $direction";
				break;
			case "customer":
				$sort_string = "$sql_tbl[orders].login $direction";
				break;
			case "provider":
				if (!$single_mode && $search_in_order_details)
					$sort_string = "$sql_tbl[order_details].provider $direction";
				break;
			case "date":
				$sort_string = "$sql_tbl[orders].date $direction";
				break;
			case "total":
				$sort_string = "$sql_tbl[orders].total $direction";
		}
	}

	#
	# Prepare the SQL query
	#
	if ($search_in_order_details) {
		$search_from[] = $sql_tbl["order_details"];
		$search_links[] = "$sql_tbl[orders].orderid=$sql_tbl[order_details].orderid";
		if ($search_in_products) {
			$search_from[] = $sql_tbl["products"];
			$search_links[] = "$sql_tbl[order_details].productid=$sql_tbl[products].productid";
		}

	}

	if (is_array($search_from))
		$search_from = "FROM ".implode(", ", $search_from);

	if (!empty($search_links))
		$search_links = implode(" AND ", $search_links);
	else
		$search_links = "1";

	$search_condition = "$search_from WHERE $search_links $search_condition GROUP BY $sql_tbl[orders].orderid ";

	#
	# Count the items in the search results
	#
	$_res = db_query("SELECT $sql_tbl[orders].orderid $search_condition");
	$total_items = db_num_rows($_res);
	db_free_result($_res);

	if ($total_items > 0) {
		#
		# Perform the SQL and get the search results
		#
		if ($data['is_export'] == 'Y') {

			func_export_range_save("ORDERS", "SELECT $sql_tbl[orders].orderid $search_condition");
			$top_message['content'] = func_get_langvar_by_name("lbl_export_orders_add");
			$top_message['type'] = 'I';
			func_header_location("import.php?mode=export");
		}
		elseif ($HTTP_GET_VARS['export'] == 'export_found') {
			# Export all found orders
			$REQUEST_METHOD = "POST";
			$orderids = func_query_column("SELECT $sql_tbl[orders].orderid $search_condition");
			include $xcart_dir."/include/orders_export.php";

		}
		else {
			#
			# If orders do not exports, separate them on the pages
			#
			$page = $search_data["orders"]["page"];

			#
			# Prepare the page navigation
			#
			$objects_per_page = $config["Appearance"]["orders_per_page_admin"];

			$total_nav_pages = ceil($total_items/$objects_per_page)+1;

			include $xcart_dir."/include/navigation.php";

			#
			# Get the results for current pages
			#
			$orders = func_query("SELECT $sql_tbl[orders].* $search_condition ORDER BY $sort_string LIMIT $first_page, $objects_per_page");

			# Assign the Smarty variables
			$smarty->assign("navigation_script","orders.php?mode=search");
			$smarty->assign("first_item", $first_page+1);
			$smarty->assign("last_item", min($first_page+$objects_per_page, $total_items));
		}

		if ($orders) {
			foreach ($orders as $k=>$v) {
				if (!$single_mode)
					$orders[$k]["provider"] = func_query_first_cell("SELECT provider FROM $sql_tbl[order_details] WHERE orderid='$v[orderid]'");

				$orders[$k]["date"] += $config["Appearance"]["timezone_offset"];
				if (!empty($v["add_date"]))
					$orders[$k]["add_date"] += $config["Appearance"]["timezone_offset"];

				if ($current_area != 'C' && $active_modules['Stop_List']) {
					$orders[$k]['blocked'] = func_ip_exist_slist(func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE khash = 'ip' AND orderid = '$v[orderid]'"));
				}
			}
		}

		$smarty->assign("orders", $orders);
	}

	$smarty->assign("total_items", $total_items);
	$smarty->assign("mode", $mode);
}

include $xcart_dir."/include/states.php";
include $xcart_dir."/include/countries.php";

if (empty($search_data['orders']['end_date'])) {
	$search_data['orders']['end_date'] = $search_data['orders']['start_date'] = time() + $config["Appearance"]["timezone_offset"];
}

$smarty->assign("search_prefilled", $search_data["orders"]);

$payment_methods = func_query("SELECT payment_method FROM $sql_tbl[payment_methods] ORDER BY payment_method");
$smarty->assign("payment_methods", $payment_methods);

$shipping_methods = func_query("SELECT shippingid, shipping FROM $sql_tbl[shipping] WHERE active='Y' ORDER BY code, shipping");
$smarty->assign("shipping_methods", $shipping_methods);

$smarty->assign("orders_full", @$orders_full);

$smarty->assign("single_mode", $single_mode);

$smarty->assign("start_date",$start_date);
$smarty->assign("end_date",$end_date);
$smarty->assign("main","orders");

?>
