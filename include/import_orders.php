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
# $Id: import_orders.php,v 1.12.2.2 2006/06/15 07:01:23 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == "define") {

	$import_specification['ORDERS'] = array(
		"script"		=> "/include/import_orders.php",
		"no_import"		=> true,
		"permissions"	=> "AP",
		"tpls"			=> array(
			"main/import_option_order_details_crypt.tpl"),
		"is_range"		=> "orders.php?is_range",
		"export_sql"	=> "SELECT orderid FROM $sql_tbl[orders]",
		"orderby"		=> 20,
		"table"			=> "orders",
		"key_field"		=> "orderid",
		"columns"		=> array(
			"orderid"				=> array(
				"required"	=> true,
				"is_key"	=> true,
				"type"		=> "N"),
			"login"					=> array(
				"required"  => true),
			"membership"			=> array(),
			"total"					=> array(
				"type"      => "P",
				"required"  => true),
			"giftcert_discount"		=> array(
				"type"      => "P"),
			"applied_giftcert_id"	=> array(
				"array"		=> true,
				"type"		=> "N"),
			"applied_giftcert_cost"	=> array(
				"array"		=> true,
				"type"		=> "P"),
			"subtotal"				=> array(
				"type"      => "P",
				"required"  => true),
			"discount"				=> array(
				"type"      => "P"),
			"coupon"				=> array(),
			"coupon_discount"		=> array(
				"type"      => "P"),
			"shippingid"			=> array(
				"type"      => "N"),
			"tracking"				=> array(),
			"shipping_cost"			=> array(
				"type"      => "P"),
			"tax"					=> array(
				"type"      => "P"),
			"taxes_applied"			=> array(),
			"date"					=> array(
				"is_key"	=> true,
				"type"		=> "D",
				"required"  => true),
			"status"				=> array(
				"type"		=> "E",
				"variants"	=> array("I","Q","P","C","F","D","B"),
				"default"	=> "Q"),
			"payment_method"		=> array(
				"required"  => true),
			"flag"					=> array(
				"type"		=> "B",
				"default"	=> "N"),
			"customer_notes"		=> array(),
			"notes"					=> array(),
			"details"				=> array(),
			"clickid"				=> array(
				"type"		=> "N"),
			"b_title"				=> array(),
			"b_firstname"			=> array(),
			"b_lastname"			=> array(),
			"b_address"				=> array(),
			"b_city"				=> array(),
			"b_county"				=> array(),
			"b_state"				=> array(),
			"b_country"				=> array(),
			"b_zipcode"				=> array(),
			"title"					=> array(),
			"firstname"				=> array(),
			"lastname"				=> array(),
			"company"				=> array(),
			"s_title"				=> array(),
			"s_firstname"			=> array(),
			"s_lastname"			=> array(),
			"s_address"				=> array(),
			"s_city"				=> array(),
			"s_county"				=> array(),
			"s_state"				=> array(),
			"s_country"				=> array(),
			"s_zipcode"				=> array(),
			"email"					=> array(),
			"phone"					=> array(),
			"fax"					=> array(),
			"url"					=> array(),
			"tax_number"			=> array(),
			"tax_exempt"			=> array(
				"type"		=> "B"),
			"language"				=> array(
				"type"		=> "C"),
			"extra_field"			=> array(
				"array"		=> true),
			"extra_value"			=> array(
				"array"		=> true)
		)
	);
}
elseif ($import_step == "export") {

	# Export data
	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		if ($single_mode || AREA_TYPE == 'A') {
			$row = func_query_first("SELECT * FROM $sql_tbl[orders] WHERE orderid = '$id'");
		} else {
			$row = func_query_first("SELECT $sql_tbl[orders].* FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].orderid = '$id' AND $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[order_details].provider = '$login'");
		}
		if (empty($row))
			continue;

		# Export applied gift certificates
		if (!empty($row['giftcert_ids']) && ($single_mode || AREA_TYPE == 'A')) {
			$tmp = explode("*", $row['giftcert_ids']);
			foreach ($tmp as $v) {
				list($gid, $gcost) = explode(":", $v);
				$row['applied_giftcert_id'][] = $gid;
				$row['applied_giftcert_cost'][] = $gcost;
			}
		}

		func_unset($row, "giftcert_ids");

		if ($single_mode || AREA_TYPE == 'A') {
			$row['details'] = text_decrypt($row['details']);
			$row['details'] = (string)$row['details'];

			# Export extra fields
			$ef = func_query("SELECT khash, value FROM $sql_tbl[order_extras] WHERE orderid = '$id'");
			if (!empty($ef)) {
				foreach ($ef as $v) {
					$row['extra_field'][] = $v['khash'];
					$row['extra_value'][] = $v['value'];
				}
			}
			unset($ef);

		} else {

			unset($row['details']);
			unset($row['clickid']);
		}

		# Export row
		if (!func_export_write_row($row))
			break;
	}
}

?>
