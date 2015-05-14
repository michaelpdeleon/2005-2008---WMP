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
# $Id: import.php,v 1.47.2.10 2006/08/04 11:11:08 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','files','image','import');

set_time_limit(86400);
#
# Store some information about importing in the session variables
#
x_session_register("import_data_provider"); # CSV-file
x_session_register("import_file"); # CSV-file
x_session_register("import_pass", array()); # Import process information
x_session_register("import_cache", array()); # Data cache
x_session_register("import_data", array()); # Import start data

#
# Global import definitions
#
$max_line_size = 65536 * 3;	# Max CSV file line length
$max_errors = 2;	# Max number of errors before break importing
$step_row = 500;	# Number of steps processed in one pass
$process_row_dot = 1;

$_ok = func_get_langvar_by_name("lbl_ok", false, false, true);

# The import log file name
$import_log_filename = "x-errors_import-".preg_replace("![^a-z0-9]!i", "", $login)."-".date('ymd').".php";

# File with log of import
$import_log = $var_dirs["log"]."/".$import_log_filename;

# URL of the file with log of import
$import_log_url = "get_log.php?file=".$import_log_filename;


# Possible values for $action variable...
$_possible_actions = array("do", "check");

if (empty($need_select_provider)) {
	$import_data_provider = $data_provider;
}
else {
	$_possible_actions[] = "change_provider";
	$smarty->assign("data_provider", $import_data_provider);
}

#
# Validate the $action variable: check CSV-file, perform the data importing
# or change provider...
#
if (!in_array($action, $_possible_actions))
	$action = "check";

$_cache_id = array();

#
# Check array elements emptiness
#
function func_array_empty($data) {
	if (empty($data))
		return true;

	if (!is_array($data))
		return empty($data);

	foreach ($data as $v) {
		if (is_array($v)) {
			if (!func_array_empty($v))
				return false;

		} elseif (!empty($v)) {
			return false;
		}
	}

	return true;
}

#
# This function checks if current row contains the section tag
#
function func_import_tag($columns) {
	if (!preg_match("/^\[([\w_ ]+)\]$/S", trim($columns[0]), $found))
		return false;

	for ($i = 1; $i < count($columns); $i++) {
		if (!empty($columns[$i]))
			return false;
	}

	return trim(strtoupper($found[1]));
}

#
# This function adds message to the import log file
#
function func_import_add_to_log($message, $columns=array(), $value="", $line_index=0) {
	global $logf;

	if ($line_index > 0)
		$message = "Error on line $line_index: ".$message;

	if (!empty($value))
		$message .= " ($value)";
	if (!empty($columns)) {
		if (is_array($columns)) {
			$message .= ":\n".implode(";", $columns);
		} else {
			 $message .= ":\n".$columns;
		}
	}

	if (!empty($message))
		fwrite($logf, $message."\n\n");

	return true;
}

#
# This function adds error message to the import log file
#
function func_import_error($msg, $params = NULL) {
	global $import_pass, $columns, $section;

	$import_pass["error"]++;
	$message = func_get_langvar_by_name($msg, $params, false, true);
	func_import_add_to_log($message, $columns, $section, $import_pass["line_index"]);
}

#
# This function adds error message to the import log file from import modules
#
function func_import_module_error($msg, $params = NULL) {
	global $import_pass, $current_row, $section, $last_row_idx;

	$import_pass["error"]++;
	$row = $current_row;
	foreach ($row as $k => $v) {
		$row[$k] = implode(";", $v);
	}
	$message = func_get_langvar_by_name($msg, $params, false, true);
	func_import_add_to_log($message, implode("\n", $row), $section, $last_row_idx);
}

#
# This function prepares the import result message,
# writes this message to the import log file and displays it in the browser
#
function func_import_display_results($section, $result = NULL) {
	global $_ok;

	$_import_result_message = func_get_langvar_by_name("txt_".$section."_import_result", $result,false,true);

	$message=<<<OUT
<font color='green'>$_ok</font>
<br />$_import_result_message<br />
OUT;

	# Add stripped message to the log...
	func_import_add_to_log(func_get_langvar_by_name("lbl_".$section."_importing_",false,false,true) . (strip_tags(str_replace(array("&nbsp;","<br />"), array("","\n"), $message))));

	# Display message...
	func_flush($message);

	return true;
}

#
# Save data cache as hash array
#
function func_import_save_cache($type, $id, $value = NULL, $force_save = false) {
	global $login, $action, $old_sections, $section, $import_specification, $sql_tbl;

	if (is_array($id))
		$id = implode("_", $id);

	if ($action != "do" && !$force_save) {
		foreach ($import_specification as $s => $sec) {
			if (isset($sec['depending']) && isset($old_sections[$s]) && in_array($type, $sec['depending'])) {
				$value = NULL;
				break;
			}
		}
		if (isset($import_specification[$section]['depending']) && in_array($type, $import_specification[$section]['depending']))
			$value = NULL;
	}

	db_query("REPLACE INTO $sql_tbl[import_cache] VALUES ('$type','".addslashes($id)."','".(empty($value) ? "RESERVED" : addslashes($value))."','".addslashes($login)."')");
}

#
# Get value from data cache (by data type and cell id)
#
function func_import_get_cache($type, $id) {
	global $login, $sql_tbl;

	if (is_array($id))
		$id = implode("_", $id);

	$data = func_query_first_cell("SELECT value FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND id = '".addslashes($id)."' AND login = '".addslashes($login)."'");
	if ($data === false) {
		$data = NULL;

	} elseif ($data == "RESERVED") {
		$data = false;
	}

	return $data;
}

#
# Save old obvject IDs
#
function func_import_save_cache_ids($type, $sql_query) {
	global $action;

	$return = false;
	$ids = db_query($sql_query);
	if ($ids) {
		if (db_num_fields($ids) < 2)
			return false;

		$return = db_num_rows($ids);
		while ($row = db_fetch_row($ids)) {
			$id = array_shift($row);
			func_import_save_cache($type, implode("_", $row), $id, true);
		}
		db_free_result($ids);
	}

	return $return;
}

#
# Erase cache cell (or row)
#
function func_import_erase_cache($type, $id = false) {
	global $login, $sql_tbl;

	if ($id === false) {
		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND login = '".addslashes($login)."'")) {
			db_query("DELETE FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND login = '".addslashes($login)."'");
			return true;
		}
		return false;
	}

	$data = func_query_first_cell("SELECT value FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND id = '".addslashes($id)."' AND login = '".addslashes($login)."'");
	if ($data === false)
		return false;

	if ($data == "RESERVED")
		db_query("DELETE FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND id = '".addslashes($id)."' AND login = '".addslashes($login)."'");

	return true;
}

#
# Read cache data step-by-step
#
function func_import_read_cache($type) {
	global $_cache_id, $login, $sql_tbl;

	if (!@$_cache_id[$type]) {
		$_cache_id[$type] = db_query("SELECT id, value FROM $sql_tbl[import_cache] WHERE BINARY data_type = '$type' AND login = '".addslashes($login)."'");
	}

	if (!$_cache_id[$type])
		return false;

	if ($tmp = db_fetch_row($_cache_id[$type]))
		return $tmp;

	db_free_result($_cache_id[$type]);
	$_cache_id[$type] = false;

	return false;
}

#
# Display section header
#
function func_import_section_start() {
	global $section_start, $section, $colnames, $values, $import_step, $action, $_values;

	if (empty($section) || empty($colnames) || empty($values))
		return false;

	$import_step = "process_row";
	$_values = $values;

	if (!$section_start)
		return true;

	if ($action == "do") {
		$lbl = func_get_langvar_by_name("lbl_".strtolower($section)."_importing_",false,false,true);
	} else {
		$lbl = func_get_langvar_by_name("lbl_".strtolower($section)."_checking_",false,false,true);
	}
	if (!empty($lbl)) {
		func_flush("<br />".$lbl."<br />");
	}
	$section_start = false;

	return true;
}

#
# Display section import header
#
function func_import_section_do() {
	global $action, $section, $data_row, $import_specification, $import_pass, $result, $import_step, $values, $_values;

	if (!empty($_values))
		$values = $_values;

	if (empty($data_row) || empty($section) || $action != "do")
		return false;

	if ($import_specification[$section]["finalize"])
		$import_pass['is_finalize'] = true;
	$result[strtolower($section)] = array(
		"added" => 0,
		"updated" => 0
	);
	$import_step = "finalize";

	return true;
}

#
# Get subrows count
#
function func_import_get_count($values) {
	$max_cnt = 0;
	foreach ($values as $v) {
		if (is_array($v)) {
			foreach ($v as $i => $v2) {
				if ($i > $max_cnt)
					$max_cnt = $i;
			}
		}
	}
	return $max_cnt;
}

#
# Define $data array for direct insert/update
#
function func_import_define_data($row, $keys, $check_empty = true) {
	$data = array();
	foreach ($row as $k => $v) {
		$key = false;
		if (!empty($keys[$k])) {
			$key = $keys[$k];

		} elseif (($pos = array_search($k, $keys)) !== false) {
			if (is_int($pos))
				$key = $keys[$pos];
		}

		if (!empty($key) || (isset($key) && !$check_empty)) {
			$data[$key] = is_string($v) ? addslashes($v) : $v;
		}
	}

	return $data;
}

#
# Save image id by image type
#
function func_import_save_image($type) {
	global $sql_tbl;
	$res = db_query("SELECT imageid, id FROM ".$sql_tbl['images_'.$type]);
	if ($res) {
		while ($row = db_fetch_array($res)) {
			func_import_save_cache("I", $type."_".$row['id'], $row['imageid']);
		}
		db_free_result($res);
	}
	return true;
}

#
# Save image for import cell to DB
#
function func_import_save_image_data($type, $id, $data, $_imageid = false) {
	global $config;

	if (empty($data))
		return false;

	$temp_file_upload_data = array($type => $data);
	if (func_check_image_posted($temp_file_upload_data, $type)) {
		if ($config['available_images'][$type] == 'U')
			func_delete_image($id, $type);

		if (empty($_imageid))
			$_imageid = func_import_get_cache("I", $type."_".$id);

		return func_save_image($temp_file_upload_data, $type, $id, array(), $_imageid);
	}
	return false;
}

function func_import_is_provider_rewrite($values) {
	global $current_area, $single_mode, $login;

	if (!is_array($values))
		$values['provider'] = $values;
	return (!empty($values["provider"]) && ($current_area == "A" || $single_mode || $login == $values["provider"]));
}

#
# Define data for the navigation within section
#
include_once $xcart_dir."/include/import_tools.php";

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
if (!empty($modules_import_specification))
	$import_specification = func_array_merge_ext($import_specification, $modules_import_specification);

# Check section permission and call oninitimport event
if (is_array($import_specification) && !empty($import_specification)) {
	foreach ($import_specification as $k => $v) {
		if ((strpos($v["permissions"], $login_type) === false && empty($active_modules['Simple_Mode'])) || ($v["need_provider"] && empty($import_data_provider)) || empty($v['script']) || empty($v['columns']) || !@file_exists($xcart_dir.$v['script'])) {
			unset($import_specification[$k]);

		} elseif ($v['oninitimport'] && function_exists($v['oninitimport'])) {
			$res = $v['oninitimport']($k, $import_specification[$k]);
			if (!$res)
				unset($import_specification[$k]);
		}
	}
}

# Check import specifications and define import options service array
$import_options = array();
if (is_array($import_specification) && !empty($import_specification)) {
	foreach ($import_specification as $k => $v) {
		if (is_array($v['tpls']) && !empty($v['tpls'])) {
			$import_options = func_array_merge($import_options, $v['tpls']);
		}
	}
}
$import_options = array_unique($import_options);

#
# Process the import CSV-file
#
$provider_condition = ($single_mode ? "" : " AND $sql_tbl[products].provider='".$import_data_provider."'");

if ($REQUEST_METHOD == "POST" || !empty($continue) || $action == "do" || $action == "change_provider") {
	if ($REQUEST_METHOD == "POST")
		db_query("DELETE FROM $sql_tbl[import_cache] WHERE login = '".addslashes($login)."'");

	if ($action == "change_provider") {
	#
	# Change current provider
	#
		if (!empty($data_provider)) {
			$data_provider = stripslashes($data_provider);
			if (func_query_first_cell("SELECT login FROM $sql_tbl[customers] WHERE login='".addslashes($data_provider)."' AND usertype='P' LIMIT 1")) {
				$import_data_provider = $data_provider;
			} else {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_no_provider_found", "", false, true);
				$top_message["type"] = "E";
			}
		} else {
			$import_data_provider = "";
		}
		func_header_location("import.php");
	}

	if (empty($import_specification)) {
	# Display error and exit if no import specification defined
		$top_message["content"] = func_get_langvar_by_name("msg_adm_no_data_can_be_imported", "", false, true);
		func_header_location("import.php");
	}

	if (empty($import_file)) {
	#
	# Prepare the source of importing...
	#
		$import_file = array();
		if ($source == "server" && !empty($localfile)) {
			# File is located on the server
			$localfile = stripslashes($localfile);
			if (func_allow_file($localfile, true)) {
				$import_file["location"] = $localfile;
				$import_file["uploaded"] = false;
			}
		}
		elseif ($source == "upload" && !empty($userfile)) {
		# File is uploaded to the server from home computer
			$userfile = func_move_uploaded_file("userfile");
			if ($userfile !== false) {
				$import_file["location"] = $userfile;
				$import_file["uploaded"] = true;
			}
		}
		elseif ($source == "url" && !empty($urlfile)) {
			# File is uploaded to the server from remote host
			$urlfile = stripslashes($urlfile);
			$fsize = func_filesize($urlfile);
			if ($fsize > 0 && is_url($urlfile)) {
				$import_file["location"] = $urlfile;
				$import_file["uploaded"] = false;
			}
		}
		if (!empty($import_file)) {
		# Save CSV-delimiter and data provider
			if ($delimiter == 'tab')
				$delimiter = "\t";
			$import_file["delimiter"] = $delimiter;
			if (!empty($drop) && is_array($drop))
				$import_file["drop"] = $drop;
			if (!empty($options) && is_array($options)) {
				foreach ($options as $k => $v) {
					$options[$k] = $import_file[$k] = stripslashes($v);
				}
			}
			if (!empty($import_file["images_directory"])) {
				if (is_url($import_file["images_directory"])) {
					if (substr($import_file["images_directory"], -1) != '/')
						$import_file["images_directory"] .= "/";
				} else {
					$rpath = func_realpath($import_file["images_directory"]);
					if (file_exists($rpath) && is_dir($rpath)) {
						$import_file["images_directory"] = $rpath;
						if (substr($import_file["images_directory"], -1) != DIRECTORY_SEPARATOR)
							$import_file["images_directory"] .= DIRECTORY_SEPARATOR;
					} else {
						$import_file["images_directory"] = "";
					}
				}
			}
			$import_file["images_directory_is_url"] = is_url($images_directory);
		}
	}

	#
	# Open import file
	#
	if ($import_file != "" && isset($import_file["location"])) {
		$fp = @func_fopen($import_file["location"], "r", true);
		if (!@func_filesize($import_file["location"]) || $fp === false) {
			if ($fp !== false) {
				fclose($fp);
				$fp = false;
			}

			if ($import_file["uploaded"])
				@unlink($import_file["location"]);

			$import_file = "";
		}
	}

	if (empty($import_file)) {
	# File cannot be opened: display error
		x_session_unregister("import_file");
		$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
		$top_message["type"] = "E";
		func_header_location("import.php");
	}

	func_display_service_header();

	if ($first_pass = empty($import_pass)) {
	# Prepeare the information about first import passing...
		$import_pass = array(
			"file_position" => 0,
			"section" => "",
			"line_index" => 0,
			"colnames" => array(),
			"error" => 0,
			"step" => 1,
			"is_subrow" => false,
			"values" => array(),
			"old_sections" => array(),
			"section_lines_counter" => 0,
			"is_finalize" => false
		);
		@unlink($import_log);
		if ($action == "do")
			$echo_str = func_get_langvar_by_name("lbl_process_import_data_",false,false,true);
		else
			$echo_str = func_get_langvar_by_name("lbl_check_import_data_",false,false,true);

		if (!empty($source) && $REQUEST_METHOD == 'POST') {
			if ($delimiter == 'tab')
				$delimiter = "\t";
			$import_data = array(
				"source" => $source,
				"localfile" => $localfile,
				"urlfile" => $urlfile,
				"delimiter" => $delimiter,
				"options" => $options
			);
		}

	} elseif ($action == "do") {
		$echo_str = func_get_langvar_by_name("lbl_process_import_data_step_N_",array("step" => $import_pass["step"]),false,true);

	} else {
		$echo_str = func_get_langvar_by_name("lbl_check_import_data_step_N_",array("step" => $import_pass["step"]),false,true);
	}

	func_flush("<b>".$echo_str."</b><br />");

	$section_tags = array_keys($import_specification);

	#
	# Open the log file for writing
	#
	if (!($logf = @fopen($import_log, "a+"))) {
		$top_message["content"] = func_get_langvar_by_name("msg_err_import_log_writing");
		$top_message["type"] = "E";
		func_header_location("import.php");
	}

	if ($first_pass) {
	# Start log file writing...
		$current_date = date("d-M-Y H:i:s", mktime() + $config["Appearance"]["timezone_offset"]);
		$message =<<<OUT
Date: $current_date
Launched by: $login

OUT;

		$message = X_LOG_SIGNATURE.$message;
		func_import_add_to_log($message);
	}

	#
	# Prepare the variables
	#
	$old_sections = $import_pass["old_sections"];
	$section_lines_counter = $import_pass['section_lines_counter'];
	$colnames = $import_pass["colnames"];
	$section = $import_pass["section"];
	$values = $import_pass["values"];
	$current_row = $import_pass["current_row"];
	$last_row_idx = $import_pass["last_row_idx"];
	$data_row = $import_pass["data_row"];
	$is_subrow = $import_pass["is_subrow"];
	$line_index = 1;
	$file_position = $import_pass["file_position"];
	$section_start = true;

	# Position the file pointer
	if ($file_position > 0 && $fp) {
		if (is_url($import_file["location"])) {
			for ($x = 0; $x < floor($file_position / 8192); $x++) {
				fread($fp, 8192);
			}
			fread($fp, $file_position % 8192);

		} else {
			fseek($fp, $file_position);
		}
	}

	# Get key columns
	$key_columns = array();
	if (isset($import_specification[$section])) {
		$is_array_fields = false;
		foreach ($import_specification[$section]['columns'] as $k => $v) {
			if ($v['is_key'])
				$key_columns[] = $k;
			if ($v['array'])
				$is_array_fields = true;
		}
		if (!$is_array_fields)
			$key_columns = array();
	}

	$message = <<<OUT
<script type="text/javascript">
<!--
	var loaded = false;

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

	#
	# PROCESS THE CSV-FILE ROWS
	#
	while (($columns = fgetcsv ($fp, $max_line_size, $import_file["delimiter"])) || $import_pass["file_position"] != $file_position) {

		# Break import if too many errors occured
		if ($import_pass["error"] >= $max_errors) {
			func_flush('<script type="text/javascript"><!-- loaded = true; --></script>');
			func_html_location("import.php?error=1", 0);
		}

		if ($line_index > $step_row || $columns === false) {
			if (!empty($section)) {
				$old_sections[$section] = true;
				if ($columns === false) {
					if (func_import_section_start()) {
						include $xcart_dir.$import_specification[$section]["script"];
						if (func_import_section_do()) {
							include $xcart_dir.$import_specification[$section]["script"];
							func_import_display_results(strtolower($section), $result[strtolower($section)]);
						} else {
							func_flush(". <font color='green'>$_ok</font>");
						}

					} elseif (empty($colnames)) {
						# Empty section header
						func_import_error("msg_err_import_log_message_42", array("section" => $section));

					} elseif (empty($section_lines_counter)) {
						# Empty section body
						func_import_error("msg_err_import_log_message_43", array("section" => $section));
					}

				} elseif (!empty($values)) {
					func_flush(". <font color='green'>$_ok</font>");
				}
			}

			# Follow to the next step of importing...
			$import_pass["old_sections"] = $old_sections;
			$import_pass['section_lines_counter'] = $section_lines_counter;
			$import_pass["section"] = $section;
			$import_pass["file_position"] = $file_position;
			$import_pass["colnames"] = $colnames;
			$import_pass["data_row"] = $data_row;
			$import_pass["values"] = $values;
			$import_pass["current_row"] = $current_row;
			$import_pass["last_row_idx"] = $last_row_idx;
			$import_pass["is_subrow"] = $is_subrow;
			$import_pass["step"]++;

			fclose($logf);
			fclose($fp);

			func_flush('<script type="text/javascript"><!-- loaded = true; --></script>');
			if ($columns === false) {
				if (!empty($import_pass["error"])) {
					func_html_location("import.php?error=1", 1);

				} elseif ($action == "do") {
					if ($import_pass['is_finalize']) {
						func_html_location("import.php?finalize", 1);
					} else {
						func_html_location("import.php?complete", 1);
					}

				} elseif (empty($old_sections)) {
					$top_message = array(
						"content" => func_get_langvar_by_name("msg_data_import_no_sections", false, false, false),
						"type" => "W"
					);
					func_html_location("import.php?complete", 1);

				} else {
					$import_pass = $import_cache = array();
					func_html_location("import.php?action=do", 1);
				}
			} else {
				func_html_location("import.php?continue=1".($action == "do" ? "&action=do" : ""), 1);
			}
		}

		$file_position = ftell($fp);

		$line_index++;
		$import_pass["line_index"]++;

		# Clear empty cells on the line tail
		if (empty($colnames)) {
			for ($x = count($columns)-1; $x >= 0; $x--) {
				if (!empty($columns[$x]))
					break;
				unset($columns[$x]);
			}
		} else if (count($columns) > count($colnames)) {
			for ($x = count($colnames); $x < count($columns); $x++) {
				unset($columns[$x]);
			}
		}

		# Check if line is empty...
		if (func_array_empty($columns))
			continue;


		# Check the section tag...
		# e.g. [ZONES]
		if ($_section = func_import_tag($columns, $section_tags)) {

			# Finalize the importing of data from previous section
			if (in_array($_section, $section_tags)) {
				if (func_import_section_start()) {
					include $xcart_dir.$import_specification[$section]["script"];
					if (func_import_section_do()) {
						include $xcart_dir.$import_specification[$section]["script"];
						func_import_display_results(strtolower($section), $result[strtolower($section)]);
					} else {
						func_flush(". <font color='green'>$_ok</font>");
					}

				} elseif (!empty($section) && empty($colnames)) {
					# Empty section header
					func_import_error("msg_err_import_log_message_42", array("section" => $section));

				} elseif (!empty($section) && empty($section_lines_counter)) {
					# Empty section body
					func_import_error("msg_err_import_log_message_43", array("section" => $section));
				}

				if (!empty($import_specification[$_section]["need_provider"]) && empty($import_data_provider)) {

					# Check section permission
					func_import_error("msg_err_import_log_message_2");
					$_section = "";

				} elseif ($import_specification[$_section]["no_import"]) {

					# Check section flag 'no_import'
					$_section = "";
				} elseif (!empty($import_specification[$_section]['onstartimportsection']) && function_exists($import_specification[$_section]['onstartimportsection'])) {

					# Check section 'onstartimportsection' event
					$res = $import_specification[$_section]['onstartimportsection']($_section, $import_specification[$_section]);
					if (!$res)
						$_section = "";
				}

				$section = $_section;
				$old_sections[$section] = true;
				$current_row = $data_row = $values = $_values = array();
				$section_lines_counter = 0;
				$last_row_idx = false;
				$section_start = true;

				# Get key columns
				$key_columns = array();
				if (isset($import_specification[$section])) {
					$is_array_fields = false;
					foreach ($import_specification[$section]['columns'] as $k => $v) {
						if ($v['is_key'])
							$key_columns[] = $k;
						if ($v['array'])
							$is_array_fields = true;
					}
					if (!$is_array_fields)
						$key_columns = array();
				}

				$colnames = array();
				func_flush("<br />");
				continue;
			}
			else {
			# Add message into the log file
				func_import_error("msg_err_import_log_message_1");
				$section = "";
			}
		}

		# Get column names (header within section)...
		# e.g. !ZONE;!COUNTRY;!STATE;!COUNTY;!CITY;!ADDRESS;!ZIP
		if (!empty($section) && empty($colnames) && count(preg_grep("/^\s*\!([\w\d_]+)\s*$/S", $columns)) == count($columns)) {
			for ($i = 0; $i < count($columns); $i++) {
				$colnames[$i] = trim(strtolower(substr($columns[$i], 1)));

				# Column name does not comply with defined for this section
				if (!isset($import_specification[$section]["columns"][$colnames[$i]])) {
					func_import_error("msg_err_import_log_message_4", array("column"=>strtoupper($colnames[$i]), "section"=>strtoupper($section)));
					$section = "";
					break;
				}
			}
			continue;
		}

		# Next row if column names was not defined...
		if (empty($colnames))
			continue;

		# Detect subrow
		$is_subrow = false;
		if (!empty($values) && !empty($key_columns) && is_array($key_columns)) {
			$is_subrow = true;
			for ($i = 0; $i < count($columns); $i++) {
				if (in_array($colnames[$i], $key_columns) && !empty($columns[$i]) && $columns[$i] != $values[$colnames[$i]]) {
					$is_subrow = false;
					break;
				}
			}
		}

		# Process current row of values with subrows: validate and prepare for importing
		if (!$is_subrow && !empty($values)) {
			if (func_import_section_start()) {
				include $xcart_dir.$import_specification[$section]["script"];
				if ($line_index % $process_row_dot == 0 && !empty($line_index)) {
					func_flush(". ");
				}
				if (func_import_section_do() && count($data_row) >= $step_row) {
					include $xcart_dir.$import_specification[$section]["script"];
					func_import_display_results(strtolower($section), $result[strtolower($section)]);
					$data_row = array();
				}
			}

			$current_row = $values = array();
			$last_row_idx = false;
		}

		# Generate the array of values...
		$orig_values = array();
		for ($i = 0; $i < count($columns); $i++) {
			$columns[$i] = preg_replace("/^[ ]+/S", "", preg_replace("/[ ]+$/S", "", $columns[$i]));

			# Check value
			if (!zerolen($columns[$i])) {
				$col_type = "S";
				if (!empty($import_specification[$section]["columns"][$colnames[$i]]["type"]))
					$col_type = $import_specification[$section]["columns"][$colnames[$i]]["type"];
				$wrong_data_type = false;
				$wrong_data_type_error = false;

				if ($col_type == 'S') {
					if (empty($import_specification[$section]["columns"][$colnames[$i]]["allow_tags"]) && func_have_script_tag($columns[$i])) {
						$columns[$i] = strip_tags($columns[$i]);
					}
					if (
						isset($import_specification[$section]["columns"][$colnames[$i]]["maxlength"]) &&
						intval($import_specification[$section]["columns"][$colnames[$i]]["maxlength"]) > 0 &&
						strlen($columns[$i]) > intval($import_specification[$section]["columns"][$colnames[$i]]["maxlength"])
					) {
						$columns[$i] = substr($columns[$i], 0, intval($import_specification[$section]["columns"][$colnames[$i]]["maxlength"]));
					}

				# Check integer/float value
				} else if ($col_type == "N") {
					if (!is_numeric($columns[$i]))
						$wrong_data_type = true;
					$columns[$i] = (float)$columns[$i];

				# Check boolean value
				} elseif ($col_type == "B") {
					$columns[$i] = substr(strtoupper($columns[$i]), 0, 1);
					if (!empty($columns[$i]) && !in_array($columns[$i], array("Y","N")))
						$wrong_data_type = true;

				# Check enumerated value
				} elseif ($col_type == "E") {
					if (!empty($import_specification[$section]["columns"][$colnames[$i]]["variants"]) && is_array($import_specification[$section]["columns"][$colnames[$i]]["variants"])) {
						if (!@in_array($columns[$i], $import_specification[$section]["columns"][$colnames[$i]]["variants"])) {
							$wrong_data_type = true;
						}
					}

				# Check price value
				} elseif ($col_type == "P") {
					$columns[$i] = func_detect_price($columns[$i]);
					if ($columns[$i] === false || $columns[$i] < 0) {
						$wrong_data_type = true;
						$columns[$i] = "";
					}

				# Check markup value
				} elseif ($col_type == "M") {
					$cur_symbol = "$";

					# Detect type of markup (percent or absolute)
					if (preg_match("/([%$]|".preg_quote($cur_symbol, "/").")$/S", $columns[$i], $match)) {
						$markup_postfix = $match[1] == "%" ? "%" : "$";
						$columns[$i] = substr($columns[$i], 0, strlen($match[1])*-1);
					} else {
						$markup_postfix = "$";
					}

					# Detect markup as price formatted
					$columns[$i] = func_detect_price($columns[$i]);
					if ($columns[$i] === false) {
						$wrong_data_type = true;
						$columns[$i] = "";
					} else {
						$columns[$i] .= $markup_postfix;
					}

				# Check language code value
				} elseif ($col_type == "C") {
					$columns[$i] = strtoupper($columns[$i]);
					if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '".addslashes($columns[$i])."'") == 0) {
						$wrong_data_type = true;
						$columns[$i] = "";
					}

				# Check data value
				} elseif ($col_type == "D") {

					# Data as UNIX timestamp
					if (is_numeric($columns[$i])) {
						$columns[$i] = abs(intval($columns[$i]));

					# Data as formatted string
					} else {
						$columns[$i] = strtotime($columns[$i]);
						if ($columns[$i] == -1) {
							$wrong_data_type = true;
							$columns[$i] = 0;
						}
					}

				# Check image path value
				} elseif ($col_type == "I") {

					# Get path to image
					if (func_is_full_path($columns[$i])) {
						$file_path = $columns[$i];
					} elseif (empty($import_file["images_directory"])) {
						$file_path = $xcart_dir.DIRECTORY_SEPARATOR.$columns[$i];
					} else {
						$file_path = $import_file["images_directory"].$columns[$i];
					}

					$image_is_url = is_url($file_path);
					# Check file size (and file availability)
					if (!$image_is_url && !file_exists($file_path)) {
						func_import_error("msg_err_import_log_message_45", array("column" => strtoupper($colnames[$i])));
						$columns[$i] = "";
						$wrong_data_type = true;
						$wrong_data_type_error = true;

					} elseif (!func_allow_file($file_path, true)) {
						func_import_error("msg_err_import_log_message_46", array("column" => strtoupper($colnames[$i])));
						$columns[$i] = "";
						$wrong_data_type = true;
						$wrong_data_type_error = true;

					} else {

						# Image type exist and registered
						if ($config['setup_images'][$import_specification[$section]["columns"][$colnames[$i]]["itype"]]) {
							$data = array(
								"source" => $image_is_url ? "U" : "S",
								"type" => $import_specification[$section]["columns"][$colnames[$i]]["itype"],
								"date" => time(),
								"file_path" => $file_path,
								"filename" => basename($file_path)
							);

							list($data["file_size"], $data["image_x"], $data["image_y"], $data["image_type"]) = func_get_image_size($data["file_path"]);

							if ($data["file_size"] == 0 || empty($data["image_type"])) {
								# Image file is empty
								func_import_error("msg_err_import_log_message_47", array("column" => strtoupper($colnames[$i])));
								$columns[$i] = "";
								$wrong_data_type = true;
								$wrong_data_type_error = true;

							} elseif (($image_perms = func_check_image_perms($data['type'])) !== true) {
								# Check permissions
								func_import_error($image_perms['label'], array("path" => $image_perms['path']));
								$columns[$i] = "";
								$wrong_data_type = true;
								$wrong_data_type_error = true;

							} else {
								# Save prepared data to cell
								$columns[$i] = $data;
							}

						# Image type exist and not registered
						} elseif (!empty($import_specification[$section]["columns"][$colnames[$i]]["itype"])) {
							$wrong_data_type = true;
							$columns[$i] = "";

						# Image type not exist
						} else {
							$columns[$i] = $file_path;
						}

					}
				}

				if ($wrong_data_type && !$wrong_data_type_error) {
					func_import_error("msg_err_import_log_message_12", array("column" => strtoupper($colnames[$i])));
				}

				# EOL tag converting
				if ($col_type == 'S' && $import_specification[$section]["columns"][$colnames[$i]]["eol_safe"] && strpos($columns[$i], "<EOL>") !== false) {
					$columns[$i] = str_replace("<EOL>", "\n", $columns[$i]);
				}
			}

			# Set default value
			if (empty($columns[$i]) && isset($import_specification[$section]["columns"][$colnames[$i]]["default"])) {
				if ($import_specification[$section]["columns"][$colnames[$i]]['type'] == "D") {
					if (!empty($import_specification[$section]["columns"][$colnames[$i]]["default"])) {
						$tmp = strtotime($import_specification[$section]["columns"][$colnames[$i]]["default"]);
						if ($tmp != -1)
							$columns[$i] = $tmp;
					}
				} else {
					$columns[$i] = $import_specification[$section]["columns"][$colnames[$i]]["default"];
				}
			}

			# Check for required fields
			if (!empty($import_specification[$section]["columns"][$colnames[$i]]["required"]) && empty($columns[$i]) && !is_numeric($columns[$i]) && (!$is_subrow || $import_specification[$section]["columns"][$colnames[$i]]["array"])) {
				func_import_error("msg_err_import_log_message_7", array("column" => strtoupper($colnames[$i])));
			}

			if ($last_row_idx === false)
				$last_row_idx = $import_pass["line_index"];

			# Set value as subrow
			if ($import_specification[$section]["columns"][$colnames[$i]]["array"]) {
				if ($is_subrow) {
					$values[$colnames[$i]][] = $columns[$i];
				} else {
					$values[$colnames[$i]] = array($columns[$i]);
				}

			# Set value as string
			} elseif (!$is_subrow) {
				$values[$colnames[$i]] = $columns[$i];
			}

			# Save the original value from the current row
			# (is used within some import modules)
			$orig_values[$colnames[$i]] = $columns[$i];
		}
		$section_lines_counter++;

		$current_row[] = $orig_values;

	} # end while

	#
	# Close log file
	#
	fclose($logf);

	# Prepare the QUERY_STRING for returning...
	if (!empty($import_pass["error"])) {
		# Error occured - stop importing
		db_query("DELETE FROM $sql_tbl[import_cache] WHERE login = '".addslashes($login)."'");
		$query_str = "error=1";

	} elseif ($action == "do") {
		func_flush('<script type="text/javascript"><!-- loaded = true; --></script>');
		if ($import_pass['is_finalize']) {
			func_html_location("import.php?finalize", 1);
		} else {
			func_html_location("import.php?complete", 1);
		}

	} elseif ($action == "finalize") {
		# Import successfully completed
		$top_message["content"] = func_get_langvar_by_name("msg_data_import_success", false, false, false);
		$query_str = "complete";

	} elseif (empty($old_sections) && empty($action)) {
		# Import file hasn't any sections
		$top_message = array(
			"content" => func_get_langvar_by_name("msg_data_import_no_sections", false, false, false),
			"type" => "W"
		);
		$query_str = "complete";

	} elseif (!empty($section) && empty($colnames)) {
		# Empty section header
		func_import_error("msg_err_import_log_message_42", array("section" => $section));

	} elseif (!empty($section) && empty($section_lines_counter)) {
		# Empty section body
		func_import_error("msg_err_import_log_message_43", array("section" => $section));

	} else {
		# Continue processing
		$query_str = "action=do";
	}

	# Process finished - need to clear these variables
	$import_pass = $import_cache = array();

	func_flush('<script type="text/javascript"><!-- loaded = true; --></script>');
	func_html_location("import.php?".$query_str, 3);

#
# last step (after importing)
#
} elseif (isset($HTTP_GET_VARS['finalize']) && $REQUEST_METHOD == "GET") {

	func_display_service_header();

	func_flush("<b>".func_get_langvar_by_name("lbl_finalize_import_data",NULL,false,true)."</b><br />");

	if (!empty($import_pass['old_sections'])) {

		if (!($logf = @fopen($import_log, "a+"))) {
			$top_message["content"] = func_get_langvar_by_name("msg_err_import_log_writing");
			$top_message["type"] = "E";
			func_header_location("import.php");
		}

		foreach ($import_pass['old_sections'] as $section => $tmp) {
			if (empty($section))
				continue;
			$import_step = "complete";
			include $xcart_dir.$import_specification[$section]["script"];
		}

		fclose($logf);
	}
	db_query("DELETE FROM $sql_tbl[import_cache] WHERE login = '".addslashes($login)."'");
	$import_pass = $import_cache = array();

	$top_message["content"] = func_get_langvar_by_name("msg_data_import_success", false, false, false);
	func_flush('<script type="text/javascript"><!-- loaded = true; --></script>');
	func_html_location("import.php?complete", 3);

} elseif ($REQUEST_METHOD == "POST") {
	func_header_location("import.php");
}

#
# Delete uploaded file after it is processed
#
if (!empty($import_file["uploaded"]))
	@unlink($import_file["location"]);


$import_file = array();
$import_pass = array();
$import_cache = array();

x_session_save();

if ($fl = @fopen($import_log, "r")) {
# Prepare data about import log file for displaying
	$content = fread($fl, 16000);
	fclose($fl);
	$content = str_replace(X_LOG_SIGNATURE, "", $content);
	$content = htmlentities($content);
	$content = str_replace("\n", "<br />", $content);

	$smarty->assign("import_log_content", $content);
	$smarty->assign("import_log_url", $import_log_url);
}
$smarty->assign("import_log_file", $import_log);

if (!empty($error))
	$smarty->assign("show_error", 1);

include $xcart_dir."/include/categories.php";

foreach ($import_specification as $s => $v) {
	if (!empty($v['import_note']))
		$import_specification[$s]['import_note'] = func_get_langvar_by_name($v['import_note'], array(), false, true);
}

$smarty->assign("import_specification", $import_specification);
$smarty->assign("import_options", $import_options);
$smarty->assign("my_files_location",func_get_files_location());

if (!empty($import_data))
	$smarty->assign("import_data", $import_data);

$smarty->assign("upload_max_filesize", ini_get("upload_max_filesize"));
?>
