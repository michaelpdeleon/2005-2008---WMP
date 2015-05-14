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

# $Id: pages.php,v 1.30.2.1 2006/04/25 11:28:29 svowl Exp $

# This script allow to create static html pages within  X-Cart

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);
$trusted_post_variables = array("pagecontent");

define("IS_MULTILANGUAGE", 1);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files');

$location[] = array(func_get_langvar_by_name("lbl_static_pages"), "");

function func_pages_dir($level) {
	global $xcart_dir, $smarty, $current_language;

	if ($level == "R")
		$pages_dir = $xcart_dir.DIRECTORY_SEPARATOR;
	else {
		if (!is_dir($smarty->template_dir.DIRECTORY_SEPARATOR."pages")) {
			@mkdir($smarty->template_dir.DIRECTORY_SEPARATOR."pages", 0777);
		}
		$pages_dir = $smarty->template_dir.DIRECTORY_SEPARATOR."pages".DIRECTORY_SEPARATOR.$current_language.DIRECTORY_SEPARATOR;
	}
	return $pages_dir;
}


$pageid = intval($pageid);

if ($REQUEST_METHOD == "POST") {
#
# Process the POST request
#
	require $xcart_dir."/include/safe_mode.php";

    if ($mode == "delete") {
	#
	# Delete selected pages
	#

		if (is_array($posted_data)) {
			$deleted = false;
			foreach($posted_data as $pageid=>$v) {
				$page_data = func_query_first("SELECT * FROM $sql_tbl[pages] WHERE pageid='$pageid' AND level = '$sec'");
				if (!empty($page_data) && !empty($v["to_delete"])) {
					@unlink(func_pages_dir($page_data["level"]).$page_data["filename"]);
					db_query("DELETE FROM $sql_tbl[pages] WHERE pageid='$pageid'");
					$deleted = true;
				}
			}
			$top_message["content"] = func_get_langvar_by_name("msg_adm_pages_del");
		}
		func_header_location("pages.php");
	}

	if ($mode == "update") {
	#
	# Update pages list
	#
		if (is_array($posted_data)) {
			foreach($posted_data as $pageid=>$v) {
				db_query("UPDATE $sql_tbl[pages] SET orderby='".intval($v["orderby"])."', active='$v[active]' WHERE pageid='$pageid' AND level = '$sec'");
			}
		}

		if ($parse_smarty_tags != "Y")
			$parse_smarty_tags = "N";

		db_query("UPDATE $sql_tbl[config] SET value='$parse_smarty_tags' WHERE name='parse_smarty_tags' AND category='General'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_pages_upd");
	}

	if ($mode == "modified") {
	#
	# Save created/modified page
	#

		$fillerr = (empty($pagetitle) || empty($pagecontent) || !in_array($active,array("Y","N")));
		if (!$fillerr) {
			$pages_dir = func_pages_dir($level);
			if (!is_dir($pages_dir)) {
				@mkdir($pages_dir, 0777);
			}
			$pagetitle = htmlspecialchars($pagetitle);
			$orderby = intval($orderby);

			if (empty($pageid)) {
				$filename = $HTTP_POST_VARS["filename"];
			} else {
				$filename = func_query_first_cell("SELECT filename FROM $sql_tbl[pages] WHERE pageid='$pageid'");
				if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pages] WHERE pageid='$pageid' AND language = '$shop_language'") == 0) {
					$pageid = func_query_first_cell("SELECT pageid FROM $sql_tbl[pages] WHERE filename = '".addslashes($filename)."' AND language = '$shop_language'");
				}
			}

			if (empty($pageid) && file_exists($pages_dir.$filename)) {
				$top_message["content"] = func_get_langvar_by_name("msg_err_page_file_exists");
				$top_message["type"] = "E";
				func_header_location("pages.php");
			
			} elseif ($fd = @func_fopen($pages_dir.$filename, "w", true)) {
				fwrite($fd, stripslashes($pagecontent));
				fclose($fd);
			}
			else {
				$top_message["content"] = func_get_langvar_by_name("msg_err_file_permission_denied");
				$top_message["type"] = "E";
				func_header_location("pages.php");
			}
			if (empty($pageid)) {
				db_query("INSERT INTO $sql_tbl[pages] (filename, title, level, orderby, active, language) VALUES ('$filename', '$pagetitle', '$level', '$orderby', '$active', '$current_language')");
				$pageid = db_insert_id();
				$top_message["content"] = func_get_langvar_by_name("msg_adm_pages_add");
			}
			else {
				db_query("UPDATE $sql_tbl[pages] SET title='$pagetitle', orderby='$orderby', active='$active' WHERE pageid='$pageid'");
				$top_message["content"] = func_get_langvar_by_name("msg_adm_page_upd");
			}
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("err_filling_form");
			$top_message["type"] = "E";
		}
		func_header_location("pages.php?pageid=$pageid");
	}

	if ($mode == "check") {
	#
	# Find already existed static pages that is not encountered in the database
	#
		$languages = func_query("SELECT DISTINCT(code) FROM $sql_tbl[languages]");
		foreach($languages as $k=>$v)
			$dirs[] = $smarty->template_dir."/pages/".$v["code"];
		$dirs[] = $xcart_dir;

		foreach($dirs as $dir) {

			if ($dp = @opendir($dir)) {
				while ($file = readdir($dp)) {
					if (is_file($dir.DIRECTORY_SEPARATOR.$file) && (substr($file,-5,5)==".html" || substr($file,-4,4)==".htm")) {
						if ($dir == $xcart_dir)
							$root_pages[] = $dir.DIRECTORY_SEPARATOR.$file;
						else
							$embedded_pages[] = $dir.DIRECTORY_SEPARATOR.$file;
					}
				}
				closedir($dp);
			}
		}

		if (is_array($root_pages)) {
			$orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='R'");
			foreach ($root_pages as $k=>$file) {
				if (!preg_match("/^(.+)\/(.*)$/S", $file, $found))
					continue;
				$file = $found[2];

				if (!func_query_first("SELECT filename FROM $sql_tbl[pages] WHERE filename='$file' AND level='R'")) {
					$orderby += 10;
					db_query("INSERT INTO $sql_tbl[pages] (filename, title, level, orderby, active, language) VALUES ('$file', '".basename($file)."', 'R', '$orderby', 'Y', '$current_language')");
				}
			}
		}

		if (is_array($embedded_pages)) {
			$orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='E'");
			foreach ($embedded_pages as $k=>$file) {
				if (!preg_match("/^(.+)\/(.*)\/(.*)$/S", $file, $found))
					continue;
				$file = $found[3];
				$lang = $found[2];

				if (!func_query_first("SELECT filename FROM $sql_tbl[pages] WHERE filename='$file' AND level='E' AND language='$lang'")) {
					$orderby += 10;
					db_query("INSERT INTO $sql_tbl[pages] (filename, title, level, orderby, active, language) VALUES ('$file', '$file', 'E', '$orderby', 'Y', '$lang')");
				}
			}
		}
	}

	func_header_location("pages.php");

} # /if ($REQUEST_METHOD == "POST")


if (isset($HTTP_GET_VARS["pageid"])) {
#
# Prepare data for editing
#
	$page_data = func_query_first("SELECT * FROM $sql_tbl[pages] WHERE pageid='$pageid'");
	if (!empty($page_data) && $page_data['language'] != $shop_language) {
		$tmp = func_query_first("SELECT * FROM $sql_tbl[pages] WHERE filename = '".addslashes($page_data['filename'])."' AND language = '$shop_language'");
		if (!empty($tmp))
			$page_data = $tmp;
	}

	if ($page_data) {
		$pages_dir = func_pages_dir($page_data["level"]);
		$filename = $pages_dir.$page_data["filename"];
		if ($fd = func_fopen($filename, "r", true)) {
			$page_content = "";
			if (filesize($filename) > 0)
				$page_content = fread($fd, filesize($filename));
			fclose($fd);
		}
		else {
			$page_content = func_get_langvar_by_name("lbl_file_has_not_been_found", array(), false, true);
		}
		$level = $page_data["level"];
		$smarty->assign("page_path", $filename);
		$smarty->assign("page_data", $page_data);
		$smarty->assign("page_content", $page_content);
		$location[count($location)-1][1] = "pages.php";
		$location[] = array(func_get_langvar_by_name("lbl_edit_page"), "");
	}
	else {
		$pages_dir = func_pages_dir($HTTP_GET_VARS["level"]);
		$smarty->assign("page_path", $pages_dir);
		$flag = true;
		while($flag) {
			$index++;
			$default_filename = sprintf("page_%03d.html",$index);
			if (!file_exists($pages_dir.$default_filename))
				$flag = false;
		}
		$level = ($HTTP_GET_VARS["level"]=="E" || $HTTP_GET_VARS["level"]=="R" ? $HTTP_GET_VARS["level"] : "E");
		$smarty->assign("default_filename", $default_filename);
		$smarty->assign("default_index", $index);
		$default_orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='$level'");
		$smarty->assign("default_orderby", $default_orderby+10);
		$location[count($location)-1][1] = "pages.php";
		$location[] = array(func_get_langvar_by_name("lbl_create_page"), "");
	}

	$smarty->assign("level", $level);
	$smarty->assign("main", "page_edit");
}
else {
#
# Prepare data for pages list
#
	$pages = func_query("SELECT * FROM $sql_tbl[pages] WHERE language='$current_language' ORDER BY orderby, title");

	$smarty->assign("pages", $pages);
	$smarty->assign("main", "pages");
}

# Assign the current location line
$smarty->assign("location", $location);

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
