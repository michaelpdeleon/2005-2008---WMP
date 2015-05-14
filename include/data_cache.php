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
# $Id: data_cache.php,v 1.13.2.1 2006/04/27 13:14:28 max Exp $
#
# Repository of data cache functions
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$data_caches = array(
	"modules" => array("func" => "func_dc_modules"),
	"setup_images" => array("func" => "func_dc_setup_images"),
	"charsets" => array("func" => "func_dc_charsets"),
	"languages" => array("func" => "func_dc_languages"),
	"payments_https" => array("func" => "func_dc_payments_https")
);

if (empty($config['data_cache_expiration']) || (time()-$config['data_cache_expiration']) > 3600) {
	func_data_cache_clear();
	$config['data_cache_expiration'] = time();
	func_array2insert(
		"config", 
		array(
			"value" => $config['data_cache_expiration'],
			'name' => 'data_cache_expiration',
			'defvalue' => '',
			'variants' => ''
		), 
		true
	);
}

function func_dc_modules() {
	global $sql_tbl;

	$all_active_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules] USE INDEX (active) WHERE active='Y'");
	$active_modules = array();
	if ($all_active_modules) {
		foreach($all_active_modules as $active_module) {
			$active_modules[$active_module]=true;
		}
	}

	return $active_modules;
}

function func_dc_setup_images() {
	global $sql_tbl;

	return func_query_hash("SELECT * FROM $sql_tbl[setup_images]", "itype", false);
}

function func_dc_charsets() {
	global $sql_tbl;

	$data =  func_query("SELECT $sql_tbl[languages].code, $sql_tbl[countries].charset FROM $sql_tbl[languages], $sql_tbl[countries] WHERE $sql_tbl[languages].code = $sql_tbl[countries].code GROUP BY $sql_tbl[languages].code");

	if ($data) {
		foreach ($data as $v) {
			$return[$v['code']] = $v['charset'];
		}
	}

	return $return;
}

function func_dc_languages($code) {
	global $sql_tbl;

	$lngs = func_query("SELECT $sql_tbl[countries].*, lng_c.value as country, lng_l.value as language FROM $sql_tbl[languages], $sql_tbl[countries] LEFT JOIN $sql_tbl[languages] as lng_c ON lng_c.name = CONCAT('country_', $sql_tbl[countries].code) AND lng_c.code = '$code' LEFT JOIN $sql_tbl[languages] as lng_l ON lng_l.code = '$code' AND lng_l.name = CONCAT('language_', $sql_tbl[countries].code) WHERE $sql_tbl[languages].code = $sql_tbl[countries].code GROUP BY $sql_tbl[languages].code ORDER BY $sql_tbl[languages].code");
	if (!empty($lngs)) {
		foreach ($lngs as $k => $lng) {
			if (is_null($lng['country'])) {
				$lngs[$k]['country'] = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE name = 'country_".$lng['code']."' LIMIT 1");
			}
			if (is_null($lng['language'])) {
				$lngs[$k]['language'] = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE name = 'language_".$lng['code']."' LIMIT 1");
			}
		}
	}

	return $lngs;
}

function func_dc_payments_https() {
	global $sql_tbl;

	return func_query("SELECT paymentid FROM $sql_tbl[payment_methods] USE INDEX (protocol) WHERE protocol = 'https'");
}
?>
