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
# $Id: import_products.php,v 1.26.2.12 2006/08/08 10:55:23 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('category','product');

/******************************************************************************
Used cache format:
Products (by Product ID):
	data_type: 	PI
	key:		<Product ID>
	value:		[<Product code> | RESERVED]
Products (by Product code):
	data_type: 	PR
	key:		<Product code>
	value:		[<Product ID> | RESERVED]
Products (by Product name):
	data_type:  PN
	key:		<Product name>
	value:		[<Product ID> | RESERVED]
Memberships:
	data_type: 	M
	key:		<Membership name>
	value:		<Membership ID>
Taxes:
	data_type:	T
	key:		<Tax service name>
	value:		[<Tax ID> | RESERVED]
Category for counting the number of products in categories:
	data_type:  Pr
	key:        <Category ID>
	value:      <Category ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == "define") {

	$provider_condition = ($single_mode ? "" : " AND provider='".$import_data_provider."'");

#
# Make default definitions (only on first inclusion!)
#
	$import_specification["PRODUCTS"] = array(
		"script" 		=> "/include/import_products.php",
		"tpls" 			=> array(
			"main/import_option_default_category.tpl",
			"main/import_option_category_path_sep.tpl",
			"main/import_option_images_directory.tpl"),
		"export_tpls" 			=> array(
			"main/export_option_export_images.tpl",
			"main/export_option_category_path_sep.tpl"),
		"is_range" 		=> "search.php?is_range",
		"export_sql" 	=> "SELECT productid FROM $sql_tbl[products]",
		"table"			=> "products",
		"key_field"		=> "productid",
		"permissions" 	=> "AP", # Admin and provider can import products
		"need_provider" => 1,
		"orderby"		=> 10,
		"finalize"		=> true,
		"columns"		=> array(
			"productid" => array( # Integer: productid
				"is_key"	=> true,
				"required"	=> 0,  # Required field
				"inherit"	=> 0,  # Can inherit value
				"type"		=> "N",
				"default"	=> 0), # Default value
			"productcode" 	=> array( # String: product code (SKU)
				"maxlength"	=> 32,
				"is_key"	=> true),
			"product" 		=> array( # String: product name
				"is_key"	=> true),
			"weight"		=> array( # Decimal: product weight
				"type"		=> "N",
				"default"	=> 0.00),
			"list_price"	=> array( # Decimal: product market price
				"type"		=> "N",
				"default"	=> 0.00),
			"descr"			=> array(	# Text: short description
				"eol_safe"	=> true),
			"fulldescr"		=> array(	# Text: detailed description
				"eol_safe"	=> true),
			"keywords"		=> array(), # String: product search keywords
			"avail"			=> array( # Integer: quantity of products
				"type"		=> "N",
				"default"	=> 0),
			"rating"		=> array( # Integer: product rating (0-5)
				"type"		=> "N",
				"default"	=> 0),
			"forsale"		=> array( # Char: product availability (Y,N,B)
				"type"		=> "E",
				"variants"	=> array("Y","N","B"),
				"default"	=> "N"),
			"shipping_freight"	=> array( # Decimal: shipping freight
				"type"		=> "N",
				"default"	=> 0.00),
			"free_shipping"	=> array( # Char: free shipping flag (Y,N)
				"type"		=> "B",
				"default"	=> "N"),
			"discount_avail"=> array( # Char: apply global discounts
				"type"		=> "B",
				"default"	=> "N"),
			"min_amount"	=> array( # Integer: minimum qty for ordering
				"type"		=> "N",
				"default"	=> 0),
			"dim_x"			=> array( # Integer: product length
				"type"		=> "N",
				"default"	=> 0),
			"dim_y"			=> array( # Integer: product width
				"type"		=> "N",
				"default"	=> 0),
			"dim_z"			=> array( # Integer: product height
				"type"		=> "N",
				"default"	=> 0),
			"low_avail_limit"	=> array( # Integer: qty warning
				"type"		=> "N",
				"default"	=> 0),
			"free_tax"		=> array( # Char: tax exempt flag (Y,N)
				"type"		=> "B",
				"default"	=> "N"),
			"categoryid"	=> array( # Integer: product categoryid
				"array"		=> true,
				"type"		=> "N",
				"default"	=> 0),
			"category"		=> array( # String: product category (Example: Books///Categories)
				"array"		=> true),
			"membership"	=> array( # String: product membership
				"array"		=> true),
			"price"			=> array( # Decimal: product price
				"type"		=> "P",
				"default"	=> 0.00),
			"thumbnail"		=> array( # String: thumbnail image file
				"type"		=> "I",
				"itype"		=> "T"),
			"image"			=> array( # String: product image file
				"type"		=> "I",
				"itype"		=> "P"),
			"taxes"			=> array( # String: applied tax names
				"array"		=> true),
			"add_date"		=> array(
				"type"		=> "D",
				"default"	=> "now"),
			"views_stats"	=> array(
				"type"		=> "N"),
			"sales_stats"	=> array(
				"type"		=> "N"),
			"del_stats"		=> array(
				"type"		=> "N")
		)
	);

	if (!empty($active_modules['Egoods'])) {
		$import_specification["PRODUCTS"]["columns"]["distribution"] = array();
	}

	if (!empty($active_modules['RMA'])) {
		$import_specification["PRODUCTS"]["columns"]["return_time"] = array(
			"type" => "N"
		);
	}

	$categories_in_db = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories]");
	if (empty($categories_in_db)) {
		$import_specification["PRODUCTS"]["columns"]["category"]['required'] = true;
	}

} elseif ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	if (isset($values["productid"]) && intval(@$values["productid"]) <= 0)
		$values["productid"] = "";

	if (!empty($values['productid'])) {
		$tmp = func_import_get_cache("PI", $values["productid"]);
		if (is_null($tmp))
			func_import_save_cache("PI", $values["productid"], "");
	}
	if (!empty($values['productcode'])) {
		$tmp = func_import_get_cache("PR", $values["productcode"]);
		if (is_null($tmp))
			func_import_save_cache("PR", $values["productcode"], "");
	}
	if (!empty($values['product'])) {
		$tmp = func_import_get_cache("PN", $values["product"]);
		if (is_null($tmp))
			func_import_save_cache("PN", $values["product"], "");
	}

	# Check categoryids
	if (!empty($values['categoryid'])) {
		foreach ($values['categoryid'] as $k => $v) {

			# Check - empty categoryid
			if (empty($v)) {
				unset($values['categoryid'][$k]);
				continue;
			}

			# Check - exists categoryid
			$cname = func_import_get_cache("CI", $v);
			if (is_null($cname)) {
				$cname = func_categoryid_path2category_path(func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid = '$v'"));
				if (empty($cname)) {
					$cname = NULL;
				} else {
					$cname = implode($import_file["category_sep"], $cname);
					func_import_save_cache("CI", $v, $cname);
					func_import_save_cache("C", $cname, $v);
				}
			}

			if (is_null($cname) || ($action == "do" && empty($cname))) {
				func_import_module_error("msg_err_import_log_message_11");
				unset($values['categoryid'][$k]);
				continue;
			}
		}
	}

	# Check category column
	if (!empty($values['category'])) {
		foreach($values['category'] as $k => $v) {

			# Check - empty categoryid
			$cid = func_import_get_cache("C", $v);
			if (is_null($cid)) {
				$subcats = explode($import_file["category_sep"], $v);
				if (empty($v) || empty($subcats)) {
					unset($values['category'][$k]);
					continue;
				}

				# Check category path
				$parentid = 0;
				foreach ($subcats as $cv) {
					$cid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE category = '".addslashes($cv)."' AND parentid = '$parentid'");

					# Category does not exists
					if (empty($cid)) {
						$cid = false;
						break;
					}
					$parentid = $cid;
				}

				if (empty($cid)) {
					$cid = NULL;
				} else {
					func_import_save_cache("C", $v, $cid);
					func_import_save_cache("CI", $cid, $v);
				}
			}

			if (is_null($cid) || ($action == "do" && empty($cid))) {
				func_import_module_error("msg_err_import_log_message_11");
				unset($values['category'][$k]);
				continue;
			} elseif (!empty($cid) && $action == "do") {
				if (is_array($values['categoryid'])) {
					if (!in_array($cid, $values['categoryid']))
						$values['categoryid'][] = $cid;
				} else {
					$values['categoryid'] = array($cid);
				}
			}
		}
	}

	if (empty($values['categoryid']) && empty($import_file['categoryid']))
		func_import_module_error("msg_err_import_log_message_44");

	# Check taxes
	if (!empty($values['taxes'])) {
		foreach ($values['taxes'] as $v) {
			if (empty($v))
				continue;
			$_taxid = func_import_get_cache("T", $v);
			if (is_null($_taxid)) {
				$_taxid = func_query_first_cell("SELECT taxid FROM $sql_tbl[taxes] WHERE tax_name = '".addslashes($v)."'");
				if (empty($_taxid)) {
					$_taxid = NULL;
				} else {
					func_import_save_cache("T", $v, $_taxid);
				}
			}
			if (is_null($_taxid) || ($action == "do" && empty($_taxid))) {
				func_import_module_error("msg_err_import_log_message_3");
			}
		}
	}

	$data_row[] = $values;

	# Save price id
	if (empty($import_file['PP_save_priceid'])) {
		$res = db_query("SELECT $sql_tbl[pricing].* FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[products].productid = $sql_tbl[pricing].productid".$provider_condition);
		if ($res) {
			while ($row = db_fetch_array($res)) {
				func_import_save_cache("PP", $row['productid']."_".$row['quantity']."_".$row['membershipid']."_".$row['variantid'], $row['priceid'], true);
			}
			db_free_result($res);
		}
		$import_file['PP_save_priceid'] = "Y";
	}

}
elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	#
	# Update the products...
	#
	if ($import_file["drop"]["products"] == "Y") {

		func_import_save_image("T");
		func_import_save_image("P");
		if ($provider_condition) {
			# Search for products created by provider...
			$products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 $provider_condition");
			if ($products_to_delete) {
				while ($value = db_fetch_array($products_to_delete))
					func_delete_product ($value["productid"],false);
			}
		}
		else {
		# Delete all products and related information...
			func_delete_product (0, false, true);
		}

		$import_file["drop"]["products"] = "";
	}

	if (isset($import_pass['products'])) {
		$last_productcode = $import_pass['products']['last_productcode'];
		$last_productcode_cnt = $import_pass['products']['last_productcode_cnt'];
	} else {
		$last_productcode = false;
		$last_productcode_cnt = 1;
	}

	foreach ($data_row as $product) {
	#
	# Import products data...
	#

		# Detect productid
		$_productid = false;
		$old_productid = $product['productid'];
		$old_productcode = $product['productcode'];
		if (isset($product['productid'])) {
			# Detect product by productid
			if (!empty($product['productid'])) {
				$_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE productid = '$product[productid]' ".$provider_condition);
				if (!$_productid && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productid = '$product[productid]'"))
					unset($product['productid']);
			}

		} elseif (isset($product['productcode'])) {
			# Detect product by product code
			if (!empty($product['productcode']))
				$_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE productcode = '".addslashes($product['productcode'])."' ".$provider_condition);

		} elseif (isset($product['product'])) {
			# Detect product by product name
			if (!empty($product['product']))
				$_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE product = '".addslashes($product['product'])."' ".$provider_condition);
		}

		# Check SKU (originality)
		if (!empty($product['productcode'])) {
			if (empty($_productid)) {
				if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '".addslashes($product['productcode'])."'") > 0)
					unset($product['productcode']);
			} else {
				if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '".addslashes($product['productcode'])."' AND productid != '$_productid'") > 0)
					unset($product['productcode']);
			}
		}

		$is_last_productcode = false;
		if (empty($product['productcode'])) {
			if (empty($_productid)) {
				# If SKU is empty and new product: get SKU from $last_productcode or as 'SKU' service-word
				if (!empty($last_productcode)) {
					$product['productcode'] = $last_productcode;
					$is_last_productcode = true;

				} else {
					$product['productcode'] = "SKU";
				}

			} elseif (isset($product['productcode'])) {
				# If SKU is empty and isset in imported data and product is old: SKU is defined as 'SKU' service word + productid
				$product['productcode'] = "SKU".$_productid;
			}
		}

		if (isset($product['productcode'])) {

			# Check SKU if SKU issed in imported data
			$check_productcode = $product['productcode'];
			while (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '".addslashes($check_productcode)."'".(empty($_productid) ? "" : " AND productid != '$_productid'")) > 0) {
				$check_productcode = $product['productcode'].$last_productcode_cnt++;
			}
			$product['productcode'] = $check_productcode;

			if (!$is_last_productcode) {
				# Redefine last SKU saved data if current SKU isn't generated based on last saved SKU
				$last_productcode = $product['productcode'];
				$last_productcode_cnt = 1;
			}
		}

		# Get field for direct import
		$cols = func_query_column("SHOW COLUMNS FROM ".$sql_tbl['products']);
		$exclude_cols = array(
			"provider",
			"manufacturerid"
		);
		foreach ($exclude_cols as $v) {
			$idx = array_search($v, $cols);
			if ($idx !== false) {
				unset($cols[$idx]);
			}
		}

		$data = array();
		foreach ($cols as $name) {
			if(isset($product[$name])) {
				$data[$name] = $product[$name];
			}
		}

		if (empty($data['add_date']))
			$data['add_date'] = time();

		# Direct import
		$is_new = ($_productid === false);
		if (!empty($data)) {
			$data = func_addslashes($data);
			if ($_productid === false) {
				$data['provider'] = $import_data_provider;
				$_productid = func_array2insert("products", $data);
			} else {
				func_array2update("products", $data, "productid = '$_productid'");
			}
		}
		if (empty($_productid))
			continue;

		if ($is_new) {
			$result["products"]["added"]++;
		} else {
			$result["products"]["updated"]++;
		}

		# Store $_productid in the cache
		if (!empty($old_productcode))
			func_import_save_cache("PR", $old_productcode, $_productid);
		if (!empty($product['product']))
			func_import_save_cache("PN", $product["product"], $_productid);
		if (!empty($old_productid))
			func_import_save_cache("PI", $old_productid, $_productid);

		# Import categories data
		if (empty($product['categoryid']) && $is_new)
			$product['categoryid'] = array($import_file['categoryid']);

		if (!empty($product['categoryid'])) {
			db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid = '$_productid'");
			$product['categoryid'] = array_unique($product['categoryid']);
			$is_main = false;
			foreach ($product['categoryid'] as $v) {
				if (empty($v))
					continue;

				func_array2insert("products_categories", array("productid" => $_productid, "categoryid" => $v, "main" => ($is_main ? "" : "Y")), true);
				$is_main = true;
			}
		}
		$ids = func_query_column("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid = '$_productid'");
		if (!empty($ids)) {
			foreach ($ids as $cid) {
				func_import_save_cache("Pr", $cid, $cid);
			}
		}
		func_import_save_cache("Prp", $_productid, $_productid);

		# Import price
		if (isset($product['price']) || $is_new) {
			$product['price'] = doubleval($product['price']);
			$priceid = func_query_first_cell("SELECT priceid FROM $sql_tbl[pricing] WHERE productid = '$_productid' AND quantity = 1 AND membershipid = 0 AND variantid = 0");
			if ($priceid) {
				func_array2update("pricing", array("price" => $product['price']), "priceid = '$priceid'");

			} else {
				$data = array(
					"productid"	=> $_productid,
					"quantity"	=> 1,
					"price"		=> $product['price'],
					"variantid"	=> 0
				);
				$priceid = func_import_get_cache("PP", $data['productid']."_1_0_0");
				if (!empty($priceid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pricing] WHERE priceid = '$priceid'") == 0) {
					$data['priceid'] = $priceid;
				}
				$priceid = func_array2insert("pricing", $data);
			}
		}

		# Import thumbnails
		if (!empty($product['thumbnail'])) {
			func_import_save_image_data("T", $_productid, $product['thumbnail']);
		}

		# Import product image
		if (!empty($product['image'])) {
			func_import_save_image_data("P", $_productid, $product['image']);
		}

		# Import taxes
		if (!empty($product['taxes'])) {
			foreach ($product['taxes'] as $v) {
				if (empty($v))
					continue;
				$_taxid = func_import_get_cache("T", $v);
				if (!empty($_taxid)) {
					if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_taxes] WHERE productid = '$_productid' AND taxid = '$_taxid'")) {
						func_array2insert("product_taxes", array("productid" => $_productid, "taxid" => $_taxid));
					}
				}

			}
		}

		# Import extra fields
		if (!empty($import_specification["PRODUCTS"]["extra_columns"])) {
			foreach ($import_specification["PRODUCTS"]["extra_columns"] as $c) {
				if (!empty($product[$c])) {
					$efid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE field = '".addslashes($c)."'");
					if (!empty($efid)) {
						func_array2insert("extra_field_values", array("productid" => $_productid, "fieldid" => $efid, "value" => addslashes($product[$c])), true);
					}
				}
			}
		}

		# Import manufacturer
		$_mid = false;
		if (!empty($product['manufacturer'])) {
			$mid = func_query_first_cell("SELECT manufacturerid FROM $sql_tbl[manufacturers] WHERE manufacturer = '".addslashes($product['manufacturer'])."'");
			if (empty($mid)) {
				$mid = func_array2insert("manufacturers", array("manufacturer" => addslashes($product['manufacturer']), "provider" => $import_data_provider));
			}

		} elseif (!empty($product['manufacturerid'])) {
			$mid = func_query_first_cell("SELECT manufacturerid FROM $sql_tbl[manufacturers] WHERE manufacturerid = '$product[manufacturerid]'");
		}
		if (!empty($mid)) {
			func_array2update("products", array("manufacturerid" => $mid), "productid = '$_productid'");
		}

		func_flush(". ");

	}

	$import_pass['products'] = array(
		"last_productcode" => $last_productcode,
		"last_productcode_cnt" => $last_productcode_cnt
	);

# Post-import step
} elseif ($import_step == "complete") {

    $is_display_header = false;
    while (list($cid, $tmp) = func_import_read_cache("Pr")) {
        if (!$is_display_header) {
            $message = func_get_langvar_by_name("txt_products_counting_",NULL,false,true);
            func_import_add_to_log($message);
            func_flush("<br />\n".$message."<br />\n");
            $is_display_header = true;
        }
        func_recalc_product_count(func_get_category_parents($cid));

        func_flush(". ");
    }
    func_import_erase_cache("Pr");

    while (list($pid, $tmp) = func_import_read_cache("Prp")) {
        if (!$is_display_header) {
            $message = func_get_langvar_by_name("txt_products_counting_",NULL,false,true);
            func_import_add_to_log($message);
            echo "<br />".$message."<br />";
            func_flush();
            $is_display_header = true;
        }
		func_import_rebuild_product($pid);

        echo ". ";
        func_flush();
    }
    func_import_erase_cache("Prp");

# Export data
} elseif ($import_step == "export") {

	while ($productid = func_export_get_row($data)) {
		if (empty($productid))
			continue;

		$row = func_query_first("SELECT $sql_tbl[products].*, MIN($sql_tbl[pricing].price) as price FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[products].productid = '$productid' AND $sql_tbl[products].productid = $sql_tbl[pricing].productid AND $sql_tbl[pricing].quantity = 1 AND $sql_tbl[pricing].membershipid = 0 AND $sql_tbl[pricing].variantid = 0 ".(empty($provider_sql) ? "" : "AND $sql_tbl[products].provider = '$provider_sql' ")."GROUP BY $sql_tbl[products].productid");
		if (empty($row))
			continue;

		# Export categories
		$cats = func_query("SELECT $sql_tbl[categories].categoryid, $sql_tbl[categories].categoryid_path FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[products_categories].productid = '$productid' ORDER BY $sql_tbl[products_categories].main DESC, $sql_tbl[products_categories].orderby");
		if (!empty($cats)) {
			foreach ($cats as $v) {
				$row['categoryid'][] = $v['categoryid'];
				$tmp = func_categoryid_path2category_path($v['categoryid_path']);
				$row['category'][] = (empty($tmp) ? "" : implode($export_data['options']['category_sep'], $tmp));
			}
		}
		unset($cats);

		# Export memberships
		$mems = func_query_column("SELECT $sql_tbl[memberships].membership FROM $sql_tbl[memberships], $sql_tbl[product_memberships] WHERE $sql_tbl[memberships].membershipid = $sql_tbl[product_memberships].membershipid AND $sql_tbl[product_memberships].productid = '$productid'");
		if (!empty($mems)) {
			foreach ($mems as $v) {
				$row['membership'][] = $v;
			}
		}

		# Export manufacturer
		if (!empty($row['manufacturerid'])) {
			$manufacturer = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers] WHERE manufacturerid = '$row[manufacturerid]'");
			if (!empty($manufacturer))
				$row['manufacturer'] = $manufacturer;
		}

		# Export taxes
		$taxes = func_query_column("SELECT $sql_tbl[taxes].tax_name FROM $sql_tbl[product_taxes], $sql_tbl[taxes] WHERE $sql_tbl[taxes].taxid = $sql_tbl[product_taxes].taxid AND $sql_tbl[product_taxes].productid = '$productid'");
		if (!empty($taxes)) {
			foreach ($taxes as $v) {
				$row['taxes'][] = $v;
			}
		}

		# Export thumbnails
		$row["thumbnail"] = $productid;

		# Export product image
		$row["image"] = $productid;

		if (!func_export_write_row($row))
			break;
	}

}

?>
