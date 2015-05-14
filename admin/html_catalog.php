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

# $Id: html_catalog.php,v 1.82.2.5 2006/06/15 11:28:08 max Exp $

# This script generates search engine friendly HTML catalog for X-cart

@set_time_limit(2700);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files','http');

$location[] = array(func_get_langvar_by_name("lbl_html_catalog"), "");

define ('DIR_CATALOG', '/catalog');

$sort_fields = array("productcode","title","price","orderby","quantity");
$per_page = $config["Appearance"]["products_per_page"];
$max_name_length = 64;
$php_scripts = array("search.php","giftcert.php","help.php", "cart.php", "product.php","register.php", "home.php", "pconf.php", "giftregs.php", "manufacturers.php","news.php","orders.php", "giftreg_manage.php","returns.php");
$site_location = parse_url($http_location);

$robot_cookies = array("is_robot=1");

$name_styles = array (
	"hyphen" => array (
		"category" => "{category_name}-{sort}-p-{page}-c-{categoryid}.html",
		"product" => "{product_name}-p-{productid}.html",
		"staticpage" => "{page_name}-sp-{pageid}.html",
		"manufacturer" => "{name}-mf-{id}.html",
		"name_delim" => "_"
	),
	"hyphen_4" => array (
		"category" => "{category_name}-{sort}-p-{page}-c-{categoryid}.html",
		"product" => "{product_name}-p-{productid}.html",
		"staticpage" => "{page_name}-sp-{pageid}.html",
		"manufacturer" => "{name}-mf-{id}.html",
		"name_delim" => "-"
	),
	"new" => array (
		"category" => "{category_name}_{sort}_page_{page}_c_{categoryid}.html",
		"product" => "{product_name}_p_{productid}.html",
		"staticpage" => "{page_name}_sp_{pageid}.html",
		"manufacturer" => "{name}_mf_{id}.html",
		"name_delim" => "_"
	),
	# Old scheme (before 3.5.0)
	"default" => array (
		"category" => "category_{categoryid}_{category_name}_{sort}_page_{page}.html",
		"product" => "product_{productid}_{product_name}.html",
		"staticpage" => "page_{pageid}_{page_name}.html",
		"manufacturer" => "manufacturer_{id}_{name}.html",
		"name_delim" => "_"
	)
);

if (!empty($active_modules['Feature_Comparison'])) {
		include $xcart_dir."/modules/Feature_Comparison/html_catalog.php";
}

if (!empty($active_modules['Special_Offers'])) {
		include $xcart_dir."/modules/Special_Offers/html_catalog.php";
}

function func_fetch_page($host, $path, $arg, $cookies) {
	$max_count = 5;

	while ($max_count >= 0) {
		$result = func_http_get_request($host, $path, $arg, $cookies);
		list($http_headers, $page_src) = $result;
		if (!empty($page_src)) break;

		sleep(1);
		$max_count--;
	}

	return $result;
}

function func_hc_mkdir($dir,$recursive=false) {
	if (empty($dir)) return true;

	if (!file_exists($dir)) {
		$r = mkdir($dir);
		if ($r) return true;

		if ($recursive) {
			return func_hc_mkdir(dirname($dir)) && mkdir($dir);
		}

		return false;
	}
	elseif (is_dir($dir)) {
		return true;
	}

	return false;
}

function my_save_data($filename, $data) {
	global $hc_state;

	$filename = func_normalize_path($filename);

	func_hc_mkdir(dirname($filename));
	$fp = fopen($filename, "w+");
	if ($fp === false) {
		echo "<font color=\"red\">".func_get_langvar_by_name("lbl_cannot_save_file_N", array("file" => func_relative_path($filename)),false,true)."</font>";
		x_session_save();
		exit;
	}

	fwrite($fp, $data);
	fclose($fp);
	$hc_state["count"] ++;

	func_flush(func_relative_path($filename)."<br />\n");
	if ($hc_state["pages_per_pass"] > 0 && $hc_state["count"] > 0 && ($hc_state["count"] % $hc_state["pages_per_pass"]) == 0) {
		echo "<hr />";
		func_html_location("html_catalog.php?mode=continue",1);
	}
}

function normalize_name($name) {
	global $max_name_length, $hc_state;
	static $r_match = false;
	static $r_repl = false;

	if ($r_match == false) {
		$r_match = array(
			"/[ \/".$hc_state["namestyle"]["name_delim"]."]+/S",
			"/[^A-Za-z0-9_".$hc_state["namestyle"]["name_delim"]."]+/S"
		);
		$r_repl = array($hc_state["namestyle"]["name_delim"], "");
	}

	if (strlen($name) > $max_name_length)
		$name = substr($name, 0, $max_name_length);

	$name = preg_replace($r_match, $r_repl, $name);

	return $name;
}

#
# Generate filename for a category page
#
function category_filename($cat, $cat_name, $page = 1, $sort_field, $sort_direction){
	global $max_name_length;
	global $sql_tbl;
	global $hc_state;

	if (empty($cat_name)) $cat_name = func_query_first_cell("SELECT category FROM $sql_tbl[categories] where categoryid='$cat'");
	if (empty($cat_name)) $cat_name = $cat;

	$cat_name = normalize_name($cat_name);
	$cat_name = str_replace(array("{category_name}", "{page}", "{categoryid}","{sort}"),array($cat_name, $page, $cat, $sort_field.$sort_direction),$hc_state["namestyle"]["category"]);

	return $cat_name;
}

#
# Generate filename for a product page
#

function product_filename($productid, $prod_name=false){
	global $max_name_length, $sql_tbl;
	global $hc_state;

	if (empty($prod_name)) $prod_name = func_query_first_cell("SELECT product FROM $sql_tbl[products] WHERE productid = '$productid'");
	if (empty($prod_name)) $prod_name = $productid;

	$prod_name = normalize_name($prod_name);
	$prod_name = preg_replace(array("!{product_name}!S", "!{productid}!S"),array($prod_name, $productid),$hc_state["namestyle"]["product"]);

	return $prod_name;
}

function staticpage_filename($pageid, $page_name=false) {
	global $config;
	global $max_name_length, $sql_tbl;
	global $hc_state, $current_lng;

	if (empty($page_name)) $page_name = func_query_first_cell("SELECT title FROM $sql_tbl[pages] WHERE active = 'Y' AND pageid='$pageid' AND level='E' AND language='$current_lng'");
	if (empty($page_name)) $page_name = $pageid;

	$page_name = normalize_name($page_name);
	$page_name = preg_replace(array("!{page_name}!S", "!{pageid}!S"),array($page_name, $pageid),$hc_state["namestyle"]["staticpage"]);

	return $page_name;
}

function manufacturer_filename($id, $name=false) {
	global $config;
	global $max_name_length, $sql_tbl;
	global $hc_state, $current_lng;

	if (empty($name)) $name = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers_lng] WHERE manufacturerid='$id' AND code='$current_lng'");
	if (empty($name)) $name = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers] WHERE manufacturerid='$id'");
	if (empty($name)) $name = $id;

	$name = normalize_name($name);
	$name = preg_replace(array("!{name}!S", "!{id}!S"),array($name, $id),$hc_state["namestyle"]["manufacturer"]);

	return $name;
}

function category_callback($found) {
	global $hc_state, $config;

	$cat = false;
	$fn = array(0,1);
	$sort = array($config['Appearance']['products_order'], 0);
	if (preg_match("/cat=([0-9]+)/S",$found[2], $m)) $fn[0] = $cat = $m[1];
	if (preg_match("/page=([0-9]+)/S",$found[2], $m)) $fn[1] = $m[1];
	if (preg_match("/sort=([\w\d_]+)/S",$found[2], $m)) $sort[0] = $m[1];
	if (preg_match("/sort_direction=([01]+)/S",$found[2], $m)) $sort[1] = $m[1];

	return $found[1].$hc_state["catalog"]["webpath"].category_filename($fn[0],false,$fn[1], $sort[0], $sort[1]).$found[3];
}

function product_callback($found) {
	global $hc_state;

	if (preg_match("/productid=([0-9]+)/S",$found[2], $m))
		return $found[1].$hc_state["catalog"]["webpath"].product_filename($m[1]).$found[3];

	return $found[1].$found[3];
};

function staticpage_callback($found) {
	global $hc_state;

	if (preg_match("/pageid=([0-9]+)/S",$found[2], $m))
		return $found[1].$hc_state["catalog"]["webpath"].staticpage_filename($m[1]).$found[3];

	return $found[1].$found[3];
};

function manufacturer_callback($found) {
	global $hc_state;

	if (preg_match("/manufacturerid=([0-9]+)/S",$found[2], $m))
		return $found[1].$hc_state["catalog"]["webpath"].manufacturer_filename($m[1]).$found[3];

	return $found[1].$found[3];
};

function make_page_name($name_func, $name_params, $lng_code=null) {
	global $current_lng;

	if (empty($name_func)) {
		$page_name = "index.html";
	}
	else {
		if(!is_null($lng_code)) {
			$saved_lng = $current_lng;
			$current_lng = $lng_code;

			$page_name = call_user_func_array($name_func."_filename", $name_params);

			$current_lng = $saved_lng;
		}
		else {
			$page_name = call_user_func_array($name_func."_filename", $name_params);
		}

	}

	return $page_name;
}

#
# Modify hyperlinksks to point to HTML pages of the catalogue
#
function process_page($page_src, $page_name, $name_func, $name_params) {
	global $php_scripts_long;
	global $XCART_SESSION_NAME;
	global $site_location;
	global $hc_state;
	global $current_lng;

	# <select name="sl" onChange="javascript: document.sl_form.submit()">
	# <select name="sl" onChange="javascript: window.location=document.sl_form.sl.value">
	#
	# Remove the "select language" form
	if ($hc_state["remove_slform"]) {
		$page_src = preg_replace("!<form[^<>]*name=[^<>]*sl_form.*</form>!isUS","",$page_src);
	}
	else {
		$page_src = preg_replace('!(<select[^<>]*name=[^<>]*sl.*)javascript: this.form.submit\(\)(;[\">])!isUS',"\\1javascript: window.location=this.form.sl.value\\2",$page_src);
		foreach ($hc_state["catalog_dirs"] as $inst) {
			$lng_page_name = $page_name;
			if ($current_lng != $inst['code']) {
				$lng_name_params = $name_params;
				$lng_name_params[1] = false; # remove name of item
				$lng_page_name = make_page_name($name_func, $lng_name_params, $inst['code']);
			}
			
			$path = $inst["webpath"].$lng_page_name;
			$page_src = preg_replace("!(<form[^<>]*name=[^<>]*sl_form.*<option[^<>]*value=\")".$inst["code"]."(\".*</form>)!isUS","\\1".$path."\\2",$page_src);
			$updated_lng[] = $inst["code"];
		}

		if (isset($hc_state["remove_lng_line"]))
			$page_src = preg_replace("!<option[^<>]*value=\"(".$hc_state["remove_lng_line"].")\".*</option>!isUS","",$page_src);
	}

	$page_src = preg_replace('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*home.php(["\'])/iS', "\\1index.html\\2", $page_src);

	# Modify links to categories
	$page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*home.php\?(cat=[^"\'>]+)(["\'])/iS', "category_callback", $page_src);
	# FancyCategories links
	$page_src = preg_replace_callback('/(window.location[ ]*=[ ]*["\']*)[^"\']*home.php\?(cat=[^"\'>]+)(["\'])/iS', "category_callback", $page_src);

	# Modify links to products
	$page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*product.php\?(productid=[^"\'>]+)(["\'>])/iUS', "product_callback", $page_src);

	if ($hc_state["process_staticpages"]) {
		# Modify links to static_pages
		$page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*pages.php\?(pageid=[^"\'>]+)(["\'>])/iUS', "staticpage_callback", $page_src);
	}

	# Manufacturers
	$page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*manufacturers.php\?(manufacturerid=[^"\'>]+)(["\'>])/iUS', "manufacturer_callback", $page_src);

	# Modify links to PHP scripts

	$page_src = preg_replace("/<a(.+)href[ ]*=[ ]*[\"']*(".$php_scripts_long.")([^\"^']*)[\"']/iUS", "<a\\1href=\"".$site_location["path"].DIR_CUSTOMER."/\\2\\3\"", $page_src);
	$page_src = preg_replace("/self\.location[ ]*=[ ]*([\"'])(".$php_scripts_long.")([^\"^']*)[\"']/iUS", "self.location=\\1".$site_location["path"].DIR_CUSTOMER."/\\2\\3\\1", $page_src);

	# Modify action values in HTML forms

	$page_src = preg_replace("/action[ ]*=[ ]*[\"']*(".$php_scripts_long.")([^\"^']*)[\"']/iUS", "action=\"".$site_location["path"].DIR_CUSTOMER."/\\1\\2\"", $page_src);

	# Strip all PHP transsids if any
	while (preg_match("/<a(.+)href[ ]*=[ ]*[\"']*([^\"^']*)(\?".$XCART_SESSION_NAME."=|&".$XCART_SESSION_NAME."=)([^\"^']*)[\"']/iS", $page_src))
		$page_src = preg_replace("/<a(.+)href[ ]*=[ ]*[\"']*([^\"^']*)(\?".$XCART_SESSION_NAME."=|&".$XCART_SESSION_NAME."=)([^\"^']*)[\"']/iS", "<a\\1href=\"\\2\"", $page_src);

	$page_src = preg_replace("/<input[ ]+type=\"hidden\"[ ]+name=\"".$XCART_SESSION_NAME."\"[ ]+value=\"[a-zA-z0-9]*\"[ ]*[\/]?>/iS", "", $page_src);

	$page_src = preg_replace("/(<form [^>]+>)/Ss", "\\1<input type=\"hidden\" name=\"is_hc\" value=\"Y\" />", $page_src);

	return $page_src;
}

function convert_page($store_path, $page_src, $name_func, $name_params=array()) {
	global $hc_state;

	if (empty($store_path))
		$store_path = $hc_state["catalog"]["path"];

	$page_name = make_page_name($name_func, $name_params);
	$page_src = process_page($page_src, $page_name, $name_func, $name_params);
	my_save_data($store_path."/".$page_name, $page_src);
}

#
# Detect sort category subpages
#
function process_sort_page($page_src, $params, $category_data, $i) {
	global $site_location, $robot_cookies, $sort_fields, $config, $hc_state;

	$subparams = array();
	if (preg_match_all("/home\.php\?[^\"' >]+&amp;sort\=([\w\d_]+)/S", $page_src, $match)) {
		foreach ($match[1] as $k => $v) {
			if (!in_array($v, $sort_fields) || isset($subparams[$v]))
				continue;
			
			$subparams[$v] = true;
			
			list($http_headers, $subpage_src) = func_fetch_page(
				$site_location["host"].":".$site_location["port"],
				$site_location["path"].DIR_CUSTOMER."/home.php",
				$params."&sort=".$v."&sort_direction=1",
				$robot_cookies);

			convert_page('',$subpage_src,'category',array($category_data["categoryid"], $category_data["category"], $i, $v, 1));

			if ($config['Appearance']['products_order'] == $v)
				continue;

			list($http_headers, $subpage_src) = func_fetch_page(
				$site_location["host"].":".$site_location["port"],
				$site_location["path"].DIR_CUSTOMER."/home.php",
				$params."&sort=".$v."&sort_direction=0",
				$robot_cookies);

			convert_page('',$subpage_src,'category',array($category_data["categoryid"], $category_data["category"], $i, $v, 0));
		}
	}

	return $page_src;
}

if ($REQUEST_METHOD=="POST" && $mode=="catalog_gen" || $REQUEST_METHOD=="GET" && $mode=="continue") {

	require $xcart_dir."/include/safe_mode.php";

	echo func_get_langvar_by_name("lbl_generating_catalog",false,false,true)."<br /><br />";
	func_flush();

	# variables initiation
	x_session_register("hc_state");
	if($config["General"]["shop_closed"] == "Y" && !empty($config["General"]["shop_closed_key"])) {
		$shop_closed_var = "&shopkey=".$config["General"]["shop_closed_key"];
	}

	if (empty($hc_state) || $REQUEST_METHOD=="POST") {
		$hc_state="";
		$hc_state["category_processed"] = false;
		$hc_state["catproducts_processed"] = false;
		$hc_state["last_cid"] = 0;
		$hc_state["last_pid"] = 0;
		$hc_state["cat_pages"] = 0;
		$hc_state["cat_page"] = 1;
		$hc_state["last_pageid"] = 0;
		$hc_state["last_manufacturerid"] = 0;
		$hc_state["count"] = 0;
		$hc_state["start_category"] = $start_category;
		$hc_state["pages_per_pass"] = $pages_per_pass;
		$hc_state["gen_action"] = $gen_action;
		$hc_state["process_subcats"] = isset($process_subcats);
		$hc_state["process_staticpages"] = isset($process_staticpages);
		$hc_state["process_manufacturers"] = true;
		$hc_state["time_start"] = func_microtime();

		if (!isset($name_styles[$namestyle])) {
			if(isset($name_styles["default"])) {
				$namestyle = "default";
			}
			else {
				reset($name_styles);
				$namestyle = key($name_styles);
			}
		}

		$hc_state["namestyle"] = $name_styles[$namestyle];
		$genlng = array();
		$lngdel = array();
		if (is_array($lngcat)) {
			foreach ($lngcat as $code=>$path) {
				if (trim($path) != "") {
					$genlng[] = array("code"=>$code, "path"=>func_normalize_path($xcart_dir."/".$path), "webpath"=>func_normalize_path($site_location["path"]."/".$path."/","/"));
				}
				else {
					$lngdel[] = $code;
				}
			}
		}

		if (empty($genlng)) {
			$top_message["content"] = func_get_langvar_by_name("msg_err_hc_no_languages");
			$top_message["type"] = "E";
			func_header_location("html_catalog.php");
		}

		if (!empty($lngdel))
			$hc_state["remove_lng_line"] = implode("|", $lngdel);

		$hc_state["catalog_dirs"] = $genlng;
		$hc_state["catalog_idx"] = 0;

		# If only one language, then remove the "select language" form
		if (count($hc_state["catalog_dirs"]) == 1)
			$hc_state["remove_slform"] = true;

		if ($drop_pages == "on") {
			echo func_get_langvar_by_name("lbl_deleting_old_catalog",false,false,true)."<br />";
			func_flush();
			$__tmp = func_query("SELECT filename FROM $sql_tbl[pages] WHERE level='R' AND active = 'Y'");
			$static_root_pages = array();
			if (is_array($__tmp)) {
				foreach($__tmp as $__v)
					$static_root_pages[] = $__v["filename"];
			}

			foreach ($hc_state["catalog_dirs"] as $catdir) {
				if (!file_exists($catdir["path"]))
					continue;

				if (!is_dir($catdir["path"])) {
					unlink($catdir["path"]);
					continue;
				}

				$dir = opendir ($catdir["path"]);
				while ($file = readdir ($dir)) {
					if (($file == ".") || ($file == "..") || ($file=="shop_closed.html") || (strstr($file,".html")!=".html"))
						continue;

					if (in_array($file, $static_root_pages)) continue;

					if ((filetype ($catdir["path"]."/".$file) != "dir")) {
						unlink ($catdir["path"]."/".$file);
					}
				}
			}
		}

		echo func_get_langvar_by_name("lbl_converting_pages_to_html",false,false,true)."<br />"; func_flush();

		# Dump X-cart home page to disk

		foreach ($hc_state["catalog_dirs"] as $catdir) {
			$hc_state["catalog"] = $catdir;
			$current_lng = $catdir["code"];
			list($http_headers, $page_src) = func_fetch_page(
				$site_location["host"].":".$site_location["port"],
				$site_location["path"].DIR_CUSTOMER."/home.php",
				"sl=".$catdir["code"].$shop_closed_var,
				$robot_cookies);

			if (empty($hc_state['sid'])) {
				if (!empty($http_headers['cookies'][$XCART_SESSION_NAME])) {
					$sid = $http_headers['cookies'][$XCART_SESSION_NAME];
				}
				else {
					$sid = md5('HTML_Catalog_'.rand());
				}

				$hc_state['sid'] = $sid;
				$robot_cookies[] = $XCART_SESSION_NAME.'='.$sid;
			}

			if (!$hc_state["process_staticpages"]) $php_scripts [] = "pages.php";
			$php_scripts_long = implode("|", $php_scripts);

			convert_page($catdir["path"],$page_src,'');
		}

		$hc_state["catalog"] = $hc_state["catalog_dirs"][$hc_state["catalog_idx"]];
	}
	else {
		echo func_get_langvar_by_name("lbl_continue_converting_pages_to_html", array("count" => $hc_state["count"]),false,true)."<br />"; func_flush();
		if (!$hc_state["process_staticpages"])
			$php_scripts [] = "pages.php";

		$php_scripts_long = implode("|", $php_scripts);

		if (empty($hc_state['sid'])) {
			$hc_state['sid'] = md5('HTML_Catalog_'.rand());
		}

		$robot_cookies[] = $XCART_SESSION_NAME.'='.$hc_state['sid'];
	}

	#
	# Process static pages
	#
	if ($hc_state["process_staticpages"]) {
		$current_lng = $hc_state["catalog"]["code"];
		$pages_data = db_query("SELECT pageid, title FROM $sql_tbl[pages] WHERE active = 'Y' AND pageid > '$hc_state[last_pageid]' AND level='E' AND language='$current_lng' ORDER BY pageid");

		while ($page_data = db_fetch_array($pages_data)) {
			$hc_state["last_pageid"] = $page_data["pageid"];
			list($http_headers, $page_src) = func_fetch_page(
				$site_location["host"].":".$site_location["port"],
				$site_location["path"].DIR_CUSTOMER."/pages.php",
				"pageid=$page_data[pageid]&sl=".$hc_state["catalog"]["code"].$shop_closed_var,
				$robot_cookies);

			convert_page('',$page_src,'staticpage',array($page_data["pageid"], $page_data["title"]));
		}

		db_free_result($pages_data);
	}

	#
	# Process manufacturers
	#
	if ($hc_state["process_manufacturers"]) {
		$current_lng = $hc_state["catalog"]["code"];
		$pages_data = db_query("
SELECT $sql_tbl[manufacturers].manufacturerid,
IF($sql_tbl[manufacturers_lng].manufacturer IS NOT NULL AND $sql_tbl[manufacturers_lng].manufacturer<>'',$sql_tbl[manufacturers_lng].manufacturer,$sql_tbl[manufacturers].manufacturer) AS manufacturer
FROM $sql_tbl[manufacturers]
	LEFT JOIN $sql_tbl[manufacturers_lng] ON
		$sql_tbl[manufacturers].manufacturerid=$sql_tbl[manufacturers_lng].manufacturerid AND
		$sql_tbl[manufacturers_lng].code='$current_lng'
WHERE $sql_tbl[manufacturers].manufacturerid > '$hc_state[last_manufacturerid]'
ORDER BY $sql_tbl[manufacturers].manufacturerid
");

		while ($page_data = db_fetch_array($pages_data)) {
			$hc_state["last_manufacturerid"] = $page_data["manufacturerid"];
			list($http_headers, $page_src) = func_fetch_page(
				$site_location["host"].":".$site_location["port"],
				$site_location["path"].DIR_CUSTOMER."/manufacturers.php",
				"manufacturerid=$page_data[manufacturerid]&sl=".$current_lng.$shop_closed_var,
				$robot_cookies);

			convert_page('',$page_src,'manufacturer',array($page_data["manufacturerid"], $page_data["manufacturer"]));
		}

		db_free_result($pages_data);
	}

	#
	# Let's generate the catalog
	#
	if ($hc_state["cat_pages"] > 0 || isset($hc_state["catproducts"]))
		$categories_cond = "$sql_tbl[categories].categoryid >= ".$hc_state["last_cid"];
	else
		$categories_cond = "$sql_tbl[categories].categoryid > ".$hc_state["last_cid"];

	if (!empty($hc_state["start_category"]))
		$categories_cond .= " AND ($sql_tbl[categories].categoryid_path='".$hc_state["start_category"]."' ".(@$hc_state["process_subcats"]?" OR $sql_tbl[categories].categoryid_path LIKE '$hc_state[start_category]/%'":"").") ";

	$categories_cond .= " AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[category_memberships].categoryid IS NULL";

	if (!$hc_state["process_subcats"]) {
		if (!empty($hc_state["start_category"]))
			$categories_cond .= " AND $sql_tbl[categories].categoryid_path NOT LIKE '$hc_state[start_category]/%/%'";
		else
			$categories_cond .= " AND $sql_tbl[categories].categoryid_path NOT LIKE '%/%'";
	}

	$categories_data = db_query("SELECT $sql_tbl[categories].categoryid, $sql_tbl[categories].category, $sql_tbl[categories].categoryid_path FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[categories].categoryid = $sql_tbl[category_memberships].categoryid WHERE ".$categories_cond." GROUP BY $sql_tbl[categories].categoryid ORDER BY $sql_tbl[categories].categoryid");

	$avail_condition = "";
	if ($config["General"]["unlimited_products"] == "N" && $config["General"]["disable_outofstock_products"] == "Y")
		$avail_condition = " AND $sql_tbl[products].avail>0 ";

	if ($categories_data) {
		while ($category_data = db_fetch_array($categories_data)) {

			# Check parent categories availability
			$parents = explode("/", $category_data['categoryid_path']);
			array_pop($parents);
			if (!empty($parents)) {
				$res = db_query("SELECT $sql_tbl[categories].categoryid FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[categories].categoryid = $sql_tbl[category_memberships].categoryid WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $parents)."') AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[category_memberships].categoryid IS NULL GROUP BY $sql_tbl[categories].categoryid");
				if (!$res)
					continue;
				$parents_cnt = db_num_rows($res);
				db_free_result($res);
				if ($parents_cnt != count($parents))
					continue;
			}

			$hc_state["last_cid"] = $category_data["categoryid"];

			if (($hc_state["gen_action"] & 1) === 1 && !isset($hc_state["catproducts"])) {
				if ($hc_state["cat_pages"]==0 && !isset($hc_state["cat_done"])) {
					$product_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[products].productid = $sql_tbl[product_memberships].productid WHERE $sql_tbl[products_categories].categoryid = '$category_data[categoryid]' AND $sql_tbl[products_categories].productid = $sql_tbl[products].productid AND $sql_tbl[product_memberships].productid IS NULL ".$avail_condition);

					$pages = ceil($product_count/$per_page);
					if ($pages == 0) $pages = 1;

					$first = 1;
					$hc_state["cat_pages"] = $pages;
					$hc_state["cat_done"] = false;
				}
				else {
					$first = $hc_state["cat_page"]+1;
					$pages = $hc_state["cat_pages"];
				}

				# process pages of category
				if (!isset($hc_state["cat_done"]) || !@$hc_state["cat_done"]) {
					$current_lng = $hc_state["catalog"]["code"];
					for ($i = $first; $i <= $pages; $i++) {
						$page_query = "cat=$category_data[categoryid]&page=$i&sl=".$hc_state["catalog"]["code"].$shop_closed_var;

						list($http_headers, $page_src) = func_fetch_page(
							$site_location["host"].":".$site_location["port"],
							$site_location["path"].DIR_CUSTOMER."/home.php",
							$page_query,
							$robot_cookies);
						$hc_state["cat_page"] = $i;
						$page_src = process_sort_page($page_src, $page_query, $category_data, $i);
						convert_page('',$page_src,'category',array($category_data["categoryid"], $category_data["category"], $i, $config['Appearance']['products_order'], 0));
					}
				}

				unset($hc_state["cat_done"]);
				$hc_state["cat_page"] = 1;
				$hc_state["cat_pages"] = 0;
			}

			# process products in category
			if (($hc_state["gen_action"] & 2) === 2) {
				$prod_cond = " AND $sql_tbl[products].productid>".$hc_state["last_pid"];

				$products_data = db_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].product FROM $sql_tbl[products_categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[products].productid = $sql_tbl[product_memberships].productid WHERE $sql_tbl[products_categories].categoryid=$category_data[categoryid] AND $sql_tbl[products_categories].productid=$sql_tbl[products].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[product_memberships].productid IS NULL $prod_cond $avail_condition ORDER BY $sql_tbl[products].productid");
				if ($products_data) {
					$hc_state["catproducts"] = false;
					while($product_data = db_fetch_array($products_data)) {
						$hc_state["last_pid"] = $product_data["productid"];
						$current_lng = $hc_state["catalog"]["code"];

						list($http_headers, $page_src) = func_fetch_page(
							$site_location["host"].":".$site_location["port"],
							$site_location["path"].DIR_CUSTOMER."/product.php",
							"productid=$product_data[productid]&sl=".$hc_state["catalog"]["code"].$shop_closed_var,
							$robot_cookies);

						convert_page('',$page_src,'product',array($product_data["productid"], $product_data["product"]));
					}

					$hc_state["last_pid"] = 0;
					unset($hc_state["catproducts"]);
				}
			}
		}
	}

	$hc_state["catalog_idx"]++;
	if (isset($hc_state["catalog_dirs"][$hc_state["catalog_idx"]])) {
		$hc_state["catalog"] = $hc_state["catalog_dirs"][$hc_state["catalog_idx"]];
		$hc_state["category_processed"] = false;
		$hc_state["catproducts_processed"] = false;
		$hc_state["last_cid"] = 0;
		$hc_state["last_pid"] = 0;
		$hc_state["cat_pages"] = 0;
		$hc_state["cat_page"] = 1;
		$hc_state["last_pageid"] = 0;
		$hc_state["last_manufacturerid"] = 0;

		echo "<hr />";
		func_html_location("html_catalog.php?mode=continue",20);
	}

	$time_end = func_microtime();

	echo "<br />".func_get_langvar_by_name("lbl_html_catalog_created_successfully",false,false,true)."<br />";
	echo func_get_langvar_by_name("lbl_time_elapsed_n_secs", array("sec" => round($time_end-$hc_state["time_start"],2)),false,true);
	x_session_unregister("hc_state");
	func_html_location("html_catalog.php",30);
}
else {
	#
	# Grab all categories
	#
	x_session_unregister("hc_state");
	$categories = func_query("SELECT * FROM $sql_tbl[categories] WHERE parentid=0 ORDER BY category");

	#
	# Smarty display code goes here
	#
	$smarty->assign("cat_dir", func_normalize_path($xcart_dir.DIR_CATALOG));
	$smarty->assign("cat_url", $http_location.DIR_CATALOG."/index.html");
	$smarty->assign("default_catalog_path", DIR_CATALOG);
	$smarty->assign("categories", $categories);

	$smarty->assign("main","html_catalog");

	# Assign the current location line
	$smarty->assign("location", $location);

	@include $xcart_dir."/modules/gold_display.php";
	func_display("admin/home.tpl",$smarty);
}
?>
