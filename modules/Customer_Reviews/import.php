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
# $Id: import.php,v 1.4 2006/03/16 12:19:00 max Exp $
#

/******************************************************************************
Used cache format:
Products (by Product ID):
	data_type: 	PI
	key:		<Product ID>
	value:		[<Product code> | RESERVED]
Products (by Product code):
	data_type: 	PR
	key:		<Product code>
	value:		[<Product ID> | RESERVED]
Products (by Product name):
	data_type:  PN
	key:		<Product name>
	value:		[<Product ID> | RESERVED]
Deleted product data:
	data_type:	DP
	key:		<Product ID>
	value:		<Flags>

Note: RESERVED is used if ID is unknown
******************************************************************************/


if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == "process_row") {
#
# PROCESS ROW from import file
#

	# Check productid / productcode / product
	list($_productid, $_variantid) = func_import_detect_product($values);
	if (is_null($_productid) || ($action == "do" && empty($_productid))) {
		func_import_module_error("msg_err_import_log_message_14");
		return false;
	}

	$values['productid'] = $_productid;

	$data_row[] = $values;
	
}
elseif ($import_step == "finalize") {
#
# FINALIZE rows processing: update database
#

	if ($import_file["drop"][strtolower($section)] == "Y") {
		if ($provider_condition) {
			# Search for products created by provider...
			$products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 ".$provider_condition);
			if ($products_to_delete) {
				while ($value = db_fetch_array($products_to_delete)) {
					db_query("DELETE FROM $sql_tbl[product_reviews] WHERE productid = '$value[productid]'");
				}
			}
		}
		else {
		# Delete all products and related information...
			db_query("DELETE FROM $sql_tbl[product_reviews]");
		}
			
		$import_file["drop"][strtolower($section)] = "";
	}

	foreach ($data_row as $row) {
	#
	# Import pricing data...
	#

		# Delete old data
		$tmp = func_import_get_cache("DP", $row['productid']);
		if (strpos($tmp, "r") === false) {
			db_query("DELETE FROM $sql_tbl[product_reviews] WHERE productid = '$row[productid]'");
			func_import_save_cache("DP", $row['productid'], $tmp."r");
		}

		# Import customer review
		foreach ($row['message'] as $k => $v) {
			$data = array(
				"productid"	=> $row['productid'],
				"message"	=> $v,
				"email"		=> $row['email'][$k]
			);
			func_array2insert("product_reviews", $data);
			$result["customer_revies"]["added"]++;
		}

		echo ". ";
		func_flush();

	}

# Export data
} elseif ($import_step == "export") {

	while ($productid = func_export_get_row($data)) {
		if (empty($productid))
			continue;

		# Get product signature
		$p_row = func_export_get_product($productid);

		# Get data
		$row = func_query("SELECT email, message FROM $sql_tbl[product_reviews] WHERE productid = '$productid'");

		if (empty($p_row) || empty($row))
			continue;

		foreach ($row as $v) {
			$v = func_array_merge($v, $p_row);

			# Export row
			if (!func_export_write_row($v))
				break;
		}

	}
}

?>
