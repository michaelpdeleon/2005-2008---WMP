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
# $Id: categories.php,v 1.31 2006/02/14 14:45:23 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_categories_management"), "categories.php");

define('MANAGE_CATEGORIES', 1);

require $xcart_dir."/include/categories.php";

if (empty($mode)) $mode = "";

#
# Counts products and subcategories
#
if (is_array(@$subcategories)) {
	$smarty->assign("subcategories",$subcategories);
}

#
# Ajust category_location array
#
require "./location_ajust.php";

$category_location[count($category_location)-1][1] = "";
$smarty->assign("category_location", $category_location);

# FEATURED PRODUCTS
$f_cat = (empty ($cat) ? "0" : $cat);

if ($REQUEST_METHOD == "POST") {

	if ($mode == "update") {

		#
		# Update featured products list
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $productid=>$v) {
				$query_data = array(
					"avail" => (!empty($v["avail"]) ? "Y" : "N"),
					"product_order" => intval($v["product_order"])
				);
				func_array2update("featured_products", $query_data, "productid='$productid' AND categoryid='$f_cat'");
			}
			$top_message["content"] = func_get_langvar_by_name("msg_adm_featproducts_upd");
			$top_message["anchor"] = "featured";
		}
	
	} elseif ($mode == "delete") {

		#
		# Delete selected featured products from the list
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $productid=>$v) {
				if (empty($v["to_delete"]))
					continue;
				db_query ("DELETE FROM $sql_tbl[featured_products] WHERE productid='$productid' AND categoryid='$f_cat'");
			}
			$top_message["content"] = func_get_langvar_by_name("msg_adm_featproducts_del");
		}

	} elseif ($mode == "add" && intval($newproductid) > 0) {

		#
		# Add new featured product
		#
		$newavail = (!empty($newavail) ? "Y" : "N");
		if ($neworder == "") {
			$maxorder = func_query_first_cell("SELECT MAX(product_order) FROM $sql_tbl[featured_products] WHERE categoryid='$f_cat'");
			$neworder = $maxorder + 10;
		}

		if (func_query_first("SELECT productid FROM $sql_tbl[products] WHERE productid='$newproductid'")) {
			db_query("REPLACE INTO $sql_tbl[featured_products] (productid, product_order, avail, categoryid) VALUES ('$newproductid','$neworder','$newavail', '$f_cat')");
			$top_message["content"] = func_get_langvar_by_name("msg_adm_featproducts_upd");
		}
	}
	
	$top_message["anchor"] = "featured";
	
	func_header_location("categories.php?cat=$cat");

}


$products = func_query ("SELECT $sql_tbl[featured_products].productid, $sql_tbl[products].product, $sql_tbl[featured_products].product_order, $sql_tbl[featured_products].avail from $sql_tbl[featured_products], $sql_tbl[products] where $sql_tbl[featured_products].productid=$sql_tbl[products].productid AND $sql_tbl[featured_products].categoryid='$f_cat' order by $sql_tbl[featured_products].product_order");
$smarty->assign ("products", $products);
$smarty->assign ("f_cat", $f_cat);

$smarty->assign("main","categories");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
