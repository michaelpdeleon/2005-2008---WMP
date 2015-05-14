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
# $Id: func.product.php,v 1.32.2.12 2006/08/02 05:39:12 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Delete product from products table + all associated information
# $productid - product's id
#
function func_delete_product($productid, $update_categories=true, $delete_all=false) {
	global $sql_tbl, $xcart_dir, $smarty;

	x_load('backoffice','category', 'image');

	if ($delete_all === true) {
		db_query("DELETE FROM $sql_tbl[pricing]");
		db_query("DELETE FROM $sql_tbl[product_links]");
		db_query("DELETE FROM $sql_tbl[featured_products]");
		db_query("DELETE FROM $sql_tbl[products]");
		db_query("DELETE FROM $sql_tbl[delivery]");
		db_query("DELETE FROM $sql_tbl[extra_field_values]");
		db_query("DELETE FROM $sql_tbl[products_categories]");
		db_query("DELETE FROM $sql_tbl[product_taxes]");
		db_query("DELETE FROM $sql_tbl[product_votes]");
		db_query("DELETE FROM $sql_tbl[product_reviews]");
		db_query("DELETE FROM $sql_tbl[products_lng]");
		db_query("DELETE FROM $sql_tbl[subscriptions]");
		db_query("DELETE FROM $sql_tbl[subscription_customers]");
		db_query("DELETE FROM $sql_tbl[download_keys]");
		db_query("DELETE FROM $sql_tbl[discount_coupons]");
		db_query("DELETE FROM $sql_tbl[stats_customers_products]");
		db_query("DELETE FROM $sql_tbl[wishlist]");
		db_query("DELETE FROM $sql_tbl[product_bookmarks]");
		db_query("DELETE FROM $sql_tbl[product_memberships]");
		func_delete_images("T");
		func_delete_images("P");
		func_delete_images("D");

		# Feature comparison module
		if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Feature_Comparison'")) {
			if (!isset($sql_tbl['product_features']) || !isset($sql_tbl['product_foptions'])) {
				include_once $xcart_dir."/modules/Feature_Comparison/config.php";
			}

			db_query("DELETE FROM $sql_tbl[product_features]");
			db_query("DELETE FROM $sql_tbl[product_foptions]");
		}

		# Product options module
		if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Product_Options'")) {
			if (!isset($sql_tbl['classes']) || !isset($sql_tbl['class_options'])) {
				include_once $xcart_dir."/modules/Product_Options/config.php";
			}

			db_query("DELETE FROM $sql_tbl[classes]");
			db_query("DELETE FROM $sql_tbl[class_options]");
			db_query("DELETE FROM $sql_tbl[product_options_lng]");
			db_query("DELETE FROM $sql_tbl[product_options_ex]");
			db_query("DELETE FROM $sql_tbl[product_options_js]");
			db_query("DELETE FROM $sql_tbl[variant_items]");
			db_query("DELETE FROM $sql_tbl[variants]");
			func_delete_images("W");
		}

		# Product configurator module
		if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Product_Configurator'")) {
			if (!isset($sql_tbl['pconf_products_classes'])) {
				include_once $xcart_dir."/modules/Product_Configurator/config.php";
			}

			db_query("DELETE FROM $sql_tbl[pconf_products_classes]");
			db_query("DELETE FROM $sql_tbl[pconf_class_specifications]");
			db_query("DELETE FROM $sql_tbl[pconf_class_requirements]");
			db_query("DELETE FROM $sql_tbl[pconf_wizards]");
			db_query("DELETE FROM $sql_tbl[pconf_slots]");
			db_query("DELETE FROM $sql_tbl[pconf_slot_rules]");
			db_query("DELETE FROM $sql_tbl[pconf_slot_markups]");
		}

		# Magnifier module
		if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Magnifier'")) {
			if (!isset($sql_tbl['images_Z'])) {
				include_once $xcart_dir."/modules/Magnifier/config.php";
			}

			db_query("DELETE FROM $sql_tbl[images_Z]");
			$dir_z = func_image_dir("Z");
			if (is_dir($dir_z) && file_exists($dir_z))
				func_rm_dir($dir_z);
		}

		if ($update_categories) {
			$res = db_query("SELECT categoryid FROM $sql_tbl[categories]");
			func_recalc_product_count($res);
		}

		func_data_cache_get("fc_count", array("Y"), true);
		func_data_cache_get("fc_count", array("N"), true);

		db_query("DELETE FROM $sql_tbl[quick_flags]");
		db_query("DELETE FROM $sql_tbl[quick_prices]");

		return true;
	}

	$product_categories = func_query_column("SELECT $sql_tbl[categories].categoryid_path FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[products_categories].productid='$productid'");

	db_query("DELETE FROM $sql_tbl[pricing] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1='$productid' OR productid2='$productid'");
	db_query("DELETE FROM $sql_tbl[featured_products] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[products] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[delivery] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[extra_field_values] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_memberships] WHERE productid='$productid'");
	func_delete_image($productid, "T");
	func_delete_image($productid, "P");
	func_delete_image($productid, "D");

	# Feature comparison module
	if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Feature_Comparison'")) {
		if (!isset($sql_tbl['product_features']) || !isset($sql_tbl['product_foptions'])) {
			include_once $xcart_dir."/modules/Feature_Comparison/config.php";
		}

		db_query("DELETE FROM $sql_tbl[product_features] WHERE productid='$productid'");
		db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid='$productid'");
	}

	# Product options module
	if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Product_Options'")) {
		if (!isset($sql_tbl['classes']) || !isset($sql_tbl['class_options'])) {
			include_once $xcart_dir."/modules/Product_Options/config.php";
		}

		$classes = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE productid='$productid'");
		db_query("DELETE FROM $sql_tbl[classes] where productid='$productid'");
		if (!empty($classes)) {
			$options = func_query_column("SELECT optionid FROM $sql_tbl[class_options] where classid IN ('".implode("','", $classes)."')");
			db_query("DELETE FROM $sql_tbl[class_lng] where classid IN ('".implode("','", $classes)."')");
			if (!empty($options)) {
				db_query("DELETE FROM $sql_tbl[class_options] where classid IN ('".implode("','", $classes)."')");
				db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $options)."')");
				db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $options)."')");
				db_query("DELETE FROM $sql_tbl[variant_items] WHERE optionid IN ('".implode("','", $options)."')");
			}
		}

		db_query("DELETE FROM $sql_tbl[product_options_js] WHERE productid='$productid'");
		$vids = db_query("SELECT variantid FROM $sql_tbl[variants] WHERE productid='$productid'");
		if ($vids) {
			while ($row = db_fetch_array($vids)) {
				func_delete_image($row['variantid'], "W");
			}
			db_free_result($vids);
		}
		db_query("DELETE FROM $sql_tbl[variants] WHERE productid='$productid'");
	}

	# Magnifier module
	if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Magnifier'")) {
		if (!isset($sql_tbl['images_Z'])) {
			include_once $xcart_dir."/modules/Magnifier/config.php";
		}

		db_query("DELETE FROM $sql_tbl[images_Z] WHERE id = '$productid'");
		$dir_z = func_image_dir("Z").DIRECTORY_SEPARATOR.$productid;
		if (is_dir($dir_z) && file_exists($dir_z))
			func_rm_dir($dir_z);
	}

	db_query("DELETE FROM $sql_tbl[product_taxes] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_votes] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_reviews] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[subscriptions] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[download_keys] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[discount_coupons] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[stats_customers_products] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[wishlist] WHERE productid='$productid'");
	db_query("DELETE FROM $sql_tbl[product_bookmarks] WHERE productid='$productid'");

	# Product configurator module
	if (func_query_first_cell("SELECT module_name FROM $sql_tbl[modules] WHERE module_name='Product_Configurator'")) {
		#
		# If Product Configurator installed delete the related information
		#
		include_once $xcart_dir."/modules/Product_Configurator/config.php";

		$classes = func_query_column("SELECT classid FROM $sql_tbl[pconf_products_classes] WHERE productid='$productid'");
		if (!empty($classes)) {
			
			#
			# Delete all classification info related with this product
			#
			db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid IN ('".implode("','", $classes)."')");
			db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid IN ('".implode("','", $classes)."')");
		}

		db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE productid='$productid'");

		#
		# Delete configurable product
		#
		$steps = func_query_column("SELECT stepid FROM $sql_tbl[pconf_wizards] WHERE productid='$productid'");
		if (!empty($steps)) {

			#
			# Delete the data related with wizards' steps
			#
			$slots = func_query("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $steps)."')");
			if (!empty($slots)) {

				#
				# Delete data related with slots
				#
				db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $steps)."')");
				db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid IN ('".implode("','", $slots)."')");
				db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid IN ('".implode("','", $slots)."')");
			}
		}

		db_query("DELETE FROM $sql_tbl[pconf_wizards] WHERE productid='$productid'");
	}

	#
	# Update product count for categories
	#
	if ($update_categories && !empty($product_categories)) {
		$cats = array();
		foreach ($product_categories as $c) {
			$cats = array_merge($cats, explode("/", $c));
		}
		$cats = array_unique($cats);
		func_recalc_product_count($cats);
	}

	func_data_cache_get("fc_count", array("Y"), true);
	func_data_cache_get("fc_count", array("N"), true);

	db_query("DELETE FROM $sql_tbl[quick_flags] WHERE productid = '$productid'");
	db_query("DELETE FROM $sql_tbl[quick_prices] WHERE productid = '$productid'");

	return true;
}

#
# Search for products in products database
#
function func_search_products($query, $membershipid, $orderby="", $limit="") {
	global $current_area, $user_account, $active_modules, $xcart_dir, $current_location, $single_mode;
	global $store_language, $sql_tbl;
	global $config;
	global $cart, $login;
	global $active_modules;
	static $orderby_rules = NULL;

	x_load('files','taxes');

	if (is_null($orderby_rules)) {
		$orderby_rules = array (
			"title" => "product",
			"quantity" => "$sql_tbl[products].avail",
			"orderby" => "$sql_tbl[products_categories].orderby",
			"quantity" => "$sql_tbl[products].avail",
			"price" => "price",
			"productcode" => "$sql_tbl[products].productcode");
	}

	#
	# Generate ORDER BY rule
	#
	if (empty($orderby)) {
		$orderby = ($config["Appearance"]["products_order"] ? $config["Appearance"]["products_order"] : "orderby");
		if (!empty($orderby_rules))
			$orderby = $orderby_rules[$orderby];
	}

	#
	# Initialize service arrays
	#
	$fields = array();
	$from_tbls = array();
	$inner_joins = array();
	$left_joins = array();
	$where = array();
	$groupbys = array();
	$orderbys = array();

	#
	# Generate membershipid condition
	#
	$membershipid_condition = "";
	if ($current_area == "C") {
		$where[] = "($sql_tbl[category_memberships].membershipid = '$membershipid' OR $sql_tbl[category_memberships].membershipid IS NULL)";
		$where[] = "$sql_tbl[products].forsale='Y'";
		$where[] = "($sql_tbl[product_memberships].membershipid = '$membershipid' OR $sql_tbl[product_memberships].membershipid IS NULL)";
	}

	#
	# Generate products availability condition
	#
	if ($config["General"]["unlimited_products"]=="N" && (($current_area == "C" || $current_area == "B") && $config["General"]["disable_outofstock_products"] == "Y"))
		$where[] = "$sql_tbl[products].avail > 0";

	$from_tbls[] = "pricing";
	$inner_joins = array(
		"products_categories" => array(
			"on" => "$sql_tbl[products_categories].productid = $sql_tbl[products].productid",
		),
		"categories" => array(
			"on" => "$sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[categories].avail = 'Y'",
		)
	);
	$left_joins = array();

	$fields[] = "$sql_tbl[products].productid";
	if ($current_area == "C") {
		$left_joins["products_lng"] = array(
			"on" => "$sql_tbl[products].productid = $sql_tbl[products_lng].productid AND code = '$store_language'"
		);
		$fields[] = "IF($sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product";

	} else {
		$fields[] = "$sql_tbl[products].product";
	}

	$fields[] = "$sql_tbl[products].productcode";
	$fields[] = "$sql_tbl[products].avail";
	$fields[] = "MIN($sql_tbl[pricing].price) as price";

	if ($current_area == "C" && !$single_mode) {
		$inner_joins["ACHECK"] = array(
			"tblname" => "customers",
			"on" => "$sql_tbl[products].provider=ACHECK.login AND ACHECK.activity='Y'",
		);
	}

	$left_joins['category_memberships'] = array(
		"on" => "$sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid",
		"parent" => "categories"
	);
	$left_joins['product_memberships'] = array(
		"on" => "$sql_tbl[product_memberships].productid = $sql_tbl[products].productid"
	);

	$where[] = "$sql_tbl[products].productid = $sql_tbl[products_categories].productid";
	$where[] = "$sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid";
	$where[] = "$sql_tbl[products].productid = $sql_tbl[pricing].productid";
	$where[] = "$sql_tbl[pricing].quantity = '1'";
	if (empty($membershipid)) {
		$where[] = "$sql_tbl[pricing].membershipid = 0";
	} else {
		$where[] = "$sql_tbl[pricing].membershipid IN ('$membershipid', 0)";
	}

	if ($current_area == 'C' && empty($active_modules['Product_Configurator'])) {
		$where[] = "$sql_tbl[products].product_type <> 'C'";
		$where[] = "$sql_tbl[products].forsale <> 'B'";
	}

	if ($current_area == 'C' && !empty($active_modules['Product_Options'])) {
		$where[] = "($sql_tbl[pricing].variantid = 0 OR ($sql_tbl[variants].variantid = $sql_tbl[pricing].variantid".(($config["General"]["disable_outofstock_products"] == "Y" && $config["General"]["unlimited_products"] != "Y")?" AND $sql_tbl[variants].avail > 0":"")."))";
	}
	else {
		$where[] = "$sql_tbl[pricing].variantid = '0'";
	}

	$groupbys[] = "$sql_tbl[products].productid";
	$orderbys[] = $orderby;

	#
	# Check if product have prodyct class (Feature comparison)
	#
	if (!empty($active_modules['Feature_Comparison']) && $current_area == "C") {
		global $comparison_list_ids;

		$left_joins['product_features'] = array(
			"on" => "$sql_tbl[product_features].productid = $sql_tbl[products].productid"
		);
		$fields[] = "$sql_tbl[product_features].fclassid";
		if (($config['Feature_Comparison']['fcomparison_show_product_list'] == 'Y') && $config['Feature_Comparison']['fcomparison_max_product_list'] > @count((array)$comparison_list_ids)) {
			$fields[] = "IF($sql_tbl[product_features].fclassid IS NULL || $sql_tbl[product_features].productid IN ('".@implode("','",@array_keys((array)$comparison_list_ids))."'),'','Y') as is_clist";
		}
	}

	#
	# Check if product have product options (Product options)
	#
	if (!empty($active_modules['Product_Options'])) {
		$left_joins['classes'] = array(
			"on" => "$sql_tbl[classes].productid = $sql_tbl[products].productid"
		);
		$left_joins['variants'] = array(
			"on" => "$sql_tbl[variants].productid = $sql_tbl[products].productid"
		);
		$fields[] = "IF($sql_tbl[classes].classid IS NULL,'','Y') as is_product_options";
		$fields[] = "IF($sql_tbl[variants].variantid IS NULL,'','Y') as is_variant";
	}

	if ($config["Images"]["thumbnails_location"] == "FS") {
		$left_joins['images_T'] = array(
			"on" => "$sql_tbl[images_T].id = $sql_tbl[products].productid"
		);
		$fields[] = "IF($sql_tbl[images_T].id IS NULL, '', 'Y') as is_thumbnail";
		$fields[] = "$sql_tbl[images_T].image_path";
	}

	if ($current_area == "C") {
		$left_joins['product_taxes'] = array(
			"on" => "$sql_tbl[product_taxes].productid = $sql_tbl[products].productid"
		);
		$fields[] = "$sql_tbl[product_taxes].taxid";
	}

	#
	# Generate search query
	#
	foreach ($inner_joins as $j) {
		if (!empty($j['fields']) && is_array($j['fields']))
			$fields = func_array_merge($fields, $j['fields']);
	}
	foreach ($left_joins as $j) {
		if (!empty($j['fields']) && is_array($j['fields']))
			$fields = func_array_merge($fields, $j['fields']);
	}

	$search_query = "SELECT ".implode(", ", $fields)." FROM ";
	if (!empty($from_tbls)) {
		foreach ($from_tbls as $k => $v) {
			$from_tbls[$k] = $sql_tbl[$v];
		}
		$search_query .= implode(", ", $from_tbls).", ";
	}
	$search_query .= $sql_tbl['products'];

	foreach ($left_joins as $ljname => $lj) {
		if (!empty($lj['parent']))
			continue;
		$search_query .= " LEFT JOIN ";
		if (!empty($lj['tblname'])) {
			$search_query .= $sql_tbl[$lj['tblname']]." as ".$ljname;
		} else {
			$search_query .= $sql_tbl[$ljname];
		}
		$search_query .= " ON ".$lj['on'];
	}

	foreach ($inner_joins as $ijname => $ij) {
		$search_query .= " RIGHT JOIN ";
		if (!empty($ij['tblname'])) {
			$search_query .= $sql_tbl[$ij['tblname']]." as ".$ijname;
		} else {
			$search_query .= $sql_tbl[$ijname];
		}
		$search_query .= " ON ".$ij['on'];
		foreach ($left_joins as $ljname => $lj) {
			if ($lj['parent'] != $ijname)
				continue;
			$search_query .= " LEFT JOIN ";
			if (!empty($lj['tblname'])) {
				$search_query .= $sql_tbl[$lj['tblname']]." as ".$ljname;
			} else {
				$search_query .= $sql_tbl[$ljname];
			}
			$search_query .= " ON ".$lj['on'];
		}
	}

	$search_query .= " WHERE ".implode(" AND ", $where).$query;
	if (!empty($groupbys))
		$search_query .= " GROUP BY ".implode(", ", $groupbys);
	if (!empty($orderbys))
		$search_query .= " ORDER BY ".implode(", ", $orderbys);
	if (!empty($limit))
		$search_query .= " LIMIT ".$limit;

	db_query("SET OPTION SQL_BIG_SELECTS=1");

	$result = func_query($search_query);

	$ids = array();
	if (!empty($result)) {
		foreach($result as $v) {
			$ids[] = $v['productid'];
		}
	}

	if ($result && ($current_area=="C" || $current_area=="B") ) {
		#
		# Post-process the result products array
		#

		if (!empty($active_modules['Extra_Fields'])) {
			$tmp = func_query("SELECT *, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid IN ('".implode("','", $ids)."') AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby");
			$products_ef = array();
			if (!empty($tmp) && is_array($tmp)) {
				foreach($tmp as $v) {
					$products_ef[$v['productid']][] = $v;
				}
			}
		}

		if (!empty($active_modules['Product_Options']) && !empty($ids)) {
			$avail_where = "";
			if ($config["General"]["disable_outofstock_products"] == "Y") {
				$avail_where = ($config["General"]["unlimited_products"] =="N")? " AND $sql_tbl[variants].avail>0 " : "";
			}

			$variant_def = func_query_hash("SELECT $sql_tbl[variants].*, MIN($sql_tbl[pricing].price) as price FROM $sql_tbl[pricing], $sql_tbl[variants] WHERE $sql_tbl[pricing].variantid = $sql_tbl[variants].variantid AND $sql_tbl[variants].productid IN ('".implode("','", $ids)."') AND $sql_tbl[variants].def = 'Y' AND $sql_tbl[pricing].quantity=1 AND $sql_tbl[pricing].membershipid IN ('$membershipid', 0) $avail_where GROUP BY $sql_tbl[variants].productid", "productid", false);

			$tmp = func_query("SELECT $sql_tbl[classes].productid, $sql_tbl[class_options].* FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].productid IN ('".implode("','", $ids)."') AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].is_modifier = 'Y' AND $sql_tbl[class_options].avail = 'Y' ORDER BY $sql_tbl[class_options].price_modifier");
			$modi_def = array();
			if (!empty($tmp) && is_array($tmp)) {
				foreach ($tmp as $v) {
					$modi_def[$v['productid']][$v['classid']][$v['optionid']] = array("price_modifier" => $v['price_modifier'], "modifier_type" => $v['modifier_type']);
				}
			}
		}

		foreach ($result as $key => $value) {

			if (!empty($active_modules['Product_Options']) && isset($variant_def[$value['productid']])) {
				$result[$key] = func_array_merge($value, $variant_def[$value['productid']]);
				if (isset($modi_def[$value['productid']])) {
					$modi_price = $result[$key]['price'];
					foreach ($modi_def[$value['productid']] as $vc) {
						$best_price = false;
						foreach ($vc as $vo) {
							if ($vo['modifier_type'] == '%')
								$tmp = $result[$key]['price']/100*$vo['price_modifier'];
							else
								$tmp = $vo['price_modifier'];

							if ($best_price === false || ($best_price > $tmp && $best_price !== false)) {
								$best_price = $tmp;
							}
						}

						$modi_price += $best_price;
					}

					$result[$key]['price'] = $modi_price;
				}

				$result[$key]['taxed_price'] = $result[$key]['price'];
				$value = $result[$key];
			}

			if (!empty($cart) && !empty($cart["products"]) && $current_area=="C") {
				#
				# Update quantity for products that already placed into the cart
				#
				$in_cart = 0;
				foreach ($cart["products"] as $cart_item) {
					if ($cart_item["productid"] == $value["productid"] && $cart_item["variantid"] == $variant_def[$value["productid"]]['variantid'])
						$in_cart += $cart_item["amount"];
				}
				$result[$key]["avail"] -= $in_cart;
			}

			if (!empty($active_modules['Extra_Fields'])) {
				if (isset($products_ef[$v['productid']])) {
					$result[$key]['extra_fields'] = $products_ef[$v['productid']];
				}
			}

			#
			# Get thumbnail's URL (uses only if images stored in FS)
			#
			$value['is_thumbnail'] = ($value['is_thumbnail'] == 'Y');
			if ($value['is_thumbnail'] && !empty($value['image_path']))
				$result[$key]["tmbn_url"] = func_get_image_url($value['productid'], 'T', $value['image_path']);

			unset($result[$key]['image_path']);

			if ($current_area == "C" && $value['taxid'] > 0) {
				$result[$key]["taxes"] = func_get_product_taxes($result[$key], $login);
			}
		}
	}

	return $result;
}

#
# Put all product info into $product array
#
function func_select_product($id, $membershipid, $redirect_if_error=true, $clear_price=false, $always_select=false) {
	global $login, $login_type, $current_area, $single_mode, $cart, $current_location;
	global $store_language, $sql_tbl, $config, $active_modules;

	x_load('files','taxes');

	$in_cart = 0;

	$membershipid = intval($membershipid);
	$p_membershipid_condition = $membershipid_condition = "";
	if ($current_area == "C") {
		$membershipid_condition = " AND ($sql_tbl[category_memberships].membershipid = '$membershipid' OR $sql_tbl[category_memberships].membershipid IS NULL) ";
		$p_membershipid_condition = " AND ($sql_tbl[product_memberships].membershipid = '$membershipid' OR $sql_tbl[product_memberships].membershipid IS NULL) ";
	}

	if ($current_area == "C" && !empty($cart) && !empty($cart["products"])) {
		foreach ($cart["products"] as $cart_item) {
			if ($cart_item["productid"] == $id) {
				$in_cart += $cart_item["amount"];
			}
		}
	}

	$login_condition = "";
	if (!$single_mode) {
		$login_condition = (($login != "" && $login_type == "P") ? "AND $sql_tbl[products].provider='$login'" : "");
	}

	$add_fields = "";
	$join = "";

	if (!empty($active_modules['Product_Options']) && $current_area != "C" && $current_area != "B") {
		$join .= " LEFT JOIN $sql_tbl[variants] ON $sql_tbl[products].productid = $sql_tbl[variants].productid";
		$add_fields .= ", IF($sql_tbl[variants].productid IS NULL, '', 'Y') as is_variants";
	}

	if (!empty($active_modules['Feature_Comparison'])) {
		$join .= " LEFT JOIN $sql_tbl[product_features] ON $sql_tbl[product_features].productid = $sql_tbl[products].productid";
		$add_fields .= ", $sql_tbl[product_features].fclassid";
	}

	if (!empty($active_modules["Manufacturers"])) {
		$join .= " LEFT JOIN $sql_tbl[manufacturers] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[products].manufacturerid";
		$add_fields .= ", $sql_tbl[manufacturers].manufacturer";
	}

	if ($current_area == "C") {
		$add_fields .= ", IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product, IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) as descr, IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].fulldescr, $sql_tbl[products].fulldescr) as fulldescr, $sql_tbl[quick_flags].*, $sql_tbl[quick_prices].variantid";
		$join .= " LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products_lng].code='$store_language' AND $sql_tbl[products_lng].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[quick_flags] ON $sql_tbl[products].productid = $sql_tbl[quick_flags].productid LEFT JOIN $sql_tbl[quick_prices] ON $sql_tbl[products].productid = $sql_tbl[quick_prices].productid AND $sql_tbl[quick_prices].membershipid IN ('$membershipid', 0)";
	}

	$join .= " LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid";

	if ($current_area == 'C' && empty($active_modules['Product_Configurator'])) {
		$login_condition .= " AND $sql_tbl[products].product_type <> 'C' AND $sql_tbl[products].forsale <> 'B' ";
	}

	$product = func_query_first("SELECT $sql_tbl[products].*, $sql_tbl[products].avail-$in_cart AS avail, MIN($sql_tbl[pricing].price) as price $add_fields FROM $sql_tbl[pricing], $sql_tbl[products] $join WHERE $sql_tbl[products].productid='$id' ".$login_condition." AND $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[pricing].quantity=1 AND $sql_tbl[pricing].variantid = 0 $p_membershipid_condition AND $sql_tbl[pricing].membershipid IN ($membershipid, 0) GROUP BY $sql_tbl[products].productid");

	$categoryid = func_query_first_cell("SELECT $sql_tbl[products_categories].categoryid FROM $sql_tbl[products_categories] USE INDEX (cpm), $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid WHERE $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid $membershipid_condition AND $sql_tbl[products_categories].productid = '$id' ORDER BY main DESC LIMIT 1");

	# Check product's provider activity
	if (!$single_mode && $current_area == "C" && !empty($product)) {
		if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE login = '$product[provider]' AND activity='Y'"))
			$product = array();
	}

	#
	# Error handling
	#
	if (!$product || !$categoryid) {
		if ($redirect_if_error)
			func_header_location("error_message.php?access_denied&id=33");
		else
			return false;
	}

	$product["categoryid"] = $categoryid;
	$tmp = func_query_column("SELECT membershipid FROM $sql_tbl[product_memberships] WHERE productid = '$product[productid]'");
	if (!empty($tmp) && is_array($tmp)) {
		$product['membershipids'] = array();
		foreach ($tmp as $v) {
			$product['membershipids'][$v] = 'Y';
		}
	}

	if (!empty($product['variantid']) && !empty($active_modules['Product_Options'])) {
		$tmp = func_query_first("SELECT * FROM $sql_tbl[variants] WHERE variantid = '$product[variantid]'");
		if (!empty($tmp)) {
			func_unset($tmp, "def");
			$product = func_array_merge($product, $tmp);
		} else {
			func_unset($product, "variantid");
		}
	}

	# Detect product thumbnail and image
	$tmp = func_query_first("SELECT image_path as image_path_T, image_x as image_x_T, image_y as image_y_T FROM $sql_tbl[images_T] WHERE id = '$product[productid]'");
	if (!empty($tmp)) {
		$product = func_array_merge($product, $tmp);
		$product['is_thumbnail'] = true;
	}

	$tmp = false;
	if (!empty($product['variantid']) && !empty($active_modules['Product_Options']) && ($current_area == "C" || $current_area == "B"))
		$tmp = func_query_first("SELECT image_path as image_path_P, image_x as image_x_P, image_y as image_y_P FROM $sql_tbl[images_W] WHERE id = '$product[variantid]'");
	if (empty($tmp))
		$tmp = func_query_first("SELECT image_path as image_path_P, image_x as image_x_P, image_y as image_y_P FROM $sql_tbl[images_P] WHERE id = '$product[productid]'");
	if (!empty($tmp)) {
		$product = func_array_merge($product, $tmp);
		$product['is_image'] = true;
	}

	unset($tmp);

	if ($current_area == "C" || $current_area == "B") {
		#
		# Check if product is not available for sale
		#
		if (empty($active_modules["Egoods"]))
			$product["distribution"] = "";

		global $pconf;

		if ($product["forsale"] == "B" && empty($pconf)) {
			if (is_array(@$cart["products"])) {
				foreach ($cart["products"] as $k=>$v) {
					if ($v["productid"] == $product["productid"]) {
						$pconf = $product["productid"];
						break;
					}
				}
			}
			if (empty($pconf)) {
				x_session_register("configurations");
				global $configurations;

				if (!empty($configurations)) {
					foreach ($configurations as $c) {
						foreach ($c['steps'] as $s) {
							foreach($s['slots'] as $sl) {
								if ($sl['productid'] == $product["productid"]) {
									$pconf = $product["productid"];
									break;
								}
							}
						}
					}
				}
			}
		}

		$product['taxed_price'] = $product['price'];

		if (!$always_select && ($product["forsale"] == "N" || ($product["forsale"] == "B" && empty($pconf)))) {
			if ($redirect_if_error)
				func_header_location("error_message.php?product_disabled");
			else
				return false;
		}

		if ($current_area == "C" && !$clear_price) {
			#
			# Calculate taxes and price including taxes
			#
			global $login;

			$product["taxes"] = func_get_product_taxes($product, $login);
		}
	}

	# Add product features
	if (!empty($active_modules['Feature_Comparison']) && $product['fclassid'] > 0) {
		$product['features'] = func_get_product_features($product['productid']);
		$product['is_clist'] = func_check_comparison($product['productid'], $product['fclassid']);
	}

	$product["producttitle"] = $product['product'];

	if ($current_area == "C" || $current_area == "B") {
		$product["descr"] = func_eol2br($product["descr"]);
		$product["fulldescr"] = func_eol2br($product["fulldescr"]);
	}

	#
	# Get thumbnail's URL (uses only if images stored in FS)
	#
	if ($product['is_image'])
		$product["tmbn_url_P"] = func_get_image_url($product["productid"], "P", $product['image_path_P']);

	if ($product['is_thumbnail'])
		$product["tmbn_url_T"] = func_get_image_url($product["productid"], "T", $product['image_path_T']);

	if (!$product['is_image'] && !$product['is_thumbnail']) {
		$product["tmbn_url"] = func_get_default_image("P");

	} elseif ($product['is_image']) {
		$product["tmbn_url"] = $product["tmbn_url_P"];
		$product["image_x"] = $product["image_x_P"];
		$product["image_y"] = $product["image_y_P"];

	} else {
		# Use thumbnail instead of product image for product details page
		# when product image is not available.
		# Necessary only for the image dimensions because of
		# usage in <img> tag
		$product["tmbn_url"] = $product["tmbn_url_T"];
		$product["image_x"] = $product["image_x_T"];
		$product["image_y"] = $product["image_y_T"];
	}

	return $product;
}

#
# Get delivery options by product ID
#
function func_select_product_delivery($id) {
	global $sql_tbl;

	return func_query("select $sql_tbl[shipping].*, count($sql_tbl[delivery].productid) as avail from $sql_tbl[shipping] left join $sql_tbl[delivery] on $sql_tbl[delivery].shippingid=$sql_tbl[shipping].shippingid and $sql_tbl[delivery].productid='$id' where $sql_tbl[shipping].active='Y' group by shippingid");
}

#
# Add data to service array (Group editing of products functionality)
#
function func_ge_add($data, $geid = false) {
	global $sql_tbl, $XCARTSESSID;

	if (strlen($geid) < 32)
		$geid = md5(uniqid(rand(0, time())));

	if (!is_array($data))
		$data = array($data);

	$query_data = array(
		"sessid" => $XCARTSESSID,
		"geid" => $geid
		);

	foreach ($data as $pid) {
		if (empty($pid))
			continue;
		$query_data['productid'] = $pid;
		func_array2insert("ge_products", $query_data);
	}

	return $geid;
}

#
# Get length of service array (Group editing of products functionality)
#
function func_ge_count($geid) {
	global $sql_tbl;

	return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ge_products] WHERE geid = '$geid'");
}

#
# Get next line of service array (Group editing of products functionality)
#
function func_ge_each($geid, $limit = 1, $productid = 0) {
	global $__ge_res, $sql_tbl;

	if (!is_bool($__ge_res) && (!is_resource($__ge_res) || strpos(@get_resource_type($__ge_res), "mysql ") !== 0)) {
		$__ge_res = false;
	}

	if ($__ge_res === true) {
		$__ge_res = false;
		return false;
	}
	elseif ($__ge_res === false) {
		$__ge_res = db_query("SELECT productid FROM $sql_tbl[ge_products] WHERE geid = '$geid'");
		if (!$__ge_res) {
			$__ge_res = false;
			return false;
		}
	}

	$res = true;
	$ret = array();
	$limit = intval($limit);
	if ($limit <= 0)
		$limit = 1;

	$orig_limit = $limit;
	while (($limit > 0) && ($res = db_fetch_row($__ge_res))) {
		if ($productid == $res[0])
			continue;
		$ret[] = $res[0];
		$limit--;
	}

	if (!$res) {
		func_ge_reset($geid);
		$__ge_res = !empty($ret);
	}

	if (empty($ret))
		return false;

	return ($orig_limit == 1) ? $ret[0] : $ret;
}

#
# Check element of service array (Group editing of products functionality)
#
function func_ge_check($geid, $id) {
	global $sql_tbl;

	return (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ge_products] WHERE geid = '$geid' AND productid = '$id'") > 0);
}

#
# Reset pointer of service array (Group editing of products functionality)
#
function func_ge_reset($geid) {
	global $__ge_res;

	if ($__ge_res !== false)
		@db_free_result($__ge_res);

	$__ge_res = false;
}

#
# Get stop words list
#
function func_get_stopwords($code = false) {
	global $xcart_dir, $shop_language;

	if ($code === false)
		$code = $shop_language;

	if (!file_exists($xcart_dir."/include/stopwords_".$code.".php"))
		return false;

	$stopwords = array();
	include $xcart_dir."/include/stopwords_".$code.".php";

	return $stopwords;
}
?>
