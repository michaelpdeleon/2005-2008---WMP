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
# $Id: func.php,v 1.43.2.9 2007/01/24 11:59:15 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

# x_load('cart','user','taxes'); // not really necessary: 'user' is loaded in 'cart';

function func_get_column($column, $query) {
	$result = false;

	if ($p_result = db_query($query)) {
		while ($row = db_fetch_array($p_result))
			$result[] = $row[$column];

		db_free_result($p_result);
	}

	return $result;
}

function func_default_userinfo($login, $type) {
	global $config;

	if (empty($login)) {
		$result = "";
		foreach (array("b_","s_") as $p) {
			$result[$p."country"] = $config["General"]["default_country"];
			$result[$p."state"] = $config["General"]["default_state"];
			$result[$p."county"] = $config["General"]["default_county"];
			$result[$p."zipcode"] = $config["General"]["default_zipcode"];
			$result[$p."city"] = $config["General"]["default_city"];
		}
		$result['membershipid'] = 0;
	}
	else {
		x_load("user");
		$result = func_userinfo($login, empty($type)?"C":$type);
	}

	return $result;
}

function func_offer_get_conditions($offerid, $provider, $filter="") {
	global $config, $sql_tbl, $single_mode, $shop_language;

	$prov_cond = ($single_mode || empty($provider) ? "" : " AND provider='$provider'");

	$conditions = func_query("SELECT * FROM $sql_tbl[offer_conditions] WHERE offerid = '$offerid' $prov_cond $filter ORDER BY conditionid");
	if (!is_array($conditions) || empty($conditions))
		return false;

	foreach ($conditions as $k => $cnd) {
		$conditions[$k]['params'] = func_query("SELECT * FROM $sql_tbl[offer_condition_params] WHERE conditionid = '$cnd[conditionid]' ORDER BY paramid");

		if (!empty($conditions[$k]['params'])) {
			foreach ($conditions[$k]['params'] as $pk => $p) {
				$tmp = array();
				switch ($p['param_type']) {
					case "P":
						$tmp = func_query_first("SELECT product, productcode FROM $sql_tbl[products] WHERE productid = '$p[param_id]'");
						break;
					case "C":
						$tmp = func_query_first("SELECT category FROM $sql_tbl[categories] WHERE categoryid = '$p[param_id]'");
						break;
					case "Z":
						$tmp = func_query_first("SELECT zone_name FROM $sql_tbl[zones] WHERE zoneid = '$p[param_id]'");
						break;
				}
				if (!empty($tmp))
					$conditions[$k]['params'][$pk] = func_array_merge($p, $tmp);
			}
		}

		if ($cnd['condition_type'] == 'M') {
			$list = func_get_memberships();
			$keys = func_query_column("SELECT membershipid FROM $sql_tbl[condition_memberships] WHERE conditionid = '$cnd[conditionid]'");
			$memberships = array();
			$memberships_arr = array();

			if (!empty($list)) {
				foreach($list as $m) {
					$m['selected'] = in_array($m['membershipid'], $keys);
					$m['name'] = $m['membership'];
					$memberships[$m['membershipid']] = $m;
					if ($m['selected']) {
						$memberships_arr[$m['membershipid']] = $m['name'];
					}
				}
			}
			$conditions[$k]['memberships'] = $memberships;
			$conditions[$k]['memberships_arr'] = $memberships_arr;
		}
	}

	return $conditions;
}

function func_offer_get_bonuses($offerid, $provider, $filter="") {
	global $single_mode, $shop_language;
	global $sql_tbl;
	global $config;

	$prov_cond = ($single_mode||empty($provider)?"":" AND provider='$provider'");

	$bonuses = func_query("SELECT * FROM $sql_tbl[offer_bonuses] WHERE offerid='$offerid' $prov_cond $filter ORDER BY bonusid");

	if (!is_array($bonuses))
		return false;

	foreach ($bonuses as $k => $bonus) {
		$bonuses[$k]["params"] = func_query("SELECT * FROM $sql_tbl[offer_bonus_params] WHERE bonusid = '$bonus[bonusid]' ORDER BY paramid");

		if (is_array($bonuses[$k]['params'])) {
			foreach ($bonuses[$k]['params'] as $pk => $p) {
				$tmp = array();
				switch ($p['param_type']) {
					case "P":
						$tmp = func_query_first("SELECT product, productcode FROM $sql_tbl[products] WHERE productid = '$p[param_id]'");
						break;
					case "C":
						$tmp = func_query_first("SELECT category FROM $sql_tbl[categories] WHERE categoryid = '$p[param_id]'");
						break;
				}
				if (!empty($tmp))
					$bonuses[$k]['params'][$pk] = func_array_merge($p, $tmp);

				$bonuses[$k]["params"][$pk]['param_qnty_work'] = $p['param_qnty'];
			}
		}

		if ($bonus['bonus_type'] == 'M') {
			$list = func_get_memberships();
			$keys = func_query_column("SELECT membershipid FROM $sql_tbl[bonus_memberships] WHERE bonusid = '$bonus[bonusid]'");
			$memberships = array();
			$memberships_arr = array();

			if (!empty($list)) {
				foreach($list as $m) {
					$m['selected'] = in_array($m['membershipid'], $keys);
					$m['name'] = $m['membership'];
					$memberships[$m['membershipid']] = $m;
					if ($m['selected']) {
						$memberships_arr[$m['membershipid']] = $m['name'];
					}
				}
			}

			$bonuses[$k]['memberships'] = $memberships;
			$bonuses[$k]['memberships_arr'] = $memberships_arr;
		}
	}

	return $bonuses;
}

function func_offer_count_products(&$products, $productid=false) {
	$count = array();
	if (!is_array($products) || empty($products)) return $count;
	#
	# Collect quantity of products
	#
	foreach ($products as $product) {

		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if ($productid!==false && $product['productid'] != $productid)
			continue;

		if (!isset($count[$product['productid']])) {
			$count[$product['productid']] = 0;
		}

		$count[$product['productid']] += $product['amount'];
	}

	return $count;
}

function func_offer_mark_products(&$products, $productid, $value) {
	global $config;

	if (empty($products) || !is_array($products)) return;

	if (empty($config['special_offers_mark_products']))
		$value = false;

	foreach ($products as $k=>$v) {
		if (!empty($v['productid']) && $v['productid'] == $productid) {
			$products[$k]['have_offers'] = $value;
		}
	}
}

#
# mode ::= E | N
# E - equal or greater
# N - Nth product
#
function func_offer_check_catproducts(&$products, $categoryid, $quantity, $mode, $recursive, &$locked_amount) {
	global $sql_tbl;
	global $config;

	$locked_amount = array();
	$count = func_offer_count_products($products);

	if ($recursive == "Y") {
		$path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid='$categoryid'");
		$path_condition = " OR $sql_tbl[categories].categoryid_path LIKE '$path/%'";
	}
	else $path_condition = "";

	# validate and reduce index
	$local_quantity = 0;
	foreach ($count as $productid=>$amount) {
		$r = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories] WHERE
				$sql_tbl[products_categories].productid=$productid AND
				$sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND
				($sql_tbl[categories].categoryid='$categoryid' $path_condition)
		");

		if ($r < 1) {
			unset($count[$productid]);
			continue;
		}

		func_offer_mark_products($products, $productid, true);

		$local_quantity += $amount;
	}

	if ($local_quantity < $quantity)
		return false;

	$locked_amount[$categoryid]['amount'] = $quantity;
	$locked_amount[$categoryid]['products'] = $count;

	if ($mode == 'N')
		return floor($local_quantity / $quantity);

	return true;
}

#
# mode ::= E | N
# E - equal or greater
# N - Nth product
#
function func_offer_check_products(&$products, $productid, $quantity, $mode, &$locked_amount) {

	$locked_amount = array();

	$count = func_offer_count_products($products, $productid);
	if (empty($count[$productid])) return false;

	if (($count[$productid] >= $quantity)) {
		func_offer_mark_products($products, $productid, true);
		$locked_amount[$productid] = $quantity;

		if ($mode == 'N')
			return floor($count[$productid] / $quantity);

		return true;
	}

	return false;
}

function func_offer_check_condition_set($provider, &$products, &$customer_info, &$condition) {
	global $sql_tbl;

	$mult = 0;

	$type = ($condition['amount_type'] == 'N') ? 'N' : 'E';

	$locked_amount = array('C'=>array(), 'P'=>array(), 'R'=>array());

	foreach ($condition['params'] as $param) {
		$r = false;
		if ($param['param_qnty'] < 1) continue;

		$tmp_locked_amount = array('C'=>array(), 'P'=>array());
		if ($param['param_type'] == 'P') {
			$r = func_offer_check_products($products, $param['param_id'], $param['param_qnty'], $type, $tmp_locked_amount['P']);
		}
		elseif ($param['param_type'] == 'C') {
			$r = func_offer_check_catproducts($products, $param['param_id'], $param['param_qnty'], $type, $param['param_arg'], $tmp_locked_amount['C']);
		}

		if ($r<1) {
			$mult = 0;
			break;
		}

		if ($mult > 0)
			$mult = min($r, $mult);
		else
			$mult = $r;

		# merge index for products
		foreach ($tmp_locked_amount['P'] as $pid=>$qnty) {
			if (!isset($locked_amount['P'][$pid]))
				$locked_amount['P'][$pid] = $qnty;
			else
				$locked_amount['P'][$pid] = max($locked_amount['P'][$pid],$qnty);
		}

		# merge index for categories
		$l_key = ($param['param_arg']=='Y') ? 'R' : 'C';
		foreach ($tmp_locked_amount['C'] as $cid=>$value) {
			if (!isset($locked_amount[$l_key][$cid]) || $locked_amount[$l_key][$cid]['amount'] < $value['amount']) {
				$locked_amount[$l_key][$cid] = $value;
			}
		}
	}

	if ($mult > 0 && $type!='N')
		$mult = 1;

	$condition['mult'] = $mult;

	if ($mult > 0)
		$condition['locked_amount'] = $locked_amount;
	else
		$condition['locked_amount'] = array();

	return $mult > 0;
}

function func_offer_check_condition_membership($provider, &$products, &$customer_info, &$condition) {
	if (empty($condition['memberships_arr'])) {
		if (empty($customer_info['membershipid']))
			return true;
		return false;
	}

	return isset($condition['memberships_arr'][$customer_info['membershipid']]);
}

function func_offer_check_condition_zone($provider, &$products, &$customer_info, &$condition) {
	x_load('cart'); # for func_get_customer_zones_avail()

	foreach ($condition['params'] as $param) {
		$zones = func_get_customer_zones_avail($customer_info, $provider, $param['param_arg']);

		$weight = 0;
		$check = array();
		foreach ($zones as $zoneid=>$w) {
			if ($w < $weight) break;
			$weight = $w;
			$check[] = $zoneid;
		}

		if (in_array($param['param_id'], $check)) {
			return true;
		}
	}

	return false;
}

function func_offer_get_subtotal(&$products) {
	$subtotal = 0;
	foreach ($products as $product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		$amount = $product["amount"];
		if (isset($product["free_amount"]))
			$amount = $product["amount"] - $product["free_amount"];

		$subtotal += $product["display_price"] * $amount;
	}

	return $subtotal;
}

function func_offer_get_discounted_subtotal(&$products) {
	$subtotal = 0;
	foreach ($products as $product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		$subtotal += $product["display_discounted_price"];
	}
		return $subtotal;

}

function func_offer_check_condition_subtotal($provider, &$products, &$customer_info, &$condition) {
	$subtotal = func_offer_get_subtotal($products);

	return (($condition['amount_min'] <= $subtotal) && ($condition['amount_max'] == 0.00 || $subtotal <= $condition['amount_max']));
}

function func_offer_check_condition_points($provider, &$products, &$customer_info, &$condition) {
	global $sql_tbl;

	$points = func_query_first_cell("SELECT points FROM $sql_tbl[customer_bonuses] WHERE login='$customer_info[login]'");
	if ($points === false) $points = 0;

	return (($condition['amount_min'] <= $points) && ($condition['amount_max'] == 0 || $points <= $condition['amount_max']));
}

function func_offer_condition_is_empty(&$condition) {
	static $empty_param_func = array('S','Z');

	if ($condition['avail'] !== 'Y') return true;

	#
	# Ignore some conditions without parameters
	#
	if (empty($condition['params']) && in_array($condition['condition_type'], $empty_param_func)) {
		return true;
	}

	return false;
}

function func_offer_bonus_is_empty(&$bonus) {
	global $sql_tbl;
	static $empty_param_func = array('N');

	if ($bonus['avail'] !== 'Y') return true;

	if (empty($bonus['params']) && in_array($bonus['bonus_type'], $empty_param_func)) {
		return true;
	}

	if ($bonus['bonus_type'] == 'M') {
		if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[bonus_memberships] WHERE bonusid = '$bonus[bonusid]'"))
			return true;
	}

	return false;
}

function func_offer_check_condition($provider, &$products, &$customer_info, &$condition) {
	static $functions = array(
		'S' => 'func_offer_check_condition_set',
		'T' => 'func_offer_check_condition_subtotal',
		'M' => 'func_offer_check_condition_membership',
		'B' => 'func_offer_check_condition_points',
		'Z' => 'func_offer_check_condition_zone'
	);

	if (func_offer_condition_is_empty($condition)) return "I";

	if (!empty($functions[$condition['condition_type']])) {
		$func = $functions[$condition['condition_type']];
		return $func($provider, $products, $customer_info, $condition);
	}

	return false;
}

#
# Get offer promo blocks
#
function func_get_offer_promo($offerid, $lngcode) {
	global $sql_tbl;

	if (empty($offerid)) return array();

	$promo = func_query_first("SELECT $sql_tbl[offers_lng].promo_short, IF($sql_tbl[images_S].id IS NULL, '', 'Y') AS promo_short_img, $sql_tbl[offers_lng].promo_long FROM $sql_tbl[offers_lng] LEFT JOIN $sql_tbl[images_S] ON $sql_tbl[images_S].id = '$lngcode$offerid' WHERE $sql_tbl[offers_lng].offerid='$offerid' AND $sql_tbl[offers_lng].code='$lngcode'");
	$result = array();

	if (!empty($promo)) {
		$result['promo_lng_code'] = $lngcode;
		$result['promo_short'] = $promo['promo_short'];
		$result['promo_short_img'] = $promo['promo_short_img'];
		$result['promo_long'] = $promo['promo_long'];
		$result['html_short'] = (strip_tags($promo['promo_short']) != $promo['promo_short']);
		$result['html_long'] = (strip_tags($promo['promo_long']) != $promo['promo_long']);
	}

	return $result;
}

#
# Get offer by id
#
function func_get_offer($offerid, $full=false) {
	global $sql_tbl;
	global $store_language;

	if (empty($offerid)) return false;

	$now = time();

	$result = func_query_first("SELECT * FROM $sql_tbl[offers] WHERE offerid='$offerid' AND offer_avail='Y' AND offer_start<='$now' AND offer_end>='$now'");
	if ($result) {
		$promo = func_get_offer_promo($offerid, $store_language);
		$result = func_array_merge($result, $promo);

		if ($full) {
			$result['conditions'] = func_offer_get_conditions($offerid, "", "AND avail='Y'");
			$result['bonuses'] = func_offer_get_bonuses($offerid, "", "AND avail='Y'");
		}
	}

	return $result;
}

function func_check_offer(&$offer) {
	$valid = true;

	# check details
	$now = time();
	$offer['incorrect_period'] = $offer['offer_end'] < $offer['offer_start'];
	$offer['upcoming'] = $offer['offer_start'] > $now;
	$offer['expired'] = $now > $offer['offer_end'];


	if ($offer['incorrect_period'])
		$valid = false;

	# check conditions
	$offer['conditions_valid'] = !empty($offer['conditions']);
	if ($offer['conditions_valid']) {
		$non_avail = 0;
		foreach ($offer['conditions'] as $k=>$condition) {
			if ($condition['avail'] !== 'Y') {
				$non_avail++;
				continue;
			}

			if (func_offer_condition_is_empty($condition)) {
				$offer['conditions'][$k]['valid'] = false;
				$valid = false;
				$offer['conditions_valid'] = false;
			}
			else
				$offer['conditions'][$k]['valid'] = true;
		}

		if ($non_avail >= count($offer['conditions'])) {
			$offer['conditions_valid'] = false;
			$valid = false;
		}
	}
	else
		$valid = false;

	# check bonuses
	$offer['bonuses_valid'] = !empty($offer['bonuses']);
	if ($offer['bonuses_valid']) {
		$non_avail = 0;
		foreach ($offer['bonuses'] as $k=>$bonus) {
			if ($bonus['avail'] !== 'Y') {
				$non_avail++;
				continue;
			}
			if (func_offer_bonus_is_empty($bonus)) {
				$offer['bonuses'][$k]['valid'] = false;
				$valid = false;
				$offer['bonuses_valid'] = false;
			}
			else
				$offer['bonuses'][$k]['valid'] = true;
		}

		if ($non_avail >= count($offer['bonuses'])) {
			$offer['bonuses_valid'] = false;
			$valid = false;
		}
	}
	else
		$valid = false;

	$offer['valid'] = $valid;
	if (!$valid || $offer['expired'])
		$offer['invalid'] = true;
	else
		$offer['invalid'] = false;
}

function func_get_applicable_offers(&$products, &$customer_info, $provider, $use_conditions="", $offerid=false) {
	global $sql_tbl, $config, $single_mode;

	$now = time();

	$provider_condition = "";
	if (!$single_mode && $provider != "") {
		$provider_condition = " AND provider='$provider'";
	}

	$offerid_condition = "";
	if (is_array($offerid) && !empty($offerid)) {
		$offerid_condition = " AND offerid IN (".implode(',',$offerid).")";
	}

	$avail_offers = array();
	$p_result = db_query("SELECT * FROM $sql_tbl[offers] WHERE offer_avail='Y' AND offer_start<='$now' AND offer_end>='$now'".$provider_condition.$offerid_condition);
	if (!$p_result) return false;

	while ($offer = db_fetch_array($p_result)) {
		$offer['conditions'] = func_offer_get_conditions($offer['offerid'], $offer['provider'], "AND avail='Y'");
		if ($offer['conditions'] === false) continue;

		$offer['mult'] = false;

		$good = 0;
		foreach ($offer['conditions'] as $condition_key=>$condition) {
			if (func_offer_condition_is_empty($condition)) {
				$valid = "I";
			}
			else {
				if (!empty($use_conditions) && strpos($use_conditions, $condition['condition_type']) === false) {
					$valid = true;
				} else {
					$valid = func_offer_check_condition($offer['provider'], $products, $customer_info, $condition);
					$offer['conditions'][$condition_key] = $condition;
				}
			}

			if (!$valid) {
				$good = 0;
				break;
			}

			if ($valid === true) {
				if (!empty($condition['mult'])) {
					if ($offer['mult'] === false)
						$offer['mult'] = $condition['mult'];
					else
						$offer['mult'] = min($offer['mult'],$condition['mult']);
				}
				$good ++;
			}
		}
		if ($offer['mult'] === false)
			$offer['mult'] = 1;

		if ($good > 0) {
			$offer['bonuses'] = func_offer_get_bonuses($offer['offerid'], $offer['provider'], "AND avail='Y'");
			if ($offer['bonuses'] !== false) {
				if ($offer['mult'] > 1) {
					# increase number of 'free products' according to $offer['mult']
					foreach($offer['bonuses'] as $kb=>$bonus) {
						if ($bonus['bonus_type'] != 'N' || empty($bonus['params'])) continue;

						/* we will fix it in func_offer_set_free_products()
						foreach ($bonus['params'] as $kp=>$param) {
							$offer['bonuses'][$kb]['params'][$kp]['param_qnty'] *= $offer['mult'];
						}
						*/
					}
				}
				$avail_offers[] = $offer;
			}
		}
	}
	db_free_result($p_result);

	if (empty($avail_offers)) return false;

	return $avail_offers;
}

function func_get_applicable_offers_cart($cart, $login, $usertype) {
	if (empty($cart['orders'])) return false;

	$customer_info = func_default_userinfo($login, $usertype);
	$result = array();
	foreach($cart['orders'] as $order) {
		$offers = func_get_applicable_offers($order['products'], $customer_info, $order['provider']);
		if (!empty($offers)) {
			$result = func_array_merge($result, $offers);
		}
	}

	return empty($result) ? false : $result;
}

function func_get_offers($login, $usertype, $cart) {
	if (is_array($cart)) return func_get_applicable_offers_cart($cart, $login, $usertype);

	$products = array();
	$customer_info = func_default_userinfo($login, $usertype);
	return func_get_applicable_offers($products, $customer_info, "", "MZ");
}

function func_bonus_check_product(&$products, &$params, $productid, $check_all=false) {
	global $sql_tbl;

	$result = false;
	$matched = array();
	foreach ($params as $key=>$param) {
		switch ($param['param_type']) {
			case 'P': # match by productid
				if ($param['param_id'] == $productid) {
					$result = true;
					$param['kp'] = $key;
					$matched[] = $param;
				}
				break;

			case 'C': # match by categoryid
				$path_condition = "";
				if ($param['param_arg'] == "Y" || $check_all) {
					$path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid='$param[param_id]'");
					$param['category_path'] = $path;
					if ($param['param_arg'] == "Y") {
						$path_condition = " OR $sql_tbl[categories].categoryid_path LIKE '$path/%'";
					}
				}

				$r = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products_categories].productid=$productid AND $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid='$param[param_id]' $path_condition)");
				if ($r) {
					$result = true;
					$param['kp'] = $key;
					$matched[] = $param;
				}
				break;
		}

		if ($result && !$check_all)
			return $matched;
	}

	return $result ? $matched : false;
}

function func_bonus_free_sort($a, $b) {
	global $offer_products_priority;

	# manualy added product from list at special page
	# should have higher priority
	if (!empty($offer_products_priority) && is_array($offer_products_priority)) {
		$pa = array_search($a['productid'], $offer_products_priority);
		$pb = array_search($b['productid'], $offer_products_priority);

		if (!is_int($pb) && is_int($pa)) return -1;
		if (is_int($pb) && !is_int($pa)) return 1;
		if (is_int($pb) && is_int($pa) && $pb != $pa)
			return $pb - $pa;
	}

	# place products from cart first
	if (empty($b['amount']) && !empty($a['amount'])) return -1;
	if (!empty($b['amount']) && empty($a['amount'])) return 1;

	$c = $a['price'] - $b['price'];
	if ($c < 0) return 1; elseif ($c > 0) return -1;

	return 0;
}

function func_bonus_free_matches_sort($a, $b) {
	if ($a['param_type'] == 'P' && $b['param_type'] == 'C') return -1;
	if ($a['param_type'] == 'C' && $b['param_type'] == 'P') return 1;

	if ($a['param_type'] == 'P') return 0;

	$a_cnt = count(explode('/',$a['category_path']));
	$b_cnt = count(explode('/',$b['category_path']));
	return $b_cnt - $a_cnt;
}

#
# This function generates the unique cartid number
#
function func_gen_new_cartid(&$cart, &$products) {
	if (empty($cart["max_cartid"]))
		$cart["max_cartid"] = 0;

	$cartid = $cart["max_cartid"]+1;
	if (is_array($products)) {
		foreach ($products as $product) {
			if ($cartid > $product['cartid'])
				continue;
			$cartid = $product['cartid']+1;
		}
	}

	$cart["max_cartid"] = $cartid;
	return $cartid;
}

#
# Correct price for 'products for free' bonus
#
function func_offer_set_free_products(&$offers, &$products, &$order_bonuses, $in_cart=true) {
	global $special_offers_max_cartid;

	$discount_idx = array();
	$apply_bonus = array();

	#
	# Create index for free products and sort it by price in descent order
	#
	$free_idx = array();
	foreach ($products as $pk=>$product) {

		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if ($product["product_type"] == "C" || isset($product["catalogprice"]) || isset($product["hidden"])) continue;

		$free_idx[$pk] = array(
			'pk' => $pk,
			'productid' => $product['productid'],
			'amount' => $product['amount'],
			'price' => $product['price']
			);
	}
	usort($free_idx, 'func_bonus_free_sort');

	#
	# Part 1: Calculate free amount
	#
	$free_products = array();
	$order_bonuses = array('points'=>0, 'memberships'=>array());
	foreach ($products as $pk=>$product) {

		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if ($product["product_type"] == "C" || isset($product["catalogprice"]) || isset($product["hidden"])) continue;

		$productid = $product['productid'];

		foreach ($offers as $ko=>$offer) {
			foreach ($offer['bonuses'] as $kb=>$bonus) {
				if ($bonus['bonus_type'] != 'N') continue;

				$found_first = false;
				$bonusid = $bonus['bonusid'];
				if (!isset($discount_idx[$productid][$bonusid])) {
					$found = true;
					if (!empty($bonus['params'])) {
						$found = func_bonus_check_product($products, $bonus['params'], $productid, ($bonus['bonus_type']=='N'));
						if (is_array($found)) {
							$found_first = true;
							foreach ($found as $km=>$vm) {
								$found[$km]['kb'] = $kb;
								$found[$km]['ko'] = $ko;
							}
						}
					}

					$discount_idx[$productid][$bonusid] = $found;
				}

				#
				# Check product according applicable bonuses
				#
				$product_checked = (!empty($discount_idx[$productid][$bonusid]));

				if ($product_checked) {
					$matches = $discount_idx[$productid][$bonusid];
					if ($found_first) {
						if (!isset($free_products[$productid])) {
							$free_products[$productid] = array();
						}

						$free_products[$productid] = func_array_merge($free_products[$productid], $matches);
					}
				}
			}
		}
	}

	if (empty($free_products))
		return;

	# Correct index for 'free products'
	foreach ($free_products as $productid=>$matches) {
		usort($matches, 'func_bonus_free_matches_sort');
		$free_products[$productid] = $matches;
	}

	$locked_index = array();
	foreach ($free_idx as $k=>$f) {
		$locked_index[] = $k;
	}
	$locked_index = array_reverse($locked_index);

	#
	# Correct bonuses multiplier
	#
	$products_count = func_offer_count_products($products);

	foreach ($offers as $offer_key=>$offer) {
		$mult = $offer['mult'];
		if ($mult <= 0) $mult = 1;

		if ($mult < 2) {
			# nothing to correct
			$offers[$offer_key]['free_mult'] = $mult;
			continue;
		}

		# first pass: check for maximum allowed multiplier
		foreach ($offer['conditions'] as $condition_key=>$condition) {
			if ($condition['condition_type'] != 'S' || empty($condition['locked_amount'])) {
				continue;
			}
			#...
			$locked_amount = $condition['locked_amount'];

			foreach ($locked_index as $f) {
				$productid = $free_idx[$f]['productid'];

				if (empty($locked_amount['P'][$productid])
				&&  empty($locked_amount['C'])
				&&  empty($locked_amount['R'])) {
					continue;
				}

				if (!isset($free_products[$productid]) || !is_array($free_products[$productid])) {
					continue;
				}

				$matches = $free_products[$productid];
				$free_amount = 0;

				foreach ($matches as $key=>$match) {
					$ko = $match['ko'];
					if ($ko !== $offer_key) continue;

					$kb = $match['kb'];
					$kp = $match['kp'];
					$param = $offers[$ko]['bonuses'][$kb]['params'][$kp];
					if ($param['param_qnty'] < 1) continue;
					$free_amount += $param['param_qnty'];
				}

				if ($free_amount < 1) continue;

				# product lock
				if (!empty($locked_amount['P'][$productid])) {
					$new_mult = floor($products_count[$productid] / ( $free_amount + $locked_amount['P'][$productid] ));
					$mult = min($mult, $new_mult);
				}

				if ($mult < 2) continue;

				# category lock
				foreach (array('C','R') as $l_key) {
					if (empty($locked_amount[$l_key])) continue;

					foreach ($locked_amount[$l_key] as $cid=>$c_amount) {
						if (empty($c_amount['amount']) || empty($c_amount['products'][$productid]))
							continue;

						$new_mult = floor(array_sum($c_amount['products']) / ( $free_amount + $c_amount['amount'] ));

						$mult = min($mult, $new_mult);
						if ($mult < 2) break;
					}
				}
			}
		}

		$offers[$offer_key]['free_mult'] = $mult;
		if ($mult < 2)
			continue;

		$mult_bonuses = array();

		# second pass: increase $locked_amount acording multiplier
		foreach ($offer['conditions'] as $condition_key=>$condition) {
			if ($condition['condition_type'] != 'S' || empty($condition['locked_amount'])) {
				continue;
			}
			$locked_amount = $condition['locked_amount'];

			foreach (array('P','C','R') as $l_key) {
				if (empty($locked_amount[$l_key])) continue;

				foreach ($locked_amount[$l_key] as $cid=>$c_amount) {
					if ($l_key == 'P') {
						if (empty($free_products[$cid]) || !is_array($free_products[$cid])) continue;
						# advance quantity locked by productid
						$locked_amount['P'][$cid] *= $mult;
						$matches = $free_products[$cid];
						foreach ($matches as $key=>$match) {
							$ko = $match['ko'];
							if ($ko !== $offer_key) continue;

							$kb = $match['kb'];
							$kp = $match['kp'];
							# mark bonus params for increase
							$mult_bonuses[$kb][$kp] = true;
						}
						continue;
					}

					if (empty($c_amount['amount']))
						continue;

					# advance quantity locked by caterogoryid
					$locked_amount[$l_key][$cid]['amount'] *= $mult;
					foreach ($c_amount['products'] as $productid) {
						if (empty($free_products[$productid]) || !is_array($free_products[$productid])) continue;
						$matches = $free_products[$productid];
						foreach ($matches as $key=>$match) {
							$ko = $match['ko'];
							if ($ko !== $offer_key) continue;

							$kb = $match['kb'];
							$kp = $match['kp'];
							# mark bonus params for increase
							$mult_bonuses[$kb][$kp] = true;
						}
					}
				}
			}

			$offers[$offer_key]['conditions'][$condition_key]['locked_amount'] = $locked_amount;
		}

		if (empty($mult_bonuses) || !is_array($mult_bonuses)) {
			if (empty($free_products))
				continue;

			# conditional products != bonus products: fill $mult_bonuses from $free_products
			foreach ($free_products as $productid=>$matches) {
				foreach ($matches as $key=>$match) {
					$ko = $match['ko'];
					if ($ko !== $offer_key) continue;

					$kb = $match['kb'];
					$kp = $match['kp'];
					# mark bonus params for increase
					$mult_bonuses[$kb][$kp] = true;
				}
			}
		}

		# correct bonus amounts
		foreach ($mult_bonuses as $kb=>$params) {
			foreach ($params as $kp=>$_void) {
				$offers[$offer_key]['bonuses'][$kb]['params'][$kp]['param_qnty_work'] *= $mult;
			}
		}
	}

	# lock product amounts: conditional products cannot be free
	foreach ($offers as $offer_key=>$offer) {

		foreach ($offer['conditions'] as $condition_key=>$condition) {
			if ($condition['condition_type'] != 'S' || empty($condition['locked_amount'])) {
				continue;
			}

			$locked_amount = $condition['locked_amount'];

			foreach ($locked_index as $f) {
				$productid = $free_idx[$f]['productid'];

				if (empty($locked_amount['P'][$productid])
				&&  empty($locked_amount['C'])
				&&  empty($locked_amount['R'])) {
					continue;
				}

				# Skip if product is not in the free products list
				if (!isset($free_products[$productid]) || !is_array($free_products[$productid])) {
					continue;
				}
				# Skip if product is in free products list and in the locked_amount
				else {
					$_flag = false;
					foreach (array('C','R') as $_key) {
						foreach ($locked_amount[$_key] as $_locked) {
							if (in_array($productid, array_keys($_locked['products'])) && array_sum($_locked['products']) > $_locked['amount']) {
								$_flag = true;
								break;
							}
						}
					}
					if ($_flag) continue;
				}

				$matches = $free_products[$productid];
				foreach ($matches as $key=>$match) {
					$ko = $match['ko'];
					$kb = $match['kb'];
					$kp = $match['kp'];
					$param = $offers[$ko]['bonuses'][$kb]['params'][$kp];
					if ($param['param_qnty_work'] < 1) continue;

					if ($ko != $offer_key) continue;

					# check product index
					if (!empty($locked_amount['P'][$productid])) {
						if ($free_idx[$f]['amount'] >= $locked_amount['P'][$productid]) {
							$free_idx[$f]['amount'] -= $locked_amount['P'][$productid];
							unset($locked_amount['P'][$productid]);
						}
						else {
							$locked_amount['P'][$productid] -= $free_idx[$f]['amount'];
							unset($free_idx[$f]);
						}
					}

					# check category index
					foreach (array('C','R') as $l_key) {
						foreach ($locked_amount[$l_key] as $cid=>$c_amount) {
							if (empty($c_amount['amount']) || empty($c_amount['products'][$productid]))
								continue;

							$delta = 0;
							if ($free_idx[$f]['amount'] >= $c_amount['products'][$productid]) {
								if ($c_amount['products'][$productid] > $c_amount['amount']) {
									$delta = $c_amount['amount'];
								}
								else {
									$delta = $c_amount['products'][$productid];
								}
							}
							else {
								$delta = $free_idx[$f]['amount'];
							}

							if ($delta <= 0) continue;

							$free_idx[$f]['amount'] -= $delta;
							$locked_amount[$l_key][$cid]['amount'] -= $delta;
							$locked_amount[$l_key][$cid]['products'][$productid] -= $delta;

							if ($free_idx[$f]['amount'] <= 0) {
								unset($free_idx[$f]);
							}

							if ($locked_amount[$l_key][$cid]['products'][$productid] <=0)
								unset($locked_amount[$l_key][$cid]['products'][$productid]);

							if ($locked_amount[$l_key][$cid]['amount'] <= 0)
								unset($locked_amount[$l_key][$cid]);
						}
					}
				}
			}
		}
	}

	#
	# Part 2: Apply 'products for free' bonus
	#
	foreach ($free_idx as $f) {
		$productid = $f['productid'];
		$free_amount = 0;
		if (!empty($free_products[$productid])) {
			# find bonus param and calculate free amount of product
			$product_amount = $f['amount'];
			$matches = $free_products[$productid];
			foreach ($matches as $key=>$match) {
				$ko = $match['ko'];
				$kb = $match['kb'];
				$kp = $match['kp'];
				$param = $offers[$ko]['bonuses'][$kb]['params'][$kp];
				if ($param['param_qnty_work'] < 1)
					continue;

				if (!$in_cart) {
					$products[$f['pk']]['use_special_price'] = true;
					$products[$f['pk']]['special_price'] = 0.00;
				}

				if ($product_amount - $free_amount <= $param['param_qnty_work']) {
					$amount = $product_amount - $free_amount;
				}
				else {
					$amount = $param['param_qnty_work'];
				}

				$offers[$ko]['bonuses'][$kb]['params'][$kp]['param_qnty_work'] -= $amount;
				$free_amount += $amount;

				if ($free_products[$productid][$key]['param_qnty_work'] < 1) {
					unset($free_products[$productid][$key]);
				}

				if ($product_amount == $free_amount) break;
			}
		}

		if ($free_amount < 1) {
			$products[$f['pk']]['free_amount'] = 0;
			continue;
		}

		# split cart items
		if ($products[$f['pk']]['amount'] > $free_amount) {
			$new_item = $products[$f['pk']];
			$products[$f['pk']]['amount'] -= $free_amount;
			$products[$f['pk']]['free_amount'] = 0;
			$new_item['amount'] = $free_amount;
			$new_item['free_amount'] = $free_amount;
			$new_item['price'] = 0.00;
			$new_item['cartid'] = ++$special_offers_max_cartid;

			$products[] = $new_item;
		}
		else {
			$products[$f['pk']]['free_amount'] = $products[$f['pk']]['amount'];
			$products[$f['pk']]['price'] = 0.00;
		}
	}
}

#
# Returns total discount after applying bonuses
#
function func_offer_apply_discounts(&$offers, &$products, &$order_bonuses) {
	global $config;

	$discount_idx = array();
	$apply_bonus = array();

	#
	# Part 1: Calculate bonuses
	#
	$order_bonuses = array('points'=>0, 'memberships'=>array());
	foreach ($products as $pk=>$product) {

		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		$productid = $product['productid'];
		$apply = array (
			'discount' => 0.00,
			'free_shipping' => false
		);

		foreach ($offers as $ko=>$offer) {
			foreach ($offer['bonuses'] as $kb=>$bonus) {
				$found_first = false;
				$bonusid = $bonus['bonusid'];
				if (!isset($discount_idx[$productid][$bonusid])) {
					$found = true;
					if (!empty($bonus['params'])) {
						$found = func_bonus_check_product($products, $bonus['params'], $productid, ($bonus['bonus_type']=='N'));
						if (is_array($found)) {
							$found_first = true;
							foreach ($found as $km=>$vm) {
								$found[$km]['kb'] = $kb;
								$found[$km]['ko'] = $ko;
							}
						}
					}

					$discount_idx[$productid][$bonusid] = $found;
				}

				#
				# Check product according applicable bonuses
				#
				$product_checked = (!empty($discount_idx[$productid][$bonusid]));

				switch ($bonus['bonus_type']) {
				case 'M': # memberships
					$memberships = func_array_merge_assoc($order_bonuses['memberships'], $bonus['memberships_arr']);
					$order_bonuses['memberships'] = $memberships;
					break;

				case 'S': # free shipping
					if ($product_checked) {
						$apply['free_shipping'] = true;
					}
					break;

				case 'D': # discount
					if ($product_checked) {
						if ($bonus['amount_type'] == '%') {
							$discount = $product['price'] * $bonus['amount_min'] / 100.00;
							$limit = price_format($bonus['amount_max']);
						}
						else {
							$discount = price_format($bonus['amount_min']);
							$limit = price_format($product['price'] * $bonus['amount_max'] / 100.00);
						}
						if ($discount > $limit && $limit !== '0.00') {
							$discount = $limit;
						}
						if ($discount > $product['price']) {
							$discount = $product['price'];
						}

						$apply['discount'] = max($apply['discount'], $discount);
					}
					break;
				}
			}
		}
		$apply_bonus[$pk] = $apply;
	}

/*
	if (!empty($order_bonuses['memberships'])) {
		$memberships = array_unique($order_bonuses['memberships']);
		sort($memberships);
		$order_bonuses['memberships'] = $memberships;
	}
*/

	#
	# Part 2: Apply bonuses
	#
	$discount = 0.00;

	# Apply discount bonus
	foreach ($products as $pk=>$product) {

		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if (!empty($apply_bonus[$pk])) {
			$bonus = $apply_bonus[$pk];

			if ($bonus['discount'] > 0.00) {
				if ($config["Taxes"]["display_taxed_order_totals"] =="Y")
					$products[$pk]['saved_original_price'] = $products[$pk]['taxed_price'];
				else
					$products[$pk]['saved_original_price'] = $products[$pk]['price'];
				$products[$pk]['price'] -= $bonus['discount'];
				$products[$pk]['special_price_used'] = true;
			}

			if ($bonus['free_shipping']) {
				$products[$pk]['free_shipping'] = 'Y';
				$products[$pk]['free_shipping_used'] = true;
			}
		}
	}

	# Create array of not used 'free products' and collect bonus points
	foreach ($offers as $k=>$offer) {
		$not_used_free_products = array();
		if (empty($offer['bonuses']))
			continue;

		$mult = 1;
		if (!empty($offer['mult']))
			$mult = $offer['mult'];

		foreach ($offer['bonuses'] as $bonus) {
			if ($bonus['bonus_type'] == 'B') {	# apply bonus points
				if ($bonus['amount_type'] == 'S') {
					# points per subtotal
					$subtotal = func_offer_get_discounted_subtotal($products);
					if ($bonus['amount_max'] > 0)
						$tmp_amount = floor($subtotal / $bonus['amount_max']) * $bonus['amount_min'];
					else 
						$tmp_amount = 0;
				}
				else {
					# fixed amount
					$tmp_amount = $bonus['amount_min'] * $mult;
				}

				$order_bonuses['points'] += $tmp_amount;

				continue;
			}
			# discount
			if ($bonus['bonus_type'] == 'D' && empty($bonus['params'])) {
				$not_used_free_products['F'][] = $offer['provider'];
				$not_used_free_products['DISCOUNT_PROV_'.$offer['provider']][] = $bonus['bonusid'];
				continue;
			}

			if (($bonus['bonus_type'] != 'N' && $bonus['bonus_type'] != 'D') || empty($bonus['params']))
				continue;

			foreach ($bonus['params'] as $param) {
				if ($bonus['bonus_type'] == 'D')
					$param['param_qnty_work'] = 1;

				if (($param['param_type'] != 'P' && $param['param_type'] != 'C') || $param['param_qnty_work'] < 1)
					continue;

				if ($param['param_type'] == 'P') {
					$key = 'P';
				}
				else if ($param['param_type'] == 'C') {
					if ($param['param_arg'] == 'Y')
						$key = 'R';
					else
						$key = 'C';
				}

				$id = $param['param_id'];

				if (empty($not_used_free_products[$key][$id]))
					$not_used_free_products[$key][$id] = 0;

				$not_used_free_products[$key][$id] += $param['param_qnty_work'];
				$not_used_free_products["DISCOUNT_GEN_$key"][] = $bonus['bonusid'];
			}
		}

		if (!empty($not_used_free_products))
			$offers[$k]['not_used_free_products'] = $not_used_free_products;
	}

	return $discount;
}

#
# Get offers applicable to the categoryid(s)
#
function func_get_offers_categoryid($categoryid) {
	if (empty($categoryid)) return false;

	if (is_array($categoryid)) $list = $categoryid;
	else $list = array ($categoryid);

	$result = func_get_offers_categoryid_sub($list, 'C');
	if (!is_array($result)) $result = array();

	$bonuses = func_get_offers_categoryid_sub($list, 'B');
	if (is_array($bonuses)) {
		$result = func_array_merge($result, $bonuses);
	}

	$result = array_unique($result);

	return empty($result) ? false : $result;
}

function func_get_offers_categoryid_sub($list, $tbl_prefix) {
	global $sql_tbl;

	if ($tbl_prefix == 'B') {
		$items_tbl = $sql_tbl['offer_bonuses'];
		$item_params_tbl = $sql_tbl['offer_bonus_params'];
		$items_tbl_link = "$items_tbl.bonusid=$item_params_tbl.bonusid";
	}
	else {
		$items_tbl = $sql_tbl['offer_conditions'];
		$item_params_tbl = $sql_tbl['offer_condition_params'];
		$items_tbl_link = "$items_tbl.conditionid=$item_params_tbl.conditionid";
	}

	$list_str = implode(",", $list);

	if (count($list) > 1) $id_reg = '('.implode('|',$list).')';
	else $id_reg = array_pop($list);

	$query = "SELECT DISTINCT $sql_tbl[offers].offerid
				FROM
					$sql_tbl[offers],
					$items_tbl,
					$item_params_tbl,
					$sql_tbl[categories]
				WHERE
					$sql_tbl[offers].offerid=$items_tbl.offerid AND
					$items_tbl.avail = 'Y' AND
					$items_tbl_link AND
					$item_params_tbl.param_type='C' AND (
						$item_params_tbl.param_id IN ($list_str) AND
						$item_params_tbl.param_id=$sql_tbl[categories].categoryid OR
						$item_params_tbl.param_arg='Y' AND
						$sql_tbl[categories].categoryid_path REGEXP CONCAT('(^|/)',$item_params_tbl.param_id,'(/.+/|/)$id_reg($|/)')
					)";

	$offers = func_get_column("offerid", $query);

	return $offers;
}

#
# Get all offers matching product
#
# Arguments:
#	array of productid's
#	optional array of categoryid's
#
# Return:
#	associative array of offers.
#
function func_get_offers_productid(&$list, $categories=false, $full=false) {
	if (empty($list) && (!is_array($categories) || empty($categories)))
		return false;

	$result = func_get_offers_productid_sub($list, $categories, $full, 'C');
	if (!is_array($result)) $result = array();

	$bonuses = func_get_offers_productid_sub($list, $categories, $full, 'B');
	if (is_array($bonuses)) {
		foreach ($bonuses as $k=>$v) {
			if (isset($result[$k]))
				$result[$k] = func_array_merge($result[$k], $v);
			else
				$result[$k] = $v;
		}
	}

	foreach ($result as $k=>$v) {
		$result[$k] = array_unique($v);
	}

	return empty($result) ? false : $result;
}

function func_get_offers_productid_sub(&$list, $categories, $full, $tbl_prefix) {
	global $sql_tbl;

	if ($tbl_prefix == 'B') {
		$items_tbl = $sql_tbl['offer_bonuses'];
		$item_params_tbl = $sql_tbl['offer_bonus_params'];
		$items_tbl_link = "$items_tbl.bonusid=$item_params_tbl.bonusid";
	}
	else {
		$items_tbl = $sql_tbl['offer_conditions'];
		$item_params_tbl = $sql_tbl['offer_condition_params'];
		$items_tbl_link = "$items_tbl.conditionid=$item_params_tbl.conditionid";
	}

	$tables = array();
	$tables[] = $sql_tbl['offers'];
	$tables[] = $items_tbl;
	$tables[] = $item_params_tbl;
	$search = array();

	if (!empty($list)) {
		$search[] = "$item_params_tbl.param_id IN ('".implode("','", $list)."')";
	}

	if (!empty($categories)) {
		$tables[] = $sql_tbl['products_categories'];
		$tables[] = $sql_tbl['categories'];
		$like = array();
		foreach ($categories as $id) {
			$like[] = "$sql_tbl[categories].categoryid_path LIKE '%/$id/%'";
			$like[] = "$sql_tbl[categories].categoryid_path LIKE '%/$id'";
			$like[] = "$sql_tbl[categories].categoryid_path LIKE '$id/%'";
		}
		$like_str = implode(' OR ', $like);

		$categories_str = implode(',', $categories);

		$search[] = "
			$item_params_tbl.param_id=$sql_tbl[products_categories].productid AND
			$sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND
			(
				$sql_tbl[products_categories].categoryid IN ($categories_str)
				OR
				$like_str
			)";
	}

	$search_str = implode(' AND ', $search);

	$tables_str = implode(',', $tables);

	$select_columns = "$item_params_tbl.param_id AS productid, $sql_tbl[offers].offerid";

	$query = "SELECT $select_columns
				FROM $tables_str
				WHERE
					$sql_tbl[offers].offerid=$items_tbl.offerid AND
					$items_tbl.avail = 'Y' AND
					$items_tbl_link AND
					$item_params_tbl.param_type='P' AND
					$search_str
					GROUP BY productid, offerid";

	$result = array();
	if ($p_result = db_query($query)) {
		while ($row = db_fetch_array($p_result)) {
			if (!isset($result[$row['productid']]))
				$result[$row['productid']] = array();
			$result[$row['productid']][] = $row['offerid'];
		}
		db_free_result($p_result);

	}

	return empty($result) ? false : $result;
}

#
# Get offers applicable to the categoryid(s) and all it products
#
function func_get_category_offers($login, $usertype, $categoryid, $full=false) {
	if (empty($categoryid)) return false;

	$customer_info = func_default_userinfo($login, $usertype);

	$result = array();
	if (!is_array($categoryid)) $categoryid = array($categoryid);

	$offers = func_get_offers_categoryid($categoryid);
	if (is_array($offers)) $result = func_array_merge($result, $offers);

	$products = false;
	$product_offers = func_get_offers_productid($products, $categoryid);

	if (is_array($product_offers)) {
		foreach ($product_offers as $offers) {
			$result = func_array_merge($result, $offers);
		}
	}

	if (!empty($result)) {
		$null_products = false;
		$avail_offers = func_get_applicable_offers($null_products, $customer_info, "", "MZ", $result);

		if ($full)
			$result = $avail_offers;
		elseif (is_array($avail_offers)) {
			$result = array();
			foreach ($avail_offers as $offer) {
				$result[] = $offer['offerid'];
			}
		}
		else $result = array();
	}

	return empty($result) ? false : $result;
}

#
# Get offers applicable to the productid(s)
#
function func_get_product_offers($login, $usertype, &$products, $full=false) {
	global $sql_tbl, $active_modules, $single_mode;

	if (empty($products)) return false;

	$customer_info = func_default_userinfo($login, $usertype);

	$list = array();
	if ($products !== false) {
		if (!is_array($products)) {
			$list[] = $products;
		}
		else {
			$v = array_values($products);
			if (!is_array($v[0])) {
				$list = $products;
			}
			else {
				foreach ($products as $v) {
					if (!empty($v['productid'])) $list[] = $v['productid'];
				}
			}
		}
	}

	$offers = func_get_offers_productid($list);
	if (empty($offers))
		$offers = array();

	$cat_idx = func_query_hash("SELECT categoryid, productid FROM $sql_tbl[products_categories] WHERE productid IN ('".implode("','",$list)."')", "categoryid", true, true);
	if (empty($cat_idx) || !is_array($cat_idx))
		return false;

	foreach ($cat_idx as $pids) {
		foreach($pids as $pid) {
			if (!isset($offers[$pid]))
				$offers[$pid] = array();
		}
	}

	# add information about offers of product categories
	foreach ($cat_idx as $categoryid => $pids) {
		$cat_offers = func_get_offers_categoryid($categoryid);
		if (!is_array($cat_offers))
			continue;

		if (empty($active_modules['Simple_Mode']) && !$single_mode)
			$products2offers = func_query_hash("SELECT $sql_tbl[offers].offerid, $sql_tbl[products].productid FROM $sql_tbl[offers], $sql_tbl[products] WHERE $sql_tbl[offers].offerid IN ('".implode("','", $cat_offers)."') AND $sql_tbl[products].productid IN ('".implode("','", array_keys($offers))."') AND $sql_tbl[offers].provider = $sql_tbl[products].provider GROUP BY $sql_tbl[offers].offerid", "productid", true, true);

		foreach ($offers as $productid => $product_offers) {
			if (!in_array($productid, $pids))
				continue;

			if (empty($active_modules['Simple_Mode']) && !$single_mode) {
				if (!isset($products2offers[$productid]) || empty($products2offers[$productid]))
					continue;

				$cat_offers = $products2offers[$productid];
			}

			$offers[$productid] = func_array_merge($product_offers, $cat_offers);
		}
	}

	if (empty($offers))
		return false;

	# validate offers
	$all_offers = array();
	foreach ($offers as $k => $v) {
		if (is_array($v)) {
			foreach ($v as $id) {
				$all_offers[$id] = false;
			}
		}
	}

	$result = array();
	if (!empty($all_offers)) {
		$null_products = false;
		$avail_offers = func_get_applicable_offers($null_products, $customer_info, "", "MZ", array_keys($all_offers));
		if (is_array($avail_offers)) {
			foreach ($avail_offers as $v) {
				$all_offers[$v['offerid']] = $v;
			}

			foreach ($offers as $k=>$v) {
				$validated = array();
				$v = array_unique($v);
				foreach ($v as $id) {
					if (!empty($all_offers[$id])) {
						if ($full)
							$validated[] = $all_offers[$id];
						else
							$validated[] = $id;
					}
				}
				$result[$k] = $validated;
			}
		}
	}

	return empty($result) ? false : $result;
}

#
# Check products for offers
#
function func_offers_check_products($login, $usertype, &$products) {
	global $config;

	if (!is_array($products)) return;

	$product_offers = func_get_product_offers($login, $usertype, $products);
	if (!is_array($product_offers)) return;

	if (empty($config['special_offers_mark_products']))
		return;

	foreach ($products as $k=>$v) {
		if (empty($product_offers[$v['productid']])) continue;

		$products[$k]['have_offers'] = true;
	}
}

#
# Get customer bonuses
#
function func_get_customer_bonus($login) {
	global $sql_tbl, $shop_language;

	$bonus = func_query_first("SELECT * FROM $sql_tbl[customer_bonuses] WHERE login='$login'");
	if (!is_array($bonus))
		return false;

	$memberships = func_get_memberships("C", true);
	$keys = explode("|", $bonus['memberships']);
	if (!empty($memberships)) {
		foreach ($memberships as $k => $v) {
			if(!in_array($k, $keys))
				unset($memberships[$k]);
		}
	}
	$bonus['memberships'] = (empty($memberships) ? array() : $memberships);

	return $bonus;
}

#
# Update customer bonuses
#
function func_update_customer_bonus($login, $bonus) {
	static $bonus_keys = array('points', 'memberships');
	global $sql_tbl;

	if (!is_array($bonus)) {
		db_query("DELETE FROM $sql_tbl[customer_bonuses] WHERE login='$login'");
		return;
	}

	foreach ($bonus as $k=>$v) {
		if (!in_array($k, $bonus_keys)) {
			unset ($bonus[$k]);
			continue;
		}

		if ($k == 'memberships') {
			$bonus[$k] = ((!empty($v) && is_array($v)) ? implode("|", array_keys($v)) : "");
		}
	}

	if (!empty($bonus)) {
		if (!isset($bonus['memberships']))
			$bonus['memberships'] = '';
		$bonus['login'] = $login;
		func_array2insert('customer_bonuses', $bonus, true);
	}
}

#
# Generate sorted list of offers for 'category', 'product' and
# 'random' pages
#
function func_get_sorted_offers($offerid_list) {
	global $sql_tbl;
	global $config;

	if (!is_array($offerid_list)) return false;

	$limit = '';
	if (!empty($config['Special_Offers']['offers_list_limit']))
		$limit = ' LIMIT '.(int)$config['Special_Offers']['offers_list_limit'];

	$tmp = func_get_column('offerid', "SELECT offerid FROM $sql_tbl[offers] WHERE offerid IN (".join(',',$offerid_list).") AND show_short_promo='Y' ORDER BY modified_time DESC $limit");

	if ($tmp === false || !is_array($tmp)) return false;

	$result = array();
	foreach($tmp as $offerid) {
		$offer = func_get_offer($offerid, true);
		$empty_offer = empty($offer['promo_short']) && !$offer['promo_short_img'];
		if (empty($result) || !$empty_offer) {
			$result[] = $offer;
		}
	}

	return empty($result) ? false : $result;
}

function func_offer_merge_free_products($orig, $new) {
	if (empty($new)) return $orig;

	foreach ($new as $key=>$values) {
		if (!isset($orig[$key])) {
			$orig[$key] = $values;
			continue;
		}

		if ($key == 'F' || $key[0]=='D') { # array of provider names ('discount' bonus)
			$orig[$key] = func_array_merge($orig[$key], $values);
			continue;
		}

		foreach ($values as $id=>$qnty) {
			if (!isset($orig[$key][$id])) {
				$orig[$key][$id] = $qnty;
				continue;
			}
			$orig[$key][$id] += $qnty;
		}
	}

	return $orig;
}

function func_offers_search_apply_special_price(&$product, $login) {
	if ($product['x_special_price'] < 0.00)
		$product['x_special_price'] = 0.00;

	$orig_product = $product;
	$taxes = func_get_product_tax_rates($orig_product, $login);

	if ($product['x_special_price'] != $product['price']) {
		$orig_product['price'] = $orig_product['x_special_price'];
		func_get_product_taxes($orig_product, $login, false, $taxes);
		$product['use_special_price'] = true;
		$product['special_price'] = $orig_product['taxed_price'];
	}

	func_get_product_taxes($product, $login, false, $taxes);
	$product["taxes"] = $taxes;
}

function func_offer_correct_cartid(&$return, $__key, &$cart, $single_mode) {
	$max_cartid = 1;
	if (!empty($cart['max_cartid']) && $cart['max_cartid'] > $max_cartid) $max_cartid = $cart['max_cartid'];
	if (!empty($return['max_cartid']) && $return['max_cartid'] > $max_cartid) $max_cartid = $return['max_cartid'];

	$return['max_cartid'] = $max_cartid;

	if (!is_array($return["orders"][$__key]['products']) || empty($return["orders"][$__key]['products'])) {
		return;
	}

	foreach ($return["orders"][$__key]['products'] as $k=>$v) {
		if (empty($v['cartid'])) {
			# should not occurs
			continue;
		}
		if ($v['cartid'] > $max_cartid) {
			$max_cartid = $v['cartid'];
		}
	}

	$return['max_cartid'] = $max_cartid;
}

?>
