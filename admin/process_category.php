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
# $Id: process_category.php,v 1.33.2.1 2006/07/10 05:24:21 svowl Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('category');

require $xcart_dir."/include/categories.php";

if ($REQUEST_METHOD == "POST") {

	if ($mode == "apply") {

		#
		# Update categories list
		#
		$k = 0;
		if ($posted_data) {
			if (!empty($active_modules['Fancy_Categories'])) {
				$old_data = func_fc_save_category_data(array_keys($posted_data));
			}

			foreach ($posted_data as $k => $v) {
				$query_data = array(
					"order_by" => intval($v["order_by"]),
					"avail" => ($v["avail"] == "Y" ? "Y" : "N")
				);
				func_array2update("categories", $query_data, "categoryid='".intval($k)."'");
			}

			# Update categories data cache
			if (!empty($active_modules['Fancy_Categories'])) {
				$cats = func_fc_check_rebuild(array_keys($posted_data), "C", $old_data);
				if (!empty($cats))
					func_fc_build_categories($cats, 1);

			}
		}

		# Update subcategories counters
		if (!empty($k)) {
			$path = explode("/", func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid = '$k'"));
			if (!empty($path)) {
				array_shift($path);
				if (!empty($path))
					func_recalc_subcat_count($path);
			}
		}

		$top_message["content"] = func_get_langvar_by_name("msg_adm_categories_upd");
		$top_message["type"] = "I";
		func_header_location("categories.php?cat=$cat_org");

	}
	elseif ($mode == "update") {

		#
		# Go to modify category
		#
		func_header_location("category_modify.php?cat=$cat");

	}
	elseif ($mode == "delete") {

		#
		# Delete category
		#
		if ($confirmed == "Y") {
			#
			# Delete category from database
			# Delete all subcategories and associated products
			#
			require $xcart_dir."/include/safe_mode.php";

			$parent_categoryid = func_delete_category($cat);

			if (!empty($active_modules['Fancy_Categories']) && !empty($parent_categoryid)) {
				$cats = func_fc_check_rebuild($parent_categoryid, "C");
				if (!empty($cats))
					func_fc_build_categories($cats, 1);

			}

			$top_message["content"] = func_get_langvar_by_name("msg_adm_category_del");
			$top_message["type"] = "I";
			func_header_location("categories.php?cat=$parent_categoryid");
		}
		else {

			#
			# Go to prepare delete confirmation page
			#
			func_header_location("process_category.php?cat=$cat&mode=delete");
		}
	}
}

if ($mode == "add") {
	#
	# Add new category
	#
	func_header_location("category_modify.php?$QUERY_STRING");
}


if ($mode == "delete" && $confirmed != "Y") {
	#
	# Prepare the delete confirmation page
	#
	$location[] = array(func_get_langvar_by_name("lbl_categories_management"), "categories.php");
	$location[] = array(func_get_langvar_by_name("lbl_delete_category"), "");

	$subcats = func_query("SELECT categoryid, category FROM $sql_tbl[categories] WHERE categoryid_path LIKE '".$current_category["categoryid_path"]."/%'");
	if (!is_array($subcats))
		$subcats = array();

	array_unshift($subcats, $current_category);
	if (is_array($subcats)) {
		foreach ($subcats as $k=>$v) {
			$subcats[$k]["products"] = func_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].productcode, $sql_tbl[products].product FROM $sql_tbl[products_categories], $sql_tbl[products] WHERE $sql_tbl[products_categories].categoryid='$v[categoryid]' AND $sql_tbl[products_categories].productid=$sql_tbl[products].productid AND $sql_tbl[products_categories].main='Y'");
			$subcats[$k]["products_count"] = (is_array($subcats[$k]["products"]) ? count($subcats[$k]["products"]) : 0);
		}
	}

	$smarty->assign("subcats", $subcats);
	$smarty->assign("main","category_delete_confirmation");

	# Assign the current location line
	$smarty->assign("location", $location);

	@include $xcart_dir."/modules/gold_display.php";
	func_display("admin/home.tpl",$smarty);
}

?>
