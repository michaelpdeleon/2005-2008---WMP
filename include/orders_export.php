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
# $Id: orders_export.php,v 1.40.2.2 2006/07/17 08:26:13 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }


if ($REQUEST_METHOD != "POST") {
	func_header_location($HTTP_REFERER);
	return;
}

x_load('crypt','export');

#
# Prepare the orderid condition
#
if ($mode == "export") {
	# Passed here via include/process_order.php
	if (!empty($orderids) && is_array($orderids)) {
		$orderids = array_keys($orderids);
	}
	else {
		$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_orders_sel");

		func_header_location("orders.php?mode=search");
	}
}

#
# TODO: optimize orderid condition as (orderid>=min and orderid<=max)...
#

$condition = array();
if (!empty($orderids) && is_array($orderids))
	$condition[] = "$sql_tbl[orders].orderid IN (".implode(",",$orderids).")";
if ($provider)
	$condition[] = "$sql_tbl[order_details].provider='$provider'";

$condition = implode(" AND ", $condition);
if (!empty($condition))
	$condition = " WHERE ".$condition;

# Export through standart export procedure
if (empty($export_fmt) || $export_fmt == 'std') {
	func_export_range_save("ORDERS", "SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid $condition GROUP BY $sql_tbl[orders].orderid ORDER BY orderid");
	$top_message['content'] = func_get_langvar_by_name("lbl_export_orders_add");
	$top_message['type'] = "I";

	func_header_location("import.php?mode=export");
}

if ($provider) {

	# SQL query without Gift certificates data
	$search_orders_query = "SELECT $sql_tbl[orders].*, $sql_tbl[order_details].itemid, $sql_tbl[shipping].shipping as shipping_method FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid $condition GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC";
} else {

	# SQL query with Gift certificates data
	$search_orders_query = "SELECT $sql_tbl[orders].*, $sql_tbl[order_details].itemid, $sql_tbl[giftcerts].gcid, $sql_tbl[shipping].shipping as shipping_method FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[giftcerts] ON $sql_tbl[orders].orderid = $sql_tbl[giftcerts].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid $condition GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC";
}

$log_op_message = "Login: $login\nIP: $REMOTE_ADDR\nOperation: export orders";
if (!empty($orderids) && is_array($orderids))
	$log_op_message .= "(".implode(',',$orderids).")";
else
	$log_op_message .= "(all found)";

$log_op_message .= "\nUsed SQL query: ".$search_orders_query;
x_log_flag('log_orders_export', 'ORDERS', $log_op_message, true);

$orders_result = db_query($search_orders_query);

#
# Do array multisort by orderid
#
$total_lines = db_num_rows($orders_result);

if (empty($total_lines)) {
	$smarty->debugging = $_tmp_smarty_debug;
	exit;
}

$export_file = 'orders.txt';
$delimiter = "\t";
$ctype = 'text/plain';

$export_tbl = array (
	'csv_tab' => array (
		'ctype' => 'application/csv',
		'delim' => "\t",
		'file' => 'orders.csv'
	),
	'csv_semi' => array (
		'ctype' => 'application/csv',
		'delim' => ';',
		'file' => 'orders.csv'
	),
	'csv_comma' => array (
		'ctype' => 'application/csv',
		'delim' => ',',
		'file' => 'orders.csv'
	),
);

if (!empty($active_modules["QuickBooks"])) {
	$export_tbl['qb'] = array (
		'ctype' => 'application/csv',
		'file' => 'orders.IIF'
	);
}

if (isset($export_tbl[$export_fmt]['ctype']))
	$ctype = $export_tbl[$export_fmt]['ctype'];

if (isset($export_tbl[$export_fmt]['delim']))
	$delimiter = $export_tbl[$export_fmt]['delim'];

if (isset($export_tbl[$export_fmt]['file']))
	$export_file = $export_tbl[$export_fmt]['file'];

header("Content-Type: ".$ctype);
header("Content-Disposition: attachment; filename=\"".$export_file."\"");

$smarty->assign("delimiter", $delimiter);

$_tmp_smarty_debug = $smarty->debugging;
$smarty->debugging = false;

$date_fields = array(
	"date",
	"add_date"
);
$text_fields = array(
	"details",
	"notes",
	"customer_notes"
);

while ($data = db_fetch_array($orders_result)) {
	if (empty($data['itemid']) && empty($data['gcid']))
		continue;

	list($data["b_address"], $data["b_address_2"]) = split("[\n\r]+", $data["b_address"]);
	list($data["s_address"], $data["s_address_2"]) = split("[\n\r]+", $data["s_address"]);

	if (!($current_area == "A" || ($current_area == "P" && !empty($active_modules["Simple_Mode"]))))
		unset($data["details"]);

	$data["date"] += $config["Appearance"]["timezone_offset"];

	$orders_full = array();

	# Get products data
	if (!empty($data['itemid'])) {
		$row = $data;
		func_unset($row, "gcid");

		$order_details = db_query("SELECT $sql_tbl[products].*, $sql_tbl[order_details].* FROM $sql_tbl[order_details], $sql_tbl[products] WHERE $sql_tbl[order_details].productid = $sql_tbl[products].productid AND $sql_tbl[order_details].orderid = '$row[orderid]'");
		if ($order_details) {
			while ($product = db_fetch_array($order_details)) {
				$product["add_date"] += $config["Appearance"]["timezone_offset"];
				$orders_full[] = func_array_merge($row, $product);
			}
			db_free_result($order_details);
		}
	}

	# Get Gift certificates data
	if (!empty($data['gcid'])) {
		$row = $data;

		$giftcerts_details = db_query("SELECT * FROM $sql_tbl[giftcerts] WHERE orderid = '$row[orderid]'");
		if ($giftcerts_details) {
			while($gift = db_fetch_array($giftcerts_details)) {
				$gift["giftcert_status"] = $gift["status"];
				unset($gift["status"]);
				$gift["add_date"] += $config["Appearance"]["timezone_offset"];

				$orders_full[] = func_array_merge($row, $giftcerts_details);
			}
			db_free_result($giftcerts_details);
		}
	}

	if (empty($orders_full))
		continue;

	if ($active_modules["QuickBooks"] == "Y" && $export_fmt == "qb") {
		# QuickBooks export

		include $xcart_dir."/modules/QuickBooks/orders_export.php";
		
	} else {

		# Standart export procedure 
		foreach ($orders_full as $key => $value) {

			# Data quotation
			foreach ($value as $subkey => $subvalue) {
				if (is_array($subvalue) || strlen($subvalue) == 0 || (!in_array($subkey, $date_fields) && preg_match("/^\d+$/S", $subvalue)))
					continue;

				if ($subkey == "details")
					$subvalue = text_decrypt($subvalue);

				if (in_array($subkey, $date_fields)) {

					# Date fields
					$subvalue = strftime($config['Appearance']['date_format'], $subvalue)." ".strftime($config['Appearance']['time_format'], $subvalue);

				} elseif (is_numeric($subvalue)) {

					# Numeric fields
					$subvalue = sprintf("%01.03f", $subvalue);

				} elseif(in_array($subkey, $text_fields)) {

					# Text fields
					$subvalue = preg_replace("/[\r\n\t]/S", " ", $subvalue);

				} elseif($subkey == "product_options") {

					# Product options data
					$subvalue = str_replace("\n", ", ", $subvalue);
					$subvalue = preg_replace("/[\r\t]/S", "", $subvalue);

				} else {
					$subvalue = preg_replace("/[\r\n\t]/S", "", $subvalue);
				}

				$orders_full[$key][$subkey] = '"'.str_replace('"', '""', $subvalue).'"';
			}
		}

		$smarty->assign("orders", $orders_full);
		func_display("main/orders_export.tpl", $smarty);
	}

	func_flush();
	unset($orders_full);

}
db_free_result($orders_result);

$smarty->debugging = $_tmp_smarty_debug;
exit;

?>
