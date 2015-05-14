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
# $Id: modules.php,v 1.39.2.3 2006/07/06 07:25:33 xcart Exp $
#
# For explanation of modules please refer to X-Cart developer's documentation
#

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_modules"), "");

#
# Generate module files array
#

if ($REQUEST_METHOD=="POST") {
	require $xcart_dir."/include/safe_mode.php";

#
# Disable all module files, then enable certain
#
	db_query("update $sql_tbl[modules] set active='N'");
	
	$old_mode = (!empty($active_modules['Simple_Mode']) ? 'Y' : 'N');
	$new_mode = 'N';

	$redirect = "";
	foreach ($HTTP_POST_VARS as $module_name => $val) {
		if ($val=="on") {
			db_query("update $sql_tbl[modules] set active='Y' where module_name='$module_name'");
			if ($module_name == 'Simple_Mode') {
				$new_mode = 'Y';
			} elseif ($module_name == "Fancy_Categories" && empty($active_modules['Fancy_Categories'])) {
				$redirect = "fc_rebuild";
			} elseif ($module_name == "Product_Options" && empty($active_modules['Product_Options'])) {
				$redirect = "cache_rebuild";
			}
		}
	}

	if (!empty($active_modules['Product_Options']) && func_query_first_cell("SELECT active FROM $sql_tbl[modules] WHERE module_name='Product_Options'") != 'Y') {
		$redirect = "cache_rebuild";
	}

	x_session_register("identifiers",array());
	$tmp = func_query_first_cell("SELECT active FROM $sql_tbl[modules] WHERE module_name='Simple_Mode'");
	if ($new_mode != $old_mode && $tmp!==false) {
		if ($new_mode == 'Y') {
			$identifiers['P'] = $identifiers['A'];
		}
		else {
			func_unset($identifiers, 'P');
		}
	}
	func_data_cache_get("modules", array(), true);

	if (!empty($redirect))
		func_header_location("modules.php?mode=".$redirect);
		
	func_header_location("modules.php");

} elseif ($mode == "fc_rebuild") {
	if (!empty($active_modules['Fancy_Categories']) && func_fc_check_rebuild())
		func_fc_build_categories(false, 10);
	
	func_header_location("modules.php");

} elseif ($mode == "cache_rebuild") {
	func_build_quick_prices(false, 10);
	func_flush("<br />");
	func_build_quick_flags(false, 10);
	
	func_header_location("modules.php");
}
#
# Generate modules list
#
$modules = func_query("SELECT * FROM $sql_tbl[modules] ORDER BY module_name");
$mod_options = func_query_column("SELECT $sql_tbl[modules].module_name FROM $sql_tbl[modules], $sql_tbl[config] WHERE $sql_tbl[modules].module_name=$sql_tbl[config].category GROUP BY $sql_tbl[modules].module_name", "module_name");

if (is_array($modules)) {
	$force_rebuild = false;
	foreach ($modules as $k => $v) {
		if ((!empty($active_modules[$v['module_name']]) && $v['active'] != 'Y') || (empty($active_modules[$v['module_name']]) && $v['active'] == 'Y')) {
			if ($v['active'] == 'Y') {
				$active_modules[$v['module_name']] = true;
			} else {
				$modules[$k]['active'] = 'Y';
			}
			$force_rebuild = true;
		}

		if (in_array($v["module_name"], $mod_options)) {
			if ($v["module_name"] == "UPS_OnLine_Tools")
				$modules[$k]["options_url"] = "ups.php";
			else
				$modules[$k]["options_url"] = "configuration.php?option=".addslashes($v["module_name"]);
		}
		$predefined_lng_variables[] = "module_descr_".$v["module_name"];
		$predefined_lng_variables[] = "module_name_".$v["module_name"];
		$tmp = func_get_langvar_by_name("module_name_".$v["module_name"], NULL, false, true);
		$modules[$k]['true_name'] = (empty($tmp) ? $v["module_name"] : $tmp);
	}

function func_sort_modules($a, $b) {
	return strcmp($a['true_name'], $b['true_name']);
}

	usort($modules, "func_sort_modules");	

	if ($force_rebuild) {
		func_data_cache_get("modules", array(), true);
	}
}


$smarty->assign("modules",$modules);
$smarty->assign("main","modules");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
