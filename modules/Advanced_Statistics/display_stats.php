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
# $Id: display_stats.php,v 1.30.2.1 2006/05/17 06:26:41 max Exp $
#
# This module generates lists to be displayed in advanced statistics 
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

#
# Navigation code
#
$objects_per_page = 15;

$last_visited = "last_visited+'".$config["Appearance"]["timezone_offset"]."'";

$res = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[referers] WHERE ($last_visited>='$start_date' AND $last_visited<='$end_date')");
$total_nav_pages = ceil($res/$objects_per_page)+1;

require $xcart_dir."/include/navigation.php";


#
# List of category views
#
if (!empty($cat)) {
	include $xcart_dir."/include/categories.php";
	$cat_path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid = '$cat'");
	$search_condition = " AND c.categoryid_path LIKE '$cat_path/%'";
}
else
	$search_condition = " AND c.parentid='0'";

$category_viewes = func_query("SELECT c.categoryid, c.category, c.categoryid_path, COUNT(ss.id) as views_stats FROM $sql_tbl[categories] as c, $sql_tbl[stats_shop] as ss WHERE ss.action='C' AND ss.id=c.categoryid $search_condition AND $date_condition GROUP BY c.categoryid ORDER BY views_stats DESC");

$max_category_viewes = 0;
if (is_array($category_viewes)) {
	$re_cat_path = "/^".preg_quote($cat_path, "/")."\//S";
	foreach($category_viewes as $k=>$v) {
		# Get the maximum of category_views
		$max_category_viewes = max($max_category_viewes, $v["views_stats"]);
		
		# Get the category path
		$_category_names[$v["categoryid"]] = $v["category"];
		$categoryid_path = preg_replace($re_cat_path, "", $v["categoryid_path"]);
		
		$path = explode("/", $categoryid_path);
		$category_path = array();
		foreach ($path as $i=>$catid) {
			if (empty($_category_names[$catid]))
				$_category_names[$catid] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid='$catid'");

			$category_path[] = $_category_names[$catid];
		}

		$category_viewes[$k]["category_path"] = implode("/",$category_path);
	}
}

#
# Make navigation bar
#
$nav_bar = $current_category["category_location"];
if (is_array($nav_bar))
	foreach ($nav_bar as $k=>$v)
		$nav_bar[$k][1] = str_replace("home.php?","statistics.php?mode=shop&", $v[1]);



if ($current_category["categoryid_path"] != "") {
#
# List of product views
#
	$product_viewes = func_query("SELECT p.productid, p.product, COUNT(ss.id) as views_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid_path LIKE '$current_category[categoryid_path]%' AND ss.id=p.productid AND ss.action='V' AND $date_condition GROUP BY p.productid ORDER BY views_stats DESC");

	$max_product_viewes = 0;
	if (is_array($product_viewes))
		foreach($product_viewes as $k=>$v) {
			$max_product_viewes = max($max_product_viewes, $v["views_stats"]);
		}
#
# List of product sales 
#
	$product_sales = func_query("SELECT p.productid, p.product, COUNT(ss.id) as sales_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid_path LIKE '$current_category[categoryid_path]%' AND ss.id=p.productid AND ss.action='S' AND $date_condition GROUP BY p.productid ORDER BY sales_stats DESC");

	$max_product_sales = 0;
	if (is_array($product_sales))
		foreach($product_sales as $k=>$v) {
			$max_product_sales = max($max_product_sales, $v["sales_stats"]);
		}

#
# List of deleted from the cart products 
#
	$product_deleted = func_query("SELECT p.productid, p.product, COUNT(id) as del_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid_path LIKE '$current_category[categoryid_path]%' AND ss.id=p.productid AND ss.action='D' AND $date_condition GROUP BY p.productid ORDER BY del_stats DESC");

	$max_product_deleted = 0;
	if (is_array($product_deleted))
		foreach($product_deleted as $k=>$v) {
			$max_product_deleted = max($max_product_deleted, $v["del_stats"]);
		}

}

#
# Prepare statistics on referers
#
$referers_array = func_query("SELECT * FROM $sql_tbl[referers] WHERE ($last_visited>='$start_date' AND $last_visited<='$end_date') ORDER BY visits DESC LIMIT $first_page, $objects_per_page");
$res = func_query_first("SELECT MAX(visits) FROM $sql_tbl[referers]");
$max_visits = $res["MAX(visits)"];


#
# Assign Smarty variables
#
$smarty->assign("category_viewes", $category_viewes);
$smarty->assign("product_viewes", $product_viewes);
$smarty->assign("product_sales", $product_sales);
$smarty->assign("product_deleted", $product_deleted);
$smarty->assign("referers_array", $referers_array);
$smarty->assign("max_category_viewes", $max_category_viewes);
$smarty->assign("max_product_viewes", $max_product_viewes);
$smarty->assign("max_product_sales", $max_product_sales);
$smarty->assign("max_product_deleted", $max_product_deleted);
$smarty->assign("max_visits", $max_visits);
$smarty->assign("cat_name", $cat_name);
$smarty->assign("nav_bar", $nav_bar);
$smarty->assign("navigation_script","statistics.php?cat=$cat&mode=shop");

?>
