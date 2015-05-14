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
# $Id: customer_offers.php,v 1.17.2.1 2006/09/05 09:55:05 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

if (!defined('SO_CUSTOMER_OFFERS')) {
	define('SO_CUSTOMER_OFFERS', 1);
}

x_load('product','user');

$offers = false;
if (empty($mode) || !in_array($mode,array('cart','cat','product','offer','add_free', 'unused'))) {
	$mode = "";
}

if ($mode == 'add_free' || $mode == 'unused') {
	$offers_return_url = 'cart.php?mode=checkout';
}
else
if (empty($offers_return_url) && !empty($HTTP_REFERER)) {
	$offers_url = $xcart_catalogs['customer'].'/offers.php';

	if (strncmp($HTTP_REFERER, $offers_url, strlen($offers_url)) && !strncmp($HTTP_REFERER,$xcart_catalogs['customer'],strlen($xcart_catalogs['customer']))) {
		$offers_return_url = substr($HTTP_REFERER,strlen($xcart_catalogs['customer']));
		$offers_return_url = preg_replace('!^/+!','', $offers_return_url);
	}
}

if (!empty($offers_return_url)) {
	$smarty->assign('offers_return_url', $offers_return_url);
}

if ($mode == 'add_free') {
	x_session_register('cart');
	if (!empty($cart['not_used_free_products'])) {
		$old_search_data = $search_data["products"];
		$old_mode = $mode;

		$search_data["products"] = array();
		$search_data["products"]["search_in_subcategories"] = "";

		$tmp_cond = array();

		if (!empty($cart['not_used_free_products']['F']) && $single_mode) {
			$tmp_cond = array(1);
		}
		else {
			if (!empty($cart['not_used_free_products']['P'])) {
				$tmp_cond[] = "$sql_tbl[products].productid IN (".implode(',',array_keys($cart['not_used_free_products']['P'])).")";
			}

			if (!empty($cart['not_used_free_products']['C'])) {
				$tmp_cond[] = "$sql_tbl[products_categories].categoryid IN (".implode(',',array_keys($cart['not_used_free_products']['C'])).")";
			}

			if (!empty($cart['not_used_free_products']['R'])) {
				$id_list = array_keys($cart['not_used_free_products']['R']);

				if (count($id_list) > 1)
					$id_str = '('.implode('|',$id_list).')';
				else
					list($id_str) = $id_list;

				$tmp_cond[] = "$sql_tbl[products_categories].categoryid IN (".implode(',',$id_list).") OR $sql_tbl[categories].categoryid_path REGEXP '(^|/)$id_str($|/)'";
			}

			if (!empty($cart['not_used_free_products']['F'])) {
				$products_providers = array_unique($cart['not_used_free_products']['F']);
				$tmp_cond[] = "$sql_tbl[products].provider IN ('".implode("','",$products_providers)."')";
			}
		}
		
		# Prepare products filter by providers if $single_mode = false
		if (!$single_mode && !empty($cart['orders']) && is_array($cart['orders'])) {
			$_providers = array();
			foreach ($cart['orders'] as $_order) {
				if (!empty($_order['applied_offers']) && is_array($_order['applied_offers'])) {
					foreach ($_order['applied_offers'] as $_applied_offer) {
						$_providers[] = addslashes($_applied_offer['provider']);
					}
				}
			}
			$_providers = array_unique($_providers);
			if (count($_providers) > 1)
				$search_data["products"]["provider"] = $_providers;
			else
				$search_data["products"]["provider"] = $_providers[0];
		}

		if (empty($tmp_cond)) $tmp_cond = array(1);

		$search_data["products"]['_']['where'][] = '(('.implode(') OR (', $tmp_cond).'))';

		$params_join = array();
		$bonuses_join = array();
		$all_bonuses_list = array();
		foreach ($cart['not_used_free_products'] as $k=>$bonus_id_list) {
			$bonus_id_list = array_unique($bonus_id_list);
			$id_str = implode(',', $bonus_id_list);
			$id_condition = "$sql_tbl[offer_bonus_params].bonusid IN (".$id_str.")";

			if ($k == 'DISCOUNT_GEN_P') {
				$all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);
				$params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='P' AND $sql_tbl[products].productid=$sql_tbl[offer_bonus_params].param_id";
			}
			else
			if ($k == 'DISCOUNT_GEN_C') {
				$all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);
				$params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='C' AND $sql_tbl[offer_bonus_params].param_arg<>'Y' AND $sql_tbl[products_categories].categoryid=$sql_tbl[offer_bonus_params].param_id";
			}
			else
			if ($k == 'DISCOUNT_GEN_R') {
				$all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);
				$params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='C' AND $sql_tbl[offer_bonus_params].param_arg='Y' AND ($sql_tbl[products_categories].categoryid=$sql_tbl[offer_bonus_params].param_id OR $sql_tbl[categories].categoryid_path REGEXP CONCAT('(^|/)',$sql_tbl[offer_bonus_params].param_id,'($|/)'))";
			}
			else
			if (preg_match('!^DISCOUNT_PROV_(.*)$!S', $k, $m)) {
				$all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);
				$params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type IN ('N','D')";
				$bonuses_join[] = "$sql_tbl[offer_bonuses].bonusid IN (".implode(',', $bonus_id_list).")";
			}
		}

		$bonuses_join_str = "$sql_tbl[offer_bonuses].avail='Y' AND $sql_tbl[offer_bonuses].bonusid IN (".implode(',',$all_bonuses_list).")";

		$parent = '';
		if (!empty($params_join)) {
			$search_data["products"]['_']['left_joins']['offer_bonus_params'] = array(
				"on" => '(('.implode(') OR (', $params_join).'))',
				"parent" => "category_memberships"
			);
			$bonuses_join[] = "$sql_tbl[offer_bonus_params].bonusid = $sql_tbl[offer_bonuses].bonusid";
		}

		if (!empty($bonuses_join)) {
			$bonuses_join_str .= " AND ((".implode(') OR (', $bonuses_join)."))";
		}

		$search_data["products"]['_']['left_joins']['offer_bonuses'] = array(
			"on" => $bonuses_join_str,
		);

		if (!empty($params_join)) {
			$search_data["products"]['_']['left_joins']['offer_bonuses']['parent'] = "offer_bonus_params";
		}

		$search_data["products"]['_']['fields_count'][] = $search_data["products"]['_']['fields'][] =
"MIN($sql_tbl[pricing].price -
IF ($sql_tbl[offer_bonuses].bonus_type='D',
	-- true ('D')
	IF ($sql_tbl[offer_bonuses].amount_type='%',
		-- % discount
		IF ( $sql_tbl[offer_bonuses].amount_max>0.00 AND ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_min / 100) > $sql_tbl[offer_bonuses].amount_max,
			-- true
			$sql_tbl[offer_bonuses].amount_max
			,
			-- false (without delimiter)
			($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_min / 100)
		)
		,
		-- $ discount
		IF ( $sql_tbl[offer_bonuses].amount_max>0.00 AND $sql_tbl[offer_bonuses].amount_min > ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_max / 100),
			-- true
			($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_max / 100)
			,
			-- false (without delimiter)
			$sql_tbl[offer_bonuses].amount_min
		)
	)
	,
	-- false (NOT 'D')
	IF ($sql_tbl[offer_bonuses].bonus_type='N',
		-- true (FREE PRODUCT)
		$sql_tbl[pricing].price
		,
		-- false
		0.00
	)
)
) AS x_special_price";

		if (!isset($sort)) $sort = 'price';

		if (!isset($sort_direction)) $sort_direction = 0;

		$search_data["products"]["show_special_prices"] = true;
		$mode = "search";
		include $xcart_dir."/include/search.php";

		$search_data["products"] = $old_search_data;
		$mode = $old_mode;
		$page = $old_page;
		$smarty->clear_assign("products");

		if (!empty($products)) {
			$smarty->assign('special_offers_add_to_cart', 'Y');
			$smarty->assign('free_products', $products);
			$smarty->assign("navigation_script","offers.php?mode=add_free&sort=$sort&sort_direction=$sort_direction&offers_return_url=".urlencode($offers_return_url));
			$smarty->_tpl_vars['config']['Appearance']['max_select_quantity'] = 1;
		}
	}

	x_session_register('customer_unused_offers');
	if (!empty($customer_unused_offers)) {
		$info_offers = func_get_sorted_offers($customer_unused_offers);
		$smarty->assign("new_offers", $info_offers);
	}

	$smarty->assign('mode', 'add_free');

	return;
}

# check product
if (!empty($productid)) {
	$customer_info = func_userinfo($login, $current_area);
	$membership = empty($customer_info['membership']) ? "" : $customer_info['membership'];
	$offers_product = func_select_product($productid, $membership, false);
	if (empty($offers_product)) $productid = false;
	else {
		$smarty->assign("offers_product", $offers_product);
	}
}

# check category
if (!empty($cat)) {
	$offers_category = func_get_category_data($cat);
	if (empty($offers_category)) $cat = false;
	else $smarty->assign("offers_category", $offers_category);
}

switch ($mode) {
case 'product':
	if (!empty($productid)) {
		$tmp = func_get_product_offers($login, $current_area, $productid, true);
		if (!empty($tmp)) $offers = array_pop($tmp);
	}
	break;
case 'cat':
	if (!empty($cat)) {
		$offers = func_get_category_offers($login, $current_area, $cat, true);
	}
	break;
case 'cart':
	if (!empty($cart['products'])) {
		$offers = func_get_offers($login, $current_area, $cart);
	}
	break;
case 'offer':
	if (!empty($offerid)) {
		$tmp = func_get_offer($offerid);
		if ($tmp !== false)
			$offers[] = $tmp;
	}
	break;
case 'unused':
	x_session_register('customer_unused_offers');
	$offers = func_get_sorted_offers($customer_unused_offers);
	break;
default: $mode = '';
	$offers = func_get_offers($login, $current_area, false);
}

if (is_array($offers)) {
	foreach ($offers as $key=>$offer) {
		$promo = func_get_offer_promo($offer['offerid'], $store_language);
		$offers[$key] = func_array_merge($offers[$key],$promo);
	}
}

if (empty($offers)) $offers = false;

$smarty->assign("offers", $offers);
$smarty->assign("mode", $mode);
$smarty->assign("offers_cart", !empty($cart));

?>
