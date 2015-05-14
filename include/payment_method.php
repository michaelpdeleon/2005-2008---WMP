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
# $Id: payment_method.php,v 1.58.2.3 2006/06/26 05:41:11 max Exp $
#
# CC processing payment module
#

@include_once "../top.inc.php";
if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");
include_once $xcart_dir."/payment/auth.php";

x_load('cart','user');

x_session_register("cart");
x_session_register("order_secureid");
x_session_register("intershipper_rates");

if (!isset($card_expire) && $card_expire_Month) {
	$card_expire = $card_expire_Month.substr($card_expire_Year, 2);
}

if ($card_valid_from_Month) {
	$card_valid_from = $card_valid_from_Month.substr($card_valid_from_Year, 2);
}

$HTTP_POST_VARS['card_expire'] = $card_expire;
$HTTP_POST_VARS['card_valid_from'] = $card_valid_from;

if (!empty($login)) $userinfo = func_userinfo($login, $login_type, false, false, array("C","H"));

#
# Get userinfo and cart products and output an error if empty
#
if (empty($userinfo) || func_is_cart_empty($cart))
	func_header_location($xcart_web_dir.DIR_CUSTOMER."/error_message.php?error_ccprocessor_baddata");

$userinfo = func_array_merge($userinfo,$HTTP_POST_VARS);

include_once $xcart_dir."/include/cc_detect.php";

if (!empty($paymentid)) {
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] WHERE paymentid = '$paymentid' AND af_check = 'Y'"))
		define("IS_AF_CHECK", true);
}

# Only for compatibility with old code in payment modules.
# Please, use $cart["products"] in new code instead of $products.
if (!empty($cart["products"]) && is_array($cart["products"]))
	$products = $cart["products"];
else
	$products = array();

$bill_firstname = empty($userinfo['b_firstname']) ? $userinfo['firstname'] : $userinfo['b_firstname'];
$bill_lastname = empty($userinfo['b_lastname']) ? $userinfo['lastname'] : $userinfo['b_lastname'];
$bill_name = $bill_firstname;
if (!empty($bill_lastname))
	$bill_name .= (empty($bill_firstname) ? "" : " ").$bill_lastname;

$ship_firstname = empty($userinfo['s_firstname']) ? $userinfo['firstname'] : $userinfo['s_firstname'];
$ship_lastname = empty($userinfo['s_lastname']) ? $userinfo['lastname'] : $userinfo['s_lastname'];
$ship_name = $ship_firstname;
if (!empty($ship_lastname))
    $ship_name .= (empty($ship_firstname) ? "" : " ").$ship_lastname;


?>
