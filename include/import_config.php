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
# $Id: import_config.php,v 1.6.2.1 2006/06/15 07:01:23 max Exp $
#

/******************************************************************************
Used cache format:

Note: RESERVED is used if ID is unknown
******************************************************************************/


if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == "define") {

	$import_specification['CONFIG'] = array(
		"script"		=> "/include/import_config.php",
		"permissions"	=> "A",
		"export_sql" 	=> "SELECT name FROM $sql_tbl[config]",
		"columns"		=> array(
			"name"		=> array(
				"required"	=> true,
				"is_key"	=> true
			),
			"category"	=> array(),
			"value"		=> array(
				"eol_safe"	=> true
			),
			"comment"	=> array(),
			"orderby"	=> array(
				"type"		=> "N"),
			"type"		=> array(
				"type"		=> "E",
				"variants"	=> array("text","checkbox","separator","textarea","numeric","selector","multiselector"),
				"default"	=> "text"),
			"defvalue"	=> array(),
			"variants"	=> array(
				"array"		=> true
			)
		)
	);


} elseif ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	$data_row[] = $values;
	
} elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	# Drop old data
	if ($import_file["drop"][strtolower($section)] == "Y") {

		db_query("UPDATE $sql_tbl[config] SET value = defvalue");
		$import_file["drop"][strtolower($section)] = "";
	}

	foreach ($data_row as $row) {
	#
	# Import data...
	#

		# Import config variables

		if (is_array($row['variants']))
			$row['variants'] = implode("\n", $row['variants']);

		$data = func_addslashes($row);

		# Update config variables
		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[config] WHERE name = '$data[name]' AND category = '$data[category]'")) {
			func_array2update("config", $data, "name = '$data[name]' AND category = '$data[category]'");
			$result[strtolower($section)]["updated"]++;

		# Add config variables
		} else {
			func_array2insert("config", $data);
			$result[strtolower($section)]["added"]++;
		}

		echo ". ";
		func_flush();

	}

# Export data
} elseif ($import_step == "export") {

	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		$row = func_query_first("SELECT * FROM $sql_tbl[config] WHERE name = '$id'");
		if (empty($row))
			continue;

		$row['variants'] = empty($row['variants']) ? array() : explode("\n", $row['variants']);

		# Export row
		if (!func_export_write_row($row))
			break;

	}
}

?>
