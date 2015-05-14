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
# $Id: calculate_prepare.php,v 1.6 2006/01/11 06:56:18 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

# First step: get all applicable offers and apply the 'products for free' bonus

$offers_order_bonuses = false;
$not_used_free_products = false;
$avail_offers = false;
global $customer_unused_offers;
global $customer_available_offers;

$current_offers = array();

if (is_array($products)) {
	# clear fields
	foreach ($products as $k=>$v) {
		foreach (array('free_amount','have_offers','free_shipping_used','special_price_used', 'saved_original_price') as $_tmp_k) {
			if (isset($v[$_tmp_k])) $products[$k][$_tmp_k] = false;
		}
	}

	$avail_offers = func_get_applicable_offers($products, $customer_info, $provider_for);
	if (is_array($avail_offers)) {
		func_offer_set_free_products($avail_offers, $products, $offers_order_bonuses);

		foreach ($avail_offers as $offer) {
			$current_offers[] = $offer['offerid'];
		}
	}
}

if (x_session_is_registered("customer_available_offers")) {
	x_session_register("customer_available_offers");

	if (is_array($customer_available_offers)) {
		$new_unused_offers = array_diff($customer_available_offers, $current_offers);
		if (is_array($customer_unused_offers))
			$customer_unused_offers = array_intersect($customer_unused_offers, $new_unused_offers);
		else
			$customer_unused_offers = $new_unused_offers;
	}
}

if (empty($customer_unused_offers)) $customer_unused_offers = false;

global $smarty;
$smarty->assign("customer_unused_offers", $customer_unused_offers);

?>
