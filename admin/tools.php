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
# $Id: tools.php,v 1.41.2.3 2006/06/20 06:47:38 max Exp $
#
# Service tools
#

define("TOOLS", true);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice','category','crypt','order');

x_session_register("err_store");

set_time_limit(86400);

$anchors = array(
	"clearcc" => "txt_credit_card_information_removal",
	"optimdb" => "lbl_optimize_tables",
	"integrdb" => "lbl_check_database_integrity",
	"gencache" => "lbl_force_cache_generation",
	"clearstat" => "lbl_statistics_clearing",
	"cleartmp" => "lbl_clear_templates_cache",
	"regenbk" => "lbl_regenerating_blowfish_key",
	"cleardb" => "lbl_remove_test_data");

foreach ($anchors as $anchor=>$anchor_label)
	$dialog_tools_data["left"][] = array("link" => "#".$anchor, "title" => func_get_langvar_by_name($anchor_label));

$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/general.php", "title" => func_get_langvar_by_name("lbl_summary"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/snapshots.php", "title" => func_get_langvar_by_name("lbl_snapshots"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/logs.php", "title" => func_get_langvar_by_name("lbl_shop_logs"));

#
# Start MySQL optimization procedure for all/selected tables
#
function func_optimize_table($tbl = false, $tick = 0) {
	global $sql_tbl;

	if (is_array($tbl)) {
		foreach ($tbl as $k => $v) {
			if (!empty($sql_tbl[$v]))
				$tbl[$k] = $sql_tbl[$v];
		}

		$tbls = func_query_column("SHOW TABLES");
		foreach ($tbls as $k => $v) {
			if (!in_array($v, $tbl))
				unset($tbls[$k]);
		}
	}
	elseif (!empty($tbl)) {
		if (!empty($sql_tbl[$tbl]))
			$tbl = $sql_tbl[$tbl];

		$tbls = func_query_column("SHOW TABLES LIKE '".$tbl."'");
	}
	else {
		$tbls = func_query_column("SHOW TABLES");
	}

	if (empty($tbls))
		return false;

	$i = 0;
	foreach ($tbls as $v) {
		$i++;
		db_query("REPAIR TABLE ".$v);
		db_query("OPTIMIZE TABLE ".$v);
		if ($tick > 0 && $i % $tick == 0) {
			func_flush(". ");
		}
	}
}


function test_templates_cache() {
	global $smarty;

	$val = func_get_dir_status($smarty->compile_dir);
	if ($val["size"] / (1024*1024*1024) > 0.9) $val["size"] = round($val["size"] / (1024*1024*1024))." Gbytes";
	else
	if ($val["size"] / (1024*1024) > 0.9) $val["size"] = round($val["size"] / (1024*1024))." Mbytes";
	else
	if ($val["size"] / 1024 > 0.9) $val["size"] = round($val["size"] / 1024)." kbytes";
	else
		$val["size"] = $val["size"]." bytes";

	$val["dir"] = $smarty->compile_dir;

	return $val;
}

#
# Translation table alias to table name
#
function func_trans_tbl_name($data, $added = array()) {
	global $sql_replace;

	if (empty($data))
		return $data;

	$replace = $sql_replace;
	if (!empty($added)) {
		foreach ($added as $alias => $name) {
			$replace["/(^|[^\w\d_])".preg_quote($alias, "/")."(\.|$)/S"] = "\\1".$name."\\2";
		}

		$replace = array_reverse($replace);
	}

	return preg_replace(array_keys($replace), $replace, $data);
}

$location[] = array(func_get_langvar_by_name("lbl_tools"), "");

if ($HTTP_GET_VARS["mode"] == "templates") {
	$smarty->clear_all_cache();
	$smarty->clear_compiled_tpl();
	$top_message["content"] = func_get_langvar_by_name("msg_adm_summary_templates_del")." '".$smarty->compile_dir."'";
	func_header_location("tools.php");
}

if ($REQUEST_METHOD=="POST" && isset($mode_clear)) {
	require $xcart_dir."/include/safe_mode.php";

	$updates = array();

	$rsd_limit = 0;
	if ($rsd_date == 's' && !empty($RSD_Day)) {
		$rsd_limit = mktime(0, 0, 0, date($RSD_Month), date($RSD_Day), date($RSD_Year));
	}
	$rsd_limit = intval($rsd_limit);

	if ($HTTP_POST_VARS["track_stat"] == "Y") {
		#
		# Delete ALL tracking statistics from the database
		#
		if (empty($rsd_limit)) {
			db_query("DELETE FROM $sql_tbl[stats_cart_funnel]");
			db_query("DELETE FROM $sql_tbl[stats_pages]");
			db_query("DELETE FROM $sql_tbl[stats_pages_paths]");
			db_query("DELETE FROM $sql_tbl[stats_pages_views]");

		} else {
			db_query("DELETE FROM $sql_tbl[stats_cart_funnel] WHERE date < '$rsd_limit'");
			db_query("DELETE FROM $sql_tbl[stats_pages_paths] WHERE date < '$rsd_limit'");
			db_query("DELETE FROM $sql_tbl[stats_pages_views] WHERE date < '$rsd_limit'");

			$query = "SELECT $sql_tbl[stats_pages].pageid FROM $sql_tbl[stats_pages] LEFT JOIN $sql_tbl[stats_pages_views] ON $sql_tbl[stats_pages].pageid = $sql_tbl[stats_pages_views].pageid WHERE $sql_tbl[stats_pages_views].pageid IS NULL GROUP BY $sql_tbl[stats_pages].pageid LIMIT 100";
			while (($pageids = func_query_column($query))) {
				db_query("DELETE FROM $sql_tbl[stats_pages] WHERE pageid IN ('".implode("','", $pageids)."')");
			}
		}

		$updates[] = func_get_langvar_by_name("msg_adm_summary_track_stat_del");
	}

	if ($HTTP_POST_VARS["shop_stat"] == "Y") {
		#
		# Delete ALL shop statistics from the database
		#
		db_query("DELETE FROM $sql_tbl[stats_customers_products]");
		if (empty($rsd_limit)) {
			db_query("DELETE FROM $sql_tbl[stats_shop]");
			db_query("UPDATE $sql_tbl[products] SET views_stats='0', sales_stats='0', del_stats='0'");
			db_query("UPDATE $sql_tbl[categories] SET views_stats='0'");

		} else {
			db_query("DELETE FROM $sql_tbl[stats_shop] WHERE date < '$rsd_limit'");
		}

		$updates[] = func_get_langvar_by_name("msg_adm_summary_shop_stat_del");
	}

	if ($HTTP_POST_VARS["referer_stat"] == "Y") {
		#
		# Delete ALL shop statistics from the database
		#
		if (empty($rsd_limit)) {
			db_query("DELETE FROM $sql_tbl[referers]");

		} else {
			db_query("DELETE FROM $sql_tbl[referers] WHERE last_visited < '$rsd_limit'");
		}
		$updates[] = func_get_langvar_by_name("msg_adm_summary_ref_stat_del");
	}

	if ($HTTP_POST_VARS["adaptive_stat"] == "Y") {
		#
		# Delete ALL adaptive statistics from the database
		#
		if (empty($rsd_limit)) {
			db_query("DELETE FROM $sql_tbl[stats_adaptive]");

		} else {
			db_query("DELETE FROM $sql_tbl[stats_adaptive] WHERE last_date < '$rsd_limit'");
		}
		$updates[] = func_get_langvar_by_name("msg_adm_summary_adaptive_stat_del");
	}

	if ($HTTP_POST_VARS["search_stat"] == "Y") {
		#
		# Delete ALL search statistics from the database
		#
		if (empty($rsd_limit)) {
			db_query("DELETE FROM $sql_tbl[stats_search]");

		} else {
			db_query("DELETE FROM $sql_tbl[stats_search] WHERE date < '$rsd_limit'");
		}
		$updates[] = func_get_langvar_by_name("msg_adm_summary_search_stat_del");
	}

	if ($HTTP_POST_VARS["remove_ccinfo_profiles"] == "Y") {
		#
		# Delete credit card information from customers' profiles
		#
		db_query("UPDATE $sql_tbl[customers] SET card_number='', card_name='', card_type='', card_expire='', card_cvv2=''");
		$updates[] = func_get_langvar_by_name("msg_adm_summary_ccinfo_del");
	}

	if ($HTTP_POST_VARS["remove_ccinfo_orders"] == "Y") {
		func_display_service_header("txt_credit_card_information_removal");
		func_flush("<br />&nbsp;&nbsp;".func_get_langvar_by_name("lbl_remove_from_completed_orders", NULL, false, true));
		#
		# Delete credit card information from completed and processed orders
		#
		$orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE status IN ('P','C')");

		while ($order = db_fetch_array($orders)) {
			$details = text_decrypt($order["details"]);
			if (is_string($details)) {
				$details = func_order_remove_ccinfo($details, ($HTTP_POST_VARS['save_4_numbers'] == "Y"));
				$details = addslashes(func_crypt_order_details($details));
				func_array2update("orders", array("details" => $details), "orderid = '$order[orderid]'");
				echo ". ";
			}
		}
		echo func_get_langvar_by_name("lbl_done", NULL, false, true)."<br />\n";

		db_free_result($orders);
		$updates[] = func_get_langvar_by_name("msg_adm_summary_ccinfo_orders_del");
	}

	if ($HTTP_POST_VARS["bench_stat"] == "Y" && empty($rsd_limit)) {
		#
		# Delete ALL benchmark pages from database and records from file system
		#
		db_query("DELETE FROM $sql_tbl[benchmark_pages]");
		$dir = @opendir($path);
		if ($dir) {
			$re_bench_files = "/".preg_quote(constant("BENCH_FILE_PREFIX"), "/")."(\d{6})\.php/S";
			while ($file = readdir($dir)) {
				if ($file == '.' || $file == '..' || !preg_match($re_bench_files, $file, $match))
					continue;

				@unlink($path."/".$file);
			}

			closedir($dir);
		}

		$updates[] = func_get_langvar_by_name("msg_adm_summary_adaptive_stat_del");
	}

	if (is_array($updates) && !empty($updates)) {
		$top_message["content"] = implode("\n<br /><br />\n", $updates);
		$top_message['type'] = "I";
	}

	func_header_location("tools.php");
}
elseif (isset($mode_optimize)) {
	# Optimize table
	require $xcart_dir."/include/safe_mode.php";
	func_optimize_table(false, 1);

	$top_message['content'] = func_get_langvar_by_name("lbl_table_optimization_successfully");
	$top_message['type'] = "I";

	func_html_location("tools.php");
}
elseif (isset($mode_clear_db)) {
	# Clear DB
	require $xcart_dir."/include/safe_mode.php";

	$tbl_demo_data[] = 'categories';
	$tbl_demo_data[] = 'categories_lng';
	$tbl_demo_data[] = 'categories_subcount';
	$tbl_demo_data[] = 'categories_memberships';
	$tbl_demo_data[] = 'discounts';
	$tbl_demo_data[] = 'discount_memberships';
	$tbl_demo_data[] = 'featured_products';
	$tbl_demo_data[] = 'images_C';
	$tbl_demo_data[] = 'images_P';
	$tbl_demo_data[] = 'images_T';
	$tbl_demo_data[] = 'pages';
	$tbl_demo_data[] = 'pricing';
	$tbl_demo_data[] = 'product_bookmarks';
	$tbl_demo_data[] = 'product_links';
	$tbl_demo_data[] = 'product_memberships';
	$tbl_demo_data[] = 'product_taxes';
	$tbl_demo_data[] = 'products';
	$tbl_demo_data[] = 'products_categories';
	$tbl_demo_data[] = 'products_lng';
	$tbl_demo_data[] = 'shipping_rates';
	$tbl_demo_data[] = 'quick_flags';
	$tbl_demo_data[] = 'quick_prices';
	$tbl_demo_data[] = 'orders';
	$tbl_demo_data[] = 'order_items';
	$tbl_demo_data[] = 'order_extras';

	foreach ($tbl_demo_data as $tbl) {
		if (isset($sql_tbl[$tbl])) {
			db_query("DELETE FROM ".$sql_tbl[$tbl]);
			func_flush(". ");
		}
	}

	$dir = @opendir($var_dirs["cache"]);
	if ($dir) {
		while ($file = readdir($dir)) {
			if ($file == '.' || $file == '..' || !preg_match("/\.(php|js)$/S", $file))
				continue;

			@unlink($var_dirs["cache"].DIRECTORY_SEPARATOR.$file);
			func_flush(". ");
		}
		closedir($dir);
	}

	$top_message['content'] = func_get_langvar_by_name("lbl_remove_test_data_successfully");
	$top_message['type'] = "I";

	func_html_location("tools.php");
}
elseif (isset($mode_rebuild) && !empty($active_modules['Product_Options']) && function_exists("func_rebuild_variants")) {
	# Rebuild product variants
	require $xcart_dir."/include/safe_mode.php";

	$pids = db_query("SELECT productid FROM $sql_tbl[products]");
	if ($pids) {
		$i = 0;
		func_display_service_header("lbl_rebuild_variants");
		while ($pid = db_fetch_array($pids)) {
			$i++;
			func_rebuild_variants($pid['productid'], true, 0);
			if ($i % 10 == 0) {
				func_flush(". ");
			}
		}
		db_free_result($pids);
	}

	func_html_location("tools.php");
}
elseif (isset($mode_clear_cache)) {
	# Clear data cache

	require $xcart_dir."/include/safe_mode.php";

	func_display_service_header("lbl_clear_data_cache");

	$dir = @opendir($var_dirs["cache"]);
	if ($dir) {
		while ($file = readdir($dir)) {
			if ($file == '.' || $file == '..' || !preg_match("/\.(php|js)$/S", $file))
				continue;

			@unlink($var_dirs["cache"].DIRECTORY_SEPARATOR.$file);
			func_flush(". ");
		}
	}

	func_flush("<br />\n");

	func_build_quick_flags(false, 100);
	func_flush("<br />\n");

	func_build_quick_prices(false, 100);
	func_flush("<br />\n");

	if (!empty($active_modules['Fancy_Categories']) && function_exists("func_fc_build_categories")) {
		func_fc_remove_cache(10);
		func_flush("<br />\n");
		if (func_fc_check_rebuild()) {
			func_fc_build_categories(false, 10);
			func_flush("<br />\n");
		}
	}

	func_recalc_subcat_count(false, 10);

	$top_message['content'] = func_get_langvar_by_name("lbl_cache_generation_successfully");
	$top_message['type'] = "I";

	func_html_location("tools.php");
}
elseif (isset($mode_check_integrity)) {
	# Check DB integrity

	require $xcart_dir."/include/safe_mode.php";

	$tbl_keys["categories.parentid"] = array(
		"keys" => array("categories.parentid" => "categories.categoryid"),
		"where" => "categories.parentid != 0",
		"fields" => array("categoryid","category")
	);
	$tbl_keys["categories_lng.categoryid"] = array(
		"keys" => array("categories_lng.categoryid" => "categories.categoryid"),
		"fields" => array("categoryid")
	);
	$tbl_keys["categories_lng.code"] = array(
		"keys" => array("categories_lng.code" => "languages.code"),
		"fields" => array("categoryid"),
		"type" => "W"
	);
	$tbl_keys["categories_subcount.categoryid"] = array(
		"keys" => array("categories_subcount.categoryid" => "categories.categoryid"),
		"fields" => array("categoryid")
	);
	$tbl_keys["categories_subcount.memberships"] = array(
		"keys" => array("categories_subcount.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"where" => "categories_subcount.membershipid != 0",
		"fields" => array("categoryid")
	);
	$tbl_keys["category_memberships.categoryid"] = array(
		"keys" => array("category_memberships.categoryid" => "categories.categoryid"),
		"fields" => array("categoryid")
	);
	$tbl_keys["category_memberships.memberships"] = array(
		"keys" => array("category_memberships.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"fields" => array("categoryid")
	);
	$tbl_keys["counties.stateid"] = array(
		"keys" => array("counties.stateid" => "states.stateid"),
		"fields" => array("county")
	);
	$tbl_keys["country_currencies.country_code"] = array(
		"keys" => array("country_currencies.country_code" => "countries.code"),
		"fields" => array("code")
	);
	$tbl_keys["country_currencies.code"] = array(
		"keys" => array("country_currencies.code" => "currencies.code"),
		"fields" => array("country_code")
	);
	$tbl_keys["customers.parent"] = array(
		"keys" => array("customers.parent" => "customers.login"),
		"where" => "customers.parent != ''",
		"fields" => array("login")
	);
	$tbl_keys["customers.memberships"] = array(
		"keys" => array("customers.membershipid" => "memberships.membershipid"),
		"where" => "customers.membershipid != 0",
		"fields" => array("login")
	);
	$tbl_keys["customers.pending_membershipid"] = array(
		"keys" => array("customers.pending_membershipid" => "memberships.membershipid"),
		"where" => "customers.pending_membershipid != 0",
		"fields" => array("login")
	);
	$tbl_keys["discount_memberships.membershipid"] = array(
		"keys" => array("discount_memberships.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"fields" => array("discountid")
	);
	$tbl_keys["discounts.provider"] = array(
		"keys" => array("discounts.provider" => "customers.login"),
		"on" => "customers.usertype IN ('A','P')",
		"fields" => array("discountid")
	);
	$tbl_keys["featured_products.productid"] = array(
		"keys" => array("featured_products.productid" => "products.productid"),
		"fields" => array("productid","categoryid")
	);
	$tbl_keys["featured_products.categoryid"] = array(
		"keys" => array("featured_products.categoryid" => "categories.categoryid"),
		"where" => "featured_products.categoryid != 0",
		"fields" => array("productid", "categoryid")
	);
	$tbl_keys["ge_products.productid"] = array(
		"keys" => array("ge_products.productid" => "products.productid"),
		"fields" => array("geid")
	);
	$tbl_keys["images_C.id"] = array(
		"keys" => array("images_C.id" => "categories.categoryid"),
		"fields" => array("imageid")
	);
	$tbl_keys["images_P.id"] = array(
		"keys" => array("images_P.id" => "products.productid"),
		"fields" => array("imageid")
	);
	$tbl_keys["images_T.id"] = array(
		"keys" => array("images_T.id" => "products.productid"),
		"fields" => array("imageid")
	);
	$tbl_keys["languages_alt.code"] = array(
		"keys" => array("languages_alt.code" => "languages.code"),
		"fields" => array("name","value"),
		"type" => "W"
	);
	$tbl_keys["memberships_lng.membershipid"] = array(
		"keys" => array("memberships_lng.membershipid" => "memberships.membershipid"),
		"fields" => array("code")
	);
	$tbl_keys["memberships_lng.code"] = array(
		"keys" => array("memberships_lng.code" => "languages.code"),
		"fields" => array("membershipid"),
		"type" => "W"
	);
	$tbl_keys["order_details.orderid"] = array(
		"keys" => array("order_details.orderid" => "orders.orderid"),
		"fields" => array("itemid")
	);
	$tbl_keys["order_extras.orderid"] = array(
		"keys" => array("order_extras.orderid" => "orders.orderid"),
		"fields" => array("khash")
	);
	$tbl_keys["pages.language"] = array(
		"keys" => array("pages.language" => "languages.code"),
		"fields" => array("pageid","title"),
		"type" => "W"
	);
	$tbl_keys["pmethod_memberships.memberships"] = array(
		"keys" => array("pmethod_memberships.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"fields" => array("paymentid")
	);
	$tbl_keys["pricing.products"] = array(
		"keys" => array("pricing.productid" => "products.productid"),
		"fields" => array("priceid")
	);
	$tbl_keys["pricing.memberships"] = array(
		"keys" => array("pricing.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"where" => "pricing.membershipid != 0",
		"fields" => array("priceid")
	);
	$tbl_keys["product_bookmarks.productid"] = array(
		"keys" => array("product_bookmarks.productid" => "products.productid"),
	);
	$tbl_keys["product_bookmarks.login"] = array(
		"keys" => array("product_bookmarks.login" => "customers.login")
	);
	$tbl_keys["product_memberships.productid"] = array(
		"keys" => array("product_memberships.productid" => "products.productid"),
		"fields" => array("membershipid")
	);
	$tbl_keys["product_memberships.memberships"] = array(
		"keys" => array("product_memberships.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"fields" => array("productid")
	);
	$tbl_keys["product_taxes.productid"] = array(
		"keys" => array("product_taxes.productid" => "products.productid"),
		"fields" => array("taxid")
	);
	$tbl_keys["product_taxes.taxid"] = array(
		"keys" => array("product_taxes.taxid" => "taxes.taxid"),
		"fields" => array("productid")
	);
	$tbl_keys["products.provider"] = array(
		"keys" => array("products.provider" => "customers.login"),
		"on" => "customers.usertype IN ('P','A')",
		"fields" => array("productid","product")
	);
	$tbl_keys["products.productid"] = array(
		"keys" => array("products.productid" => "products_categories.productid"),
		"fields" => array("productid","product")
	);
	$tbl_keys["products.quick_flags"] = array(
		"keys" => array("products.productid" => "quick_flags.productid"),
		"fields" => array("productid","product")
	);
    $tbl_keys["products.quick_prices"] = array(
        "keys" => array("products.productid" => "quick_prices.productid"),
        "fields" => array("productid","product")
    );
	$tbl_keys["products.price_productid"] = array(
		"keys" => array("products.productid" => "pricing.productid"),
		"on" => "pricing.quantity = 1 AND pricing.membershipid = 0",
		"fields" => array("productid","product")
	);
	$tbl_keys["products_categories.productid"] = array(
		"keys" => array("products_categories.productid" => "products.productid"),
		"fields" => array("categoryid","main")
	);
	$tbl_keys["products_categories.categoryid"] = array(
		"keys" => array("products_categories.categoryid" => "categories.categoryid"),
		"fields" => array("productid","main")
	);
	$tbl_keys["products_lng.productid"] = array(
		"keys" => array("products_lng.productid" => "products.productid"),
		"fields" => array("code")
	);
	$tbl_keys["products_lng.code"] = array(
		"keys" => array("products_lng.code" => "languages.code"),
		"fields" => array("productid"),
		"type" => "W"
	);
	$tbl_keys["quick_flags.productid"] = array(
		"keys" => array("quick_flags.productid" => "products.productid")
	);
	$tbl_keys["quick_prices.productid"] = array(
		"keys" => array("quick_prices.productid" => "products.productid"),
		"fields" => array("membershipid")
	);
	$tbl_keys["quick_prices.priceid"] = array(
		"keys" => array(
			"quick_prices.priceid" => "pricing.priceid",
			"quick_prices.productid" => "pricing.productid"
		),
		"fields" => array("membershipid")
	);
	$tbl_keys["quick_prices.variant_priceid"] = array(
		"keys" => array(
			"quick_prices.priceid" => "pricing.priceid",
			"quick_prices.productid" => "pricing.productid",
			"quick_prices.variantid" => "pricing.variantid",
		),
		"where" => "quick_prices.variantid != 0",
		"fields" => array("membershipid")
	);
	$tbl_keys["quick_prices.memberships"] = array(
		"keys" => array("quick_prices.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"where" => "quick_prices.membershipid != 0",
		"fields" => array("productid")
	);
	$tbl_keys["register_field_values.fieldid"] = array(
		"keys" => array("register_field_values.fieldid" => "register_fields.fieldid"),
		"fields" => array("login","value")
	);
	$tbl_keys["register_field_values.login"] = array(
		"keys" => array("register_field_values.login" => "customers.login"),
		"fields" => array("fieldid","value")
	);
	$tbl_keys["shipping_rates.shippingid"] = array(
		"keys" => array("shipping_rates.shippingid" => "shipping.shippingid"),
		"fields" => array("rateid","zoneid","provider")
	);
	$tbl_keys["shipping_rates.provider"] = array(
		"keys" => array("shipping_rates.provider" => "customers.login"),
		"on" => "customers.usertype IN ('P','A')",
		"fields" => array("rateid","shippingid","zoneid")
	);
	$tbl_keys["shipping_rates.zoneid"] = array(
		"keys" => array("shipping_rates.zoneid" => "zones.zoneid"),
		"where" => "shipping_rates.zoneid != 0",
		"fields" => array("rateid","shippingid","provider")
	);
	$tbl_keys["shipping_options.carrier"] = array(
		"keys" => array("shipping_options.carrier" => "shipping.code"),
		"where" => "shipping_options.carrier != 'INTERSHIPPER'"
	);
	$tbl_keys["states.country_code"] = array(
		"keys" => array("states.country_code" => "countries.code"),
		"fields" => array("code","state")
	);
	$tbl_keys["stats_cart_funnel.login"] = array(
		"keys" => array("stats_cart_funnel.login" => "customers.login"),
		"on" => "customers.usertype = 'C'",
		"where" => "stats_cart_funnel.login != ''",
		"fields" => array("transactionid")
	);
	$tbl_keys["stats_customers_products.login"] = array(
		"keys" => array("stats_customers_products.login" => "customers.login"),
		"on" => "customers.usertype = 'C'",
		"fields" => array("productid")
	);
	$tbl_keys["stats_customers_products.productid"] = array(
		"keys" => array("stats_customers_products.productid" => "products.productid"),
		"fields" => array("login")
	);
	$tbl_keys["tax_rate_memberships.rateid"] = array(
		"keys" => array("tax_rate_memberships.rateid" => "tax_rates.rateid"),
		"fields" => array("membershipid")
	);
	$tbl_keys["tax_rate_memberships.memberships"] = array(
		"keys" => array("tax_rate_memberships.membershipid" => "memberships.membershipid"),
		"on" => "memberships.area = 'C'",
		"fields" => array("rateid")
	);
	$tbl_keys["tax_rates.taxid"] = array(
		"keys" => array("tax_rates.taxid" => "taxes.taxid"),
		"fields" => array("rateid")
	);
	$tbl_keys["tax_rates.provider"] = array(
		"keys" => array("tax_rates.provider" => "customers.login"),
		"on" => "customers.usertype IN ('P','A')",
		"fields" => array("rateid")
	);
	$tbl_keys["tax_rates.zoneid"] = array(
		"keys" => array("tax_rates.zoneid" => "zones.zoneid"),
		"where" => "tax_rates.zoneid != 0",
		"fields" => array("rateid")
	);
	$tbl_keys["zone_element.zoneid"] = array(
		"keys" => array("zone_element.zoneid" => "zones.zoneid"),
		"fields" => array("field","field_type")
	);
	$tbl_keys["zones.provider"] = array(
		"keys" => array("zones.provider" => "customers.login"),
		"on" => "customers.usertype IN ('P','A')",
		"fields" => array("zone_name")
	);

	$sql_replace = array();
	foreach ($sql_tbl as $alias => $name) {
		$sql_replace["/(^|[^\w\d_])".preg_quote($alias, "/")."(\.|$)/S"] = "\\1".$name."\\2";
	}

	$total_num = $warn_num = $err_num = 0;
	$err_store = array();
	$err_limit = 100;
	$lbl_error = func_get_langvar_by_name("lbl_error", array(), false, true);
	$lbl_warning = func_get_langvar_by_name("lbl_warning", array(), false, true);

	# Scan exist key links
	foreach ($tbl_keys as $kname => $d) {
		$join = $from = $added = array();
		$as = $where = $join_on = $tbl2 = "";

		$kname = trim($kname);
		if (!is_array($d['fields']))
			$d['fields'] = array();

		# Get parent table name
		$tbl1 = substr($kname, 0, strpos($kname, "."));
		$key_name = substr($kname, strpos($kname, ".")+1);

		# Translate key pairs
		foreach ($d['keys'] as $kk => $key) {
			$from[] = $d['fields'][] = substr($kk, strpos($kk, ".")+1);

			# Get child table name
			if (empty($tbl2)) {
				$tbl2 = substr($key, 0, strpos($key, "."));
				if ($tbl1 == $tbl2) {
					$as = "_".$tbl2;
					$added[$tbl2] = $as;
				}

				$tbl2 = func_trans_tbl_name($tbl2);
			}

			$key = func_trans_tbl_name($key, $added);
			$kk = func_trans_tbl_name($kk);
			$join[] = $kk." = ".$key;

			if (empty($where))
				$where = $key;
		}

		# Translate JOIN ON (if exist)
		if (!empty($d['on']))
			$join_on = " AND ".func_trans_tbl_name($d['on']);

		$tbl1 = func_trans_tbl_name($tbl1);

		foreach ($d['fields'] as $k => $v) {
			$d['fields'][$k] = $tbl1.".".$v;
		}

		if (!empty($as))
			$as = " as ".$as;

		$query = "SELECT ".implode(", ", $d['fields'])." FROM $tbl1 LEFT JOIN $tbl2 $as ON ".implode(" AND ", $join).$join_on." WHERE ".$where." IS NULL";

		# Translate where (if exist)
		if (!empty($d['where']))
			$query .= " AND ".func_trans_tbl_name($d['where']);

		$res = db_query($query);

		if ($res) {
			if (db_num_rows($res) > 0) {
				func_flush("<br />\n");
				if ($d['type'] == 'W') {
					echo $lbl_warning;
					$warn_num++;
				}
				else {
					echo $lbl_error;
				}

				echo ": ".func_get_langvar_by_name("lbl_unrelated_data", array("table_parent" => $tbl1, "table_child" => $tbl2), false, true)."<br />\n";
				while ($row = db_fetch_array($res)) {
					if ($d['type'] != 'W') {
						$err_num++;
						$keys = array();
						foreach ($from as $v) {
							$keys[$v] = $row[$v];
						}

						$err_store[$tbl1][$tbl2][] = array(
							"row" => $row,
							"keys" => $keys
						);
					}

					echo "&nbsp;&nbsp;&nbsp;";
					$is_first = true;
					foreach ($row as $k => $v) {
						if (!in_array($k, $from))
							continue;

						if (!$is_first)
							echo "; ";

						$is_first = false;
						echo $k.": ".$v;
					}

					func_flush("<br />\n");
					$total_num++;
					if ($err_num >= $err_limit)
						break;
				}
			}

			db_free_result($res);
		}

		func_flush(". ");

		if ($err_num >= $err_limit)
			break;
	}

	if ($err_num == 0) {
		$top_message['content'] = func_get_langvar_by_name("lbl_integrity_check_successfully");
		$top_message['type'] = "I";
		$delay = 3;
	}
	else {
		echo "<br />\n<br />\n".func_get_langvar_by_name("lbl_total", array(), false, true).": ".$err_num;
		$top_message['content'] = func_get_langvar_by_name("lbl_integrity_check_successfully_err", array("err_num" => $err_num));
		$top_message['type'] = "W";
		$delay = 30;
	}

	if ($dbic_log_file)
		fclose($dbic_log_file);

	func_html_location("tools.php", $delay);
}
elseif ($regenerate_blowfish) {

	set_time_limit(86400);

	func_display_service_header("lbl_regenerating_blowfish_key");
	$fp = @fopen($xcart_dir."/config.php", "r+");
	if (!$fp) {
		$top_message = array(
			"content" => func_get_langvar_by_name("lbl_cannot_write_config"),
			"type" => "E"
		);
		func_header_location("tools.php");
	}

	mt_srand(time());
	$new_blowfish_key = md5(mt_rand(0, time()));
		
    $allfile = "";
	$is_added = false;
    while (!feof($fp)) {
		$buffer = fgets($fp, 4096);
        if (ereg('^\$blowfish_key *=', $buffer)) {
            $buffer = ereg_replace('=.*;', "='".$new_blowfish_key."';", $buffer);
			$is_added = true;
		}
        $allfile .= $buffer;
    }
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, $allfile);
    fclose($fp);

	if (!$is_added) {
		$top_message = array(
			"content" => func_get_langvar_by_name("txt_regen_blowfish_key_failed"),
			"type" => "E"
		);
		func_header_location("tools.php");
	}

	foreach ($bf_crypted_tables as $tbl => $s) {
		if (!isset($sql_tbl[$tbl]) || empty($s['fields']) || empty($s['key']))
			continue;

		# Get data
		if (!empty($s['where']))
			$s['where'] = " WHERE 1 ".$s['where'];

		$data = db_query("SELECT $s[key], ".implode(", ", $s['fields'])." FROM ".$sql_tbl[$tbl].$s['where']);

		if (!$data)
			continue;

		while ($row = db_fetch_array($data)) {
			$key = array_shift($row);

			foreach ($row as $fname => $fvalue) {

				# Check field crypt type
				$type = substr($fvalue, 0, 1);
				if (!in_array($type, $encryption_types) || in_array($type, $ingnored_encryption_types)) {
					unset($row[$fname]);
					continue;
				}

				# Decrypt with old (default) key
				$row[$fname] = text_decrypt($row[$fname]);

				# If data is empty (null or false or empty) - pass field
				if (empty($row[$fname])) {
					unset($row[$fname]);
					continue;
				}

				# Crypt with new key
				$row[$fname] = addslashes(text_crypt($row[$fname], false, $new_blowfish_key));
			}

			if (empty($row))
				continue;

			# Update row
			func_array2update($tbl, $row, $s['key']." = '".addslashes($key)."'");
			func_flush(". ");
		}

		db_free_result($data);
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("txt_regen_blowfish_key_success"),
		"type" => "I"
	);
	func_html_location("tools.php");

}

$rsd_serach_date = array(
	"stats_cart_funnel" => "date",
	"stats_pages_paths" => "date",
	"stats_pages_views" => "date",
	"stats_shop" => "date",
	"stats_adaptive" => "last_date",
	"stats_search" => "date",
);

$rsd_start_year = date("Y");
foreach ($rsd_serach_date as $tbl => $fld) {
	$tmp = func_query_first_cell("SELECT MIN($fld) FROM ".$sql_tbl[$tbl]);
	if (empty($tmp))
		continue;

	$tmp = date("Y", $tmp);
	if ($rsd_start_year > $tmp)
		$rsd_start_year = $tmp;
}
$smarty->assign("rsd_start_year", $rsd_start_year);

if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name = 'Subscriptions'")) {
	$smarty->assign("is_subscription", true);
}

if (!is_writable($xcart_dir."/config.php")) {
	$smarty->assign("config_non_writable", true);
}

if (!empty($err_store))
	$smarty->assign("err_store", $err_store);
$smarty->assign("templates_cache", test_templates_cache());

$smarty->assign("main","tools");

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
