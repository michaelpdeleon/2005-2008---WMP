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
# $Id: order.php,v 1.50 2006/01/11 06:56:25 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('order');

if ($active_modules["Simple_Mode"])
	func_header_location($xcart_catalogs['admin']."/order.php?$QUERY_STRING");

#
# Collect infos about ordered products
#
require $xcart_dir."/include/history_order.php";

#
# Security protection from updating another's order
#

if(!$single_mode && $order_data["products"][0]["provider"]!=$login) {
	func_header_location("error_message.php?access_denied&id=46");
}


if ($REQUEST_METHOD=="POST") {
	#
	# Update order.
	# Providers don't have full access to orders as admins
	# order_notes & tracking_number can be modified +
	# providers can set 'C' order status (complete order)
	#

	if ($mode == "status_change") {
		db_query("update $sql_tbl[orders] set tracking='$tracking', customer_notes='$customer_notes', notes='$notes' where orderid='$orderid'");
	}
	elseif ($mode == "complete_order")	{
		db_query("update $sql_tbl[orders] set status='C' where orderid='$orderid'");
		func_complete_order($orderid);
	}

	$top_message = array(
		"content" => func_get_langvar_by_name("txt_order_has_been_changed")
	);
	func_header_location("order.php?orderid=".$orderid);
}

if ($mode == "printable") {
	func_display("provider/order_printable.tpl",$smarty);
}
else {
	#
	# Delete order
	#
	if ($mode == "delete") {

		func_delete_order($orderid);

		func_header_location("orders.php?".$query_string);
	}

	$smarty->assign("main","history_order");

	@include $xcart_dir."/modules/gold_display.php";
	func_display("provider/home.tpl",$smarty);
}
?>
