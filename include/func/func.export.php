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
# $Id: func.export.php,v 1.2.2.1 2006/06/15 07:01:24 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Save export range
#
function func_export_range_save($section, $data) {
	global $sql_tbl, $export_ranges;

	if (empty($data))
		return false;

	$section = strtoupper($section);
	if (is_string($data)) {
		$export_ranges[$section] = $data;
		db_query("DELETE FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."'");
	}
	elseif (is_array($data)) {
		func_unset($export_ranges, $section);
		foreach ($data as $v) {
			func_array2insert("export_ranges", array("sec" => addslashes($section), "id" => $v), true);
		}
	}
	else {
		return false;
	}

	return true;
}

# Get export range
function func_export_range_get($section) {
	global $sql_tbl, $export_ranges;

	$type = func_export_range_type($section);
	if ($type == 'S') {
		return $export_ranges[$section];

	} elseif ($type == 'C') {
		return "SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."'";
	}

	return false;
}

#
# Get export range type
#
function func_export_range_type($section) {
	global $sql_tbl, $export_ranges;

	$section = strtoupper($section);
	if (isset($export_ranges[$section])) {
		return "S";
	}
	else {
		if (func_query_column("SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."'"))
			return "C";
	}
	return false;
}

#
# Get parent section with not empty export range
#
function func_export_range_detect($section, $last_range = "") {
	global $sql_tbl, $export_ranges, $import_specification;

	$section = strtoupper($section);
	if (func_export_range_get_num($section) !== false)
		$last_range = $section;

	if (!empty($import_specification[$section]['parent']))
		return func_export_range_detect($import_specification[$section]['parent'], $last_range);

	return $last_range;
}

#
# Get count of export range
#
function func_export_range_get_num($section) {
	$tmp = func_export_range_get($section);
	if ($tmp === false)
		return false;

	if (is_string($tmp) && !zerolen($tmp)) {
		$res = db_query($tmp);
		if ($res) {
			$tmp = db_num_rows($res);
			db_free_result($res);

			return $tmp;
		}

		return 0;
	}

	return false;
}

# Erase export range
function func_export_range_erase($section) {
	global $sql_tbl, $export_ranges;

	$section = strtoupper($section);
	func_unset($export_ranges, $section);
	db_query("DELETE FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."'");

	return true;
}

?>
