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
# $Id: shipping_options.php,v 1.30 2006/01/11 06:55:58 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_shipping_options"), "");

$carriers = array();

if ($config["Shipping"]["use_intershipper"] == "Y") {
	$carriers[] = array("Intershipper","InterShipper");
	$carrier = "Intershipper";
}
else {
	$carriers[] = array("CPC","Canada Post");
	$carriers[] = array("FDX","FedEx");
	$carriers[] = array("USPS","U.S.P.S");
	$carriers[] = array("ARB","Airborne");
	$carriers[] = array("APOST","Australia Post");
	$carriers[] = array("DHL","DHL");
}

$carrier_valid = false;
foreach ($carriers as $k=>$v)
	if ($v[0] == $carrier) {
		$carrier_valid = true;
		break;
	}

if (!$carrier_valid && $carrier !="FDX_IMPORT" )
	$carrier = "";

if ($REQUEST_METHOD == "POST") {
#
# Update the shipping options
#
	if ($carrier == "FDX") {
	#
	# FEDEX options update
	#
		$check = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='FDX'");
		if (!$check)
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('FDX')");

		db_query("UPDATE $sql_tbl[shipping_options] SET param00='$company_type', param01='$packaging', param02='$dropoff_type', param03='$expr_fuel_surch', param04='$grnd_fuel_surch' WHERE carrier='FDX'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");
		
		func_header_location("shipping_options.php?carrier=FDX");
	}

	if ($carrier == "FDX_IMPORT") {
	#
	# Import shipping rates tables from CSV files
	#
		$fdx_flag = false;
		$fdx_ozip = $config["Company"]["location_zipcode"];

		include $xcart_dir."/shipping/fedex_import.php";

		$fedex_path_to_import = $HTTP_POST_VARS["fdx_import_files_path"];
		
		if (!preg_match("/^(.*)\\".DIRECTORY_SEPARATOR.'$/', $fedex_path_to_import)) 
			$fedex_path_to_import .= DIRECTORY_SEPARATOR;
		
		$cnf = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='fdx_files_path'");
		if ($cnf)
			db_query("UPDATE $sql_tbl[config] SET value='$fedex_path_to_import' WHERE name='fdx_files_path'");
		else
			db_query("INSERT INTO $sql_tbl[config] (name, value, type) VALUES('fdx_files_path', '$fedex_path_to_import','text')");
	
		# get real names
		fedex_check_for_files($fdx_ozip);

		# parse zone locator
		if (isset($fedex_zone_file))
		fedex_parse_zone_locator_file($fedex_path_to_import.$fedex_zone_file);

		# import rates
		$methods_count = 0;
		fedex_import_rates($methods_count);

		if ((isset($fedex_zone_file)) || ($methods_count>0)) {
		
			# Get rates count
			$r = db_query("SELECT COUNT(r_id) FROM $sql_tbl[fedex_rates] WHERE 1");
			$res = db_fetch_array($r);
			$r_cnt = $res[0];

			# Read FedEx import statistics
			$r = db_query("SELECT value FROM $sql_tbl[config] WHERE name='fedex_import_stat'");
			if (db_num_rows($r)!=0) {
				$res = db_fetch_array($r);
				$fdx_import_stat = unserialize($res['value']);
			}

			# Add info into array
			$dt = time();

			foreach($fedex_rates_files as $file) {
				if (isset($fdx_import_stat['files'][$file['name']]['updated']))
					$fdx_import_stat['files'][$file['name']]['updated'] = false;

				if (isset($file["real_name"])) {
					$fdx_import_stat['files'][$file['name']]['date'] = $dt;
					$fdx_import_stat['files'][$file['name']]['updated'] = true;
				}
			}

			if (isset($fedex_zone_file)) {
				$fdx_import_stat["ozip"]=$fdx_ozip;
				$fdx_import_stat["date"]=$dt;
				$fdx_import_stat['updated'] = true;
			}
			else
				$fdx_import_stat['updated'] = false;

			# Serialize to write into cnfig
			$s = serialize($fdx_import_stat);

			if (db_num_rows($r) == 0)
				db_query("INSERT INTO $sql_tbl[config](name, value) VALUES('fedex_import_stat','".$s."')");
			else
				db_query("UPDATE $sql_tbl[config] SET value='".$s."' WHERE name='fedex_import_stat'");

			x_session_register("fdx_import_updated");
			$fdx_import_updated = 'yes';
		}
		else {
			x_session_register("fdx_import_updated");
			$fdx_import_updated = 'no';
		}
	
		$top_message["content"] = func_get_langvar_by_name("msg_adm_fedex_rates_imported");
		
		func_header_location("shipping_options.php?carrier=FDX");
	}

	if ($carrier == "USPS") {
	#
	# USPS options update
	#
		$check = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='USPS'");
		if (!$check)
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('USPS')");

		db_query("UPDATE $sql_tbl[shipping_options] SET param00='$mailtype', param01='$package_size', param02='$machinable', param03='$container_express', param04='$container_priority' WHERE carrier='USPS'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=USPS");
	}

	if ($carrier == "Intershipper") {
	#
	# INTERSHIPPER options update
	#
		$check = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='INTERSHIPPER'");
		if (!$check)
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('INTERSHIPPER')");
	
		if($pickup)
			$pickup = implode('|',$pickup);
			
		$length = doubleval($length);
		$width = doubleval($width);
		$height = doubleval($height);
		
		$codvalue = doubleval($codvalue);
		$insvalue = doubleval($insvalue);

		db_query("UPDATE $sql_tbl[shipping_options] SET param00='$delivery', param01='$pickup', param02='$length', param03='$width', param04='$height', param05='$dunit', param06='$packaging', param07='$contents', param08='$codvalue', param09='$insvalue' WHERE carrier='INTERSHIPPER'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=Intershipper");
	}

	if ($carrier == "CPC") {
	#
	# Canada Post options update
	#
		$check = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='CPC'");
		if (!$check)
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('CPC')");

		$currency_rate = doubleval($currency_rate);
		if ($currency_rate <= 0)
			$currency_rate = 1;
			
		$width = intval($width);
		$length = intval($length);
		$height = intval($height);
		
		db_query("UPDATE $sql_tbl[shipping_options] SET param00='$descr', param01='$length', param02='$width', param03='$height', param04='$insvalue', param05='$currency_rate' WHERE carrier='CPC'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=CPC");
	}

	if ($carrier == "ARB") {
		#
		# Airborne ShipIt options update
		#
		$check = func_query_first("SELECT COUNT(*) AS cnt FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");
		if (!$check["cnt"])
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('ARB')");

		$param01 = intval($param01);
		$param02 = intval($param02); $param03 = intval($param03); $param04 = intval($param04);
		$param06 = intval($param06);
		if ($param06 < 1) $param05 = 'NR';
		# COD payment
		if (empty($param08) || $param08 != "P") $param08 = "M";
		# COD value
		$param09 = intval($param09);

		# options: HAZ & allow customers to provide airborne account
		if (empty($opt_haz) || $opt_haz != "Y") $opt_haz = "N";
		if (empty($opt_own_account) || $opt_own_account != "Y") $opt_own_account = "N";
		$param07 = $opt_haz.",".$opt_own_account;

		db_query("UPDATE $sql_tbl[shipping_options] SET param00='$param00', param01='$param01', param02='$param02', param03='$param03', param04='$param04', param05='$param05', param06='$param06', param07='$param07', param08='$param08', param09='$param09' WHERE carrier='ARB'");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=ARB");
	}

	if ($carrier == "APOST") {
		#
		# Australia Post options update
		#

		$param00 = intval($param00);
		$param01 = intval($param01);
		$param02 = intval($param02);

		db_query("REPLACE INTO $sql_tbl[shipping_options] (param00,param01,param02,carrier) VALUES ('$param00','$param01','$param02','APOST')");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=".$carrier);
	}
 
	if ($carrier == "DHL") {
		#
		# DHL options update
		#

		$param01 = intval($param01);
		$param02 = intval($param02);
		$param03 = intval($param03);

		db_query("REPLACE INTO $sql_tbl[shipping_options] (param01,param02,param03,carrier) VALUES ('$param01','$param02','$param03','DHL')");

		$top_message["content"] = func_get_langvar_by_name("msg_adm_shipping_option_upd");

		func_header_location("shipping_options.php?carrier=".$carrier);
	}

} # /if ($REQUEST_METHOD == "POST")

#
# Collect options for current carrier
#
$shipping_options = array ();

$shipping_options [strtolower($carrier)] = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='$carrier'");
if ($carrier == "FDX") {
#
# Get the shipping options for FedEx service
#
	# Prepare FEDEX status information
	$fdx_files_path = ($config["fdx_files_path"] ? $config["fdx_files_path"] : $fedex_default_rates_dir);
	
	$smarty->assign ("fdx_files_path", $fdx_files_path);

	$fdx_import_stat = unserialize($config["fedex_import_stat"]);

	if (!$fdx_import_stat) 
		$fdx_import_stat = array();
	
	$smarty->assign ("fdx_import_stat", $fdx_import_stat);
}

if ($carrier == "Intershipper") {
#
# Get the shipping options for Intershipper service
#
	$shipping_options["intershipper"]["pickup"] = explode('|',$shipping_options["intershipper"]["param01"]);
}

if ($carrier == "ARB") {
	$_data = explode(',',$shipping_options["arb"]["param07"]);
	$shipping_options["arb"]["opt_haz"] = @$_data[0];
	$shipping_options["arb"]["opt_own_account"] = @$_data[1];
}

$smarty->assign("carriers", $carriers);
$smarty->assign("carrier", $carrier);

$smarty->assign ("shipping_options", $shipping_options);

$smarty->assign("main","shipping_options");

# Assign the current location line
$smarty->assign("location", $location);

include "./shipping_tools.php";

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
