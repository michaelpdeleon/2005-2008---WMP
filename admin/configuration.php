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
# $Id: configuration.php,v 1.81.2.2 2006/06/27 05:51:09 svowl Exp $
#

define('USE_TRUSTED_POST_VARIABLES',1);
$trusted_post_variables = array("gpg_key", "pgp_key");

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice','mail','order');

$options = func_query_column("SELECT category FROM $sql_tbl[config] WHERE category NOT IN ('UPS_OnLine_Tools', 'Taxes') AND category != '' GROUP BY category");
$disabled_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules] WHERE active != 'Y'");
if (!empty($disabled_modules)) {
	foreach ($disabled_modules as $mn) {
		if (in_array($mn, $options) && !in_array($mn, array_keys($active_modules))) {
			func_unset($options, array_search($mn, $options));
		}
	}
}
$modules_detected = false;
foreach ($options as $on) {
	if (!empty($active_modules[$on])) {
		$modules_detected = true;
		break;
	}
}

if (!in_array($option, $options)) {
	$option = "General";
}

require $xcart_dir."/include/countries.php";
require $xcart_dir."/include/states.php";
#
# Update configuration variables
# these variables are for internal use in PHP scripts
#

$location[] = array(func_get_langvar_by_name("lbl_general_settings"), "configuration.php");

if ($REQUEST_METHOD=="POST") {
	require $xcart_dir."/include/safe_mode.php";
}

if ($option == "User_Profiles") {
	include "./user_profiles.php";
}
elseif ($option == "Contact_Us") {
    include "./contact_us_profiles.php";
}
elseif ($option == "Search_products") {
    include "./search_products_form.php";
}
elseif ($REQUEST_METHOD=="POST") {
	func_array2update("config", array("value" => "N"), "type IN ('checkbox','multiselector') AND category='".$option."'");

	$var_properties = func_query_hash("SELECT name, type FROM $sql_tbl[config] WHERE category='$option'", "name", false, true);

	$section_data = array();
	foreach ($HTTP_POST_VARS as $key => $val) {
		if ($key == "periodic_logs") {
			if (!is_array($val)) {
				$val = '';
			}
			else {
				$val = implode(',',$val);
			}
		}

		if (isset($var_properties[$key])) {
			if ($var_properties[$key] == "numeric") {
				$val = doubleval(func_convert_numeric($val));

			}
			elseif ($var_properties[$key] == "multiselector") {
				$val = implode(";", $val);
			}
			elseif ($var_properties[$key] == "checkbox" && $val=="on") {
				$val = "Y";
			}

			func_array2update("config", array("value" => $val), "name='".$key."' AND category='".$option."'");
			$section_data[stripslashes($key)] = stripslashes($val);
		}
	}

	# Checking whether Blowfish encryption of order details using Merchant key is enabled
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[config] WHERE name = 'blowfish_enabled' AND category='$option'")) {
		$new_value = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = 'blowfish_enabled' AND category='$option'");
		if ($new_value != $config['Security']['blowfish_enabled']) {
			if ($new_value == 'Y') {
				if (empty($config['mpassword'])) {
					db_query("UPDATE $sql_tbl[config] SET value='".$config['Security']['blowfish_enabled']."' where name='blowfish_enabled' AND category='$option'");
					func_header_location($xcart_catalogs['admin']."/change_mpassword.php?from_config=".$option);
				}
				else {
					func_data_recrypt();
				}
			}
			elseif ($new_value != 'Y') {
				if ($merchant_password) {
					func_data_decrypt();
					$merchant_password = '';
				}
				else {
					db_query("UPDATE $sql_tbl[config] SET value='".$config['Security']['blowfish_enabled']."' WHERE name='blowfish_enabled' AND category='$option'");
				}
			}
		}
	}

	#
	# Apply default values to "empty" fields
	#
	db_query("UPDATE $sql_tbl[config] SET value = defvalue WHERE TRIM(value) = ''");

	if (!empty($active_modules['Fancy_Categories'])) {
		include $xcart_dir."/modules/Fancy_Categories/admin_config.php";
	}

	if ($option == "Security") {
		func_pgp_remove_key();
		$config[$option] = $section_data; # no code after func_pgp_add_key() using these settings
		func_pgp_add_key();
	}

	func_header_location("configuration.php?option=$option");
}

#
# Select default options tab
#
if ($option == "Appearance") {
	$date_formats = array(
		"%d-%m-%Y",
		"%d/%m/%Y",
		"%d.%m.%Y",
		"%m-%d-%Y",
		"%m/%d/%Y",
		"%Y-%m-%d",
		"%b %e, %Y",
		"%A, %B %e, %Y");
	$time_formats = array(
		"",
		"%H:%M:%S",
		"%H.%M.%S",
		"%I:%M:%S %p");

	$smarty->assign("gmnow", time()+$config["Appearance"]["timezone_offset"]);
	$smarty->assign("date_formats", $date_formats);
	$smarty->assign("time_formats", $time_formats);
	$date_formats_alt = array();
	$r_search = array("%d","%m","%Y","%b","%e", "%A", "%B");
	$r_replace = array("DD","MM","YYYY", "month", "day", "day of week", "month");
	foreach ($date_formats as $k=>$v) {
		$date_formats_alt[$k] = str_replace($r_search,$r_replace,$v);
	}

	$smarty->assign("date_formats_alt", $date_formats_alt);
}
elseif ($option == "XAffiliate" && !empty($active_modules['XAffiliate'])) {
	$partner_plans = func_query ("SELECT * FROM $sql_tbl[partner_plans] ORDER BY plan_id");
	$smarty->assign ("partner_plans", $partner_plans);
}
elseif ($option == 'Maintenance_Agent') {
	$periodical_log_labels = array();
	foreach (explode(',', $config['Maintenance_Agent']['periodic_logs']) as $k=>$v) {
		$periodical_log_labels[$v] = true;
	}

	$smarty->assign('periodical_log_labels', $periodical_log_labels);
	$smarty->assign('periodical_logs_names', x_log_get_names());
}
elseif ($option == "Gift_Certificates") {
	$smarty->assign('gc_templates', func_gc_get_templates($smarty->template_dir));
}

$configuration = func_query("SELECT * from $sql_tbl[config] WHERE category='$option' ORDER BY orderby");

if (is_array($options)) {
	#
	# Define data for the navigation within section
	#

	# Get the list of core options (w/o module options)...
	$modules_detected = false;
	$dt_general = $dt_modules = array();
	foreach ($options as $catname) {

		$option_title = func_get_langvar_by_name("option_title_$catname");
		if (empty($option_title))
			$option_title = str_replace("_", " ", $catname)." options";

		$highlighted = ($option == $catname) ? "hl" : "";

		$tmp = array(
			"link" => "configuration.php?option=$catname",
			"title" => $option_title,
			"style" => $highlighted
		);

		if (empty($active_modules[$catname])) {
			$dt_general[] = $tmp;

		} else {
			$dt_modules[] = $tmp;
		}
	}

	$dialog_tools_data["mc_left"][] = array("data" => $dt_general);
	if (!empty($dt_modules)) {
		$dialog_tools_data["mc_left"][] = array("data" => $dt_modules, "title" => func_get_langvar_by_name("option_title_Modules"));
	}
	$dialog_tools_data["left"] = array();
	$dialog_tools_data["columns"] = 3;
}

if (!empty($active_modules["Fancy_Categories"]) && $option == "Fancy_Categories") {
	include $xcart_dir."/modules/Fancy_Categories/admin_config.php";
}

# Postprocessing service array with configuration variables of the current section
if (!empty($configuration)) {
	foreach ($configuration as $k => $v) {
		switch ($v['name']) {
		case 'sns_script_extension':
			if (empty($sns_extensions)) {
				unset($configuration[$k]);
				continue;
			}

			$v['variants'] = "";
			foreach ($sns_extensions as $ek => $ev) {
				$v['variants'] .= $ek.":".$ev."\n";
			}

			break;
		case 'cmpi_currency':
			$currs = func_query_hash("SELECT code, name FROM $sql_tbl[currencies]", "code", false, true);
			if (empty($currs)) {
				unset($configuration[$k]);
				continue;
			}

			$v['variants'] = "";
			foreach ($currs as $ek => $ev) {
				$v['variants'] .= $ek.":($ek) ".$ev."\n";
			}

			break;
		}

		$configuration[$k]['variants'] = $v['variants'];

		# Define array with variable variants
		if (in_array($v['type'], array("selector","multiselector"))) {
			if (empty($v['variants'])) {
				unset($configuration[$k]);
				continue;
			}

			$vars = func_parse_str(trim($v['variants']), "\n", ":");
			$vars = func_array_map("trim", $vars);

			# Check variable data
			if ($v['type'] == "multiselector") {
				$configuration[$k]['value'] = $v['value'] = explode(";", $v['value']);
				foreach ($v['value'] as $vk => $vv) {
					if (!isset($vars[$vv]))
						unset($v['value'][$vk]);
				}

				$configuration[$k]['value'] = $v['value'] = array_values($v['value']);
			}

			$configuration[$k]['variants'] = array();
			foreach ($vars as $vk => $vv) {
				$configuration[$k]['variants'][$vk] = array("name" => $vv);
				if (strpos($vv, " ") === false) {
					$name = func_get_langvar_by_name($vv, NULL, false, true);
					if (!empty($name)) {
						$configuration[$k]['variants'][$vk] = array("name" => $name);
					}
				}

				if ($v['type'] == "selector") {
					$configuration[$k]['variants'][$vk]['selected'] = ($v['value'] == $vk);
				}
				else {
					$configuration[$k]['variants'][$vk]['selected'] = (in_array($vk, $v['value']));
				}
			}
		}

		$predefined_lng_variables[] = "opt_".$v['name'];
	}
}

if ($option) {
	$predefined_lng_variables[] = "option_title_".$option;
}

if ($option == 'Shipping') {
	$is_realtime = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code != ''") > 0);
	if ($is_realtime)
		$smarty->assign("is_realtime", $is_realtime);
}

$smarty->assign("configuration", array_values($configuration));
$smarty->assign("options", $options);
$smarty->assign("option", $option);
$smarty->assign("main","configuration");

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
