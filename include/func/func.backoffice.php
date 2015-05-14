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
# $Id: func.backoffice.php,v 1.11.2.3 2006/06/23 07:14:18 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

#
# This function determine the files location for current user
#
function func_get_files_location () {
	global $login, $current_area, $active_modules, $single_mode, $files_dir_name;
	global $user_account;
	
	if ($single_mode || $user_account["usertype"] == "A")
		return $files_dir_name;

	return $files_dir_name.DIRECTORY_SEPARATOR.$login;
}

#
# This function updates/inserts the language variable into 'languages_alt'
#
function func_languages_alt_insert($name, $value, $code="") {
	global $sql_tbl, $all_languages;

	if (!is_array($all_languages))
		return false;

	if (empty($code)) {
		#
		# For empty code update/insert variables for all languages
		#
		foreach($all_languages as $k=>$v) {
			db_query("REPLACE INTO $sql_tbl[languages_alt] (code, name, value) VALUES ('$v[code]', '$name', '$value')");
		}
	}
	else {
		#
		# For not empty $code...
		#
		$result = false;

		#
		# Check if $code is valid
		#
		foreach($all_languages as $k=>$v) {
			if ($code == $v["code"]) {
				$result = true;
				break;
			}
		}

		if (!$result)
			return false;
		#
		# Update/insert variable for $code
		#
		db_query("REPLACE INTO $sql_tbl[languages_alt] (code, name, value) VALUES ('$code', '$name', '$value')");
	}

	return true;
}

#
# Callback function: determination of empty field
#
function func_callback_empty($value) {
	return strlen($value) > 0;
}

function func_disable_paypal_methods($paypal_solution, $enable=false) {
	global $sql_tbl;

	$paypal_direct = func_query_first("SELECT $sql_tbl[payment_methods].paymentid, $sql_tbl[payment_methods].active FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid<>$sql_tbl[ccprocessors].paymentid");
	$paypal_express = func_query_first("SELECT $sql_tbl[payment_methods].paymentid, $sql_tbl[payment_methods].active FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid=$sql_tbl[ccprocessors].paymentid");
	$paypal_standard = func_query_first("SELECT $sql_tbl[payment_methods].paymentid, $sql_tbl[payment_methods].active FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid=$sql_tbl[ccprocessors].paymentid");

	$paypal_directid = $paypal_direct['paymentid'];
	$paypal_expressid = $paypal_express['paymentid'];
	$paypalid = $paypal_standard['paymentid'];

	$disable_methods = array();
	$enable_methods = array();
	switch ($paypal_solution) {
	case 'ipn':
		$disable_methods = array($paypal_expressid, $paypal_directid);
		$enable_methods[] = $paypalid;
		break;
	case 'pro':
		$disable_methods[] = $paypalid;
		$enable_methods = array($paypal_expressid, $paypal_directid);
		if (!$enable && $paypal_express['active'] != 'Y') {
			$disable_methods[] = $paypal_expressid;
			$disable_methods[] = $paypal_directid;
		}
		break;
	case 'express':
		$disable_methods = array($paypalid, $paypal_directid);
		$enable_methods[] = $paypal_expressid;
		break;
	}

	if (!empty($disable_methods)) {
		db_query("UPDATE $sql_tbl[payment_methods] SET active='N' WHERE paymentid IN ('".implode("','", $disable_methods)."')");
	}

	if ($enable && !empty($enable_methods)) {
		db_query("UPDATE $sql_tbl[payment_methods] SET active='Y' WHERE paymentid IN ('".implode("','", $enable_methods)."')");
	}
}

#
# This function inserts the zone elements
# country (C), state (S), county (G), city (T), zip code (Z), address (A)
#
function func_insert_zone_element($zoneid, $field_type, $zone_elements) {
	global $sql_tbl;

	db_query("DELETE FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid' AND field_type='$field_type'");
	if (!empty($zone_elements) && is_array($zone_elements)) {
		foreach ($zone_elements as $k=>$v) {
			$v = trim($v);
			if (empty($v)) continue;

			db_query("REPLACE INTO $sql_tbl[zone_element] (zoneid, field, field_type) VALUES ('$zoneid', '$v', '$field_type')");
		}
	}
}

function func_array_merge_ext() {
	$vars = func_get_args();

	if (!is_array($vars) || empty($vars))
		return array();

	foreach($vars as $k => $v) {
		if (!is_array($v) || empty($v))
			unset($vars[$k]);
	}

	if (empty($vars))
		return array();

	$vars = array_values($vars);
	$orig = array_shift($vars);
	foreach ($vars as $var) {
		foreach ($var as $k => $v) {
			if (isset($orig[$k]) && is_array($orig[$k]) && is_array($v)) {
				$orig[$k] = func_array_merge_ext($orig[$k], $v);
			}
			else {
				$orig[$k] = $v;
			}
		}
	}

	return $orig;
}

#
# Get information about directory:
#  - how many files does directory contain
#  - what size does directory have
#
function func_get_dir_status( $directory ) {
	$result = array("files"=>0, "size"=>0);
	$dp = opendir ($directory);
	while ($file = readdir ($dp)) {
		if ($file == "." || $file == "..") continue;

		$path = $directory.DIRECTORY_SEPARATOR.$file;

		if( is_file( $path ) ) {
			$result["files"] ++;
			$result["size"]  += filesize($path);
		}
		else {
			$temp = func_get_dir_status($path);
			$result["files"] += $temp["files"];
			$result["size"]  += $temp["size"];
		}
	}

	closedir($dp);

	return $result;
}

#
# Delete single image
#
function func_delete_image($id, $type = 'T', $is_unique = false) {
	global $config, $sql_tbl, $xcart_dir;

	$where = ($is_unique ? "imageid" : "id");
	if (is_array($id)) {
		$where .= " IN ('".implode("','", $id)."')";
	}
	else {
		$where .= " = '$id'";
	}

	return func_delete_images($type, $where);
}

#
# Delete group of images.
# Advanced version of func_delete_image()
#
function func_delete_images($type = 'T', $where = '') {
	global $config, $sql_tbl, $xcart_dir;

	if (!isset($config['available_images'][$type]))
		return false;

	if (!empty($where))
		$where = " WHERE ".$where;

	$_table = $sql_tbl['images_'.$type];

	if (func_query_first_cell("SELECT COUNT(*) FROM ".$_table.$where) == 0)
		return false;

	$res = db_query("SELECT imageid, image_path, filename, (image IS NOT NULL AND LENGTH(image)>0) AS in_db FROM ".$_table.$where);
	if ($res) {
		x_load('image');
		$img_dir = func_image_dir($type)."/";
		while ($v = db_fetch_array($res)) {
			if ((!zerolen($v['image_path']) && is_url($v['image_path'])) || ($v['in_db'] && zerolen($v['image_path']))) {
				# Ignore URL and images in database
				continue;
			}

			$image_path = $v['image_path'];
			if (zerolen($image_path)) {
				$image_path = func_relative_path($img_dir.$v['filename']);
			}
				
			$is_found = false;
			# check other types
			foreach ($config['available_images'] as $k => $i) {
				$is_found = func_query_first_cell("SELECT COUNT(*) FROM ".$sql_tbl['images_'.$k]." WHERE image_path='".addslashes($image_path)."'".($k == $type ? " AND imageid != '$v[imageid]'" : "")) > 0;
				if ($is_found) break;
			}

			$image_file = $xcart_dir."/".$image_path;
			if (!$is_found && file_exists($image_file))
				@unlink($image_file);
		}

		db_free_result($res);
	}

	db_query("DELETE FROM ".$_table.$where);

	return true;
}

#
# This function updates the field 'display_states' of xcart_countries table
# depending on existing states information
#
function func_update_country_states ($country, $all_countries=false) {
	global $sql_tbl;

	$countries = array();
	
	if (empty($country) && !$all_countries) {
		return;
	}
	elseif (!$all_countries) {

		if (is_array($country))
			$countries = $country;
		elseif (!empty($country))
			$countries[] = $country;
	
	}

	$countries_with_states = func_query_column("SELECT DISTINCT(country_code) FROM $sql_tbl[states] WHERE 1 " . (!empty($countries) ? " AND country_code IN ('".implode("','", $countries)."')" : ""));

	db_query("UPDATE $sql_tbl[countries] SET display_states='N' WHERE 1" . (!empty($countries) ? " AND code IN ('".implode("','", $countries)."')" : ""));

	if (!empty($countries_with_states))
		db_query("UPDATE $sql_tbl[countries] SET display_states='Y' WHERE code IN ('" . implode("','", $countries_with_states) . "')");

}

#
# Display time period
#
function func_display_time_period($t) {
	if (empty($t))
		return "0:0:0";

	$ms = $t - floor($t);
	$ms = $ms > 0 ? round($ms*1000, 0) : 0;

	$t = floor($t);
	$s = $t % 60;

	$t = floor($t / 60);
	$m = $t > 0 ? $t % 60 : 0;

	if ($t > 0)
		$t = floor($t / 60);

	$h = $t > 0 ? $t % 24 : 0;
	
	return $h.":".$m.":".$s;

}

#
# Detect max data size for inserting to DB
#
function func_get_max_upload_size() {
	global $sql_max_allowed_packet;

	$upload_max_filesize = trim(ini_get("upload_max_filesize"));
	if (preg_match("/^\d+(G|M|K)$/", $upload_max_filesize, $match)) {
		$upload_max_filesize = doubleval(substr($upload_max_filesize, 0, -1));
		switch ($match[1]) {
			case "G":
				$upload_max_filesize = $upload_max_filesize*1024;
			case "M":
				$upload_max_filesize = $upload_max_filesize*1024;
			case "K":
				$upload_max_filesize = $upload_max_filesize*1024;
		}

	} else {
		$upload_max_filesize = intval($upload_max_filesize);
	}

	if ($sql_max_allowed_packet && $sql_max_allowed_packet < $upload_max_filesize) {
		$upload_max_filesize = $sql_max_allowed_packet-1024;
	}

	if ($upload_max_filesize > 1073741824) {
		$upload_max_filesize = round($upload_max_filesize/1073741824, 1)."G";

	} elseif ($upload_max_filesize > 1048576) {
		$upload_max_filesize = round($upload_max_filesize/1048576, 1)."M";

	} elseif ($upload_max_filesize > 1024) {
		$upload_max_filesize = round($upload_max_filesize/1024, 1)."K";
	}

	return $upload_max_filesize;
}

?>
