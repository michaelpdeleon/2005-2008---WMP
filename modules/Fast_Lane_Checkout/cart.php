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
# $Id: cart.php,v 1.9 2006/03/21 07:17:16 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }


# Skip if module is disabled
if (empty($active_modules["Fast_Lane_Checkout"]))
	return;

# Skip if cart is empty
if ($func_is_cart_empty)
	return;

# Skip if current mode is not checkout or cart
if ($smarty->get_template_vars("main") != "cart" && !in_array($mode, array("checkout", "auth")) && !($mode == "update" && $action == "cart"))
	return;


#
# The tabs array for disaplying on the checkout
#
$checkout_tabs = array();
$checkout_tabs["cart"] = array(
	"title" => func_get_langvar_by_name("lbl_your_cart"),
	"link" => "cart.php");
$checkout_tabs["account"] = array(
	"title" => func_get_langvar_by_name("lbl_my_account"),
	"link" => "register.php?mode=update&action=cart&paymentid=$paymentid");
$checkout_tabs["method"] = array(
	"title" => func_get_langvar_by_name("lbl_shipping_and_payment"),
	"link" => "cart.php?mode=checkout");
$checkout_tabs["place"] = array(
	"title" => func_get_langvar_by_name("lbl_place_order"),
	"link" => "cart.php?paymentid=$paymentid&mode=checkout");

$checkout_tabs_hash = array(
	0 => "account",
	1 => "account",
	2 => "method",
	3 => "place");

if ($mode == "checkout" && !empty($paymentid)) {
	if ($mode == "checkout" && !empty($cart["products"]) && empty($shipping) && !empty($login) && $need_shipping && $config["Shipping"]["disable_shipping"] != "Y") {
		$top_message["content"] = func_get_langvar_by_name("msg_flc_select_shipping_err");
		$top_message["type"] = "E";
		$checkout_step = 2;
		func_header_location("cart.php?mode=checkout");
	}
	else {
		$checkout_step = 3;
	}
}
elseif ($mode == "checkout" && empty($paymentid)) {
	$checkout_step = 2;
}

if (empty($login)) {
	$checkout_step = 0;
}
else {
	if ($mode == "update" && $action == "cart")
		$checkout_step = 1;
}

if ($smarty->get_template_vars("main") == "cart")
	$checkout_step = -1;

if (!$need_shipping || count($shipping) == 1) {
	if (!function_exists("check_payment_methods")) {
		require $xcart_dir."/include/cart_process.php";
	}

	$_payment_methods = check_payment_methods(@$user_account["membershipid"]);
	if (count($_payment_methods) <= 1)
		unset($checkout_tabs["method"]);
}

if ($checkout_step >= 0) {
	$checkout_tabs[$checkout_tabs_hash[$checkout_step]]["selected"] = "Y";
	if (is_array($location) && $checkout_step != 1)
		unset($location[count($location)-1]);
}

$smarty->assign("checkout_tabs",$checkout_tabs);
$smarty->assign("checkout_step",$checkout_step);
$smarty->assign("paymentid",$paymentid);

$smarty->assign("main", "fast_lane_checkout");

# Assign the current location line
$smarty->assign("location", $location);

func_display("modules/Fast_Lane_Checkout/home.tpl", $smarty);
exit;

?>
