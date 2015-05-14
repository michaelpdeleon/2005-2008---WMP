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
# $Id: customer_manufacturers_list.php,v 1.14.2.1 2006/06/08 09:29:17 svowl Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

require $xcart_dir."/include/categories.php";

if ($active_modules["Manufacturers"])
    include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

$location[] = array(func_get_langvar_by_name("lbl_manufacturers"), "");

if ($manufacturerid) {
	#
	# Get products data for current category and store it into $products array
	#
	$old_search_data = $search_data["products"];
	$old_mode = $mode;

	$search_data["products"] = array();
	$search_data["products"]["manufacturers"] = array($manufacturerid);
	$search_data["products"]["forsale"] = 'Y';
	if (!isset($sort)) {
		$search_data["products"]['sort_field'] = $config["Appearance"]["products_order"];
	}
	else {
		$search_data["products"]['sort_field'] = $sort;
	}

	if (!isset($sort_direction)) {
		$search_data["products"]['sort_direction'] = 0;
	}
	else {
		$search_data["products"]['sort_direction'] = $sort_direction;
	}

	$mode = "search";

	include $xcart_dir."/include/search.php";

	$smarty->assign("sort",$search_data["products"]['sort_field']);
	$smarty->assign("sort_direction",$search_data["products"]['sort_direction']);
	$search_data["products"] = $old_search_data;
	$mode = $old_mode;

	if (!empty($active_modules["Subscriptions"]))
		include $xcart_dir."/modules/Subscriptions/subscription.php";

	$smarty->assign("products",$products);

	$manufacturer = func_query_first("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, $sql_tbl[images_M].image_path, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[images_M].id WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid' ORDER BY $sql_tbl[manufacturers].orderby");
	$smarty->assign("manufacturer", $manufacturer);

	$smarty->assign("main","manufacturer_products");

	$location[count($location)-1][1] = "manufacturers.php";
	$location[] = array($manufacturer['manufacturer'], "");
}
else {
	$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE avail = 'Y'");
	if ($total_items > 0) {
		$objects_per_page = $config["Manufacturers"]["manufacturers_per_page"];
		$total_nav_pages = ceil($total_items/$objects_per_page)+1;
		include $xcart_dir."/include/navigation.php";
		$manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' WHERE avail = 'Y' ORDER BY $sql_tbl[manufacturers].orderby, manufacturer LIMIT $first_page, $objects_per_page");
		$smarty->assign("manufacturers", $manufacturers);
	}

	$smarty->assign("main","manufacturers_list");
}

$smarty->assign("navigation_script","manufacturers.php?manufacturerid=".$manufacturerid."&sort=".$sort."&sort_direction=".$sort_direction);

$smarty->assign("manufacturerid", $manufacturerid);
?>
