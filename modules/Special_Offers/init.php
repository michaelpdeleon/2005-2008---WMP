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
# $Id: init.php,v 1.10 2006/01/11 06:56:18 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_session_register("offer_products_priority");
x_session_register('cart');

ini_set('include_path',
	$xcart_dir . "/modules/Special_Offers"
	. PATH_SEPARATOR . ini_get('include_path'));

array_unshift($smarty->plugins_dir, $xcart_dir.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'Special_Offers');

array_unshift($mail_smarty->plugins_dir, $xcart_dir.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'Special_Offers');


if ($REQUEST_METHOD == "POST" && $mode=="add" && !empty($HTTP_REFERER) && strpos($HTTP_REFERER, 'offers.php?mode=add_free') !== false) {
	# manualy added product from list at special page
	# should have higher priority

	if (empty($offer_products_priority))
		$offer_products_priority = array();

	array_unshift($offer_products_priority, $productid);

	$offer_products_priority = array_unique($offer_products_priority);
}

if ($REQUEST_METHOD == "GET" && empty($cart['products'])) {
	$offer_products_priority = array();
}

if (defined('OFFERS_DONT_CHECK') || !defined('AREA_TYPE') || AREA_TYPE != 'C') return;

x_session_register('login');
x_session_register('login_type');
x_session_register('new_offers_message');

if (!empty($new_offers_message)) {
	$smarty->assign("new_offers_message", $new_offers_message);
	$new_offers_message = "";
}

function func_check_new_offers() {
	global $customer_available_offers, $cart, $login, $login_type;
	global $smarty, $new_offers_message;

	$is_new_visitor = !x_session_is_registered('customer_available_offers');
	x_session_register('customer_available_offers');
	$current_offers = array();
	
	$avail_offers = func_get_offers($login, $login_type, $cart);

	if (is_array($avail_offers)) {
		foreach ($avail_offers as $v)
			$current_offers[] = $v['offerid'];
	}
	
	if (is_array($cart)) {
		$avail_offers = func_get_offers($login, $login_type, false);
		if (is_array($avail_offers)) {
			foreach ($avail_offers as $v)
				$current_offers[] = $v['offerid'];
		}
	}

	if (!func_is_cart_empty($cart) && !empty($cart['orders'])) {
		foreach($cart['orders'] as $order) {
			$tmp = func_get_product_offers($login, $login_type, $order['products']);
			if (is_array($tmp)) {
				foreach ($tmp as $v) {
					if (is_array($v)) $current_offers = func_array_merge($current_offers, $v);
				}
			}
		}
	}

	if (is_array($current_offers)) {
		$current_offers = array_unique($current_offers);

		# get changes of available offers
		if (!is_array($customer_available_offers) || !is_array($current_offers) || empty($current_offers))
			$new_offers = $current_offers;
		else {
			$new_offers = array();
			foreach ($current_offers as $offerid) {
				if (!in_array($offerid, $customer_available_offers))
					$new_offers[] = $offerid;
			}
		}

		if (!defined('OFFERS_DONT_SHOW_NEW') && is_array($new_offers) && !empty($new_offers)) {
			$info_offers = func_get_sorted_offers($new_offers);
			$smarty->assign("new_offers", $info_offers);
			$tmp_content = func_display('modules/Special_Offers/customer/new_offers_short_list.tpl',$smarty,false);
			$top_data = array('content'=>$tmp_content);
			$smarty->assign("new_offers_message", $top_data);
			if ($is_new_visitor)
				$new_offers_message = $top_data;
		}
	}
	$customer_available_offers = $current_offers;
}

$config['special_offers_mark_products'] = false;

func_check_new_offers();

$config['special_offers_mark_products'] = true;

?>
