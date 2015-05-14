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
# $Id: export.php,v 1.28.2.4 2006/07/22 06:54:43 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','files','export','import');

#
# Define data for the navigation within section
#
include $xcart_dir."/include/import_tools.php";

x_session_register("export_data");

$step_row = 0; # Number of steps processed in one pass
$dot_per_row = 100;

# The export log file name
$export_log_filename = "x-errors_export-".preg_replace("![^a-z0-9]!i", "", $login)."-".date('ymd').".php";

# File with log of export
$export_log = $var_dirs["log"]."/".$export_log_filename;

# URL of the file with log of export
$export_log_url = "get_log.php?file=".$export_log_filename;

#
# Get export sections tree
#
function func_export_define($data, $parent = "") {
	global$_export_define_hash;

	if (empty($data))
		return false;

	# Create service hash array
	if (empty($parent)) {
		foreach ($data as $k => $v) {
			$_export_define_hash[$k] = true;
		}
	}

	# Build tree
	$ret = array();
	foreach ($data as $k => $v) {
		if (!isset($_export_define_hash[$v['parent']]))
			$v['parent'] = "";
		if ($v['parent'] != $parent)
			continue;
		$v['name'] = $k;
		$cnt = func_export_range_get_num($k);
		$v['range_count'] = ($cnt === false) ? -1 : $cnt;
		$ret[$k] = $v;
		$tmp = func_export_define($data, $k);
		if (!empty($tmp))
			$ret[$k]['subsections'] = $tmp;
	}
	if (empty($ret))
		return false;

	# Sort by orderby field and section name
	uasort($ret, "func_export_cmp_orderby");
	return $ret;
}

#
# Sorting function: sort sections list by 'orderby' field
#
function func_export_cmp_orderby($a, $b) {
	if ($a['orderby'] == $b['orderby']) {
		return strcmp($a['name'], $b['name']);
	}
	return $a['orderby'] > $b['orderby'] ? 1 : -1;
}

#
# This function adds message to the export log file
#
function func_export_add_to_log($message) {
	global $logf;

	if (!empty($message) && $logf)
		fwrite($logf, $message."\n");

	return true;
}

#
# Select export file
#
function func_export_open_file() {
	global $section, $export_data, $export_fp, $current_code, $current_code, $config;

	$is_reselect = false;
	$is_rw_header = false;

	# Check current file competence
	if ($export_data['line'] > $export_data['rows_per_file'] && $export_data['rows_per_file'] > 0 && empty($export_data['last_code'])) {

		# The export line limit has been exceeded
		$export_data['part']++;
		$export_data['line'] = 0;
		$is_rw_header = true;
		$is_reselect = true;

	} elseif ($current_code != $export_data['last_code']) {

		# Export data has different language code
		if ($current_code == $config['default_admin_language']) {
			if ($export_data['last_code'] != false) {
				$export_data['last_section'] = $section;
				$export_data['last_code'] = false;
				$export_data['last_limit'] = 0;
				$is_reselect = true;
			}

		} else {

			$export_data['last_section'] = $section;
			$export_data['last_code'] = $current_code;
			$export_data['last_limit'] = 0;
			$is_reselect = true;
		}

	} elseif ($section != $export_data['last_section']) {

		# Export data has different section
		$export_data['last_section'] = $section;
		$export_data['last_limit'] = 0;
		$is_reselect = true;
	}

	if ($is_reselect || !$export_fp) {
		if ($export_fp) {
			fclose($export_fp);
			$export_data['header'] = array();
		}

		# Deefine export file name
		$c = "";
		if (!empty($export_data['last_code'])) {
			$c .= "_".strtoupper($export_data['last_code']);

		} elseif ($export_data['part'] > 0) {
			$c .= "_".str_repeat("0", 3-strlen($export_data['part'])).$export_data['part'];
		}
		$name = $export_data['prefix'].$c.".csv.php";

		# Open file
		$is_new = !file_exists($name);
		$export_fp = @fopen($name, "a");
		if (!$export_fp) {
			global $top_message;
			$top_message['content'] = func_get_langvar_by_name("err_cannot_open_the_export_file");
			$top_message['type'] = "E";
			return false;
		}

		# Write header to file if file is new
		if ($is_new) {
			fwrite($export_fp, X_LOG_SIGNATURE);
			$export_data['pos'] = strlen(X_LOG_SIGNATURE);
		}
		if ($is_rw_header)
			func_export_write_header(NULL, true);
	}

	return $export_fp;
}

#
# Init export header
#
function func_export_write_header($data = NULL, $is_rw = false) {
	global $section, $export_data, $import_specification;

	if (!empty($export_data['header']) && !$is_rw)
		return true;

	# Write only section header
	if ($is_rw && !empty($export_data['header'])) {
		global $export_fp;
		$data = array();
		foreach ($export_data['header'] as $k => $v) {
			$data[] = strtoupper($k);
		}
		fwrite($export_fp, "[".$section."]\n");
		fwrite($export_fp, "!".implode($export_data['delimiter']."!", $data)."\n");
		$export_data['pos'] = ftell($export_fp);
		return true;
	}

	$is_new = empty($export_data['last_section']);

	$export_data['header'] = array();

	if (empty($data) || !is_array($data)) {
		$data = array_keys($import_specification[$section]['columns']);
	}

	$fp = func_export_open_file();
	if (!$fp)
		return false;

	foreach ($data as $k => $v) {
		if (empty($v)) {
			unset($data[$k]);
		} elseif (!isset($import_specification[$section]['columns'][$v])) {
			unset($data[$k]);
		} elseif ($export_data['options']['export_images'] != 'Y' && $import_specification[$section]['columns'][$v]['type'] == "I") {
			unset($data[$k]);
		}
	}
	if (empty($data))
		return false;

	$export_data['header'] = array_flip(array_values($data));

	return true;
}

#
# Write header to export file 
#
function func_export_write_header2file() {
	global $export_data, $section;

	if (empty($export_data['header']))
		return false;

    $fp = func_export_open_file();
    if (!$fp)
        return false;

	$data = array_map("strtoupper", array_keys($export_data['header']));
    fwrite($fp, "[".$section."]\n");
    fwrite($fp, "!".implode($export_data['delimiter']."!", $data)."\n");
    $export_data['pos'] = ftell($fp);

    return true;
}

#
# Write export row
#
function func_export_write_row($data) {
	global $section, $export_data, $import_specification, $line, $dot_per_row, $is_continue;
	global $logf;

	if (empty($data) || !is_array($data) || empty($export_data['header']))
		return false;

	$fp = func_export_open_file();
	if (!$fp)
		return false;

	if (!$is_continue) {
		if (!func_export_write_header2file())
			return false;
		$is_continue = true;
	}

	# Check row
	$row = array();
	$subrow = array();
	$max_subrow = 0;
	foreach ($export_data['header'] as $k => $v) {
		if (!isset($import_specification[$section]['columns'][$k]))
			continue;

		# Check cell
		if (!isset($data[$k])) {
			# Add empty cell
			$row[$v] = "";
			continue;
		}

		# Check array-cell as subrow
		if (is_array($data[$k]) && $import_specification[$section]['columns'][$k]['array']) {
			$row[$v] = func_export_cell_format(array_shift($data[$k]), $import_specification[$section]['columns'][$k]);
			if (!empty($data[$k])) {
				$data[$k] = array_values($data[$k]);

				if (!empty($data[$k])) {
					# Define subrows service array
					foreach ($data[$k] as $sk => $sv) {
						$subrow[$sk][$v] = func_export_cell_format($sv, $import_specification[$section]['columns'][$k]);
					}
					if ($max_subrow < count($data[$k]))
						$max_subrow = count($data[$k]);
				}
			}
		} else {
			$row[$v] = func_export_cell_format((string)$data[$k], $import_specification[$section]['columns'][$k]);
		}
	}

	if (empty($row))
		return false;

	# Write row
	ksort($row, SORT_NUMERIC);
	fwrite($fp, implode($export_data['delimiter'], $row)."\n");

	# Write subrows
	if (!empty($subrow)) {
		for ($x = 0; $x < $max_subrow; $x++) {
			foreach ($row as $k => $v) {
				if(!isset($subrow[$x][$k]))
					$subrow[$x][$k] = "";
			}

			ksort($subrow[$x], SORT_NUMERIC);
			fwrite($fp, implode($export_data['delimiter'], $subrow[$x])."\n");
		}

	}


	$export_data['pos'] = ftell($fp);
	$export_data['line']++;
	$export_data['total_line']++;
	$export_data['pass_line']++;
	$line++;

	# Echo dot
	if (($line % $dot_per_row == 0) && !empty($dot_per_row) && !empty($line)) {
		fwrite($logf, ".");
		echo ".";
		if (($line % ($dot_per_row * 100) == 0) && !empty($dot_per_row) && !empty($line)) {
			fwrite($logf, "\n");
			echo "<br />\n";
		}
		func_flush();
	}

	return true;
}

#
# Format cell by type
#
function func_export_cell_format($data, $cell) {
	global $export_data;

	# Check numeric-cell
	if ($cell['type'] == "N" || $cell['type'] == "P") {
		$data = doubleval($data);
		if ($data != floor($data))
			$data = sprintf("%01.03f", $data);

	# Check date-cell
	} elseif ($cell['type'] == "D") {
		if (is_numeric($data))
			$data = date('l d F Y h:i:s A', $data);

	# Check enumerated-cell
	} elseif ($cell['type'] == "E" && !empty($cell['variants']) && !in_array($data, $cell['variants'])) {
		$data = "";

	# Check image-cell
	} elseif ($cell['type'] == "I" && !empty($cell['itype'])) {
		if ($export_data['options']['export_images'] == 'Y') {
			$data = func_copy_image_to_fs($data, $cell['itype'], $export_data['data_dir']);
		} else {
			$data = "";
		}

	# Check date-cell
	} elseif (($cell['type'] == "S" || empty($cell['type'])) && !empty($cell['eol_safe'])) {
		$data = preg_replace("/\n/Ss", "<EOL>", $data);
	}

	return func_value_normalize($data);
}

#
# Column value normaliztion function
#
function func_value_normalize($value) {
	global $export_data;
	$value = preg_replace("/\r\n|\n|\r/Ss", " ", $value);
	if (@preg_match("/(".preg_quote($export_data['delimiter'], "/").")|\t/S", $value)) {
		$value = '"'.str_replace('"', '""', $value).'"';
		if (substr($value, -2) == '\"' && preg_match('/[^\\\](\\\+)"$/Ss', $value, $preg) && strlen($preg[1]) % 2 != 0) {
			$value = substr($value, 0, -2)."\\".substr($value, -2);
		}
	}
	return $value;
}

#
# Read section data
#
function func_export_read_data($section) {
	global $sql_tbl, $export_ranges, $export_data, $current_code, $import_specification, $parent_range_query;

	$section = strtoupper($section);
	$type = func_export_range_type($section);
	if ($type == "S") {
		$query = $export_ranges[$section];

	} elseif ($type == "C") {
		$query = "SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."'";

	} else {
		$query = $import_specification[$section]['export_sql'];
	}

	if (empty($query))
		return false;

	if (!empty($current_code)) {
		$query = str_replace("{{code}}", $current_code, $query);
	}

	$parent_range_query = false;
	if ($import_specification[$section]['parent'] && !in_array($type, array("S","C"))) {

		# Check parent sections chains by range conditions
		$s = $section;
		$childs = array();

		# Define nearest parent section wuth range condition
		while (!empty($import_specification[$s]['parent']) && empty($import_specification[$s]['is_range'])) {
			if (empty($import_specification[$s]['table']) || empty($import_specification[$s]['key_field']) || empty($sql_tbl[$import_specification[$s]['table']])) {

				$s = false;
				break;
			}
			$childs[] = array(
				"table" => $import_specification[$s]['table'],
				"key_field" => $import_specification[$s]['key_field'],
				"parent_key_field" => $import_specification[$s]['parent_key_field']
			);
			$s = $import_specification[$s]['parent'];
		}

		if (!empty($s) && !empty($import_specification[$s]['is_range'])) {
			# Check parent section range condition
			$type = func_export_range_type($s);
			if ($type == "S") {
				$parent_range_query = $export_ranges[$s];

			} elseif ($type == "C") {
				$parent_range_query = "SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($s)."'";
			}

			if (!empty($parent_range_query))
				$parent_range_query = array(
					"section" => $s,
					"type" => $type,
					"childs" => $childs,
					"query" => $parent_range_query
				);
		}
	}

	return db_query($query." LIMIT ".$export_data['last_limit'].", 999999999");
}

#
# Read section data row (step by step)
#
function func_export_get_row($res) {
	global $section, $import_specification, $parent_range_query, $sql_tbl;

	if (!$res)
		return false;

	func_export_line();

	$row = db_fetch_array($res);
	if ($row === false)
		return false;

	if (empty($parent_range_query))
		return array_shift($row);

	# Check parent section range condition
	$for = array();
	$where = array();
	$last_key = "'{{KEY}}'";
	foreach ($parent_range_query['childs'] as $k => $v) {
		$for[] = $sql_tbl[$v['table']]." as tbl$k";
		$where[] = "tbl$k.".$v['key_field']." = ".$last_key;
		$last_key = "tbl$k.";

		if (!empty($v['parent_key_field'])) {
			$last_key .= $v['parent_key_field'];

		} elseif ($k+1 < count($parent_range_query['childs'])) {
			$last_key .= $parent_range_query['childs'][$k+1]['key_field'];

		} else {
			$last_key .= $import_specification[$parent_range_query['section']]['key_field'];
		}
	}

	# Define SQL query (get parent section ID by current section ID)
	$query = "SELECT ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].".".$import_specification[$parent_range_query['section']]['key_field']." FROM ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].", ".implode(", ", $for)." WHERE ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].".".$import_specification[$parent_range_query['section']]['key_field']." = ".$last_key." AND ".implode(" AND ", $where);


	while ($row !== false) {
		$row = array_shift($row);

		# Get parent section ID by current section ID
		$ids = func_query_column(str_replace("{{KEY}}", addslashes($row), $query));

		if (!empty($ids)) {

			# Check defined parent section IDs
			$tmp = db_query($parent_range_query['query']);
			if ($tmp) {
				while ($id = db_fetch_array($tmp)) {
					$id = array_shift($id);
					if (in_array($id, $ids))
						return $row;
				}
				db_free_result($tmp);
			}
		}

		# Get next current section ID
		$row = db_fetch_array($res);
	}

	return false;
}

#
# Increments the counter of exported lines and
# performs self-redirect if the counter exceeds a certain amount 
#
function func_export_line() {
	global $sql_tbl, $export_ranges, $export_data, $import_specification, $section, $current_code, $step_row, $line;

	if ($step_row <= $export_data['pass_line'] && !empty($step_row)) {

		# Display section footer
		$message = func_get_langvar_by_name("lbl_rows", NULL, false, true).": ".$line;
		func_export_add_to_log("\n".$message."\n");
		echo "<br />\n".$message."<br />\n<br />\n";
		func_flush();

		# Self-redirect
		$export_data['last_section'] = $section;
		$export_data['last_code'] = $current_code;
		$export_data['pass']++;

		func_html_location("import.php?mode=export&action=continue", 3);
	}

	$export_data['last_limit']++;
}

#
# Export image to file system
#
function func_copy_image_to_fs($id, $type, $file_path) {
	global $sql_tbl, $config, $xcart_dir;

	if ($config['available_images'][$type] == "M") {
		$where = " WHERE imageid = '$id'";
	} else {
		$where = " WHERE id='$id'";
	}

	# Get image data
	$v = func_query_first("SELECT * FROM ".$sql_tbl['images_'.$type].$where);

	if (empty($v))
		return false;

	# Copy image from DB/FS to temp export directory
	if (!empty($v["image"]) || (!empty($v['image_path']) && !is_url($v['image_path']))) {
		$fname = (empty($v['filename']) ? strtolower($type)."_".$id : $v['filename']);

		# Detect file extension
		$ftype = "gif";
		if (empty($v['filename']) && !empty($v['image_type'])) {
			if (preg_match("/\/(.+)$S/", $v['image_type'], $match))
				$ftype = $match[1];
		}
		if(preg_match("/^(.+)\.([^\.]*)$/S", $v['filename'], $match) && !empty($v['filename'])) {
			$fname = $match[1];
			$ftype = $match[2];
		}

		# Detect unique filename
		$cnt = 1;
		$fname_orig = $fname;
		while (file_exists($file_path.DIRECTORY_SEPARATOR.$fname.".".$ftype) && $cnt < 99) {
			$fname = $fname_orig.$cnt++;
		}
		$file_name = $file_path.DIRECTORY_SEPARATOR.$fname.".".$ftype;

		# Get image content if image stored on FS
		if (empty($v["image"])) {
			if ($fp = func_fopen($xcart_dir.DIRECTORY_SEPARATOR.$v['image_path'], "rb", true)) {
				$v["image"] = fread($fp, filesize($xcart_dir.DIRECTORY_SEPARATOR.$v['image_path']));
				fclose($fp);
			} else {
				return false;
			}
		}

		# Write to temp export directory and retirn filename
		if ($fp = func_fopen($file_name, "wb", true)) {
			fwrite($fp, $v["image"]);
			fclose($fp);
			return $fname.".".$ftype;
		}

	} elseif (!empty($v['image_path']) && is_url($v['image_path'])) {

		# Return full image path (URL) if image stored as URL
		return $v['image_path'];
		
	}

	return false;
}

#
# Rename hash array cell
#
function func_export_rename_cell($data, $rename) {
	if (empty($data) || !is_array($data) || !is_array($rename) || empty($rename))
		return $data;

	foreach ($rename as $k => $v) {
		if (isset($data[$k])) {
			$data[$v] = $data[$k];
			unset($data[$k]);
		}
	}
	return $data;
}

#
# Fill the array of available types of importable data
# Key is the name of a section in a CSV-file which must be used
# for identifying the type of data being imported
#
$import_step = "define";
if ($dh = @opendir($xcart_dir."/include")) {
	while ($_file = readdir($dh)) {
		if ($_file != "import_tools.php" && preg_match("/^import_[\w\d_]+\.php$/S", $_file)) {
			include $xcart_dir."/include/".$_file;
		}
	}
}
unset($import_step);

# Add import specifications for the modules specific data
if (!empty($modules_import_specification)) {
	$import_specification = func_array_merge_ext($import_specification, $modules_import_specification);
}

# Check sections data and call 'oninitexport' event
if (is_array($import_specification) && !empty($import_specification)) {
	foreach ($import_specification as $k => $v) {
		if ((strpos($v["permissions"], $login_type) === false && empty($active_modules['Simple_Mode'])) || empty($v['script']) || empty($v['columns']) || !@file_exists($xcart_dir.$v['script']) || empty($v['export_sql'])) {
			unset($import_specification[$k]);

		} elseif (!empty($v['oninitexport']) && function_exists($v['oninitexport'])) {
			$res = $v['oninitexport']($k, $import_specification[$k]);
			if (!$res)
				unset($import_specification[$k]);
		}

	}
}

# Define import options service array
$export_options = array();
if (is_array($import_specification) && !empty($import_specification)) {
	foreach ($import_specification as $k=>$v) {
		if (is_array($v['export_tpls']) && !empty($v['export_tpls'])) {
			$export_options = func_array_merge($export_options, $v['export_tpls']);
		}
	}
}
$export_options = array_unique($export_options);

#
# Get and sort available languages
#
$tmp = func_data_cache_get("languages", array($shop_language));
$export_languages = array();
foreach ($tmp as $k => $v) {
	if ($config['default_admin_language'] == $v['code']) {
		$export_languages[] = $v;
		unset($tmp[$k]);
		break;
	}
}
if (empty($export_languages)) {
	$export_languages = $tmp;

} elseif (!empty($tmp)) {
	$export_languages = func_array_merge($export_languages, $tmp);
	$export_languages = array_values($export_languages);
}

unset($tmp);

# Change provider
if ($REQUEST_METHOD == "POST" && !empty($data_provider) && $action == "change_provider") {
	$export_data['provider'] = $data_provider;
	func_header_location("import.php?mode=export");

# Clear range
} elseif ($REQUEST_METHOD == "GET" && !empty($section) && $action == "clear_range") {
	func_export_range_erase($section);
	$lbl = func_get_langvar_by_name("lbl_export_".strtolower($section)."_clear", NULL, false, true);
	if (!empty($lbl)) {
		$top_message['content'] = $lbl;
		$top_message['type'] = 'I';
	}

	func_header_location("import.php?mode=export");

# Save export data
} elseif ($REQUEST_METHOD == "POST" && !empty($check) && !empty($data)) {
	if ($data['delimiter'] == 'tab')
		$data['delimiter'] = "\t";
	$export_data = $data;
	$export_data['check'] = array_keys($check);
	$export_data['options'] = $options;
	$action = "export";

# Delete export pack
} elseif ($REQUEST_METHOD == "POST" && !empty($packs) && $action == "delete_pack") {
	$is_delete = false;
	foreach ($packs as $key) {
		if (!preg_match("/^(\d{8})(\d{6})$/S", $key, $found))
			continue;

		$filename = "export_".$found[1]."_".$found[2];
		$dp = @opendir($export_dir);
		if (!$dp)
			continue;

		while ($file = readdir($dp)) {
			if ($file == '.' || $file == '..')
				continue;

			if (preg_match("/^".$filename."/S", $file)) {
				$is_delete = true;
				if (is_dir($export_dir.DIRECTORY_SEPARATOR.$file)) {
					func_rm_dir_files($export_dir.DIRECTORY_SEPARATOR.$file);
					func_rm_dir($export_dir.DIRECTORY_SEPARATOR.$file);
				} else {
					unlink($export_dir.DIRECTORY_SEPARATOR.$file);
				}
			}
		}
		closedir($dp);
	}

	if ($is_delete) {
		$top_message['content'] = func_get_langvar_by_name("txt_export_pack_has_been_successfully_removed");
		$top_message['type'] = "I";
	}
	func_header_location("import.php?mode=export");
}

# Export data
if ($action == "export" || $action == "continue") {

	# Open the log file for writing
	if (!($logf = @fopen($export_log, (($action == "export") ? "w+" : "a+") ))) {
		$top_message["content"] = func_get_langvar_by_name("msg_err_import_log_writing");
		$top_message["type"] = "E";
		func_header_location("import.php?mode=export");
	}

	func_display_service_header();

	# First pass
	if (empty($export_data['prefix'])) {

		# Start log file writing...
		$current_date = date("d-M-Y H:i:s", mktime() + $config["Appearance"]["timezone_offset"]);
		$message =<<<OUT
Date: $current_date
Launched by: $login

OUT;
		$message = constant("X_LOG_SIGNATURE").$message;
		func_export_add_to_log($message);

		if ($current_area == "P" && empty($active_modules['Simple_Mode']))
			$export_data['provider'] = $login;
		$export_data['prefix'] = $export_dir."/export_".date("Ymd_His");
		$export_data['data_dir'] = $export_data['prefix'];
		$export_data['pos'] = 0;
		$export_data['total_line'] = 0;
		$export_data['pass_line'] = 0;
		$export_data['line'] = 0;
		$export_data['part'] = 0;
		$export_data['last_section'] = "";
		$export_data['last_code'] = false;
		$export_data['last_limit'] = 0;
		$export_data['pass'] = 1;
		$export_fp = false;

		# Clean and check image directory
		if ($export_data['options']['export_images'] == 'Y') {

			# Create directory if not exists
			if (!is_dir($export_data['data_dir'])) {
				@mkdir($export_data['data_dir'], 0777);
			}

			# Clean directory
			if (is_dir($export_data['data_dir'])) {
				func_rm_dir_files($export_data['data_dir']);
			} else {
				$top_message['content'] = func_get_langvar_by_name("msg_images_directory_dnot_exist");
				$top_message['type'] = "E";
				func_header_location("import.php?mode=export");
			}

			# Check write permissions
			if (!is_writable($export_data['data_dir'])) {
				$top_message['content'] = func_get_langvar_by_name("msg_err_image_dir_permission_denied");
				$top_message['type'] = "E";
				func_header_location("import.php?mode=export");
			}
		}

		# Display export header
		$message = func_get_langvar_by_name("lbl_exporting_data_", NULL, false, true);
		if (!empty($message)) {
			echo "<b>".$message."</b><br />\n";
			func_flush();
		}

	# Next (non-first) pass
	} elseif ($export_data['pass'] > 1) {

		# Display export header
		$message = func_get_langvar_by_name("lbl_exporting_data_pass_", array("pass" => $export_data['pass']), false, true);
		if (!empty($message)) {
			echo "<b>".$message."</b><br />\n";
			func_flush();
		}
	}

	$message = <<<OUT
<script type="text/javascript">
<!--
	loaded = false;

	function refresh() {
		window.scroll(0, 100000);
		if (loaded == false)
			setTimeout('refresh()', 1000);
	}

	setTimeout('refresh()', 1000);
-->
</script>
OUT;

	func_flush($message);

	$end_message = <<<OUT
<script type="text/javascript">
<!--
	loaded = true;
-->
</script>
OUT;

	$export_data['pass_line'] = 0;
	$last_section = $export_data['last_section'];
	$last_code = $export_data['last_code'];
	$is_continue = false;
	$provider_sql = addslashes($export_data['provider']);

	# List sections
	foreach ($export_data['check'] as $section) {

		$section = strtoupper($section);

		if (!isset($import_specification[$section]))
			continue;

		$is_continue = false;
		if ($action == "continue" && !empty($last_section)) {
			if ($section != $last_section)
				continue;

			$last_section = false;
			$is_continue = true;
		}

		# Define export range
		$range_section = func_export_range_detect($section);
		if (!empty($range_section)) {
			$range = func_export_range_get($section);
		}

		$line = 0;

		if ($import_specification[$section]['is_language']) {

			#
			# Export multilanguage section
			#
			$is_export = false;
			foreach ($export_languages as $c) {

				$current_code = $c['code'];
				$line = 0;

				$is_continue = false;
				if ($action == "continue" && !empty($last_code)) {
					if ($last_code != $current_code)
						continue;

					$last_code = false;
					$is_continue = true;
				}

				if ($action == "export" || ($export_data['last_code'] != $current_code))
					$export_data['last_limit'] = 0;

				# Get MySQL-resource
				$data = func_export_read_data($section);
				if (!$data)
					continue;
				if (db_num_rows($data) == 0)
					continue;

				if (!$is_continue) {
					$export_data['header'] = array();
					if (!func_export_write_header())
						break;
				}

				# Display section header
				$message = func_get_langvar_by_name("lbl_".strtolower($section)."_exporting_", NULL, false, true);
				if (!empty($message)) {
					func_export_add_to_log($message."\n(".$c['language'].")");
					echo "<b>".$message."</b><br />\n(".$c['language'].")<br />\n";
					func_flush();
				}

				# Call section script
				$import_step = "export";
				include $xcart_dir.$import_specification[$section]["script"];
				$is_export = true;
				if ($data)
					@db_free_result($data);

				if ($export_fp)
					fwrite($export_fp, "\n");

				# Display section footer
				$message = func_get_langvar_by_name("lbl_rows", NULL, false, true).": ".$line;
				func_export_add_to_log("\n".$message."\n");
				echo "<br />\n".$message."<br />\n<br />\n";
				func_flush();

			}
			$current_code = false;
			if (!$is_export)
				continue;

		} else {

			#
			# Export regular section
			#
			if ($action == "export" || ($export_data['last_section'] != $section))
				$export_data['last_limit'] = 0;

			# Get MySQL-resource
			$data = func_export_read_data($section);
			if (!$data)
				continue;
			if (db_num_rows($data) == 0)
				continue;

			if (!$is_continue) {
				$export_data['header'] = array();
				if (!func_export_write_header())
					break;
			}

			# Display section header
			$message = func_get_langvar_by_name("lbl_".strtolower($section)."_exporting_", NULL, false, true);
			if (!empty($message)) {
				func_export_add_to_log($message);
				func_flush("<b>".$message."</b><br />\n");
			}

			# Call section script
			$import_step = "export";
			include $xcart_dir.$import_specification[$section]["script"];
			if ($data)
				@db_free_result($data);

			if ($export_fp)
				fwrite($export_fp, "\n");

			# Display section footer
			$message = func_get_langvar_by_name("lbl_rows", NULL, false, true).": ".$line;
			func_export_add_to_log("\n".$message."\n");
			func_flush("<br />\n".$message."<br />\n<br />\n");
		}

	}

	$export_data = array();
	$top_message['content'] = func_get_langvar_by_name("lbl_export_success");
	$top_message['type'] = 'I';
	func_html_location("import.php?mode=export&status=success", 3);

}

# Get export sepifications
if (!empty($import_specification)) {
	$export_spec = func_export_define($import_specification);
	if (!empty($export_spec)) {
		$smarty->assign("export_spec", $export_spec);
		$smarty->assign("export_spec_ids", array_keys($import_specification));
	}
}

# Get export packs list
if (($dp = @opendir($export_dir))) {
	$export_packs = array();
	while ($file = readdir($dp)) {

		if (is_dir($export_dir.DIRECTORY_SEPARATOR.$file) || !preg_match("/^export_(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})/S", $file, $found))
			continue;

		$fn = array_shift($found);
		$found = array_values($found);
		$key = implode("", $found);
		if (!isset($export_packs[$key])) {

			# Check export temporary directory
			$dir_exists = false;
			if (is_dir($export_dir.DIRECTORY_SEPARATOR.$fn) && ($imgdir = @opendir($export_dir.DIRECTORY_SEPARATOR.$fn))) {
				while ($tmp = readdir($imgdir)) {
					if ($tmp != '.' && $tmp != '..') {
						$dir_exists = true;
						break;
					}
				}
				closedir($imgdir);
			}

			$export_packs[$key] = array(
				"date"	=> mktime($found[3], $found[4], $found[5], $found[1], $found[2], $found[0]),
				"files"	=> array(),
				"count"	=> $dir_exists ? 1 : 0,
				"dir_exists" => $dir_exists ? $export_dir.DIRECTORY_SEPARATOR.$fn : false
			);
		}

		# Get export file section(s) and language code
		if(($fe = fopen($export_dir.DIRECTORY_SEPARATOR.$file, "r"))) {
			$file = str_replace(".php", "", $file);
			$export_packs[$key]['files'][$file] = array();
			if (preg_match("/_(\w{2})\./S", $file, $found)) {
				$export_packs[$key]['files'][$file]['code'] = $found[1];
				$export_packs[$key]['files'][$file]['code_name'] = func_get_langvar_by_name("language_".$found[1], NULL, false, true);
			}
			while ($s = fgets($fe, 8192)) {
				if (preg_match("/^\[([\w_ ]+)\]$/S", $s, $found)) {
					$tmp = strtoupper(trim($found[1]));
					if (isset($import_specification[$tmp]))
						$export_packs[$key]['files'][$file]['sections'][] = $tmp;
				}
			}
			$export_packs[$key]['files'][$file]['sections_count'] = count($export_packs[$key]['files'][$file]['sections']);
			$export_packs[$key]['count'] += ($export_packs[$key]['files'][$file]['sections_count'] > 0) ? $export_packs[$key]['files'][$file]['sections_count'] : 1;
			fclose($fe);
		}

	}
	closedir($dp);
	if (!empty($export_packs)) {
		krsort($export_packs, SORT_NUMERIC);
		$smarty->assign("export_packs", $export_packs);
	}
}

# Get last log content
$filename = $var_dirs["tmp"]."/".$export_log_filename;
if (($fe = @fopen($filename, "r")) !== false) {
	fseek($fe, strlen(X_LOG_SIGNATURE), SEEK_SET);
	$smarty->assign("export_log_content", fread($fe, filesize($filename)-strlen(X_LOG_SIGNATURE)));
	fclose($fe);
	$smarty->assign("export_log_url", $export_log_url);
}

$smarty->assign("export_dir", $export_dir);
$smarty->assign("export_options", $export_options);
$smarty->assign("export_data", $export_data);
$smarty->assign("export_images_dir", $xcart_dir.DIRECTORY_SEPARATOR."images");

$smarty->assign("mode", $mode);

?>
