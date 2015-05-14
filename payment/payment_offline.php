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
# $Id: payment_offline.php,v 1.32 2006/01/11 06:56:23 mclap Exp $
#
# CC processing payment module
#

require "../include/payment_method.php";

x_load('order','payment');

include $xcart_dir."/include/payment_wait.php";

#
# Generate $order_notes
#
$order_details = "";
foreach ($HTTP_POST_VARS as $key=>$val) {
	if ($key=="action" || $key=="payment_method" || $key==$XCART_SESSION_NAME || $val=="" || $key=='paymentid')
		continue;

	if ($key=="Customer_Notes")
		$customer_notes = $val;
	else
		$order_details .= str_replace("_"," ",$key).": $val\n";
}

if ($paymentid == 2) {
	if (empty($PO_Number) || empty($Company_name) || empty($Name_of_purchaser) || empty($Position)) {
		$top_message['content'] = func_get_langvar_by_name("err_filling_form");
		$top_message['type'] = 'E';
		func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=fields&paymentid=".$paymentid);
	}
}

#
# $payment_method is variable which ss POSTed from checkout.tpl
#
$orderids = func_place_order(stripslashes($payment_method), "Q", $order_details, $customer_notes);
if (is_null($orderids) || $orderids===false) {
	func_header_location($xcart_catalogs['customer'].'/error_message.php?product_in_cart_expired');
}

$_orderids = func_get_urlencoded_orderids ($orderids);

#
# Remove all from cart
#
$cart="";
if (!empty($active_modules['SnS_connector'])) {
	func_generate_sns_action("CartChanged");
}

func_header_location($current_location.DIR_CUSTOMER."/cart.php?mode=order_message&orderids=$_orderids");

?>
