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
# $Id: main.php,v 1.14 2006/03/16 05:57:13 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: error_message.php?permission_denied"); die("Access denied"); }

x_session_register("previous_login_date");

$location[] = array(func_get_langvar_by_name("lbl_top_info"), "");

$max_top_sellers = 10;

#
# Generate dates range
#
$curtime = time() + $config["Appearance"]["timezone_offset"];

$start_dates[] = $previous_login_date + $config["Appearance"]["timezone_offset"];  # Since last login
$start_dates[] = mktime(0,0,0,date("m",$curtime),date("d",$curtime),date("Y",$curtime)); # Today
$start_week = $curtime - (date("w",$curtime))*24*3600; # Week starts since Sunday
$start_dates[] = mktime(0,0,0,date("m",$start_week),date("d",$start_week),date("Y",$start_week)); # Current week
$start_dates[] = mktime(0,0,0,date("m",$curtime),1,date("Y",$curtime)); # Current month


foreach($start_dates as $start_date) {

	$date_condition = "AND $sql_tbl[orders].date>='$start_date' AND $sql_tbl[orders].date<='$curtime'";

	#
	# Get the orders info
	#
	$orders["P"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='P' $date_condition");
	$orders["F"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE (status='F' OR status='D') $date_condition");
	$orders["I"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='I' $date_condition");
	$orders["Q"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='Q' $date_condition");
	$gross_total[] = price_format(func_query_first_cell("SELECT SUM(total) FROM $sql_tbl[orders] WHERE 1 $date_condition"));
	$total_paid[] = price_format(func_query_first_cell("SELECT SUM(total) FROM $sql_tbl[orders] WHERE (status='P' OR status='C') $date_condition"));

	# Get top N products
	$ordered_products = func_query("SELECT $sql_tbl[order_details].productid, $sql_tbl[products].productcode, $sql_tbl[products].product, COUNT($sql_tbl[order_details].productid) as count FROM $sql_tbl[order_details], $sql_tbl[orders], $sql_tbl[products] WHERE $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid $date_condition AND $sql_tbl[orders].status NOT IN ('F','D') AND $sql_tbl[order_details].productid = $sql_tbl[products].productid GROUP BY $sql_tbl[order_details].productid ORDER BY count DESC LIMIT 0, $max_top_sellers");

	if (is_array($ordered_products)) {

		# Get top N categories
		$categories = func_query("SELECT $sql_tbl[products_categories].categoryid, COUNT($sql_tbl[products_categories].categoryid) as count FROM $sql_tbl[order_details], $sql_tbl[orders], $sql_tbl[products_categories] WHERE $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid $date_condition AND $sql_tbl[order_details].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main='Y' GROUP BY $sql_tbl[products_categories].categoryid ORDER BY count DESC LIMIT 0, $max_top_sellers");

		if (is_array($categories)) {
			foreach ($categories as $idx => $category) {
				$c = func_query_first("SELECT categoryid_path, category FROM $sql_tbl[categories] WHERE categoryid = '$category[categoryid]'");
				if (empty($c))
					continue;
				$category = func_array_merge($category, $c);

				if (substr_count($category["categoryid_path"], "/") > 1) {
					$__tmp = explode("/", $category["categoryid_path"]);
					$category["category"] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid='$__tmp[0]'") . "/.../" . $category["category"];
				}
				$categories[$idx] = $category;
			}
		}
	}

	$top_sellers[] = $ordered_products;
	$top_categories[] = $categories;

}

#
# Get the last order information
#
$last_order = func_query_first("SELECT orderid, status, total, title, firstname, lastname, date FROM $sql_tbl[orders] ORDER BY date DESC LIMIT 1");

if (!empty($last_order)) {
	# Get products ordered in the last order
	$last_order_products = func_query("SELECT productid, product_options, price, amount FROM $sql_tbl[order_details] WHERE orderid='$last_order[orderid]'");
	if (is_array($last_order_products)) {
		foreach ($last_order_products as $k=>$v) {
			$last_order["products"][] = func_array_merge(func_query_first("SELECT * FROM $sql_tbl[products] WHERE productid='$v[productid]'"), $v);
		}
	}
	# Get gift certificates ordered in the last order
	$last_order["giftcerts"] = func_query("SELECT gcid, amount FROM $sql_tbl[giftcerts] WHERE orderid='$last_order[orderid]'");

	$last_order['date'] += $config["General"]["timezone_offset"];
}


if (!x_session_is_registered("hide_security_warning")) {
	$smarty->assign("current_passwords_security", func_check_default_passwords($login));
	$smarty->assign("default_passwords_security", func_check_default_passwords());
	x_session_register("hide_security_warning");
	x_session_save("hide_security_warning");
}

#
# Set up the smarty templates variables
#
$smarty->assign("orders", $orders);
$smarty->assign("gross_total", $gross_total);
$smarty->assign("total_paid", $total_paid);

$smarty->assign("max_top_sellers", $max_top_sellers);
$smarty->assign("top_sellers", $top_sellers);
$smarty->assign("top_categories", $top_categories);

$smarty->assign("last_order", $last_order);

?>
