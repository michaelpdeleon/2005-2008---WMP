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
# $Id: display_wishlist.php,v 1.6 2006/03/21 07:17:16 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if(empty($active_modules['Wishlist']) || ($current_area != 'A' && $current_area != 'P')) {
	func_header_location("error_message.php?access_denied&id=61");
}
x_session_register("store_search_data_w");
if (!empty($search_data) && $mode == "search") {
	$store_search_data_w = $search_data;
	func_header_location("wishlists.php?mode=search");
} else {
	$search_data = $store_search_data_w;
}

$provider_condition = (empty($active_modules['Simple_Mode']) && $current_area == 'P') ? " AND $sql_tbl[products].provider = '$login'" : "";

$smarty->assign("main","wishlists");
$location[] = array(func_get_langvar_by_name("lbl_search_wishlists"), "");

# Search wishlists
if ($mode == "search" && !empty($search_data)) {
	$where = array();
	if (!empty($search_data['login'])) {
		$where[] = "($sql_tbl[wishlist].login LIKE '%$search_data[login]%' OR $sql_tbl[customers].firstname LIKE '%$search_data[login]%' OR $sql_tbl[customers].lastname LIKE '%$search_data[login]%')";
	}
	if (!empty($search_data['sku'])) {
		$where[] = "$sql_tbl[products].productcode = '$search_data[login]'";
	}
	if (!empty($search_data['productid'])) {
		$where[] = "$sql_tbl[products].productid = '$search_data[productid]'";
	}
	if (!empty($search_data['product'])) {
		$where[] = "($sql_tbl[products].product LIKE '%$search_data[product]%' OR $sql_tbl[products].descr LIKE '%$search_data[product]%' OR $sql_tbl[products].fulldescr LIKE '%$search_data[product]%')";
	}

	$where_str = "";
	if (!empty($where))
		$where_str = " AND ".implode(" AND ", $where);

	$_res = db_query("SELECT COUNT($sql_tbl[wishlist].wishlistid) FROM $sql_tbl[wishlist], $sql_tbl[products], $sql_tbl[customers] WHERE $sql_tbl[wishlist].productid = $sql_tbl[products].productid AND $sql_tbl[wishlist].login = $sql_tbl[customers].login".$where_str.$provider_condition." GROUP BY $sql_tbl[wishlist].login");
	$total_items = db_num_rows($_res);
	db_free_result($_res);

	$objects_per_page = $config["Appearance"]["products_per_page_admin"];
	$total_nav_pages = ceil($total_items/$objects_per_page)+1;
	include $xcart_dir."/include/navigation.php";
	$wishlists = func_query("SELECT $sql_tbl[wishlist].wishlistid, $sql_tbl[customers].*, COUNT($sql_tbl[products].productid) as products_count FROM $sql_tbl[wishlist], $sql_tbl[products], $sql_tbl[customers] WHERE $sql_tbl[wishlist].productid = $sql_tbl[products].productid AND $sql_tbl[wishlist].login = $sql_tbl[customers].login".$where_str.$provider_condition." GROUP BY $sql_tbl[wishlist].login LIMIT $first_page, $objects_per_page");

	if (!empty($wishlists)) {
		$ids = array();
		foreach ($wishlists as $v) {
			$ids[] = addslashes($v['login']);
		}
		$counts = func_query_hash("SELECT $sql_tbl[wishlist].login, COUNT($sql_tbl[products].productid) as products_count FROM $sql_tbl[wishlist], $sql_tbl[products] WHERE $sql_tbl[wishlist].productid = $sql_tbl[products].productid AND $sql_tbl[wishlist].login IN ('".implode("','", $ids)."') $provider_condition GROUP BY $sql_tbl[wishlist].login", "login", false, true);
		foreach ($wishlists as $k => $v) {
			$wishlists[$k]['products_count'] = intval($counts[$v['login']]);
		}

		$smarty->assign("first_item",$first_page+1);
		$smarty->assign("last_item",$first_page+count($wishlists));
		$smarty->assign("total_items",$total_items);
		$smarty->assign("wishlists",$wishlists);
		$smarty->assign("navigation_script","wishlists.php?mode=search");
	}

# Display wishlist
} elseif ($mode == "wishlist" && $customer) {
	$wishlist = func_query("SELECT * FROM $sql_tbl[wishlist], $sql_tbl[products], $sql_tbl[customers] WHERE $sql_tbl[wishlist].productid = $sql_tbl[products].productid AND $sql_tbl[wishlist].login = $sql_tbl[customers].login AND $sql_tbl[wishlist].login='$customer' ".$provider_condition);
	if (empty($wishlist))
		func_header_location("wishlists.php");
	foreach ($wishlist as $k => $v) {
		if (!empty($v['options'])) {
			$v['options'] = unserialize($v['options']);
			list($variant, $v['product_options']) = func_get_product_options_data($v['productid'], $v['options'], $v['membershipid']);
			if(!empty($variant))
				$v = func_array_merge($v, $variant);
			$wishlist[$k] = $v;
		}
	}

	$location[count($location)-1][1] = "wishlists.php";
	$location[] = array(func_get_langvar_by_name("lbl_wish_list"), "");

	$smarty->assign("wishlist",$wishlist);
	$smarty->assign("main","wishlist");
}

$smarty->assign("mode",$mode);
$smarty->assign("search_data",$search_data);

?>
