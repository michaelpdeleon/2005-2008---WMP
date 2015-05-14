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
# $Id: fedex_import.php,v 1.26 2006/02/10 14:08:08 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

#
# Rates by service & by zone
#
$fedex_rates_files = array (

	# US Express rates by Service
	array("name" => "2nd day"),
	array("name" => "express saver"),
	array("name" => "first overnight"),
	array("name" => "priority overnight"),
	array("name" => "standard overnight"),

	# International express
	array("name" => "international economy"),
	array("name" => "international priority"),

	# Ground & Home Delivery
	array("name" => "ground"),
	array("name" => "home delivery"),

	# Rates by zone
	array("name" => "puerto rico", "zone" => "PR")

);


/**********************************************************
* Checks if all files is present
***********************************************************/
function fedex_check_for_files($ozip) {
	global $fedex_path_to_import, $fedex_rates_files, $fedex_zone_file;

	# make the list of readable files
	$file_list = array();
	$dir = opendir($fedex_path_to_import);
	if (!$dir) return false;

	if ($dir = opendir($fedex_path_to_import)) {
		while (($file = @readdir($dir)) !== false) {
			if ($file == "." || $file == "..")
				continue;

			$f = func_fopen ($fedex_path_to_import.$file, "r", true);
			if ($f === false) continue;

			$first_line = @fgets($f, 4096);
			# extract service name
			if (strcasecmp(substr($file,-4),'.csv')==0) {
				$service_name = strtolower(extract_service($first_line));
				$file_list[$service_name] = $file;
			} else {
				$file_list[] = $file;
			}

		}

		closedir($dir);
	}
	else {
		return false;
	}

	# assign real file names
	for ($i=0; $i<sizeof($fedex_rates_files); $i++) {
		$name = $fedex_rates_files[$i]['name'];

		if (isset($file_list[$name])==true)
			$fedex_rates_files[$i]["real_name"] = $file_list[$name];
	}

	$found = false;
	foreach($file_list as $file) {
		if (preg_match("/(\d{5})-(\d{5})\.txt/iS",$file,$matches)) {
			if (($matches[1]<=$ozip) && ($ozip<=$matches[2])) {
				$fedex_zone_file = $file;
				$found = true;
				break;
			}
		}
	}
}


/**********************************************************
* Parse & import zone locator file
***********************************************************/
function fedex_import_rates(&$methods_count) {
	global $fedex_rates_files, $fedex_path_to_import, $sql_tbl;

	db_query("DELETE FROM $sql_tbl[fedex_rates]");
	$methods_count = 0;

	foreach($fedex_rates_files as $f) {
		if (isset($f['real_name'])) {
			if (isset($f['zone'])) {
				Import_ZoneFile($fedex_path_to_import.$f['real_name'], $f['zone']);
			}
			else {
				Import_ServFile($fedex_path_to_import.$f['real_name'], $f['name']);
				$methods_count++;
			}
		}
	}
}


/******************************************
* Extract service name from string
******************************************/
function extract_service($line) {

	$line = preg_replace("/([.]*)([,]{2,})\s*$/SU","\\1",$line);

	$line = preg_replace("/\s*\"\s*(.*)\s*\"\s*$/S","\\1",$line);
	$line = preg_replace("/^.*Rates:(.*)$/iS","\\1",$line);

	$line = preg_replace("/\s*FedEx(.*)$/iUS", "\\1", $line);
	$line = trim(preg_replace("/(.*)\([^\(\)]*\)/US", "\\1", $line));

	if (ord($line[strlen($line)-1])==174) $line = trim(substr($line, 0, -1));

	$line = preg_replace("/\s*(.*)\s*standard\s*list\s*base\s*rates\s*$/iUS", "\\1", $line);
	$line = preg_replace("/\s*(.*)\s*standard\s*list\s*rates\s*$/iUS", "\\1", $line);
	$line = preg_replace("/\s*(.*)\s*base\s*rates\s*$/iUS", "\\1", $line);
	$line = preg_replace("/\s*(.*)\s*rates\s*$/iUS", "\\1", $line);
	$line = preg_replace("/^\s*(.*)\s*(\d+)\s*$/iUS", "\\1", $line);

	$line = str_replace("1Day", "1st Day", $line);
	$line = str_replace("2Day", "2nd Day", $line);
	$line = trim(str_replace("3Day", "3rd Day", $line));

	$line = str_replace("US to ", "", $line);

	return $line;

}

function Parse_ZoneID($zone) {
	$result = array();
	if (strpos($zone, ",") !== false) {
		foreach(explode(",", $zone) as $val) {
			$a = Parse_ZoneID($val);
			$result = func_array_merge($result, $a);
		}
	}
	elseif (strpos($zone, "-") !== false) {
		list($val1, $val2) = explode("-", $zone);
		$result = func_array_merge($result, range((int)$val1, (int)$val2));
	}
	elseif (strpos($zone, "/") !== false) {
		list($val1, $val2) = explode("/", $zone);
		$result[] = $val1;
		$result[] = $val2;
	}
	elseif (strpos($zone, "&") !== false) {
		list($val1, $val2) = explode("&", $zone);
		$result[] = $val1;
		$result[] = $val2;
	}
	elseif (is_numeric($zone[0])) {
		$result[] = (int)$zone;
	}
	else {
		$result[] = $zone;
	}

	$result = array_unique($result);
	return $result;
}

/******************************************
* Function to Import rates by Zone
*****************************************/
function Import_ZoneFile($name, $zone) {
	global $sql_tbl;

	$zones = Parse_ZoneID($zone);

	$f = func_file($name, true);

	foreach($f as $line) {
		$line = str_replace("Wgt. (lbs.)", "Weight", $line);
		# done: process fields like that: "1,000-1,999 lbs."
		$line = preg_replace("/\"\s*(([\d]+)[,]?([\d]+))?([+-]?)(([\d]+)[,]?([\d]+))?\s*(lb(s)?\.)?\s*\"/iS", "\\2\\3\\4\\6\\7",$line);

		$line = explode(",",$line);

		# skip empty lines
		if (trim($line[0])=='') continue;

		# set $flag if it's the hundred rates
		if (strstr($line[0],"Hundredweight") || strstr($line[0],"Box")) {
			$flag++;
			continue;
		}

		# read methods into an array
		if (trim($line[0])=="Weight") {
			for ($i=1; $i<sizeof($line); $i++) {
				# get shipping method
				$method = trim($line[$i]);
				# remove string "rates" from it's end
				if (strcasecmp(substr($method, -5), 'Rates')==0)
					$method = trim(substr($method, 0, -5));
				# remove string "FedEX" from it's start
				if (strcasecmp(substr($method, 0, 5), 'FedEx')==0)
					$method = trim(substr($method, 5));

				$columns[$i]['text'] = $method;
				$res = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE shipping = 'FedEx ".$columns[$i]['text']."'");
				$columns[$i]['id'] = $res["shippingid"];
			}
			continue;
		}

		# the weight
		$weight = trim(str_replace('lbs.','',$line[0]));
		$weight = trim(str_replace('lb.','',$weight));

		# fill the rates
		for ($i=1; $i<sizeof($line); $i++) {
			if (($columns[$i]['text']!='') && (!(strpos($line[$i], '$')===false))) {
				$rate = trim(str_replace('$','',$line[$i]));
				foreach($zones as $zid) {
					db_query("INSERT INTO $sql_tbl[fedex_rates](r_zone, r_weight, r_meth_id, r_rate, r_ishundreds) VALUES('".$zid."', '".$weight."', '".(int)$columns[$i]['id']."', '".$rate."', '".(int)$flag."')");
				}
			}
		}
	}
}


/***************************************
* Function to Import rates by Service
***************************************/
function Import_ServFile($name, $serv) {
	global $sql_tbl;

	$f = func_file($name, true);

	# determine the shipping method id (and updete if needed)
	$res = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE shipping = 'FedEx ".$serv."'");
	$method_id = $res["shippingid"];

	if (!$method_id)
		return;

	foreach($f as $raw_line) {
		$raw_line = str_replace("Wgt. (lbs.)", "Weight", $raw_line);
		# done: process fields like that: "1,000-1,999 lbs."
		$raw_line = preg_replace("/\"\s*(([\d]+)[,]?([\d]+))?([+-]?)(([\d]+)[,]?([\d]+))?\s*(lb(s)?\.)?\s*\"/iS", "\\2\\3\\4\\6\\7",$raw_line);

		$line = explode(",",$raw_line);

		# skip empty lines
 		if (trim($line[0])=='' && (count($line) == 1 || trim($line[1])==''))
			continue;

		# set $flag if it's the hundred rates
		if (strstr($raw_line,"Hundredweight") || strstr($line[0],"Box")) {
			$flag++;
			continue;
		}

		# read zones into an array
		if (trim($line[0])=="Weight" || strstr(strtoupper($raw_line),'ZONE')) {
			for ($i=1; $i<sizeof($line); $i++) {
				# get shipping zone
				$zone = trim($line[$i]);
				# remove string "ZONE" from it's start
				if (strcasecmp(substr($zone, 0, 4), 'ZONE')==0)
					$zone = trim(substr($zone, 4));

				$zone = str_replace('&',',',$zone);
				$zone = str_replace('/',',',$zone);
				$zone = str_replace(' ','',$zone);
				if ($zone=='')
					continue;

				$columns[$i] = $zone;
			}
			continue;
		}

		# the weight
		$weight = trim(str_replace('lbs.','',$line[0]));
		$weight = trim(str_replace('lb.','',$weight));

		if (!preg_match('!^(?:\d+|\d+-\d+|\d+\+)$!S', $weight)) {
			# Only digits are supported.
			# Hundredweight rates are: 999-999, 999+
			continue;
		}

		# fill the rates
		for ($i=1; $i<sizeof($line); $i++) {
			if (($columns[$i]!='') && (!(strpos($line[$i], '$')===false))) {
				$rate = trim(str_replace('$','',$line[$i]));
				$zoneid = trim(str_replace('$','',$columns[$i]));
				$zones = Parse_ZoneID($zoneid);
				foreach($zones as $zid) {
					db_query("INSERT INTO $sql_tbl[fedex_rates](r_zone, r_weight, r_meth_id, r_rate, r_ishundreds) VALUES('".$zid."', '".$weight."', '".$method_id."', '".$rate."', '".(int)$flag."')");
				}
			}
		}
	}
}


/**********************************************************
* Parse & import zone locator file
***********************************************************/
function fedex_parse_zone_locator_file($fname) {
	global $sql_tbl;

	db_query("DELETE FROM $sql_tbl[fedex_zips] WHERE zip_first REGEXP \"[0-9]{3}\"");

	if (!file_exists($fname)) return false;

	$lines = func_file($fname, true);
	$cont_us_flag = 0;
	$non_cont_us_flag = 0;
	$tbl_begin_flag = 0;

	$fedex_es = array();
	$fedex_ground_id = false;

	foreach($lines as $line) {
		if (strcasecmp(trim($line),"CONTIGUOUS U.S.")==0) {
			$cont_us_flag=1; $non_cont_us_flag=0;
			continue;
		}
		elseif (strcasecmp(trim($line),"ALASKA, HAWAII, & PUERTO RICO")==0) {
			$cont_us_flag=0; $non_cont_us_flag=1;
			$express_family = array("% Overnight", "2nd Day", "Express Saver");

			foreach ($express_family as $meth_name) {
				$val = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shipping LIKE 'FedEx $meth_name' AND active='Y'");
				if ($val !== false) $fedex_es[] = $val;
			}

			$fedex_ground_id = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shipping='FedEx Ground' AND active='Y'");
			$fedex_home_id = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shipping='FedEx Home Delivery' AND active='Y'");

			continue;
		}

		$row = preg_split("!\s{3,}!S", $line);
		if (strpos($row[0],'Zip')!==FALSE && strpos($row[1],'Zone')!==FALSE) {
			if (($cont_us_flag==1)&&($tbl_begin_flag==0))
				$tbl_begin_flag=1;

			continue;
		}

		# parse continental US rates
		if ($tbl_begin_flag==1 && $cont_us_flag==1) {
			for ($i=0;$i<4;$i++) {
				if (preg_match("/\d{3}-\d{3}/S",$row[$i*2])) {
					$zone = trim($row[$i*2+1]);
					if ($zone=='NA')
						continue;
					$prefix = explode("-",$row[$i*2]);
					db_query("INSERT INTO $sql_tbl[fedex_zips](zip_first, zip_last, zip_zone) VALUES('".$prefix[0]."', '".$prefix[1]."', '".$zone."')");
				}
				elseif (preg_match("/\d{3}/S",$row[$i*2])) {
					$zone = trim($row[$i*2+1]);
					if ($zone=='NA')
						continue;
					$prefix = $row[$i*2];
					db_query("INSERT INTO $sql_tbl[fedex_zips](zip_first, zip_last, zip_zone) VALUES('".$prefix."', '".$prefix."', '".$zone."')");
				}
			}
		}

		# parse non-continental US rates
		else if ($tbl_begin_flag==1 && $non_cont_us_flag==1) {
			for ($i=0;$i<3;$i++) {
				$first = false;
				if (preg_match("/\d+-\d+/S",$row[$i*3])) {
					list($first, $last) = explode("-",$row[$i*3]);
				}
				elseif (preg_match("/\d+/S",$row[$i*3])) {
					$first = $last = $row[$i*3];
				}

				if ($first === false) continue;

				$zone_exp = trim($row[$i*3+1]);
				$zone_gnd = trim($row[$i*3+2]);

				if (strlen($first) == 3) $first .= '000';
				if (strlen($last) == 3) $last .= '999';

				if (!empty($fedex_es) && is_numeric($zone_exp)) {
					foreach ($fedex_es as $meth_id) {
						db_query("INSERT INTO $sql_tbl[fedex_zips] (zip_first, zip_last, zip_zone, zip_meth) VALUES('".$first."', '".$last."', '".$zone_exp."', '".$meth_id."')");
					}
				}

				if ($fedex_ground_id !== false && is_numeric($zone_gnd))
					db_query("INSERT INTO $sql_tbl[fedex_zips] (zip_first, zip_last, zip_zone, zip_meth) VALUES('".$first."', '".$last."', '".$zone_gnd."', '".$fedex_ground_id."')");

				if ($fedex_home_id !== false && is_numeric($zone_gnd))
					db_query("INSERT INTO $sql_tbl[fedex_zips] (zip_first, zip_last, zip_zone, zip_meth) VALUES('".$first."', '".$last."', '".$zone_gnd."', '".$fedex_home_id."')");
			}
		}
	}

	return true;
}

?>
