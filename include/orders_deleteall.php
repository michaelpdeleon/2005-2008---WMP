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
# $Id: orders_deleteall.php,v 1.18 2006/02/06 14:34:32 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_log_flag('log_orders_delete', 'ORDERS', "Login: $login\nIP: $REMOTE_ADDR\nOperation: delete all orders", true);

#
# Delete ALL orders and move them to the orders_deleted table
#
$xaff = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name='XAffiliate'") > 0);
$xrma = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name='RMA'") > 0);

$lock_tables = array(
	"orders",
	"order_details",
	"giftcerts",
	"order_extras",
	"subscription_customers"
	);

if ($xaff) {
	if (empty($active_modules['XAffiliate']))
		include_once $xcart_dir."/modules/XAffiliate/config.php";
	$lock_tables[] = "partner_payment";
	$lock_tables[] = "partner_product_commissions";
	$lock_tables[] = "partner_adv_orders";
}

if ($xrma) {
	if (empty($active_modules['RMA']))
		include_once $xcart_dir."/modules/RMA/config.php";
	$lock_tables[] = "returns";
}

foreach ($lock_tables as $k => $v) {
	if (isset($sql_tbl[$v]))
		$lock_tables[$k] = $sql_tbl[$v]." WRITE";
}

db_query("LOCK TABLES ".implode(', ', $lock_tables));

db_query("DELETE FROM $sql_tbl[orders]");
db_query("DELETE FROM $sql_tbl[order_details]");
db_query("DELETE FROM $sql_tbl[order_extras]");
db_query("DELETE FROM $sql_tbl[giftcerts]");

if ($xaff) {
	db_query("DELETE FROM $sql_tbl[partner_payment]");
	db_query("DELETE FROM $sql_tbl[partner_product_commissions]");
	db_query("DELETE FROM $sql_tbl[partner_adv_orders]");
}

if ($xrma) {
	db_query("DELETE FROM $sql_tbl[returns]");
}

db_query("DELETE FROM $sql_tbl[subscription_customers]");
db_query("UNLOCK TABLES");

$smarty->assign("deleteall","true");

?>
