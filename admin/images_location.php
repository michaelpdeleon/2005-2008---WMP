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

# $Id: images_location.php,v 1.51.2.3 2006/07/19 06:25:18 max Exp $

# This script generates search engine friendly HTML catalog for X-cart

@set_time_limit(2700);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice','files','image');

$location[] = array(func_get_langvar_by_name("lbl_images_location"), "");

$logfile = "imglog.txt";
$logfile_name = $var_dirs["log"].DIRECTORY_SEPARATOR.$logfile;
# process N images per pass
$images_step = 50;

if ($mode == "viewlog") {

	if (file_exists($logfile_name)) {
		$incfile = func_file_get($logfile_name, true);
		$smarty->assign("incfile", $incfile);
	}

	func_display("admin/main/images_location_log.tpl",$smarty);
	func_html_location("images_location.php", 60);
	exit;
}

#
# Main code
#
if ($REQUEST_METHOD == "POST") {
	require $xcart_dir."/include/safe_mode.php";

	$display_service_header = false;

	$str_out = "";
	foreach ($data as $k => $v) {
		$k = stripslashes($k);
		if (!isset($config['available_images'][$k]) || defined("NO_CHANGE_LOCATION_".$k))
			continue;

		$config_data = $config['setup_images'][$k];

		if ($v['location'] != $config_data['location'] || $v['save_url'] != $config_data['save_url']) {
			if (!$display_service_header) {
				func_display_service_header("lbl_moving_images_");
				$display_service_header = true;
			}
			$str_out .= func_get_langvar_by_name("lbl_image_title_".$k, NULL, false, true)."\n".func_get_langvar_by_name("lbl_img_location")." ".$v['location']."\n\n";

			$v['location'] = stripslashes($v['location']);
			if (!func_move_images($k, $v)) {
				# cannot move, reuse old params
				$v['location'] = $config_data['location'];
				$str_out .= func_get_langvar_by_name("lbl_failed") . "\n";

			} else {
				$str_out .= func_get_langvar_by_name("lbl_success")."\n";
			}

			$str_out .= "\n";
		}

		# If checkboxes are unchecked
		$v['save_url'] = $v['save_url'];
		$v['md5_check'] = $v['md5_check'];

		$is_update = false;
		foreach ($config_data as $k2 => $v2) {
			if ($config_data[$k2] != $v[$k2]) {
				$is_update = true;
				break;
			}
		}

		if ($is_update) {
			$config_data = func_addslashes(func_array_merge($config_data, $v));
			$config_data['itype'] = $k;
			func_array2insert("setup_images", $config_data, true);
		}
	}

	if (
		isset($data["T"]["location"]) &&
		isset($config['setup_images']["T"]["location"]) &&
		$data["T"]["location"] != $config['setup_images']["T"]["location"]
	) {
		# necessary when moving thumbnails only
		func_build_quick_flags();
	}

	func_data_cache_get("setup_images", array(), true);

	if ($str_out) {
		#
		# Creating of log file
		#
		$str_out = "\n".str_repeat("*", 70)."\n\n".func_get_langvar_by_name("lbl_log_created")." ".date("Y-m-d H:i:s")."\n".$str_out;

		$fd = func_fopen($logfile_name, "a+", true);
		if ($fd) {
			fwrite($fd, $str_out);
			fclose($fd);
			@chmod($logfile_name, 0666);
			func_header_location("images_location.php?mode=viewlog");
		}
		else {
			echo func_get_langvar_by_name("txt_log_file_N_cant_be_created", array("logfile" => $logfile_name),false,true)."<p>\n";
			echo "<pre>".$str_out."</pre>";
			echo "<br /><br /><a href=\"images_location.php\">".func_get_langvar_by_name("lbl_continue", false, false, true)."</a>";
			exit();
		}
	}

	func_header_location("images_location.php");
}

if (file_exists($logfile_name)) {
	$smarty->assign("log_file", "images_location.php?mode=viewlog");
	$smarty->assign("log_file_date", filemtime($logfile_name));
}

foreach($config['available_images'] as $t => $u) {
	if (defined("NO_CHANGE_LOCATION_".$t))
		unset($config['available_images'][$t]);
}

foreach ($config['available_images'] as $k => $v) {
	$config['setup_images'][$k]['label'] =
		func_get_langvar_by_name("lbl_image_title_".$k,
			null,false,true);

	if (empty($config['setup_images'][$k]['label'])) {
		$config['setup_images'][$k]['label'] =
			func_get_langvar_by_name("lbl_image_title_",
				array("type" => $k));
	}

	$dialog_tools_data["left"][] = array(
		"link" => "#".$k,
		"title" => $config['setup_images'][$k]['label']
	);
}

$smarty->assign_by_ref("config", $config);
#
# Smarty display code goes here
#
$smarty->assign("xcart_dir",$xcart_dir);
$smarty->assign("main","images_location");

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
