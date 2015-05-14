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
# $Id: import_categories_lng.php,v 1.11.2.1 2006/04/27 13:14:28 max Exp $
#

/******************************************************************************
Used cache format:
Categories:
	data_type:	C
	key:		<Category full path>
	value:		[<Category ID> | RESERVED]
Categories (by Category ID):
	data_type:	CI
	key:		<Category ID>
	value:		[<Category full path> | RESERVED]
Deleted category data:
	data_type:	DC
	key:		<Categoryid ID>
	value:		<Flags>
Affected languages:
	data_type:	CL
	key:		<Language code>
	value:		<Language code>

Note: RESERVED is used if ID is unknown
******************************************************************************/


if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (!defined('IMPORT_CATEGORIES_LNG')) {
#
# Make default definitions (only on first inclusion!)
#
	define('IMPORT_CATEGORIES_LNG', 1);
	$modules_import_specification['MULTILANGUAGE_CATEGORIES'] = array(
		"script"		=> "/include/import_categories_lng.php",
		"tpl"  		=> array(
			"main/import_option_category_path_sep.tpl"),
		"export_tpls" 			=> array(
			"main/export_option_category_path_sep.tpl"),
		"parent"		=> "CATEGORIES",
		"is_language"	=> true,
		"permissions"	=> "A",
		"finalize"		=> true,
		"export_sql" 	=> "SELECT categoryid FROM $sql_tbl[categories_lng] WHERE code = '{{code}}'",
		"columns"		=> array(
			"categoryid"	=> array(
				"type"		=> "N",
				"is_key"	=> true,
				"default"	=> 0),
			"category"		=> array(
				"is_key"	=> true),
			"code"			=> array(
				"array"		=> true,
				"required"	=> true),
			"category_name"	=> array(
				"array"		=> true),
			"descr"			=> array(
				"eol_safe"	=> true,
				"array"		=> true),
		)
	);
}

if ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	# Check categoryid / category
	$_categoryid = func_import_detect_category($values);
	if (is_null($_categoryid) || ($action == "do" && empty($_categoryid))) {
		func_import_module_error("msg_err_import_log_message_18");
		return false;
	}

	$values['categoryid'] = $_categoryid;
	$values['lbls'] = array();
	foreach ($values['code'] as $k => $v) {
		if (empty($values['category_name'][$k]) && empty($values['descr'][$k]))
			continue;
		if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '$v'"))
			continue;
		$values['lbls'][$v] = array(
			"category"		=> $values['category_name'][$k], 
			"description"	=> $values['descr'][$k]
		);
	}
	unset($values['code']);

	$data_row[] = $values;
	
}
elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	if ($import_file["drop"][strtolower($section)] == "Y") {
		db_query("DELETE FROM $sql_tbl[categories_lng]");
		$import_file["drop"][strtolower($section)] = "";
	}

	foreach ($data_row as $row) {
	#
	# Import data...
	#

		# Import multilanguage category labels
		foreach ($row['lbls'] as $k => $v) {

			# Delete old data
			$tmp = func_import_get_cache("DC", $row['categoryid']);
			if (strpos($tmp, "L".strtolower($k)) === false) {
				db_query("DELETE FROM $sql_tbl[categories_lng] WHERE categoryid = '$row[categoryid]' AND code = '$k'");
				func_import_save_cache("DC", $row['categoryid'], $tmp."L".strtolower($k));
			}   

			$data = $v;
			$data['categoryid']	= $row['categoryid'];
			$data['code']		= $k;
			func_array2insert("categories_lng", func_addslashes($data));
			func_import_save_cache("CL", $k, $k);
			$result[strtolower($section)]["added"]++;
		}

		echo ". ";
		func_flush();

	}

# Post-import step
} elseif ($import_step == "complete" && !empty($active_modules['Fancy_Categories']) && func_fc_check_rebuild()) {

	$is_display_header = false;
	while (list($lcode, $tmp) = func_import_read_cache("CL")) {
		if (!$is_display_header) {
			$message = func_get_langvar_by_name("txt_rebuilding_category_cache_",NULL,false,true);
			func_import_add_to_log($message);
			func_flush("<br />\n".$message."<br />\n");
			$is_display_header = true;
		}
		func_fc_build_categories(false, 10, false, $lcode);

		func_flush(". ");
	}
	func_import_erase_cache("CL");

# Export data
} elseif ($import_step == "export") {

	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		$row = func_query_first("SELECT * FROM $sql_tbl[categories_lng] WHERE categoryid = '$id' AND code = '$current_code'");
		if (empty($row))
			continue;

		$row['category_name'] = $row['category'];
		$row['descr'] = $row['description'];
		func_unset($row, "category", "description");
		$c_row = func_export_get_category($id);
		if (empty($c_row))
			continue;
		$row = func_array_merge($c_row, $row);

		# Export row
		if (!func_export_write_row($row))
			break;

	}
}

?>
