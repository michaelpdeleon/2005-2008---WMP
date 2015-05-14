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
# $Id: import_categories.php,v 1.18.2.6 2006/07/25 14:58:25 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('category', 'import');

/******************************************************************************
Used cache format:
Categories:
	data_type:	C
	key:		<Category full path>
	value:		[<Category ID> | RESERVED]
Categories:
	data_type:	CI
	key:		<Category ID>
	value:		[<Category full path> | RESERVED]
Memberships:
	data_type: 	M
	key:		<Membership name>
	value:		<Membership ID>
Images identificaters:
	data_type: 	I
	key:		<Image type>_<Image owner id>
	value:		<Image ID>
Categories for Counting the number of subcategories and products in categories:
	data_type:	CR
	key:		<Category ID>
	value:		<Category ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if (!defined('IMPORT_CATEGORIES')) {
#
# Make default definitions (only on first inclusion!)
#
	define('IMPORT_CATEGORIES', 1);
	$import_specification["CATEGORIES"] = array(
		"script" 		=> "/include/import_categories.php",
		"tpls"  			=> array(
			"main/import_option_default_category.tpl",
			"main/import_option_category_path_sep.tpl",
			"main/import_option_images_directory.tpl"),
		"export_tpls" 	=> array(
			"main/export_option_export_images.tpl",
			"main/export_option_category_path_sep.tpl"),
		"permissions"	=> "A", # Only admin can import categories
		"need_provider" => 0,
		"finalize"		=> true,
		"export_sql" 	=> "SELECT categoryid FROM $sql_tbl[categories] ORDER BY categoryid_path, order_by",
		"depending"		=> array("C","CI"),
		"columns" 		=> array(
			"categoryid"   => array(
				"is_key"	=> true,
				"type"		=> "N",
				"required"	=> false,
				"default"	=> 0),
			"category"		=> array(
				"is_key"    => true,
				"required"  => true),
			"descr"			=> array(
				"eol_safe"	=> true),
			"meta_descr"	=> array(),
			"avail"			=> array(
				"type"		=> "B",
				"default"	=> "Y"),
			"orderby"		=> array(
				"type"		=> "N",
				"default"	=> 0),
			"meta_keywords"	=> array(),
			"views_stats"	=> array(
				"type"		=> "N"),
			"product_count"	=> array(
				"type"		=> "N"),
			"membershipid"	=> array(
				"array"		=> true,
				"type"		=> "N"),
			"membership"	=> array(
				"array"     => true),
			"icon"			=> array(
				"type"		=> "I",
				"itype"		=> "C")
		)
	);

} elseif ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	if (isset($values["categoryid"]))
		$values["categoryid"] = abs(intval($values["categoryid"]));

	$tmp = func_import_get_cache("C", $values['category']);
	if (is_null($tmp)) {
		func_import_save_cache("C", $values['category']);
	}

	if (!empty($values["categoryid"])) {
		$tmp = func_import_get_cache("CI", $values['categoryid']);
		if (is_null($tmp)) {
			func_import_save_cache("CI", $values['categoryid']);
		}
	}

	# Check membership
	$values["membershipid"] = array();
	if (!empty($values['membership'])) {
		if (!is_array($values['membership']))
			$values['membership'] = array($values['membership']);
		foreach ($values['membership'] as $v) {
			if (empty($v))
				continue;
			$_membershipid = func_import_get_cache("M", $v);
			if (empty($_membershipid)) {
				$_membershipid = func_detect_membership($v, "C");
				if ($_membershipid == 0) {
					# Membership is specified but does not exist
					func_import_module_error("msg_err_import_log_message_5", array("membership" => $v));
				} else {
					func_import_get_cache("M", $v, $_membershipid);
				}
			}
			if (!empty($_membershipid))
				$values["membershipid"][] = $_membershipid;
		}
		unset($values['membership']);
	}

	$data_row[] = $values;

} elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	# Drop old categories and all related info
	if ($import_file["drop"]["categories"] == "Y") {
		func_import_save_image("C");
		db_query("DELETE FROM $sql_tbl[categories]");
		db_query("DELETE FROM $sql_tbl[category_memberships]");
		db_query("DELETE FROM $sql_tbl[products_categories]");
		db_query("DELETE FROM $sql_tbl[images_C]");

		$import_file["drop"]["categories"] = "";
	}

	# Import category data...
	$category_sep_local = empty($import_file["category_sep"]) ? "/" : $import_file["category_sep"];
	foreach ($data_row as $category) {

		$cats = explode($category_sep_local, $category['category']);
		if (empty($cats) || !is_array($cats))
			continue;

		# Import category chain
		$_parentid = 0;
		$_path = array();
		foreach ($cats as $kc => $c) {
			if (empty($c))
				continue;

			# Get old categoryid
			$_cid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE category = '".addslashes($c)."' AND parentid = '$_parentid'");

			$data = array(
				"category" => addslashes($c),
				"parentid" => $_parentid,
			);
			if ($kc == count($cats)-1) {
				# Check categoryid
				if (empty($_cid) && !empty($category['categoryid'])) {
					if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid = '".$category['categoryid']."'") > 0) {
						$_cid = $category['categoryid'];

					} else {
						$data['categoryid'] = $category['categoryid'];
					}
				}

				$data["description"]	= addslashes($category['descr']);
				$data["meta_descr"]		= addslashes($category['meta_descr']);
				if (isset($category['avail']))
					$data["avail"]		= $category['avail'];
				$data["order_by"]		= $category['orderby'];
				$data["meta_keywords"]	= addslashes($category['meta_keywords']);
				$data['views_stats']	= $category['views_stats'];
				$data['product_count']	= $category['product_count'];
			}

			# Import category data
			if (!empty($_cid)) {
				func_array2update("categories", $data, "categoryid = '$_cid'");
				$result["categories"]['updated']++;
			} else {

				$_cid = func_array2insert("categories", $data);
				if (!empty($_cid)) {
					$result["categories"]['added']++;
					$_path[$_cid] = $c;
					func_array2update("categories", array("categoryid_path" => implode("/", array_keys($_path))), "categoryid = '$_cid'");
				}
			}
			if (empty($_cid))
				continue;

			$_parentid = $_cid;
			$_path[$_cid] = $c;
			func_import_save_cache("C", implode($category_sep_local, $_path), $_cid);
			func_import_save_cache("CI", $_cid, implode($category_sep_local, $_path));
		}

		# Import category memberhips
		if (empty($_cid))
			continue;

		# Import category icon
		if (!empty($category['icon'])) {
			func_import_save_image_data("C", $_cid, $category['icon']);
		}

		# Import category memberhips
		db_query("DELETE FROM $sql_tbl[category_memberships] WHERE categoryid = '$_cid'");
		if (!empty($category['membershipid'])) {
			foreach ($category['membershipid'] as $v) {
				func_array2insert("category_memberships", array("categoryid" => $_cid, "membershipid" => $v));
			}
		}

		func_import_save_cache("CR", $_cid, $_cid);

		func_flush(". ");
	}

} elseif ($import_step == "complete") {

	# Post-import step
	$is_display_header = false;
	while (list($cid, $tmp) = func_import_read_cache("CR")) {
		if (!$is_display_header) {
			$message = func_get_langvar_by_name("txt_subcategories_and_products_counting_",NULL,false,true);
			func_import_add_to_log($message);
			func_flush("<br />\n".$message."<br />\n");
			$is_display_header = true;
		}
		func_recalc_subcat_count($cid);
		if (!empty($active_modules['Fancy_Categories']) && func_fc_check_rebuild()) {
			func_fc_build_categories(false, 10);
		}

		func_flush(". ");
	}
	func_import_erase_cache("CR");

} elseif ($import_step == "export") {

	# Export data
	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		$row = func_query_first("SELECT * FROM $sql_tbl[categories] WHERE categoryid = '$id'");
		if (empty($row))
			continue;

		$row = func_export_rename_cell($row, array("order_by" => "orderby", "description" => "descr"));
		$c_row = func_export_get_category($id);
		if (empty($c_row))
			continue;
		$row = func_array_merge($row, $c_row);

		# Export memberships
		$mems = func_query("SELECT $sql_tbl[memberships].membershipid, $sql_tbl[memberships].membership FROM $sql_tbl[memberships], $sql_tbl[category_memberships] WHERE $sql_tbl[memberships].membershipid = $sql_tbl[category_memberships].membershipid AND $sql_tbl[category_memberships].categoryid = '$id'");
		if (!empty($mems)) {
			foreach ($mems as $v) {
				$row['membershipid'][] = $v['membershipid'];
				$row['membership'][] = $v['membership'];
			}
		}

		# Export icons
		$row['icon'] = $id;

		# Export row
		if (!func_export_write_row($row))
			break;

	}
}

?>
