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
# $Id: languages.php,v 1.64.2.1 2006/04/29 11:02:16 max Exp $
#

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);
$trusted_post_variables = array("var_value", "new_var_value");

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files');

$location[] = array(func_get_langvar_by_name("lbl_edit_languages"), "");

$topics = func_query_column("SELECT topic FROM $sql_tbl[languages] WHERE topic<>'' GROUP BY topic ORDER BY topic");

if (!in_array($topic, $topics))
	$topic = "";

$d_langs = explode ("|", $config["disabled_languages"]);
if ($d_langs) {
	foreach ($d_langs as $key=>$value) {
		$d_langs [$key] = trim ($value);
	}
}

$languages = $avail_languages;

//?????????

if ($languages) {
	foreach ($languages as $key=>$value) {
		$languages[$key]["disabled"] = (in_array ($value["code"], $d_langs) ? "Y" : "N");
	}
}

if ($mode == "update_charset") {

	require $xcart_dir."/include/safe_mode.php";

	if ($text_dir == 'Y') {
		$config['r2l_languages'][$language] = true;
	} elseif(isset($config['r2l_languages'][$language])) {
		unset($config['r2l_languages'][$language]);
	}
	$tmp = serialize($config['r2l_languages']);
	db_query("REPLACE INTO $sql_tbl[config] (name,value) VALUES ('r2l_languages','".addslashes($tmp)."')");
	db_query("UPDATE $sql_tbl[countries] SET charset='$charset' WHERE code='$language'");
	func_data_cache_get("charsets", array(), true);

	func_header_location("languages.php?language=$language");

} elseif ($mode == "update") {

	require $xcart_dir."/include/safe_mode.php";

	if ($var_value) {
		foreach ($var_value as $key => $value) {
			func_array2update("languages", array("value" => $value), "code='$language' AND name='$key'");
		}
	}

	if ($topic == 'Languages') {
		func_data_cache_get("languages", array($language), true);
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("lbl_lng_variable_updated")
	);

	$smarty->clear_all_cache();
	$smarty->clear_compiled_tpl();

	func_header_location("languages.php?language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");

} elseif ($mode == "add") {

	require $xcart_dir."/include/safe_mode.php";

	if (empty($new_var_name)) {
		$top_message["content"] = func_get_langvar_by_name("msg_err_empty_label");
		$top_message["type"] = "E";
		func_header_location("languages.php?language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");

	} elseif ($new_var_name != preg_replace('/[^A-Za-z0-9_]/', '', $new_var_name)) {
		$top_message["content"] = func_get_langvar_by_name("msg_err_invalid_label");
		$top_message["type"] = "E";
		func_header_location("languages.php?language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");
	}
		
	$topic = in_array($new_topic, $topics) ? $new_topic : $topics[0];

	$is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE name = '$new_var_name' AND code='$language'") > 0;
	if ($is_exists) {
		func_array2update("languages", 
			array(
				'value' => $new_var_value
			),
			"name='$new_var_name' AND code='$language'"
		);
	} else {
		foreach ($languages as $key=>$value) {
			func_array2insert("languages", 
				array(
					"code" => $value['code'],
					"name" => $new_var_name,
					"value" => $new_var_value,
					"topic" => $topic
				),
				true
			);
		}
	}

	if ($topic == 'Languages') {
		func_data_cache_get("languages", array($language), true);
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("lbl_lng_variable_added")
	);

	func_header_location("languages.php?language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");

} elseif ($mode == "delete" && !empty($ids)) {

	require $xcart_dir."/include/safe_mode.php";

	db_query ("DELETE FROM $sql_tbl[languages] WHERE name IN ('".implode("','", $ids)."')");

	if ($topic == 'Languages') {
		func_data_cache_get("languages", array($language), true);
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("lbl_lng_variables_deleted")
	);

	func_header_location("languages.php?language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");

} elseif ($mode == "del_lang") {

	require $xcart_dir."/include/safe_mode.php";

	db_query ("DELETE FROM $sql_tbl[languages] WHERE code='$language'");
	db_query ("DELETE FROM $sql_tbl[products_lng] WHERE code='$language'");

	$lngs = func_query_column("SELECT code FROM $sql_tbl[languages] GROUP BY code");
	if (!empty($lngs)) {
		foreach ($lngs as $v) {
			func_data_cache_get("languages", array($v), true);
		}
	}

	if (!empty($active_modules['Fancy_Categories'])) {
		func_fc_remove_cache(10, false, false, array($language));
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("lbl_languages_has_been_deleted")
	);

	func_header_location("languages.php?lang_deleted");

} elseif ($mode == "export" && $language) {
	$smarty->assign ("csv_delimiter", $delimiter);

	$lng_res = func_query_first_cell ("SELECT value FROM $sql_tbl[languages] WHERE name='language_$language'");

	$data = func_query ("SELECT * FROM $sql_tbl[languages] WHERE code='$language' ORDER BY name");
	if ($data) {
		foreach ($data as $key => $value) {
			$data[$key]["value"] = "\"" . eregi_replace ("\"", "\"\"", $value["value"]) . "\"";
		}

		$smarty->assign ("data", $data);

		header ("Content-Type: text/csv");
		header ("Content-Disposition: attachment; filename=lng_".$lng_res.".csv");

		$_tmp_smarty_debug = $smarty->debugging;
		$smarty->debugging = false;

		func_display("main/lng_export.tpl",$smarty);

		$smarty->debugging = $_tmp_smarty_debug;
		exit;
	}
}

if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '$config[default_admin_language]'")) {
	$config['default_admin_language'] = func_query_first_cell("SELECT code FROM $sql_tbl[languages] ORDER BY code");
}

$new_languages = func_query ("SELECT $sql_tbl[countries].*, IFNULL(lng1c.value, lng2c.value) as country, IFNULL(lng1l.value, lng2l.value) as language FROM $sql_tbl[countries] LEFT JOIN $sql_tbl[languages] as lng1c ON lng1c.name = CONCAT('country_', $sql_tbl[countries].code) AND lng1c.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2c ON lng2c.name = CONCAT('country_', $sql_tbl[countries].code) AND lng2c.code = '$config[default_admin_language]' LEFT JOIN $sql_tbl[languages] as lng1l ON lng1l.name = CONCAT('language_', $sql_tbl[countries].code) AND lng1l.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2l ON lng2l.name = CONCAT('language_', $sql_tbl[countries].code) AND lng2l.code = '$config[default_admin_language]' WHERE (lng1l.value != '' OR lng2l.value != '') GROUP BY language ORDER BY language");

if ($mode == "add_lang") {

	require $xcart_dir."/include/safe_mode.php";

	if (!$new_language) {
		func_header_location("languages.php");
	}

	$exists_result = func_query_first ("SELECT * FROM $sql_tbl[languages] WHERE code='$new_language'");

	if (!$exists_result) {
		$result = func_query ("SELECT * FROM $sql_tbl[languages] WHERE code='$config[default_customer_language]'");
		if ($result) {
			foreach ($result as $key=>$value) {
				db_query ("INSERT INTO $sql_tbl[languages] (code, name, value, topic) VALUES ('$new_language', '".addslashes($value["name"])."','".addslashes($value["value"])."','$value[topic]')");
			}
		}

	$lngs = func_query_column("SELECT code FROM $sql_tbl[languages] GROUP BY code");
	if (!empty($lngs)) {
		foreach ($lngs as $v) {
			func_data_cache_get("languages", array($v), true);
		}
	}

	}

	if ($import_file && $import_file != "none") {
		$import_file = func_move_uploaded_file("import_file");
		if ($fp = func_fopen($import_file, "r", true)) {
			$lngs = $avail_languages;
			while ($columns = fgetcsv ($fp, 65536, $delimiter)) {
				if (sizeof($columns) >= 4) {
					$res = func_query_first ("SELECT * FROM $sql_tbl[languages] WHERE name='$columns[0]' AND $sql_tbl[languages].code = '$new_language' LIMIT 1");
					if ($res) {
						db_query ("UPDATE $sql_tbl[languages] SET value='".addslashes($columns[1])."', topic='".addslashes($columns[3])."' WHERE name='$columns[0]' AND code='$new_language'");
					} else {
						db_query ("INSERT INTO $sql_tbl[languages] (code, name, value, topic) VALUES ('$new_language','$columns[0]','".addslashes ($columns[1])."','".addslashes ($columns[3])."')");
					}
				}
			}
			fclose ($fp);
		}
	}

	func_data_cache_get("charsets", array(), true);

	func_header_location("languages.php?language=$new_language&topic=$topic&page=$page");
}

if ($mode == "change" && !empty($language)) {

	require $xcart_dir."/include/safe_mode.php";

	if (empty($d_langs))
		$d_langs = array();

	if (in_array ($language, $d_langs)) {
		$x = array_search($result["code"], $d_langs);
		unset($d_langs[$x]);
	} else {
		$d_langs[] = $language;
	}
	$d_langs = array_unique($d_langs);

	foreach ($d_langs as $k => $v) {
		if(empty($v))
			unset($d_langs[$k]);
	}

	db_query ("UPDATE $sql_tbl[config] SET value='".implode ("|", $d_langs)."' WHERE name='disabled_languages'");

	func_header_location("languages.php?language=$language&mode_changed");
}
if ($mode == "change_defaults") {

	require $xcart_dir."/include/safe_mode.php";

	if (!empty($new_customer_language))
		db_query("update $sql_tbl[config] set value='$new_customer_language' where name='default_customer_language'");
	if (!empty($new_admin_language))
		db_query("update $sql_tbl[config] set value='$new_admin_language' where name='default_admin_language'");

	func_header_location("languages.php?language=$language");
}

if ($language) {
	$r = func_query_first ("SELECT code, charset FROM $sql_tbl[countries] WHERE code='$language'");
	$r['language'] = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE name = 'language_$language'");
	$smarty->assign ("default_charset", $r["charset"]);

	$lang_disabled = (in_array ($r["code"], $d_langs) ? "Y" : "N");
	$smarty->assign ("lang_disabled", $lang_disabled);

	if ($topic)
		$topic_condition = " AND topic='$topic' ";
	else
		$topic_condition = " AND topic<>''";
	
	if (!empty($filter))
		$filter_condition = "AND (name LIKE '%$filter%' OR value LIKE '%$filter%')";
	else
		$filter_condition = "";
	
	$query = "SELECT * FROM $sql_tbl[languages] WHERE code='$language' $filter_condition $topic_condition order by topic, name";

	$objects_per_page = 20;

	$result = db_query($query);
	$total_labels_in_search = db_num_rows($result);
	
	if ($total_labels_in_search > 0) {
		$total_nav_pages = ceil ($total_labels_in_search/$objects_per_page)+1;
		include $xcart_dir."/include/navigation.php";

		$smarty->assign ("data", func_query ("$query LIMIT $first_page, $objects_per_page"));
	}
	
	$smarty->assign("total_labels_found", $total_labels_in_search);
	$smarty->assign ("navigation_script", "languages.php?language=$language&topic=$topic&filter=".urlencode($filter));
}

$smarty->assign ("filter", stripslashes($filter));
$smarty->assign ("languages", $languages);
$smarty->assign ("new_languages", $new_languages);

$smarty->assign ("topics", $topics);

$smarty->assign("main","languages");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
