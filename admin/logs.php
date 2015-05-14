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
# $Id: logs.php,v 1.11 2006/01/11 06:55:57 mclap Exp $
#
# Shop logs
#

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_shop_logs"), "logs.php");

function logs_convert_date($posted_data) {
	$start_date = false;
	$end_date = time();

	switch ($posted_data['date_period']) {
		case 'D': # Today
			$start_date = time();
			break;

		case 'W': # This week
			$first_weekday = $end_date - (date("w",$end_date) * 86400);
			$start_date = mktime(0,0,0,date("n",$first_weekday),date("j",$first_weekday),date("Y",$first_weekday));
			break;

		case 'M': # This month
			$start_date = mktime(0,0,0,date("n",$end_date),1,date("Y",$end_date));
			break;

		case 'C': # Custom range
			$start_date = $posted_data['start_date'];
			$end_date = $posted_data['end_date'];
			break;
	}

	return array($start_date, $end_date);
}

#
# Log names translation
#
$log_labels = x_log_get_names();

x_session_register('logs_search_data');

if ($REQUEST_METHOD != 'POST')
	$posted_data = $logs_search_data;

if ($REQUEST_METHOD == 'POST' && !empty($posted_data)) {

	$need_advanced_options = false;
	foreach ($posted_data as $k=>$v) {
		if (!is_array($v) && !is_numeric($v))
			$posted_data[$k] = stripslashes($v);

		if (is_array($v)) {
			$tmp = array();
			foreach ($v as $k1=>$v1) {
				$tmp[$v1] = 1;
			}
			$posted_data[$k] = $tmp;
		}
	}

	if (empty($posted_data['logs'])) {
		$posted_data['logs'] = false;
	}

	if ($StartMonth) {
			$posted_data['start_date'] = mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
			$posted_data['end_date'] = mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
	}

	$logs_search_data = $posted_data;

	if ($mode == "clean") {
		list($start_date, $end_date) = logs_convert_date($posted_data);
		$labels = array();
		if (!empty($posted_data['logs']) && is_array($posted_data['logs']))
			$labels = array_keys($posted_data['logs']);

		$error_files = array();
		$_tmp = x_log_list_files($labels, $start_date, $end_date);
		if (is_array($_tmp)) {
			foreach ($_tmp as $l=>$d) {
				foreach ($d as $ts=>$file) {
					$file = $var_dirs["log"].'/'.$file;
					if (@unlink($file) === true && ini_get('error_log') !== $file)
						$error_files[] = $file;
				}
			}
		}

		if (!empty($error_files)) {
			$top_message['type'] = 'E';
			$top_message['content'] = func_get_langvar_by_name('err_files_delete_perms', array('files'=>implode('<br />',$error_files)));
		}
		else {
			$top_message['type'] = 'I';
			$top_message['content'] = func_get_langvar_by_name('msg_logs_deleted_ok');
		}
	}

	func_header_location('logs.php');
}

if (!empty($posted_data)) {
	if (!isset($posted_data['date_period'])) $posted_data['date_period'] = 'D';

	if (!isset($posted_data['count']) || (int)$posted_data['count'] < 0)
		$posted_data['count'] = 0;
	else
		$posted_data['count'] = (int)$posted_data['count'];

	$logs_search_data = $posted_data;

	list($start_date, $end_date) = logs_convert_date($posted_data);

	$logs_data = "";
	$labels = array();

	if (!empty($posted_data['logs']) && is_array($posted_data['logs']))
		$labels = array_keys($posted_data['logs']);

	$_tmp = x_log_get_contents($labels, $start_date, $end_date, true, $posted_data['count']);
	if (is_array($_tmp) && !empty($_tmp)) {
		foreach ($_tmp as $label=>$_data) {
			$dialog_tools_data['left'][] = array("link" => '#result_'.$label, 'title' => (!empty($log_labels[$label]) ? $log_labels[$label] : $label));
		}
		$logs_data = $_tmp;
	}

	$smarty->assign("show_results", 1);
}
else {
	$posted_data = array();
	$posted_data['date_period'] = 'D';

	foreach ($log_labels as $k=>$v) {
		$posted_data['logs'][$k] = 1;
	}

	$posted_data['count'] = 5;
}

$dialog_tools_data['right'][] = array("link" => 'configuration.php?option=Logging', 'title' => func_get_langvar_by_name('option_title_Logging'));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/general.php", "title" => func_get_langvar_by_name("lbl_summary"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/tools.php", "title" => func_get_langvar_by_name("lbl_tools"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/snapshots.php", "title" => func_get_langvar_by_name("lbl_snapshots"));

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

$smarty->assign("log_labels", $log_labels);
$smarty->assign("search_prefilled", $posted_data);
$smarty->assign("logs", $logs_data);
$smarty->assign("location", $location);
$smarty->assign("main", "logs");

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
