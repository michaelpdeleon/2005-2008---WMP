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
# $Id: file_operations.php,v 1.62.2.2 2006/08/04 06:24:16 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

$is_admin_logged = ($current_area=="A" || ($active_modules["Simple_Mode"] && $current_area=="P"));

function fo_local_log_add($operation, $op_status, $op_message=false) {
	global $login;
	global $REMOTE_ADDR;

	if ($op_message !== false)
		$op_message = trim($op_message);

	$message = sprintf("Login: %s\nIP: %s\nOperation: %s\nOperation status: %s%s",
		$login,
		$REMOTE_ADDR,
		$operation,
		($op_status?'success':'failure'),
		(!empty($op_message)?"\n".$op_message:"")
	);

	x_log_flag('log_file_operations', 'FILES', $message);
}

#
# This function generates a list of all files with ".tpl" extension
#
function list_all_templates ($dir, $parent_dir) {
	$all_files = array();

	if (!$handle = opendir($dir))
		return $all_files;

	while (($file = readdir($handle)) !== false) {
		if (is_file($dir.DIRECTORY_SEPARATOR.$file) && substr($file,-4,4) == ".tpl") {
			$all_files[$parent_dir.DIRECTORY_SEPARATOR.$file]="Q";
		}
		elseif (is_dir($dir.DIRECTORY_SEPARATOR.$file) && $file != "." && $file != "..") {
			$all_files=func_array_merge($all_files, list_all_templates ($dir.DIRECTORY_SEPARATOR.$file,$parent_dir.DIRECTORY_SEPARATOR.$file));
		}
	}

	closedir($handle);
	return $all_files;
}

function fo_get_search_dirs() {
	static $locations = null;
	global $templates_repository, $root_dir, $xcart_dir;

	if (!isset($locations)) {
		$param2dir = array (
			'color' => 'colors',
			'dingbats' => 'dingbats',
			'layout' => 'templates'
		);

		$file = $root_dir.'/.skin_descr';
		if (!file_exists($file)) return false;

		$data = file($file);
		$skin_descr = array();
		foreach ($data as $line) {
			$line = trim($line);
			list($key,$value) = explode('=',$line,2);
			$skin_descr[$key] = $value;
		}

		$locations = array();
		# reverse order - see install.php, module_install_dirs()
		foreach (array('layout','dingbats','color') as $param) {
			if (!isset($skin_descr[$param]) || strlen($skin_descr[$param])<=0)
				continue;

			$locations[] = $xcart_dir.'/schemes/'.$param2dir[$param].'/'.$skin_descr[$param];
		}

		$locations[] = $templates_repository;
	}

	return $locations;
}

function fo_find_source($tpl_file) {
	$locations = fo_get_search_dirs();

	if (empty($locations))
		return false;

	foreach ($locations as $dir) {
		if (file_exists($dir.$tpl_file)) {
			return $dir.$tpl_file;
		}
	}

	return false;
}

if (!empty($opener)) {
	$opener_str_end = "&opener=".$opener;
	$opener_str_begin = "?opener=".$opener;
}

if ($REQUEST_METHOD == "POST") {
	require $xcart_dir."/include/safe_mode.php";

	#
	# Process the POST request
	#

	# Validate some input variables passed via POST request
	if (isset($filename))
		$filename = trim($filename);

	if (isset($new_directory))
		$new_directory = trim($new_directory);

	if (isset($new_file))
		$new_file = trim($new_file);

	if ($mode=="save_file" && $is_admin_logged) {
		#
		# Save file (Edit templates section)
		#
		$path = func_allowed_path($root_dir, $root_dir.$filename);

		$op_status = false;
		if ($path === false || empty($filename) || !func_is_allowed_file($root_dir.$filename)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif ($fw = @fopen($path,"w")) {
			# Success
			$filebody = str_replace("\r", '', $filebody);
			fputs($fw, stripslashes($filebody));
			fclose($fw);
			$top_message["content"] = func_get_langvar_by_name("msg_file_saved");
			$op_status = true;
		}
		else {
			# File operation is failed
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
			$top_message["type"] = "E";
		}

		fo_local_log_add('save_file', $op_status, "Filename: $filename");

		func_header_location($action_script."?dir=$dir&file=$filename".$opener_str_end);
	}
	elseif ($mode=="restore" && $is_admin_logged) {
		#
		# This facility restores the corrupted template from the repository
		#
		if (!empty($filename))
			$path = func_allowed_path($root_dir, $root_dir.$filename);

		$op_status = false;

		if (empty($filename) || $path === false || empty($filename) || !func_is_allowed_file($root_dir.$filename)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif (!file_exists(fo_find_source($filename))) {
			# Source file is not found
			$top_message["content"] = func_get_langvar_by_name("msg_err_source_file_not_found");
			$top_message["type"] = "E";
		}
		else {
			if (!@copy(fo_find_source($filename), $path)) {
				# File operation is failed
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
				$top_message["type"] = "E";
			}
			else {
				# Success
				$top_message["content"] = func_get_langvar_by_name("msg_template_restored");
				$op_status = true;
			}
		}

		fo_local_log_add('restore', $op_status, "Filename: $filename");

		func_header_location($action_script."?dir=$dir&file=$filename".$opener_str_end);
	}
	elseif ($mode=="restore_all" && $is_admin_logged) {
		#
		# Restore all files from $template_repository
		#
		$locations = fo_get_search_dirs();
		$files_to_restore = array();
		if (!empty($locations)) {
			foreach ($locations as $search_location) {
				$tmp = list_all_templates($search_location,"");
				$files_to_restore =
					func_array_merge($files_to_restore, $tmp);
				unset($tmp);
			}
		}

		if (!empty($files_to_restore)) {

print <<<JSCODE
<script language="javascript">
    loaded = false;
    finished = false;

    function refresh() {
        window.scroll(0, 100000);

        if (finished = false && loaded == false)
            setTimeout('refresh()', 1000);
    }

    setTimeout('refresh()', 1000);

</script>
JSCODE;

			$op_status = true;
			$op_msg_lines = array();

			foreach ($files_to_restore as $file_to_restore => $file_status) {

				echo func_get_langvar_by_name("lbl_restoring_n", array("file" => $file_to_restore),false,true)." - ";
				$op_line = func_get_langvar_by_name("lbl_restoring_n", array("file" => $file_to_restore),false,true)." - ";

				if (!@copy(fo_find_source($file_to_restore), $root_dir.$file_to_restore)) {
					echo "<b><font color='red'>".func_get_langvar_by_name("lbl_failed_to_restore",false,false,true)."</font></b>";
					$op_line .= func_get_langvar_by_name("lbl_failed_to_restore",false,false,true);
					$op_status = false;
				}
				else {
					echo "<font color='green'>".func_get_langvar_by_name("lbl_ok",false,false,true)."</font>";
					$op_line .= func_get_langvar_by_name("lbl_ok",false,false,true);
				}

				echo "<br />\n";
				func_flush();

				$op_msg_lines[] = $op_line;
			}

print <<<JSCODE2
<script language="javascript">

	finished = true;

</script>
JSCODE2;

			$top_message["content"] = func_get_langvar_by_name("msg_templates_restored");

			$op_message = "----\n".implode("\n", $op_msg_lines);
			fo_local_log_add('restore_all', $op_status, $op_message);

			func_html_location($action_script, 30);
			exit;
		}
		else {
			# Templates repository is not found
			$top_message["content"] = func_get_langvar_by_name("msg_err_repository_not_found");
			$top_message["type"] = "E";

			fo_local_log_add('restore_all', false, func_get_langvar_by_name("msg_err_repository_not_found",false,false,true));
		}

		func_header_location($action_script.$opener_str_begin);

	}
	elseif ($mode=="compile_all" && $is_admin_logged) {
		#
		# Compile all templates from $template_repository
		#
		@set_time_limit(1800);

		$files_to_restore = list_all_templates ($root_dir,"");

		if(!empty($files_to_restore)) {
			#
			# Generate search and replace arrays for preg_replace
			#
			$search_array = array();
			$search1_array = array();
			$replace_array = array();
			$replace1_array = array();
			$language_entries = func_query("SELECT name, value FROM $sql_tbl[languages] WHERE code='$language'");
			foreach ($language_entries as $language_entry) {
				$language_entry["value"] = str_replace("{{", "~~", str_replace("}}", "~~", $language_entry["value"]));
				$value_no_delim_inner = str_replace(array("{", "}"), array('`$ldelim`','`$rdelim`'), $language_entry["value"]);
				$value_no_delim = str_replace(array('`$ldelim`', '`$rdelim`'), array('{$ldelim}','{$rdelim}'), $value_no_delim_inner);

				$search_array[]="'{.lng\.$language_entry[name]}'S";
				$search1_array[]="'`.lng\.$language_entry[name]`'S";
				$search2_array[]="'.lng\.$language_entry[name](\W)'S";

				$replace_array[]=$value_no_delim;
				$replace1_array[]=$value_no_delim_inner;
				$replace2_array[]="\"".str_replace('"', '\"', $value_no_delim_inner)."\"\\1";
			}

			#
			# Perform compilation
			#
print <<<JSCODE
<script language="javascript">
    loaded = false;
	finished = false;

    function refresh() {
        window.scroll(0, 100000);

        if (finished = false && loaded == false)
            setTimeout('refresh()', 1000);
    }

    setTimeout('refresh()', 1000);
</script>
JSCODE;

			$op_status = true;
			$op_msg_lines = array();

			foreach ($files_to_restore as $file_to_restore => $file_status) {

				echo func_get_langvar_by_name("lbl_compiling_n", array("file" => $file_to_restore),false,true)." - ";
				$op_line = func_get_langvar_by_name("lbl_compiling_n", array("file" => $file_to_restore),false,true)." - ";

				if (is_writable($root_dir.$file_to_restore) && is_readable($root_dir.$file_to_restore)) {

					$file_strings = file($root_dir.$file_to_restore);
					$fp = fopen($root_dir.$file_to_restore, "w");

					#
					# Patching head.tpl to disable languages <select>
					#
					if ($file_to_restore == "/head.tpl") {
						if (rtrim($file_strings[0]) != "{assign var=\"all_languages_cnt\" value=0}")
							array_unshift($file_strings, "{assign var=\"all_languages_cnt\" value=0}\n");
					}

					$newfile_strings = preg_replace($search_array, $replace_array, $file_strings);
					$newfile_strings1 = preg_replace($search1_array, $replace1_array, $newfile_strings);
					$newfile_strings2 = preg_replace($search2_array, $replace2_array, $newfile_strings1);

					foreach ($newfile_strings2 as $newfile_string2)
						fputs($fp, $newfile_string2);

					echo "<font color='green'>".func_get_langvar_by_name("lbl_ok",false,false,true)."</font>";
					$op_line .= func_get_langvar_by_name("lbl_ok",false,false,true);
					fclose($fp);
				}
				else {
					echo "<b><font color='red'>".func_get_langvar_by_name("lbl_failed",false,false,true)."</font></b>";
					$op_line .= func_get_langvar_by_name("lbl_failed",false,false,true);
					$op_status = false;
				}

				echo "<br />\n";
				func_flush();
			}

print <<<JSCODE2
<script language="javascript">

	finished = true;

</script>
JSCODE2;

			$top_message["content"] = func_get_langvar_by_name("msg_templates_compiled");

			$op_message = "----\n".implode("\n", $op_msg_lines);
			fo_local_log_add('compile_all', $op_status, $op_message);

			func_html_location("file_edit.php",30);
			exit;
		}
		else {
			# Templates repository is not found
			$top_message["content"] = func_get_langvar_by_name("msg_err_repository_not_found");
			$top_message["type"] = "E";

			fo_local_log_add('compile_all', false, func_get_langvar_by_name("msg_err_repository_not_found",false,false,true));
		}

		func_header_location($action_script.$opener_str_begin);

	}
	elseif ($mode=="New directory") {
		#
		# Create new directory
		#
		if (!empty($new_directory))
			$path = func_allowed_path($root_dir, $root_dir.$dir.DIRECTORY_SEPARATOR.$new_directory);

		$op_status = false;

		if ($path === false || empty($new_directory)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif (is_dir($path)) {
			# Directory already exists
			$top_message["content"] = func_get_langvar_by_name("msg_err_dir_exists");
			$top_message["type"] = "E";
		}
		else {
			if (!@mkdir($path, 0775)) {
				# Creation of the directory is failed
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
				$top_message["type"] = "E";
			}
			else {
				# Success
				$top_message["content"] = func_get_langvar_by_name("msg_directory_created");
				$op_status = true;
			}
		}

		fo_local_log_add('New directory', $op_status, "Directory: ".$dir.DIRECTORY_SEPARATOR.$new_directory);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));
	}
	elseif ($mode=="New file" && $is_admin_logged) {
		#
		# Create new file (for 'Edit templates' section)
		#
		if (!empty($new_file))
			$path = func_allowed_path($root_dir, $root_dir.$dir.DIRECTORY_SEPARATOR.$new_file);

		$op_status = false;

		if ($path === false || empty($new_file) || !func_is_allowed_file($new_file)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif (file_exists ($path)) {
			# File already exists
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_exists");
			$top_message["type"] = "E";
		}
		else {
			if ($fw = @fopen($path, "w")) {
				# Success
				fclose ($fw);
				$top_message["content"] = func_get_langvar_by_name("msg_file_created");
				$op_status = true;
			}
			else {
				# Creation of the file is failed
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
				$top_message["type"] = "E";
			}
		}

		fo_local_log_add('New file', $op_status, "Directory: ".$dir.DIRECTORY_SEPARATOR.$new_file);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));

	}
	elseif ($mode=="Delete") {
		#
		# Delete selected file or directory
		#
		if (!empty($filename))
			$path = func_allowed_path($root_dir, $root_dir.$filename);

		$op_status = false;

		if ($path === false || empty($filename)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif ((file_exists ($path)) and (filetype ($path)=="file")) {
			if (!unlink ($path)) {
				# Deletion of the file is failed
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
				$top_message["type"] = "E";
			}
			else {
				$top_message["content"] = func_get_langvar_by_name("msg_file_deleted");
				$op_status = true;
			}
		}
		elseif (is_dir($path)){
			func_rm_dir($path);
			$top_message["content"] = func_get_langvar_by_name("msg_dir_deleted");
			$op_status = true;
		}
		else {
			# Deletion of the file is failed
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
			$top_message["type"] = "E";
		}

		fo_local_log_add('Delete', $op_status, "Directory/file: ".$filename);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));
	}
	elseif ($mode=="Upload") {
		#
		# Upload file
		#
		$path = func_allowed_path($root_dir, $root_dir.$dir.DIRECTORY_SEPARATOR.$userfile_name);

		$op_status = false;

		if ($path === false || !func_is_allowed_file($userfile_name)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		elseif (file_exists($path) && empty($rewrite_if_exists)) {
			# File already exists
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_exists");
			$top_message["type"] = "E";
		}
		else {
			if ($userfile_name == "none" || $userfile_name == "") {
				# No files to upload
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_upload");
				$top_message["type"] = "E";
			}
			else {
				if (!@move_uploaded_file ($userfile, $path)) {
					# File operation is failed
					$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
					$top_message["type"] = "E";
				}
				else {
					# Success
					$top_message["content"] = func_get_langvar_by_name("msg_file_uploaded");
					@chmod($path, 0666);
					$op_status = true;
				}
			}
		}

		fo_local_log_add('Upload', $op_status, "Filename: ".$userfile_name."\nTarget directory: ".$dir);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));

	}
	elseif ($mode=="Copy to") {
		#
		# COPY FILE
		#
		if (!empty($filename)) {
			$path = func_allowed_path($root_dir, $root_dir.$dir.'/'.$copy_file);
			$path_from = func_allowed_path($root_dir, $root_dir.$filename);
		}

		$op_status = false;

		if (empty($filename) || $path_from === false || $path === false || !func_is_allowed_file($copy_file) || file_exists ($path)) {
			# Path is not allowed or empty new dir name
			$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
			$top_message["type"] = "E";
		}
		else {
			if (!@copy ($path_from, $path)) {
				# File operation is failed
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_operation");
				$top_message["type"] = "E";
			}
			else {
				$top_message["content"] = func_get_langvar_by_name("msg_file_copied");
				$op_status = true;
			}
		}

		fo_local_log_add('Copy to', $op_status, "Filename: ".$filename."\nTarget filename: ".$dir.'/'.$copy_file);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));
	}
} # /if ($REQUEST_METHOD == "POST")

#
# Process GET-request
#

if (!empty($file)) {
	#
	# Edit file mode
	#
	$path = func_allowed_path($root_dir, $root_dir.$file);

	if ($path === false || empty($file)) {
		# Path is not allowed or empty new dir name
		$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
		$top_message["type"] = "E";

		fo_local_log_add('Open file', false, "Filename: ".$file);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));

	} elseif (!is_readable($path)) {
		# Permission denied
		$top_message["content"] = func_get_langvar_by_name("msg_err_file_read_permission_denied");
		$top_message["type"] = "E";

		fo_local_log_add('Open file', false, "Filename: ".$file);

		func_header_location($action_script.(!empty($dir) ? "?dir=$dir".$opener_str_end : $opener_str_begin));

	} else {
		$op_status = true;
		if (@getimagesize($path)) {
			$smarty->assign("file_type", "image");
		}
		else {
			$smarty->assign("filebody", file($path));
		}
	}

	$smarty->assign("filename", $file);
	$smarty->assign("main","edit_file");
}
else {
	#
	# Browse directory tree mode
	#
	$maindir = func_allowed_path($root_dir, $root_dir.$dir);
	if ($maindir === false) $maindir = $root_dir;

	if ($dh = @opendir($maindir)) {
		while (($file = readdir($dh))!==false) {
			if ($file=="." || preg_match("/^\.[^.]/S",$file))
				continue;

			$dir_entries[] = array (
				"file" => $file,
				"href" => ($file==".." ? ereg_replace("\/[^\/]*$","",$dir):"$dir/$file"),
				"filetype" => @filetype($maindir.DIRECTORY_SEPARATOR.$file)
			);
		}

		function myfilesortfunc($a,$b) {
			return strcasecmp($a["filetype"], $b["filetype"]) * 1000 + strcasecmp($a["file"], $b["file"]);
		}

		usort ($dir_entries, "myfilesortfunc");
		closedir($dh);
	}

	$smarty->assign("root_dir", $root_dir);
	$smarty->assign("dir_entries",$dir_entries);
	$smarty->assign("dir_entries_half",(int) (sizeof($dir_entries)/2));
	$smarty->assign("main","edit_dir");
	$smarty->assign("is_writeable", @is_writable($root_dir));
}

$smarty->assign("upload_max_filesize", ini_get("upload_max_filesize"));

?>
