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
# $Id: discount_coupons.php,v 1.27.2.1 2006/10/16 06:55:46 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($cart)) return;

x_load('cart');

if (!empty($cart["discount_coupon"]) && func_is_valid_coupon($cart["discount_coupon"]) > 0) {
	$cart["discount_coupon"]="";
	$cart["coupon_type"] = "";
}

if ($mode=="add_coupon" && $coupon) {
#
# Check if coupon is valid
#
	$my_coupon = func_is_valid_coupon($coupon);

	# Bad coupon provider
	if ($my_coupon == 2) {
		$top_message['content'] = func_get_langvar_by_name("err_bad_coupon_provider_msg");

	# Coupon already used by this customer
	} elseif ($my_coupon == 5) {
		$top_message['content'] = func_get_langvar_by_name("txt_coupon_already_used_by_customer");

	# Overstepping of coupon order total
	} elseif ($my_coupon == 3) {
		$top_message['content'] = func_get_langvar_by_name("txt_overstepping_coupon_order_total");

	# Not found coupon target
	} elseif ($my_coupon == 4) {
		$top_message['content'] = func_get_langvar_by_name("txt_cart_not_contain_coupon_products");

	# Bad coupon
	} elseif ($my_coupon == 1) {
		$top_message['content'] = func_get_langvar_by_name("err_bad_coupon_code_msg");

	# Add discount coupon
	} elseif($my_coupon == 0) {
		$cart["discount_coupon"] = $coupon;
	}
	if($my_coupon > 0) {
        $cart["discount_coupon"] = "";
		$cart["coupon_type"] = "";
		$top_message['type'] = 'E';
	}
	func_header_location("cart.php");
}
elseif ($mode=="unset_coupons") {
	$cart["discount_coupon"]="";
	$cart["coupon_type"] = "";
	func_header_location("cart.php");
}

?>
