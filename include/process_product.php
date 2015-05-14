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
# $Id: process_product.php,v 1.46 2006/01/11 06:55:59 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('export','product');

if ($REQUEST_METHOD == "POST" || $mode == 'clone' || ($mode == 'delete' && !empty($productid))) {

	#
	# Get the productid (the first selected item)
	#
	if(empty($productids) && !empty($productid))
		$productids = array($productid => true);
	if (is_array($productids)) {
		foreach ($productids as $k=>$v) {
			$productid = $k;
			break;
		}

		reset($productids);
	}
	else
		$productid = 0;

	if ($mode == "export" && !empty($productids)) {
		func_export_range_save("PRODUCTS", array_keys($productids));
		$top_message['content'] = func_get_langvar_by_name("lbl_export_products_add");
		$top_message['type'] = 'I';
		func_header_location("import.php?mode=export");

	}
	elseif ($mode == "update") {
		#
		# Update the products
		#
		if (is_array($posted_data)) {

			foreach ($posted_data as $k=>$v) {

				$k = intval($k);
				$update = array();

				# Include 'avail' field into the updating list
				if (isset($v["orderby"]) && is_numeric($v["orderby"])) {
					$cat = intval($cat);
					db_query("UPDATE $sql_tbl[products_categories] SET orderby='".intval($v["orderby"])."' WHERE productid='$k' AND categoryid='$cat'");
				}

				# Include 'avail' field into the updating list
				if (isset($v["avail"]) && is_numeric($v["avail"]))
					$update[] = "avail='".intval($v["avail"])."'";

				# Perform SQL query to update products
				if (!empty($update))
					db_query("UPDATE $sql_tbl[products] SET ".implode(",", $update)." WHERE productid='$k'");

				# Perform SQL query to update product prices
				if (isset($v["price"])) {
					$v["price"] = func_convert_number($v["price"]);
					db_query("UPDATE $sql_tbl[pricing] SET price='".doubleval($v["price"])."' WHERE productid='$k' AND quantity='1' AND membershipid = 0 AND $sql_tbl[pricing].variantid = 0");
				}
			}

			#
			# Prepare the information message
			#
			$top_message["content"] = func_get_langvar_by_name("msg_adm_products_upd");
			$top_message["type"] = "I";
		}
	} # /if ($mode == "update")
	elseif ($mode == "delete") {
		#
		# Delete the selected products
		#
		x_session_register("products_to_delete");

		if ($confirmed=="Y") {
			# Deleting is confirmed

			require $xcart_dir."/include/safe_mode.php";

			if (is_array($products_to_delete["products"])) {
				foreach ($products_to_delete["products"] as $k=>$v)
					func_delete_product($k);

				$force_return = $products_to_delete["search_return"];
				#
				# Prepare the information message
				#
				$top_message["content"] = func_get_langvar_by_name("msg_adm_products_del");
				$top_message["type"] = "I";
				x_log_flag('log_products_delete', 'PRODUCTS', "Login: $login\nIP: $REMOTE_ADDR\nOperation: delete products (".implode(',', array_keys($products_to_delete["products"])).")", true);
			}
			else {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_products_del");
				$top_message["type"] = "W";
			}
		}
		else {
			$products_to_delete["products"] = $productids;
			$products_to_delete["navpage"] = $navpage;
			$products_to_delete["section"] = @$section;
			if ($REQUEST_METHOD == 'POST')
				$products_to_delete["search_return"] = $HTTP_REFERER;

			$products_to_delete["cat"] = @$cat;
			func_header_location("process_product.php?mode=delete");
		}
	} # /if ($mode == "delete")
	elseif ($mode == "links" && !empty($productid)) {
		#
		# Generate HTML-links
		#
		func_header_location("product_links.php?productid=$productid");
	}
	elseif ($mode == "clone" && !empty($productid)) {
		#
		# Clone product
		#
		include $xcart_dir."/include/product_clone.php";
	}
	elseif($mode=="details" && !empty($productid)) {
		#
		# Show product details
		#
		func_header_location("product.php?productid=$productid");
	}

	if ($section == "category_products") {
		func_header_location("category_products.php?cat=$cat".(intval($navpage)>1 ? "&page=$navpage" : ""));
	}
	else {
		if(!empty($force_return)) {
			func_header_location($force_return);
		}
		elseif($mode == 'clone' || $mode == "details") {
			func_header_location($HTTP_REFERER);
		}

		func_header_location("search.php?mode=search".(intval($navpage)>1 ? "&page=$navpage" : ""));
	}

} # /if ($REQUEST_METHOD == "POST")

if ($mode == "delete" && $REQUEST_METHOD == "GET") {
	#
	# Prepare for deleting products
	#
	x_session_register("products_to_delete");
	$force_return = $products_to_delete["search_return"];

	if (is_array($products_to_delete["products"])) {

		$location[] = array(func_get_langvar_by_name("lbl_products_management"), "search.php");
		$location[] = array(func_get_langvar_by_name("lbl_delete_products"), "");
		$smarty->assign("location", $location);

		foreach ($products_to_delete["products"] as $k=>$v) {
			$condition[] = "productid='".addslashes($k)."'";
		}

		$search_condition = implode(" OR ", $condition);

		$products = func_query("SELECT productid, productcode, product, provider FROM $sql_tbl[products] WHERE $search_condition ORDER BY product, productcode");
		if (is_array($products)) {
			foreach ($products as $k=>$v) {
				$products[$k]["price"] = func_query_first_cell("SELECT MIN(price) FROM $sql_tbl[pricing] WHERE productid='$v[productid]' AND quantity='1' AND membershipid = 0 AND $sql_tbl[pricing].variantid = 0");
				$products[$k]["category"] = func_query_first_cell("SELECT $sql_tbl[categories].category FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[products_categories].productid = '$v[productid]' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[products_categories].main = 'Y'");
			}

			$smarty->assign("products", $products);

			if (!empty($products_to_delete["navpage"]))
				$smarty->assign("navpage", $products_to_delete["navpage"]);

			if (!empty($products_to_delete["section"])) {
				$smarty->assign("section", $products_to_delete["section"]);
				$smarty->assign("cat", $products_to_delete["cat"]);
			}

			$smarty->assign("search_return", $products_to_delete["search_return"]);

			$smarty->assign("main","product_delete_confirmation");

			@include $xcart_dir."/modules/gold_display.php";
			if ($current_area == "A")
				func_display("admin/home.tpl",$smarty);
			else
				func_display("provider/home.tpl",$smarty);

			exit;
		}
	}

	$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_products_del");
	$top_message["type"] = "W";
}

if (!empty($force_return)) {
	func_header_location($force_return);
}
elseif ($mode == 'clone' || $mode == "details") {
	func_header_location($HTTP_REFERER);
}

func_header_location("search.php?mode=search".(intval($navpage)>1 ? "&page=$navpage" : ""));

?>
