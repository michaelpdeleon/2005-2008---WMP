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
# $Id: import_zones.php,v 1.10 2006/03/16 12:18:59 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice');

/******************************************************************************
Used cache format:
Zones:
	data_type: 	Z
	key:		<Zone name>
	value:		[<Zone ID> | RESERVED]
Countries:
	data_type:	CN
	key:		<Country code>
	value:		<Country code>
States:
	data_type:	ST
	key:		<Country code>_<State code>
	value:		<Country code>_<State code>
Counties:
	data_type:	CO
	key:		<County ID>
	value:		<County ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if (!defined('IMPORT_ZONES')) {
#
# Make default definitions (only on first inclusion!)
#
	define('IMPORT_ZONES', 1);
	$import_specification["ZONES"] = array(
		"script" => "/include/import_zones.php",
		"permissions" => "AP", # Admin and provider can import zones
		"need_provider" => 1,
		"export_sql"	=> "SELECT zoneid FROM $sql_tbl[zones]",
		"columns" => array(
					"zoneid"  => array( # Integer: zoneid
								"is_key"	=> true,
								"type"		=> "N",
								"required"	=> 0,  # Required field
								"inherit"	=> 1,  # Can inherit value
								"default"	=> 0), # Default value
					"zone"    => array( # String: zone name
								"is_key"	=> true,
								"required"	=> 1),
					"country" => array( # String: country code
								"array"		=> true),
					"state"   => array( # String: state code (like 'US_NY')
								"array"		=> true),
					"county"  => array( # String: county code (like 'US_TX_IR')
								"array"		=> true),
					"city"    => array( # String: city
								"array"		=> true),
					"address" => array( # String: address line
								"array"		=> true),
					"zip"     =>array( # String: zip/postal code
								"array"		=> true)
					)
	);
	# Accordance: [column name] => [zone element type]
	$zone_field_types = array(
		"country" => "C",
		"state" => "S",
		"county" => "G",
		"city" => "T",
		"address" => "A",
		"zip" => "Z"
	);

	return;
}

$provider_condition = ($single_mode ? "" : " AND provider='".addslashes($import_data_provider)."'");

if ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	#
	# Set provider for current tax rate...
	#
	if (!empty($values["provider"])) {
		$tmp = func_import_save_cache("P", $values["provider"]);
		if (empty($tmp)) {
			$_provider = func_query_first_cell("SELECT login FROM $sql_tbl[customers] WHERE login='".addslashes($values["provider"])."' AND usertype='P' LIMIT 1");
			if (!empty($_provider)) {
				func_import_save_cache("P", $values["provider"], $_provider);
				$values["provider"] = $_provider;
			}
		}
		else
			$values["provider"] = $tmp;
	}
	else {
		$values["provider"] = $import_data_provider;
	}

	#
	# Prepare zoneid...
	#
	if (!empty($values["zoneid"])) {
		$values["zoneid"] = intval($values["zoneid"]);
		$tmp = func_import_get_cache("Z", $values["zone"]);
		if (empty($tmp) && $values["zoneid"] > 0) {
			func_import_save_cache("Z", $values["zone"], $values["zoneid"]);
		} elseif (!empty($tmp)) {
			$values["zoneid"] = $tmp;
		}

		if (!$simple_mode) {
			$values["zoneid"] = (func_query_first("SELECT zoneid, provider FROM $sql_tbl[zones] WHERE zoneid='$values[zoneid]' AND provider<>'".addslashes($values["provider"])."' LIMIT 1") ? "" : $values["zoneid"]);
		}
	}

	if (isset($values["zoneid"]) && empty($values["zoneid"]))
		unset($values["zoneid"]);

	#
	# Check the country code...
	#
	if (!is_array($values["country"]))
		$values["country"] = array($values["country"]);
	foreach ($values["country"] as $cn) {
		$tmp = func_import_get_cache("CN", $cn);
		if (!empty($cn) && empty($tmp)) {
			$_country_code = func_query_first_cell("SELECT code FROM $sql_tbl[countries] WHERE code='".addslashes($cn)."' LIMIT 1");
			if (!empty($_country_code)) {
				func_import_save_cache("CN", $cn, $_country_code);
			} else {
				func_import_module_error("msg_err_import_log_message_8", array("code"=>$cn));
			}
		}
	}

	#
	# Check the state code...
	#
	if (!is_array($values["state"]))
		$values["state"] = array($values["state"]);
	foreach ($values["state"] as $st) {
		$tmp = func_import_get_cache("ST", $st);
		if (!empty($st) && empty($tmp)) {
			list($_country_code, $_state_code) = explode("_", $st);
			$_state_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE code='".addslashes($_state_code)."' AND country_code='".addslashes($_country_code)."' LIMIT 1");
			if (!empty($_state_code)) {
				func_import_save_cache("ST", $st, $st);
			} else {
				func_import_module_error("msg_err_import_log_message_9", array("code"=>$st));
			}
		}
	}

	#
	# Check the county code...
	#
	if (!is_array($values["county"]))
		$values["county"] = array($values["county"]);
	foreach ($values["county"] as $co) {
		$tmp = func_import_get_cache("CO", $co);
		if ($config["General"]["use_counties"] == "Y" && !empty($co) && empty($tmp)) {
			$_countyid = func_query_first_cell("SELECT countyid FROM $sql_tbl[counties] WHERE countyid='".intval($co)."' LIMIT 1");
			if (!empty($_countyid)) {
				func_import_save_cache("CO", $co, $_countyid);
			} else {
				func_import_module_error("msg_err_import_log_message_10", array("code" => $co));
			}
		}
	}

	$tmp = func_import_get_cache("Z", $values["zone"]);
	if (is_null($tmp))
		func_import_save_cache("Z", $values["zone"], "");

	$data_row[] = $values;

}
elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	if ($import_file["drop"]["zones"] == "Y") {
	#
	# Drop old zones related info
	#
		if ($provider_condition) {
			# Search the zones created by provider...
			$zones_to_delete = db_query("SELECT zoneid FROM $sql_tbl[zones] WHERE 1 $provider_condition");
			if ($zones_to_delete) {
				while ($value = db_fetch_array($zones_to_delete)) {
				# Delete zone related information...
					db_query("DELETE FROM $sql_tbl[zone_element] WHERE zoneid='$value[zoneid]'");
					db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE zoneid='$value[zoneid]'");
					db_query("DELETE FROM $sql_tbl[tax_rates] WHERE zoneid='$value[zoneid]'");

				}
				# Delete zone information...
				db_query("DELETE FROM $sql_tbl[zones] WHERE 1 $provider_condition");
			}
		}
		else {
		# Delete all zones and zones related information...
			db_query("DELETE FROM $sql_tbl[zone_element]");
			db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE zoneid>0");
			db_query("DELETE FROM $sql_tbl[tax_rates] WHERE zoneid>0");
			db_query("DELETE FROM $sql_tbl[zones]");
		}

		$import_file["drop"]["zones"] = "";
	}

	foreach ($data_row as $zone_elements) {

		# Search if zone already exists...
		$zone_name = $zone_elements['zone'];
		$zoneid = "";
		$_need_update = false;
		if (!empty($zone_elements["zoneid"]))
			$zoneid = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zones] WHERE zoneid='$zone_elements[zoneid]' $provider_condition LIMIT 1");

		if (empty($zoneid)) {
			$zoneid = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zones] WHERE zone_name='".addslashes($zone_name)."' $provider_condition LIMIT 1");
		}

		# Update/insert zone...
		$data = array("zone_name" => addslashes($zone_name), "provider" => addslashes($import_data_provider));
		if (!empty($zoneid)) {
			func_array2update("zones", $data, "zoneid='$zoneid'");
			$result["zones"]["updated"]++;
		} else {
			if (!empty($zone_elements["zoneid"]))
				$data ['zoneid'] = $zone_elements["zoneid"];
			$zoneid = func_array2insert("zones",$data);
			if (!empty($zoneid))
				$result["zones"]["added"]++;
		}

		if (empty($zoneid))
			continue;

		func_import_save_cache("Z", $zone_name, $zoneid);

		#
		# Update/insert information about zone elements...
		#
		foreach($zone_elements as $field_type=>$value) {
			if (isset($zone_field_types[$field_type]) && !empty($value)) {
				if (is_array($value)) {
					func_insert_zone_element($zoneid, $zone_field_types[$field_type], $value);
				} else {
					func_insert_zone_element($zoneid, $zone_field_types[$field_type], array($value));
				}
			}
		}

		echo ". ";
		func_flush();

	}

# Export data
} elseif ($import_step == "export") {

	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		$row = func_query_first("SELECT * FROM $sql_tbl[zones] WHERE zoneid = '$id'");
		if (empty($row))
			continue;

		$row = func_export_rename_cell($row, array("zone_name" => "zone"));

		# Get zone elements
		$elms = func_query("SELECT * FROM $sql_tbl[zone_element] WHERE zoneid = '$row[zoneid]' AND field_type IN ('".implode("','", $zone_field_types)."')");
		if (!empty($elms)) {
			foreach ($elms as $v) {
				$key = array_search($v['field_type'], $zone_field_types);
				if ($key !== false)
					$row[$key][] = $v['field'];
			}
		}

		# Write row
		if (!func_export_write_row($row))
			break;
	}
}

?>
