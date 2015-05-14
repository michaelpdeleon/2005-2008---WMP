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
# $Id: froogle.php,v 1.42.2.9 2006/08/04 13:07:33 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

define("FROOGLE_TAIL", '...');
define("FROOGLE_TAIL_LEN", strlen(constant("FROOGLE_TAIL")));

x_session_register("store_froogle_lng");
x_session_register("store_froogle_iso");

#
# Translation string to frogle-compatibility-string
#
function func_froogle_convert($str, $max_len = false) {
	static $tbl = false;

	if ($tbl === false)
		$tbl = array_flip(get_html_translation_table(HTML_ENTITIES));

	$str = str_replace(array("\n","\r","\t"), array(" ", "", " "), $str);
	$str = strip_tags($str);
	$str = strtr($str, $tbl);

	if ($max_len > 0 && strlen($str) > $max_len) {
		$str = preg_replace("/\s+?\S+.{".intval(strlen($str)-$max_len-1+FROOGLE_TAIL_LEN)."}$/Ss", "", $str).FROOGLE_TAIL;
		if (strlen($str) > $max_len)
			$str = substr($str, 0, $max_len-FROOGLE_TAIL_LEN).FROOGLE_TAIL;
	}

	return $str;
}

x_load('backoffice','files','taxes');

set_time_limit(0);

$location[] = array(func_get_langvar_by_name("lbl_froogle_export"), "");
include $xcart_dir."/include/import_tools.php";

$is_ftp_module = '';
if(function_exists("ftp_connect") && !empty($config['Froogle']['froogle_username']) && !empty($config['Froogle']['froogle_password']))
	$is_ftp_module = 'Y';

$froogle_host = 'uploads.google.com';

x_session_register("store_froogle_filename");

# Export data
if (!empty($active_modules["Froogle"]) && $REQUEST_METHOD == "POST" && $mode == "fcreate") {
	if (empty($froogle_file))
		$froogle_file = ($config['Froogle']['froogle_username'] ? $config['Froogle']['froogle_username'] : "froogle").".txt";
	$store_froogle_filename = $froogle_file;

	if ($froogle_iso && strlen($froogle_iso) < 2 && strlen($froogle_iso) > 3)
		$froogle_iso = false;

	if ($froogle_iso)
		$froogle_iso = strtolower($froogle_iso);

	$froogle_file = func_get_files_location().DIRECTORY_SEPARATOR.$froogle_file;

	$fp = func_fopen($froogle_file, "w", true);

	if ($fp !== false) {
		# Write file header

		# Full header: 
		# title\tdescription\tlink\timage_link\tid\texpiration_date\tlabel\tprice\tprice_type\tcurrency\tpayment_accepted\tpayment_notes\tquantity\tbrand\tupc\tisbn\tmanufacturer\tmanufacturer_id\tmemory\tprocessor_speed\tmodel_number\tsize\tweight\tcondition\tcolor\tactor\tartist\tauthor\tformat\tproduct_type\tlocation
		if ($froogle_iso) {
			fputs($fp, "title\tdescription\tlink\timage_link\tid\tlabel\tprice\tcurrency\tpayment_accepted\tpayment_notes\tquantity\tmanufacturer\tmanufacturer_id\tweight\texpiration_date\tlanguage\n");

		} else {
			fputs($fp, "title\tdescription\tlink\timage_link\tid\tlabel\tprice\tcurrency\tpayment_accepted\tpayment_notes\tquantity\tmanufacturer\tmanufacturer_id\tweight\texpiration_date\n");
		}

		$where = "";
		$fields = "";
		$joins = "";

		if ($config["General"]["disable_outofstock_products"] == "Y") {
			if (!empty($active_modules['Product_Options'])) {
				$where .= " AND IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > 0";
			} else {
				$where .= " AND $sql_tbl[products].avail > 0";
			}
		}

		$joins .= " INNER JOIN $sql_tbl[quick_prices] ON $sql_tbl[quick_prices].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].membershipid = 0";
		if (!empty($active_modules['Product_Options'])) {
			$joins .= " LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].variantid = $sql_tbl[variants].variantid";
			$fields .= ", IFNULL($sql_tbl[variants].productcode, $sql_tbl[products].productcode) as productcode, IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) as avail, IFNULL($sql_tbl[variants].weight, $sql_tbl[products].weight) as weight";
		}

		if ($froogle_lng) {
			$joins .= " LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$froogle_lng'";
			$fields .= ", IF($sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product, IF($sql_tbl[products_lng].descr != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) as descr";
		}

		$products = db_query("SELECT $sql_tbl[products].*, $sql_tbl[categories].categoryid_path, $sql_tbl[pricing].price, $sql_tbl[images_T].image_path $fields FROM $sql_tbl[products], $sql_tbl[categories], $sql_tbl[products_categories], $sql_tbl[pricing] LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[products].productid = $sql_tbl[images_T].id $joins WHERE $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[pricing].priceid = $sql_tbl[quick_prices].priceid AND $sql_tbl[products].forsale = 'Y' AND $sql_tbl[categories].avail = 'Y' $where GROUP BY $sql_tbl[products].productid HAVING price > 0 ORDER BY $sql_tbl[products].product");
		$cnt = 0;
		while ($product = db_fetch_array($products)) {

			# Define product category path
			$cats = array();
			$catids = explode("/", $product['categoryid_path']);
			$cats = "";
			$product['manufacturer'] = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers] WHERE $sql_tbl[manufacturers].manufacturerid = '$product[manufacturerid]'");

			if (!empty($catids)) {
				$cats = func_query("SELECT categoryid, category FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("','", $catids)."') AND avail = 'Y'");
				$catids = array_flip($catids);
				if (!empty($cats)) {
					if (count($cats) != count($catids))
						continue;

					foreach ($cats as $k => $v) {
						if (isset($catids[$v['categoryid']])) {
							$catids[$v['categoryid']] = $v['category'];
						}
					}

					$cats = str_replace("\t", " ", implode(" > ", $catids));
				}
			}

			# Define full description
			if (!empty($product['fulldescr']))
				$product['descr'] = $product['fulldescr'];

			# Define product image
			$tmp = func_query_first("SELECT id, image_path FROM $sql_tbl[images_P] WHERE $sql_tbl[images_P].id = '$product[productid]'");
			$tmbn = "";
			$image_path = "";
			$image_type = "";
			
			if (!empty($tmp['id'])) {
				$image_path = $tmp['image_path'];
				$image_type = "P";

			} elseif (!is_null($product['image_path'])) {
				$image_path = $product['image_path'];
				$image_type = "T";
			}

			if (!empty($image_type)) {
				if (!empty($image_path))
					$tmbn = func_get_image_url($product['productid'], $image_type, $image_path);
				if ($tmbn === false || empty($tmbn)) {
					$tmbn = $http_location."/image.php?id=".$product['productid']."&type=".$image_type;

				} elseif (strpos($tmbn, $https_location) !== false) {
					$tmbn = str_replace($https_location, $http_location, $tmbn);
				}
			}

			$ci = array(
				"city" => $config['General']['default_city'],
				"state" => $config['General']['default_state'],
				"country" => $config['General']['default_country'],
				"zipcode" => $config['General']['default_zipcode']

			);

			if (!empty($active_modules['Product_Options']))
				$product['price'] += func_get_default_options_markup($product['productid'], $product['price']);

			$tmp = func_tax_price($product['price'], $product['productid'], false, NULL, $ci);
			$product['price'] = $tmp['taxed_price'];

			# Define product keywords
			$keywords = "";
			if (!empty($product['keywords'])) {
				$keywords = explode(" ", $product['keywords']);
				$keywords = func_array_map("trim", $keywords);
				foreach ($keywords as $k => $v) {
					if (strlen($v) > 40 || empty($v))
						unset($keywords[$k]);
				}
				$keywords = implode(",", $keywords);
			}

			# Post string
			$post = func_froogle_convert($product['product'], 80)."\t".
				func_froogle_convert($product['descr'], 65536)."\t".
				$http_location.constant("DIR_CUSTOMER")."/product.php?productid=".$product['productid']."\t".
				$tmbn."\t".
				$product['productid']."\t".
				func_froogle_convert($keywords, 65536)."\t".
				number_format(round($product['price'], 2), 2, ".", "")."\t".
				(empty($config['Froogle']['froogle_currency']) ? "USD" : $config['Froogle']['froogle_currency'])."\t".
				func_froogle_convert($config['Froogle']['froogle_payment_accepted'], 65536)."\t".
				func_froogle_convert($config['Froogle']['froogle_payment_notes'], 65536)."\t".
				$product['avail']."\t".
				func_froogle_convert($product['manufacturer'], 80)."\t".
				$product['manufacturerid']."\t".
				$product['weight']."\t".
				date("Y-m-d", time()+(empty($config['Froogle']['froogle_expiration_date']) ? 0.5 : $config['Froogle']['froogle_expiration_date'])*86400);

			if ($froogle_iso)
				$post .= "\t".$froogle_iso;

			fputs($fp, $post."\n");
			$cnt++;
			if ($cnt % 100 == 0) {
				echo ".";
				if($cnt % 5000 == 0) {
					echo "<br />\n";
				}

				func_flush();
			}
		}

		$user_account = $_user_account;
		fclose($fp);
		$top_message["type"] = "I";
		$top_message["content"] = func_get_langvar_by_name("msg_adm_froogle_file_success");
	}
	else {
		$top_message["type"] = "E";
		$top_message["content"] = func_get_langvar_by_name("msg_adm_froogle_file_unsuccess");
	}

	if ($froogle_lng)
		$store_froogle_lng = $froogle_lng;

	if ($froogle_iso)
		$store_froogle_iso = $froogle_iso;

	func_header_location("froogle.php");
}
elseif(!empty($active_modules["Froogle"]) && $REQUEST_METHOD == "POST" && $mode == "fdownload" && $froogle_file) {
	$froogle_file = func_get_files_location().DIRECTORY_SEPARATOR.$froogle_file;
	# Download export file
	if (!file_exists($froogle_file)) {
		$top_message['content'] = func_get_langvar_by_name("lbl_file_not_found");
		$top_message['type'] = "E";
		func_header_location("froogle.php");
	}

	header("Content-type: application/force-download");
	header("Content-Disposition: attachment; filename=".basename($froogle_file));
	func_readfile($froogle_file);
	exit;
}
elseif(!empty($active_modules["Froogle"]) && $REQUEST_METHOD == "POST" && $mode == "fupload" && $froogle_file && $is_ftp_module) {
	$froogle_file = func_get_files_location().DIRECTORY_SEPARATOR.$froogle_file;
	# Upload export file to Froogle server
	if (!file_exists($froogle_file)) {
		$top_message['content'] = func_get_langvar_by_name("lbl_file_not_found");
		$top_message['type'] = "E";
		func_header_location("froogle.php");
	}

	$store_froogle_filename = $froogle_file;

	if (function_exists("ftp_connect")) {
		$ftp = ftp_connect($froogle_host);
		$top_message["type"] = "E";
		if($ftp && ftp_login($ftp, $config['Froogle']['froogle_username'], $config['Froogle']['froogle_password'])) {
			ftp_pasv($ftp, true);
			$fp = func_fopen($froogle_file, "r", true);
			if ($fp) {
				if (@ftp_fput($ftp, basename($froogle_file), $fp, FTP_BINARY)) {
					$top_message["content"] = func_get_langvar_by_name("msg_adm_froogle_success");
					$top_message["type"] = "I";
				}
				else {
					$top_message["content"] = func_get_langvar_by_name("msg_adm_err_froogle_FTP_write_failed");
				}

				fclose($fp);
			}
			else {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_froogle_file_not_found");
			}

			ftp_quit($ftp);
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_err_froogle_FTP_failed");
		}
	}
	else {
		@exec("ftp -v -u ftp://".$config['Froogle']['froogle_username'].":".$config['Froogle']['froogle_password']."@".$froogle_host."/".func_shellquote(basename($froogle_file))." ".func_shellquote($froogle_file)." 2>&1", $res);
		$res = @implode("\n", $res);
		if (strpos($res, "226 ") !== false) {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_froogle_success");
			$top_message["type"] = "I";
		}
		else {
			$top_message["type"] = "E";
			$top_message["content"] = func_get_langvar_by_name("msg_adm_err_froogle_FTP_failed");
		}
	}

	func_header_location("froogle.php");
}

$smarty->assign("froogle_file", $store_froogle_filename);
$smarty->assign("def_froogle_file", ($config['Froogle']['froogle_username'] ? $config['Froogle']['froogle_username'] : "froogle").".txt");

$smarty->assign("is_ftp_module", $is_ftp_module);

$smarty->assign("main", "froogle_export");

if ($store_froogle_iso)
	$smarty->assign("froogle_iso", $store_froogle_iso);
$smarty->assign("froogle_lng", $store_froogle_lng ? $store_froogle_lng : $shop_language);

# Assign the current location line
$smarty->assign("location", $location);

?>
