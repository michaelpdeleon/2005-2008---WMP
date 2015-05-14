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
# $Id: patch.php,v 1.59 2006/03/30 12:23:04 max Exp $
#

@set_time_limit(1800);

define('USE_TRUSTED_SCRIPT_VARS',1);
define('USE_TRUSTED_POST_VARIABLES',1);
$trusted_post_variables = array("patch_query");

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files');

require $xcart_dir.DIR_ADMIN."/patch_sql.php";
require $xcart_dir."/include/patch.php";

$location[] = array(func_get_langvar_by_name("lbl_patch_upgrade_center"), "");

$upgrade_repository = $xcart_dir."/upgrade";
$patch_tmp_folder = $var_dirs["upgrade"];
$patch_logfile = "$patch_tmp_folder/patch.log";
$patch_reverse = @($reverse=="Y");

$ready_to_patch	= true;

$customer_files = array("auth.php","cart.php","change_password.php","download.php","error_message.php","featured_products.php","giftcert.php","help.php","home.php","https.php","manufacturers.php","minicart.php","news.php","order.php","orders.php","pages.php","popup_info.php","popup_poptions.php","process_order.php","product.php","products.php","recommends.php","referer.php","register.php","search.php","secure_login.php","send_to_friend.php","vote.php");
# for X-GiftRegistry
$customer_files[] = "giftreg_manage.php";
$customer_files[] = "giftregs.php";
# for X-Configurator
$customer_files[] = "pconf.php";
# for X-FeatureComparison
$customer_files[] = "choosing.php";
$customer_files[] = "comparison.php";
$customer_files[] = "comparison_list.php";
$customer_files[] = "popup_fc_products.php";
# for X-SpecialOffers
$customer_files[] = "bonuses.php";
$customer_files[] = "offers.php";
# for X-RMA
$customer_files[] = "returns.php";

$smarty->assign('xcart_http_host', $xcart_http_host);

if ($REQUEST_METHOD=="POST") {
	require $xcart_dir."/include/safe_mode.php";
	#
	#  Patch by file upload
	#
	if (($patch_file!="none")&&($patch_file!="")) {
		move_uploaded_file($patch_file, "$file_temp_dir/$patch_file_name");
		$patch_filename=$patch_file_name;
	}
	elseif ($patch_url!="") {
		#
		# Patch is downloaded from URL
		#
		if ($patch_lines = @func_file($patch_url)) {
			#
			# Write file to $file_temp_dir
			#
			$parsed_url = parse_url($patch_url);
			$patch_filename = basename($parsed_url["path"]);

			$fw = fopen("$file_temp_dir/$patch_filename","w");
			foreach($patch_lines as $patch_line)
				fputs($fw, $patch_line);

			fclose($fw);
		}
		else {
			func_header_location("error_message.php?cant_open_file");
		}
	}
	elseif ($patch_query) {
		#
		# Save custom queries into file
		#

		$patch_filename="query".time();

		$fw = fopen("$file_temp_dir/$patch_filename","w");
		fputs($fw, stripslashes($patch_query));
		fclose($fw);
	}

	#
	# Perform upgrade
	#
	if ($mode=="upgrade" && $patch_filename) {
		$target_version = $patch_filename;

		$patch_tmp_folder = $var_dirs["upgrade"]."/".$target_version;
		$patch_logfile = "$patch_tmp_folder/patch.log";

		if (!is_dir($patch_tmp_folder) && !func_mkdir($patch_tmp_folder, 0777)) {
			$top_message['type'] = 'E';
			$top_message['content'] = func_get_langvar_by_name("lbl_failed_to_create_work_dir", array("dir" => $patch_tmp_folder));
			func_header_location('patch.php');
		}

		#
		# Read all .diff files from $upgrade_repository/$target_version
		#

		#
		# target version is stored in patch_filename variable
		#
		$patch_files_lst    = "$upgrade_repository/$target_version/file.lst";
		$integrity_result   = array();
		$files_to_patch     = array();
		$all_files_to_patch = array();
		$patch_lines        = array();

		#
		# Extract patch_files array from temporaly storage
		#
		func_restore_phase_result(true);
		$patch_files = isset($phase_result["patch_files"]) ? $phase_result["patch_files"] : "";
		$phase_result = "";

		if (!isset($patch_files) || empty($patch_files) || $confirmed != 'Y') {
			#
			# Prepare patch files list and do all necessary tests
			#
			$patch_files = array();

			if ($_patch_files = @file($patch_files_lst)) {
				$result = func_correct_files_lst($_patch_files, $upgrade_repository.'/'.$target_version);
				if ($result !== true) {
					$top_message['type'] = 'E';
					$top_message['content'] = implode('<br /><br />',$result);
					func_header_location('patch.php');
				}

				$patch_files = func_test_patch($_patch_files);
			}
		}

		$patch_lines[] = "\n\n --- ".func_get_langvar_by_name("txt_skipped_see_diff_files", NULL, false, true)." ---";

		$phase_result["patch_text"] = htmlspecialchars(implode("",$patch_lines));
		$phase_result["patch_filename"] = $target_version;
		$phase_result["patch_type"] = "upgrade";
		$phase_result["patch_files"] = $patch_files;
		$phase_result["ready_to_patch"] = $ready_to_patch;
		$phase_result["could_not_patch"] = $could_not_patch;
		$phase_result["all_files_to_patch"] = 1;
		$phase_result["mode"] = $mode;
		$phase_result["confirmed"] = $confirmed;
		$phase_result["patch_exe"] = $patch_exe;

		if ($confirmed) {
			#
			# Apply upgrade patches
			#
			if (is_dir($patch_tmp_folder))
				require $xcart_dir.DIR_ADMIN."/upgrade.php";

			#
			# Log patch activity
			#
			if (!@is_link($patch_logfile) && $LOG = fopen($patch_logfile, "a+")) {
				fputs($LOG, strftime("%T %D ", time()) . str_repeat("=", 5) . " START " . str_repeat("=", 25) . "\n");

				fputs($LOG, "PATCH FILES\n");
				foreach ($patch_files as $patch_file_info) {
					fputs($LOG, $patch_file_info["orig_file"] . " ... [" . $patch_file_info["status"] . "]\n");
				}

				fputs($LOG, "\nPATCH RESULTS\n");
				foreach ($patch_result as $patch_resul_str) {
					fputs($LOG, $patch_resul_str . "\n");
				}

				fputs($LOG, "\nPATCH LOG\n");
				foreach ($patch_log as $patch_log_str) {
					fputs($LOG, $patch_log_str . "\n");
				}

				fputs($LOG, str_repeat("=", 25) . " END " . str_repeat("=", 25) . "\n");
				fclose($LOG);
			}
		}

		func_store_phase_result();
	}
	elseif ($patch_filename && $mode=="normal") {
		if (!is_dir($patch_tmp_folder) && !func_mkdir($patch_tmp_folder, 0777)) {
			$top_message['type'] = 'E';
			$top_message['content'] = func_get_langvar_by_name("lbl_failed_to_create_work_dir", array("dir" => $patch_tmp_folder));
			func_header_location('patch.php');
		}

		#
		# Generate 'files to patch' list
		# A list of files needed to be patch but are not writeable
		#

		#
		# Extract patch_files array from temporaly storage
		#
		func_restore_phase_result(true);
		if (isset($phase_result["patch_files"]))
			$patch_files = $phase_result["patch_files"];
		else
			$patch_files = "";

		$phase_result = "";

		$patch_realfile = $file_temp_dir.DIRECTORY_SEPARATOR.$patch_filename;
		if (!is_array($patch_files) || empty($patch_files) || $confirmed != 'Y') {
			$patch_lines = func_file($patch_realfile, true);
			$phase_result["patch_text"] = htmlspecialchars(implode("",$patch_lines));

			if (!empty($patch_files)) remove_tmp_files($patch_files);

			$patch_files = func_test_patch($patch_lines, false);
		}

		$phase_result["patch_filename"] = $patch_filename;
		$phase_result["patch_type"] = "text";
		$phase_result["patch_files"] = $patch_files;
		$phase_result["ready_to_patch"] = $ready_to_patch;
		$phase_result["could_not_patch"] = $could_not_patch;
		$phase_result["all_files_to_patch"] = 1;
		$phase_result["mode"] = $mode;
		$phase_result["confirmed"] = $confirmed;
		$phase_result["reverse"] = $reverse;
		$phase_result["patch_exe"] = $patch_exe;

		if ($confirmed) {
			#
			# Apply patch
			#
			$patch_result = array();

			if (is_dir($patch_tmp_folder)) {
				$patch_result    = array();
				$patch_log       = array();
				$sql_errorcode   = 1;
				$patch_errorcode = 1;

				func_auto_scroll(func_get_langvar_by_name("txt_applying_patch_wait", NULL, false, true)."<hr />\n");

				require $xcart_dir.DIR_ADMIN."/patch_files.php";
				$patch_completed = $patch_errorcode;
			}

			@unlink("$file_temp_dir/$patch_filename");

			$phase_result["patched_files"]	= $patched_files;
			$phase_result["excluded_files"] = $excluded_files;
			$phase_result["patch_log"]		= $patch_log;
			$phase_result["patch_phase"]	= "upgrade_final";
			$phase_result["patch_result"]	= $patch_result;
			$phase_result["patch_completed"]= $patch_completed;
		}

		func_store_phase_result();
	}
	elseif ($patch_filename && $mode=="sql") {
		$confirmed = "Y";
		$patch_lines = func_file("$file_temp_dir/$patch_filename", true);

		$phase_result["patch_text"] = htmlspecialchars(implode("",$patch_lines));
		$phase_result["patch_filename"] = $patch_filename;
		$phase_result["patch_type"] = "sql";
		$phase_result["ready_to_patch"] = 1;
		$phase_result["mode"] = $mode;
		$phase_result["all_files_to_patch"] = 1;
		$phase_result["confirmed"] = $confirmed;

		if ($confirmed) {
			#
			# Apply SQL patch
			#
			$patch_result = array();

			$sql_error = ExecuteSqlQuery(implode("",$patch_lines));
			#
			# Generate Result text
			#
			if (!empty($sql_error)) {
				$patch_result[] = "<font color=\"red\">".func_get_langvar_by_name("lbl_sql_patch_failed_at_query", NULL, false, true).":</font>";
				$patch_result[] = $sql_error;
				$patch_completed = false;
				@unlink("$file_temp_dir/$patch_filename");
			}
			else {
				$patch_result[] = func_get_langvar_by_name("txt_db_successfully_patched", NULL, false, true);
				$patch_completed = true;
			}

			$phase_result["patch_phase"]	= "upgrade_final";
			$phase_result["patch_result"]	= $patch_result;
			$phase_result["patch_completed"]= $patch_completed;
		}

		func_store_phase_result();
	}
}
elseif ($REQUEST_METHOD=="GET" && $mode=="result") {
	func_restore_phase_result();

	if (is_array($phase_result)) {
		foreach ($phase_result as $key=>$val)
			$smarty->assign($key,$val);
	}
}
else {
	func_restore_phase_result(true);
	if (is_array($phase_result["patch_files"]) && $phase_result["patch_type"]=="normal")
		remove_tmp_files($phase_result["patch_files"]);
}

#
# Get the list of target versions available in ./upgrade
#

if ($dir = @opendir($upgrade_repository)) {
	while (($file = readdir($dir)) !== false) {
		if (!is_dir($upgrade_repository.DIRECTORY_SEPARATOR.$file) || $file=="." || $file=="..")
			continue;

		$file = strtr($file, '_', ' ');
		list($source_version,$target_version) = explode("-",$file,2);

		if ($config["version"]==$source_version)
			$target_versions[]=$target_version;
	}

	closedir($dir);
}

$smarty->assign("main","patch");
$smarty->assign("target_versions",$target_versions);

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
