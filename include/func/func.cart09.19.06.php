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
# $Id: func.cart.php,v 1.31.2.13 2006/08/17 07:29:27 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files','user','taxes');

#
# Get the customer's zone
#
function func_get_customer_zone_ship ($username, $provider, $type) {
	global $sql_tbl;
	global $single_mode;

	$zones = func_get_customer_zones_avail($username, $provider, "S");
	$zone = 0; # default zone
	if (is_array($zones)) {
		$provider_condition = ($single_mode) ? "" : " AND provider='".addslashes($provider)."'";
		$tmp = func_query("SELECT zoneid FROM $sql_tbl[shipping_rates] WHERE zoneid IN ('".implode("','",array_keys($zones))."') $provider_condition AND type='$type' GROUP BY zoneid");
		if (is_array($tmp)) {
			$unused = $zones;
			# remove not available zones
			foreach($tmp as $v) unset($unused[$v["zoneid"]]);
			foreach($unused as $k=>$v) unset($zones[$k]);

			reset($zones);
			$zone = key($zones); #extract first zone
		}
	}

	return $zone;
}

#
# Get the customer's zones
#
function func_get_customer_zones_avail ($username, $provider, $address_type="S") {
	global $sql_tbl, $config, $single_mode;
	static $z_flags = array (
		"C" => 0x01,
		"S" => 0x02,
		"G" => 0x04,
		"T" => 0x08,
		"Z" => 0x10,
		"A" => 0x20);
	static $zone_element_types = array (
		"S" => "state",
		"G" => "county",
		"T" => "city",
		"Z" => "zipcode",
		"A" => "address");
	static $results_cache = array();

	if ($config["General"]["use_counties"] != "Y") {
		unset($z_flags["G"]);
		unset($zone_element_types["G"]);
	}

	# Define which address type should be compared
	if ($address_type == "B")
		$address_prefix = "b_";
	else
		$address_prefix = "s_";

	$zones = array();

	if (is_array($username)) {
		$customer_info = $username;
	}
	elseif (!empty($username)) {
		$customer_info = func_userinfo($username, "C");
	}
	elseif ($config["General"]["apply_default_country"] == "Y") {
		# Set the default user address
		$customer_info[$address_prefix."country"] = $config["General"]["default_country"];
		$customer_info[$address_prefix."state"] = $config["General"]["default_state"];
		$customer_info[$address_prefix."county"] = $config["General"]["default_county"];
		$customer_info[$address_prefix."zipcode"] = $config["General"]["default_zipcode"];
		$customer_info[$address_prefix."city"] = $config["General"]["default_city"];
	}

	$customer_login = "";
	if (!empty($customer_info)) {
		$customer_login = $customer_info["login"];

		#
		# Check local zones cache
		#
		if (isset($results_cache[$customer_login][$provider][$address_type]))
			return $results_cache[$customer_login][$provider][$address_type];

		#
		# Generate the zones list
		#
		$provider_condition = ($single_mode ? "" : "AND provider='$provider'");

		# Possible zones for customer's country...
		$possible_zones = func_query("SELECT $sql_tbl[zone_element].zoneid FROM $sql_tbl[zone_element], $sql_tbl[zones] WHERE $sql_tbl[zone_element].zoneid=$sql_tbl[zones].zoneid AND $sql_tbl[zone_element].field='".$customer_info[$address_prefix."country"]."'  AND $sql_tbl[zone_element].field_type='C' $provider_condition GROUP BY $sql_tbl[zone_element].zoneid");

		if (is_array($possible_zones)) {

			$zones_completion = array();
			$_possible_zones = array();
			foreach ($possible_zones as $pzone) {
				$_possible_zones[$pzone["zoneid"]] = func_query_column("SELECT field_type FROM $sql_tbl[zone_element] WHERE zoneid='$pzone[zoneid]' AND field<>'%' GROUP BY zoneid, field_type");
			}

			foreach ($_possible_zones as $_pzoneid=>$_elements) {
				if (is_array($_elements)) {
					foreach ($_elements as $k=>$v) {
						$zones_completion[$_pzoneid] += $z_flags[$v];
					}
				}
			}

			$cs_state = $customer_info[$address_prefix."state"];
			$cs_country = $customer_info[$address_prefix."country"];
			$cs_pair = $cs_country."_".$cs_state;

			$empty_condition = " AND $sql_tbl[zone_element].field<>'%'";

			foreach ($possible_zones as $pzone) {
				$zones[$pzone["zoneid"]] = $z_flags["C"];

				# If only country is defined for this zone, skip further actions
				if ($zones_completion[$pzone["zoneid"]] == $z_flags["C"])
					continue;

				foreach ($z_flags as $field_type=>$field_type_flag) {

					if ($field_type == "C")
						continue;

					if ($zones_completion[$pzone["zoneid"]] & $field_type_flag) {
						# Checking the field for  equal...

						if ($field_type == "S") {
							# Checking the state...
							$found_zones = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zone_element], $sql_tbl[states] WHERE $sql_tbl[zone_element].field='".addslashes($cs_pair)."' AND $sql_tbl[zone_element].field_type='S' AND $sql_tbl[states].code='".addslashes($cs_state)."' AND $sql_tbl[states].country_code='".addslashes($cs_country)."' AND $sql_tbl[zone_element].zoneid='$pzone[zoneid]'");
						} elseif ($field_type == "G") {
							# Checking the county...
							$found_zones = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zone_element] WHERE field_type='G' AND field='".$customer_info[$address_prefix."county"]."' AND zoneid='$pzone[zoneid]'");
						}
						else {
							# Checking the rest fields (city, zipcode, address)
							$found_zones = func_query_first_cell("SELECT $sql_tbl[zone_element].zoneid FROM $sql_tbl[zone_element], $sql_tbl[zones] WHERE $sql_tbl[zone_element].zoneid=$sql_tbl[zones].zoneid AND $sql_tbl[zone_element].field_type='$field_type' AND '".addslashes($customer_info[$address_prefix.$zone_element_types[$field_type]])."' LIKE $sql_tbl[zone_element].field  AND $sql_tbl[zone_element].zoneid='$pzone[zoneid]' $empty_condition $provider_condition");
						}

						if (!empty($found_zones)) {
							# Field is found: increase the priority
							$zones[$pzone["zoneid"]] += $field_type_flag;
						}
						else {
							# Remove zone from available zones list
							unset($zones[$pzone["zoneid"]]);
							continue;
						}
					}
				} # /foreach ($z_flags)
			} # /foreach ($possible_zones)
		}
	}

	$zones[0] = 0;
	arsort($zones, SORT_NUMERIC);

	if (!empty($customer_login)) {
		$results_cache[$customer_login][$provider][$address_type] = $zones;
	}

	return $zones;
}

function func_get_products_providers ($products) {
	if (empty($products) || !is_array($products))
		return array();

	$providers = array ();
	foreach ($products as $product)
		$providers[$product["provider"]] = 1;

	return array_keys($providers);
}

#
# Will return array of products with preserved indexes
#
function func_get_products_by_provider ($products, $provider) {
	global $single_mode;

	if (!is_array($products) || empty($products))
		return array();

	if ($single_mode) return $products;

	$result = array ();
	foreach ($products as $k=>$product) {
		if ($product["provider"] == $provider)
			$result[$k] = $product;
	}

	return $result;
}

#
# This function do real shipping calcs
#
function func_real_shipping($delivery) {
	global $intershipper_rates, $sql_tbl;

	$shipping_codes = func_query_first("select code, subcode from $sql_tbl[shipping] where shippingid='$delivery'");

	if (!empty($intershipper_rates) && is_array($intershipper_rates)) {
		foreach($intershipper_rates as $rate) {
			if ($rate["methodid"]==$shipping_codes["subcode"])
				return $rate["rate"];
		}
	}

	return "0.00";
}

#
# This function calculates costs of contents of shopping cart
#
function func_calculate($cart, $products, $login, $login_type, $paymentid=NULL) {
	global $config, $single_mode, $sql_tbl;
	global $xcart_dir, $active_modules;

	$return = array ();
	$return ["orders"] = array ();

	if ($active_modules["Special_Offers"]) {
		include $xcart_dir."/modules/Special_Offers/calculate_init.php";
	}

	if ($single_mode) {
		$result = func_calculate_single ($cart, $products, $login, $login_type);
		$return = $result;
		$return ["orders"][0] = $result;
		$return ["orders"][0]["provider"] = (!empty($products) ? $products[0]["provider"] : "");
		if ($active_modules["Special_Offers"]) {
			include $xcart_dir."/modules/Special_Offers/calculate_return.php";
		}
	}
	else {
		$products_providers = func_get_products_providers ($products);

		$key = 0;

		foreach ($products_providers as $provider_for) {
			$_products = func_get_products_by_provider ($products, $provider_for);
			$result = func_calculate_single ($cart, $_products, $login, $login_type, $provider_for);

			$return ["total_cost"] += $result ["total_cost"];
			$return ["shipping_cost"] += $result ["shipping_cost"];
			$return ["display_shipping_cost"] += $result ["display_shipping_cost"];
			$return ["tax_cost"] += $result ["tax_cost"];
			$return ["discount"] += $result ["discount"];
			if ($result["coupon"]) {
				$return ["coupon"] = $result ["coupon"];
			}
			$return ["coupon_discount"] += $result ["coupon_discount"];
			$return ["subtotal"] += $result ["subtotal"];
			$return ["display_subtotal"] += $result ["display_subtotal"];
			$return ["discounted_subtotal"] += $result ["discounted_subtotal"];
			$return ["display_discounted_subtotal"] += $result ["display_discounted_subtotal"];
			$return ["products"] = func_array_merge($return ["products"], $result ["products"]);

			if (empty($return["taxes"])) {
				$return["taxes"] = $result["taxes"];
			}
			elseif (is_array($result["taxes"])) {
				foreach ($result["taxes"] as $k=>$v) {
					if (in_array($k, array_keys($return["taxes"])))
						$return["taxes"][$k]["tax_cost"] += $v["tax_cost"];
					else
						$return["taxes"][$k] = $v;
				}
			}

			$return ["orders"][$key] = $result;
			$return ["orders"][$key]["provider"] = $provider_for;

			if ($active_modules["Special_Offers"]) {
				include $xcart_dir."/modules/Special_Offers/calculate_return.php";
			}

			$key ++;
		}

		if (!empty($cart["giftcerts"])) {
			$_products = array ();
			$result = func_calculate_single ($cart, $_products, $login, $login_type);
			$return ["total_cost"] += $result ["total_cost"];
			$return ["shipping_cost"] += $result ["shipping_cost"];
			$return ["display_shipping_cost"] += $result ["display_shipping_cost"];
			$return ["tax_cost"] += $result ["tax_cost"];
			$return ["discount"] += $result ["discount"];
			$return ["subtotal"] += $result ["subtotal"];
			$return ["display_subtotal"] += $result ["display_subtotal"];
			$return ["discounted_subtotal"] += $result ["discounted_subtotal"];
			$return ["display_discounted_subtotal"] += $result ["display_discounted_subtotal"];
			$return ["coupon_discount"] += $result ["coupon_discount"];

			$return ["orders"][$key] = $result;
			$return ["orders"][$key]["provider"] = ""; #$provider_for;
			$key++;
		}
	}

	$_payment_surcharge = 0;
	if ($paymentid !== NULL) {
		#
		# Apply the payment method surcharge or discount
		#
		$_payment_surcharge = func_payment_method_surcharge($return["total_cost"], $paymentid);

		if ($_payment_surcharge != 0) {
			$_payment_surcharge = price_format($_payment_surcharge);
			$return["total_cost"] += $_payment_surcharge;
			$return["payment_surcharge"] = $_payment_surcharge;
			$return["paymentid"] = $paymentid;

			if (!$single_mode) {
				# Distribute the payment method surcharge or discount among orders
				$_payment_surcharge_part = price_format($_payment_surcharge / count($return["orders"]));
				for ($i=0; $i<count($return["orders"])-1; $i++) {
					$return["orders"][$i]["total_cost"] += $_payment_surcharge_part;
					$return["orders"][$i]["payment_surcharge"] = $_payment_surcharge_part;
				}

				$_payment_surcharge_rest = price_format($_payment_surcharge - ($_payment_surcharge_part * (count($return["orders"])-1)));
				$return["orders"][count($return["orders"])-1]["total_cost"] += $_payment_surcharge_rest;
				$return["orders"][count($return["orders"])-1]["payment_surcharge"] = $_payment_surcharge_rest;
			}
		}
		else {
			$return["payment_surcharge"] = 0;
			if (!$single_mode) {
				for ($i=0; $i<count($return["orders"]); $i++)
					$return["orders"][$i]["payment_surcharge"] = 0;
			}
		}
	}

	$return["display_cart_products_tax_rates"] = "N";
	$return["product_tax_name"] = "";
	if ($config["Taxes"]["display_cart_products_tax_rates"] == "Y") {
		$_taxes = array();
		foreach ($return["orders"] as $k=>$v) {
			if (is_array($v["products"])) {
				foreach ($v["products"] as $i=>$j) {
					if (!is_array(@$j["taxes"]))
						continue;

					foreach ($j["taxes"] as $_tn=>$_tax) {
						if ($_tax["tax_value"] == 0)
							continue;
						
						if (!isset($_taxes[$_tn]))
							$_taxes[] = $_tax["tax_display_name"];
					}
				}
			}
		}
		
		if (count($_taxes) > 0) {
			$return["display_cart_products_tax_rates"] = "Y";
			if (count($_taxes) == 1)
				$return["product_tax_name"] = $_taxes[0];
		}
	}

	#
	# Recalculating applied gift certificates
	#
	$giftcert_cost = 0;
	$applied_giftcerts = array();
	if (!empty($cart["applied_giftcerts"])) {
		$gc_payed_sum = 0;
		$applied_giftcerts = array();
		foreach ($cart["applied_giftcerts"] as $k=>$v) {
			if (($gc_payed_sum + $v["giftcert_cost"]) <= $return["total_cost"]) {
				$gc_payed_sum += $v["giftcert_cost"];
				$applied_giftcerts[] = $v;
				continue;
			}

			db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE gcid='$v[giftcert_id]'");
		}

		$giftcert_cost = $gc_payed_sum;
	}

	if ($return["total_cost"] >= $giftcert_cost)
		$return["giftcert_discount"] = $giftcert_cost;
	else
		$return["giftcert_discount"] = $giftcert_cost - $return["total_cost"];

	$return["total_cost"] = price_format($return["total_cost"] - $return["giftcert_discount"]);
	$return["applied_giftcerts"] = $applied_giftcerts;

	if ($single_mode) {
		$return ["orders"][0]["total_cost"] = $return["total_cost"];
	}
	elseif (is_array($applied_giftcerts)) {
		#
		# Apply GC to all orders in cart in single_mode Off
		#
		foreach ($return["orders"] as $k=>$order) {
			$giftcert_discount = 0;
			foreach ($applied_giftcerts as $k1=>$applied_giftcert) {
				if ($applied_giftcert["giftcert_cost"] == 0)
					continue;

				if ($applied_giftcert["giftcert_cost"] > $order["total_cost"])
					$applied_giftcert["giftcert_cost"] = $order["total_cost"];

				$giftcert_discount += $applied_giftcert["giftcert_cost"];
				$order["total_cost"] = $order["total_cost"] - $giftcert_discount;
				$applied_giftcert["giftcert_cost"] = price_format($applied_giftcert["giftcert_cost"]);
				$applied_giftcerts[$k1]["giftcert_cost"] -= $applied_giftcert["giftcert_cost"];
				$return["orders"][$k]["applied_giftcerts"][] = $applied_giftcert;
				$return["orders"][$k]["giftcert_discount"] = price_format($giftcert_discount);
			}

			$return["orders"][$k]["total_cost"] = price_format($return["orders"][$k]["total_cost"] - $return["orders"][$k]["giftcert_discount"]);
		}
	}

	return $return;
}

#
# This function distributes the discount among the product prices and
# decreases the subtotal
#
function func_distribute_discount($field_name, $products, $discount, $discount_type, $avail_discount_total=0, $taxes=array()) {
	global $config;

	$sum_discount = 0;
	$return = array();
	$_orig_discount = $taxed_discount = $discount;

	if (!empty($taxes) && $config["Taxes"]["display_taxed_order_totals"] == "Y" && $config["Taxes"]["apply_discount_on_taxed_amount"] == "Y") {
		if ($discount_type=="absolute") {
			$_taxes = func_tax_price($discount, 0, false, NULL, "", $taxes, false);
			$taxed_discount = $_taxes ["net_price"];
		}
		else {
			$_taxes = func_tax_price($discount, 0, false, NULL, "", $taxes, true);
			$taxed_discount = $_taxes ["taxed_price"];
		}
	}

	if ($discount_type=="absolute" && $avail_discount_total > 0) {
		# Distribute absolute discount among the products
		$index = 0;
		$_considered_sum_discount = 0;
		$_total_discounted_products = 0;
		foreach ($products as $k=>$product) {
			if (@$product["deleted"]) continue; # for Advanced_Order_Management module
			if ($product['hidden'])
				continue;
			$_total_discounted_products++;
		}
		foreach ($products as $k=>$product) {
			if (@$product["deleted"]) continue; # for Advanced_Order_Management module
			if ($product['hidden'])
				continue;
			$index++;
			if ($field_name == "coupon_discount" || $product["discount_avail"] == "Y") {
				$koefficient = $product["price"] / $avail_discount_total;
				if ($index < $_total_discounted_products) {
					$products[$k][$field_name] = price_format($discount * $koefficient * $product["amount"]);
					$products[$k]["taxed_".$field_name] = price_format($discount * $koefficient * $product["amount"]);

					$_considered_sum_discount += $products[$k][$field_name];
					$_considered_sum_taxed_discount += $products[$k]["taxed_".$field_name];
				}
				else {
					$products[$k][$field_name] = $discount - $_considered_sum_discount;
					$products[$k]["taxed_".$field_name] = $taxed_discount - $_considered_sum_taxed_discount;
				}

				$products[$k]["discounted_price"] = max($products[$k]["discounted_price"] - $products[$k][$field_name], 0.00);
			}
		}
	}
	elseif ($discount_type=="percent") {
		# Distribute percent discount among the products
		foreach ($products as $k=>$product) {
			if (@$product["deleted"]) continue; # for Advanced_Order_Management module
			if ($product['hidden'])
				continue;

			if ($field_name == "coupon_discount" || $product["discount_avail"] == "Y") {
				$products[$k][$field_name] = price_format($product["price"] * $discount / 100 * $product["amount"]);
				if ($taxed_discount != $discount) {
					if ($product["display_price"] > 0)
						$_price = $product["display_price"];
					else
						$_price = $product["taxed_price"];
					$products[$k]["taxed_".$field_name] = price_format($_price * $_orig_discount / 100 * $product["amount"]);
				}
				else
					$products[$k]["taxed_".$field_name] = $products[$k][$field_name];

				$products[$k]["discounted_price"] = $product["discounted_price"] - $products[$k][$field_name];
			}
		}
	}

	foreach($products as $product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module
		if ($product['hidden'])
			continue;

		$sum_discount += $product["taxed_".$field_name];
	}

	if ($discount_type == "absolute" && $sum_discount > $discount)
		$sum_discount = $discount;

	if ($discount_type=="percent")
		$return[$field_name."_orig"] = $sum_discount;
	else
		$return[$field_name."_orig"] = $_orig_discount;

	$return["products"] = $products;
	$return[$field_name] = $sum_discount;

	return $return;
}

#
# Sort discounts in func_calculate_discounts in descent order
#
function func_sort_max_discount($a, $b) {
	return $b['max_discount'] - $a['max_discount'];
}

#
# This function calculates discounts on subtotal
#
function func_calculate_discounts($membershipid, $products, $discount_coupon = "", $provider="") {
	global $sql_tbl, $config, $active_modules, $single_mode, $global_store;

	#
	# Prepare provider condition for discounts gathering
	#
	$provider_condition = ($single_mode ? "" : "AND provider='$provider'");

	#
	# Search for subtotal to apply the global discounts
	#
	$avail_discount_total = 0;
	$total = 0;
	$_taxes = array();
	foreach($products as $k=>$product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module
		if ($product['hidden'])
			continue;

		$product["price"] = price_format($product["price"]);
		$products[$k]["price"] = $product["price"];
		$products[$k]["discount"] = 0;
		$products[$k]["coupon_discount"] = 0;
		$products[$k]["discounted_price"] = $product["price"] * $product["amount"];
		if ($product["discount_avail"] == "Y")
			$avail_discount_total += $product["price"] * $product["amount"];

		$total += $product["price"] * $product["amount"];
	
		if ($config["Taxes"]["apply_discount_on_taxed_amount"] == "Y" && is_array($product["taxes"]))
			$_taxes = func_array_merge($_taxes, $product["taxes"]);
	}

	$return = array(
		"discount" => 0,
		"coupon_discount" => 0,
		"discount_coupon" => $discount_coupon,
		"products" => $products);

	if ($avail_discount_total > 0) {
		#
		# Calculate global discount
		#
		if (!empty($global_store['discounts'])) {
			$discount_info = array();
			$__discounts = $global_store['discounts'];
			foreach ($__discounts as $k => $v) {
				if ($v['discount_type'] == 'absolute') {
					$__discounts[$k]['max_discount'] = $v['discount'];
				} else {
					$__discounts[$k]['max_discount'] = $avail_discount_total*$v['discount']/100;
				}
			}

			usort($__discounts, "func_sort_max_discount");

			foreach ($__discounts as $v) {
				if (($v['__override']) || ($v['minprice'] <= $avail_discount_total && (empty($v['memberships']) || @in_array($membershipid, $v['memberships'])) && ($single_mode || $v['provider'] == $provider))) {
					$discount_info = $v;
					break;
				}
			}

			unset($__discounts);
		}
		else {
			$max_discount_str =
"IF ($sql_tbl[discounts].discount_type='absolute',
	-- true ('absolute')
	$sql_tbl[discounts].discount
	,
	-- false ('percent')
	('$avail_discount_total' * $sql_tbl[discounts].discount / 100)
) as max_discount ";

			$discount_info = func_query_first("SELECT $sql_tbl[discounts].*, $max_discount_str FROM $sql_tbl[discounts] LEFT JOIN $sql_tbl[discount_memberships] ON $sql_tbl[discounts].discountid = $sql_tbl[discount_memberships].discountid WHERE minprice<='$avail_discount_total' $provider_condition AND ($sql_tbl[discount_memberships].membershipid IS NULL OR $sql_tbl[discount_memberships].membershipid = '$membershipid') ORDER BY max_discount DESC");
		}

		if (!empty($discount_info) && $discount_info['discount_type'] == 'percent' && $discount_info['discount'] > 100)
			unset($discount_info);

		if (!empty($discount_info)) {
			$return["discount"] += price_format($discount_info['max_discount']);
			#
			# Distribute the discount among the products prices
			#
			$updated = func_distribute_discount("discount", $products, $discount_info["discount"], $discount_info["discount_type"], $avail_discount_total, $_taxes);
			#
			# $products and $discount are extracted from the array $updated
			#
			extract($updated);
			unset($updated);
			$return["products"] = $products;
			$return["discount"] = $discount;
			if (isset($discount_orig))
				$return["discount_orig"] = $discount_orig;
		}
	}

	#
	# Apply discount coupon
	#
	if ($active_modules["Discount_Coupons"] && !empty($discount_coupon)) {
		#
		# Calculate discount value of the discount coupon
		#
		$coupon_total = 0;
		$coupon_amount = 0;

		if (!empty($global_store['discount_coupons'])) {
			$discount_coupon_data = array();
			foreach ($global_store['discount_coupons'] as $v) {
				if ($v['__override'] || ($v['coupon'] == $discount_coupon && ($single_mode || $v['provider'] == $provider))) {
					$discount_coupon_data = $v;
					break;
				}
			}
		}
		else {
			$discount_coupon_data = func_query_first("select * from $sql_tbl[discount_coupons] where coupon='$discount_coupon' $provider_condition");
		}

		$return["discount_coupon_data"] = $discount_coupon_data;

		if (!$single_mode && ($discount_coupon_data["provider"] != $provider || empty($products)))
			$return["discount_coupon"] = $discount_coupon_data = "";

		$return["coupon_type"] = $discount_coupon_data["coupon_type"];

		if (!empty($discount_coupon_data) && (($discount_coupon_data["coupon_type"] == "absolute") || ($discount_coupon_data["coupon_type"] == "percent"))) {
			$coupon_discount = 0;
			if ($discount_coupon_data["productid"] > 0) {
				#
				# Apply coupon to product
				#
				foreach($products as $k=>$product) {
					if (@$product["deleted"]) continue; # for Advanced_Order_Management module

					if (!empty($active_modules["Special_Offers"]) && !empty($product['free_amount'])) {
						# it's a "free product"
						# necessary only for absolute discount
						continue;
					}

					if ($product["productid"] != $discount_coupon_data["productid"])
						continue;

					$price = $product["discounted_price"];
					if ($discount_coupon_data["coupon_type"]=="absolute") {
						if ($discount_coupon_data["apply_product_once"] == "Y")
							$coupon_discount = $discount_coupon_data["discount"];
						else
							$coupon_discount = price_format($product["amount"] * $discount_coupon_data["discount"]);
						$products[$k]["coupon_discount"] = $coupon_discount;
						$products[$k]["discounted_price"] = max($price - $coupon_discount, 0.00);
					}
					else {
						$coupon_discount = price_format(($price * $discount_coupon_data["discount"] / 100 )) * $product["amount"];
						$products[$k]["coupon_discount"] = price_format($price * $discount_coupon_data["discount"] / 100);
						$products[$k]["discounted_price"] = max($price - $products[$k]["coupon_discount"], 0.00);
					}

					$return["coupon_discount"] += $coupon_discount;
				}
			}
			elseif ($discount_coupon_data["categoryid"] > 0) {
				#
				# Apply coupon to category (and subcategories)
				#
				$category_ids[] = $discount_coupon_data["categoryid"];

				if ($discount_coupon_data["recursive"] == "Y") {
					$categoryid_path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid='$discount_coupon_data[categoryid]'");
					if (!empty($categoryid_path))
						$tmp = db_query("SELECT categoryid FROM $sql_tbl[categories] WHERE categoryid_path LIKE '$categoryid_path/%'");
					while($row = db_fetch_array($tmp))
						$category_ids[] = $row["categoryid"];
				}

				#
				# Apply coupon to one category
				#
				foreach ($products as $k=>$product) {
					if (@$product["deleted"]) continue; # for Advanced_Order_Management module

					if (!empty($active_modules["Special_Offers"]) && !empty($product['free_amount'])) {
						# it's a "free product"
						# necessary only for absolute discount
						continue;
					}

					$product_categories = func_query("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$product[productid]'");
					$is_valid_product = false;
					foreach ($product_categories as $pc) {
						if (in_array($pc["categoryid"], $category_ids)) {
							$is_valid_product = true;
							break;
						}
					}

					if ($is_valid_product) {
						$price = $product["discounted_price"];
						if ($discount_coupon_data["coupon_type"]=="absolute") {
							if ($discount_coupon_data["apply_product_once"] == "Y") {
								$coupon_discount += $discount_coupon_data["discount"];
								$products[$k]["coupon_discount"] = $discount_coupon_data["discount"];
							}
							else {
								$coupon_discount += $product["amount"] * $discount_coupon_data["discount"];
								$products[$k]["coupon_discount"] = $discount_coupon_data["discount"] * $product["amount"];
							}
							$products[$k]["discounted_price"] = max($price - $products[$k]["coupon_discount"], 0.00);
						}
						else {
							$products[$k]["coupon_discount"] = price_format($price * $discount_coupon_data["discount"] / 100);
							$products[$k]["discounted_price"] = max($price - $products[$k]["coupon_discount"], 0.00);
							$coupon_discount += price_format($price * $discount_coupon_data["discount"] / 100) * $product["amount"];
						}
						$return["coupon_discount"] = $coupon_discount;

						if ($discount_coupon_data["coupon_type"] == "absolute" && $discount_coupon_data["apply_category_once"] == "Y")
							break;
					}
				}
			}
			else {
				#
				# Apply coupon to subtotal
				#
				if ($discount_coupon_data["coupon_type"]=="absolute")
					$return["coupon_discount"] = $discount_coupon_data["discount"];
				elseif ($discount_coupon_data["coupon_type"]=="percent")
					$return["coupon_discount"] = $total * $discount_coupon_data["discount"] / 100;
				$updated = func_distribute_discount("coupon_discount", $products, $discount_coupon_data["discount"], $discount_coupon_data["coupon_type"], $total, $_taxes);

				#
				# $products and $discount are extracted from the array $updated
				#
				extract($updated);
				unset($updated);

				$return["coupon_discount"] = $coupon_discount;
				if (isset($coupon_discount_orig))
					$return["coupon_discount_orig"] = $coupon_discount_orig;

			}
		}

		$return["products"] = $products;
	}

	return $return;
}

#
# This function calculates delivery cost
#
# Shipping also calculated based on zones
#
# Advanced shipping formula:
# AMOUNT = amount of ordered products
# SUM = total sum of order
# TOTAL_WEIGHT = total weight of products
#
# SHIPPING = rate+TOTAL_WEIGHT*weight_rate+AMOUNT*item_rate+SUM*rate_p/100
#
function func_calculate_shippings($products, $shipping_id, $customer_info, $provider="") {
	global $sql_tbl, $config, $active_modules, $single_mode;

	$return = array("shipping_cost" => 0);

	#
	# Prepare provider condition for shipping rates gathering
	#
	$provider_condition = ($single_mode ? "" : "AND provider='$provider'");

	#
	# Initial definitions
	#
	$total_shipping = 0;
	$total_weight_shipping = 0;
	$total_ship_items = 0;
	$shipping_cost = 0;
	$shipping_freight = 0;

	if (!empty($products)) {
		foreach($products as $k=>$product) {
			if (@$product["deleted"]) continue; # for Advanced_Order_Management module

			if ($product["free_shipping"] == "Y" || $product['product_type'] == 'C' || ($active_modules["Egoods"] && $product["distribution"] != "")) {
				continue;
			}
			else {
				if (!($config["Shipping"]["replace_shipping_with_freight"] == "Y" && $product["shipping_freight"] > 0)) {
					$total_shipping += $product["subtotal"];
					$total_weight_shipping += $product["weight"] * $product["amount"];
					$total_ship_items += $product["amount"];
				}

				$shipping_freight += $product["shipping_freight"] * $product["amount"];
			}
		}
	}

	#
	# Nothing to ship
	#
	if ($total_ship_items == 0 && $shipping_freight == 0)
		return $return;

	$customer_zone = func_get_customer_zone_ship($customer_info, $provider,"D");
	$shipping = func_query("SELECT * FROM $sql_tbl[shipping_rates] WHERE shippingid='$shipping_id' $provider_condition AND zoneid='$customer_zone' AND mintotal<='$total_shipping' AND maxtotal>='$total_shipping' AND minweight<='$total_weight_shipping' AND maxweight>='$total_weight_shipping' AND type='D' ORDER BY maxtotal, maxweight");

	if ($shipping && $total_ship_items > 0) {
		$shipping_cost =
			$shipping[0]["rate"] +
			($total_weight_shipping * $shipping[0]["weight_rate"]) +
			($total_ship_items * $shipping[0]["item_rate"]) +
			($total_shipping * $shipping[0]["rate_p"] / 100);
	}

	#
	# Get realtime shipping rates
	#
	$result = func_query_first ("SELECT * FROM $sql_tbl[shipping] WHERE shippingid='$shipping_id' AND code!=''");
	if ($config["Shipping"]["realtime_shipping"]=="Y" && $result && $total_ship_items>0) {
		$shipping_cost = func_real_shipping($shipping_id);
		$customer_zone = func_get_customer_zone_ship($customer_info, $provider,"R");
		$shipping_rt = func_query("SELECT * FROM $sql_tbl[shipping_rates] WHERE shippingid='$shipping_id' $provider_condition AND zoneid='$customer_zone' AND mintotal<='$total_shipping' AND maxtotal>='$total_shipping' AND minweight<='$total_weight_shipping' AND maxweight>='$total_weight_shipping' AND type='R' ORDER BY maxtotal, maxweight");

		if ($shipping_rt && $shipping_cost > 0)
			$shipping_cost += $shipping_rt[0]["rate"]+$total_weight_shipping*$shipping_rt[0]["weight_rate"]+$total_ship_items*$shipping_rt[0]["item_rate"]+$total_shipping*$shipping_rt[0]["rate_p"]/100;
	}

	$return["shipping_cost"] = $shipping_cost += $shipping_freight;

	return $return;
}

#
# This function calculates taxes
#
# SUM = total sum of order
#
# TAX_US = country_tax_flat + SUM*country_tax_percent/100 + state_tax_flat + SUM*state_tax_percent/100;
#
# TAX_CAN = SUM*gst_tax/100 + SUM*pst_tax/100;
#
function func_calculate_taxes(&$products, $customer_info, $shipping_cost, $provider="") {
	global $sql_tbl, $config, $active_modules, $single_mode, $shop_language;
	global $xcart_dir;

	$taxes = array();
	$taxes["total"] = 0;
	$taxes["shipping"] = 0;

	foreach ($products as $k=>$product) {

		$__taxes = array();
	
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if ($product["free_tax"] != "Y") {
			$product_taxes = func_get_product_taxes($products[$k], $customer_info["login"], true);

			if ($config["Taxes"]["display_taxed_order_totals"] =="Y")
				$products[$k]["display_price"] = doubleval($product["taxed_price"]);

			if (is_array($product_taxes)) {
				$formula_data = array();
				$formula_data["ST"] = $product["price"] * $product["amount"];
				$formula_data["DST"] = $product["discounted_price"];
				$formula_data["SH"] = 0;

				$tax_result = array();

				if (empty($shipping_cost)) {
					$index = 1;
					$tax_result[1] = 0;
				}
				else
					$index = 0;

				while ($index < 2) {
					$index++;

					foreach ($product_taxes as $tax_name=>$v) {
						if ($v["skip"])
							continue;

						if (!isset($taxes["taxes"][$tax_name])) {
							$taxes["taxes"][$tax_name] = $v;
							$taxes["taxes"][$tax_name]["tax_cost"] = 0;
						}

						if ($index == 2) {
							$formula_data["SH"] = $shipping_cost;

							if (!empty($__taxes[$tax_name]))
								$formula_data["SH"] = 0;
							else
								$__taxes[$tax_name] = true;
						}

						if ($v["rate_type"] == "%") {
							$assessment = func_calculate_assessment($v["formula"], $formula_data);
							$tax_value = $assessment * $v["rate_value"] / 100;
						}
						else
							$tax_value = $v["rate_value"] * $product["amount"];

						$formula_data[$tax_name] = $tax_value;

						$tax_result[$index] += $tax_value;

						if (empty($formula_data["SH"]))
							$taxes["taxes"][$tax_name]["tax_cost_no_shipping"] += $tax_value;

						if ($index == 2) {
							$taxes["taxes"][$tax_name]["tax_cost"] += $tax_value;
						}

					}
				}

				$taxes["shipping"] += max(0,($tax_result[2] - $tax_result[1]));
			}
		}
	}

	if ($shipping_cost == 0)
		$taxes["shipping"] = 0;

	if (is_array($taxes["taxes"])) {
		foreach ($taxes["taxes"] as $tax_name=>$tax) {
			$taxes["taxes"][$tax_name]["tax_cost"] = price_format($tax["tax_cost"]);
			$taxes["total"] += $taxes["taxes"][$tax_name]["tax_cost"];
		}
	}

	return $taxes;
}

#
# Calculate total products price
# 1) calculate total sum,
# 2) a) total = total - discount
#    b) total = total - coupon_discount
# 3) calculate shipping
# 4) calculate tax
# 5) total_cost = total + shipping + tax
# 6) total_cost = total_cost + giftcerts_cost
#
function func_calculate_single($cart, $products, $login, $login_type, $provider_for="") {
	global $single_mode;
	global $active_modules, $config, $sql_tbl;
	global $xcart_dir;

	if ($config["Taxes"]["display_taxed_order_totals"] == "Y")
		$config["Taxes"]["apply_discount_on_taxed_amount"] = "Y";

	if ($products) {
		#
		# Set the fields filter to avoid storing too much redundant data
		# in the session
		#
		list($tmp_k, $tmp_v) = each($cart["products"]);

		foreach(array_keys($tmp_v) as $k)
			$product_keys[] = $k;

		unset($tmp_k, $tmp_v);
		reset($cart["products"]);

		$product_keys[] = "cartid";
		$product_keys[] = "product";
		$product_keys[] = "productcode";
		$product_keys[] = "product_options";
		$product_keys[] = "price";
		$product_keys[] = "display_price";
		$product_keys[] = "display_discounted_price";
		$product_keys[] = "display_subtotal";
		$product_keys[] = "free_price";
		$product_keys[] = "discount";
		$product_keys[] = "coupon_discount";
		$product_keys[] = "discounted_price";
		$product_keys[] = "taxes";
		$product_keys[] = "subtotal";
		$product_keys[] = "product_type";
		$product_keys[] = "options_surcharge";
		$product_keys[] = "extra_data"; # Additional data for storing in the DB
		$product_keys[] = "provider";

		if ($active_modules["Wishlist"])
			$product_keys[] = "wishlistid";

		if ($active_modules["Egoods"])
			$product_keys[] = "distribution";

		if ($active_modules["Advanced_Order_Management"]) {
			$product_keys[] = "deleted";
			$product_keys[] = "new";
			$product_keys[] = "use_shipping_cost_alt";
			$product_keys[] = "shipping_cost_alt";
		}

		if ($active_modules["Product_Configurator"]) {
			$product_keys[] = "hidden";
			$product_keys[] = "pconf_price";
			$product_keys[] = "pconf_display_price";
			$product_keys[] = "pconf_data";
			$product_keys[] = "slotid";
			$product_keys[] = "price_modifier";
		}

		if ($active_modules["Subscriptions"]) {
			$product_keys[] = "catalogprice";
			$product_keys[] = "sub_plan";
			$product_keys[] = "sub_days_remain";
			$product_keys[] = "sub_onedayprice";
		}

		if ($active_modules["Special_Offers"]) {
			$product_keys[] = "free_amount";
			$product_keys[] = "have_offers";
			$product_keys[] = "special_price_used";
			$product_keys[] = "free_shipping_used";
			$product_keys[] = "saved_original_price";
		}
	}
	else
		$products = array();

	#
	# Calculate totals for one provider only or for all ($single_mode=true)
	#
	$provider_condition = ($single_mode ? "" : "and provider='$provider_for'");

	$shipping_id = @$cart["shippingid"];
	$giftcerts = @$cart["giftcerts"];
	$discount_coupon = @$cart["discount_coupon"];

	#
	# Get the user information
	#
	if (!empty($login)) $customer_info = func_userinfo($login,$login_type);

	if (defined('XAOM'))
		$customer_info = func_array_merge($customer_info, $cart["userinfo"]);
	
	if (!empty($active_modules["Special_Offers"])) {
		include $xcart_dir."/modules/Special_Offers/calculate_prepare.php";
		include $xcart_dir."/modules/Special_Offers/calculate.php";
	}

	if (!empty($products)) {
		#
		# Apply discounts to the products
		#
		$discounts_ret = func_calculate_discounts($customer_info["membershipid"], $products, $discount_coupon, $provider_for);

		#
		# Extract returned variables to global variables set:
		# $discount, $coupon_discount, $discount_coupon, $products
		#
		extract($discounts_ret);
		unset($discounts_ret);
	}

	#
	# Initial definitions
	#
	$subtotal = 0;
	$discounted_subtotal = 0;
	$shipping_cost = 0;
	$total_tax = 0;
	$giftcerts_cost = 0;

	#
	# Update $products array: calculate discounted prices, subtotal and
	# discounted subtotal
	#
	foreach($products as $k=>$product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if (empty($product["discount"]) && empty($product["coupon_discount"]))
			$product["discounted_price"] = $product["price"] * $product["amount"];

		if ($product["product_type"] == "C") {
			$diff_price = 0;
			if (isset($product["discounted_price"]) && $product["discounted_price"] != $product["price"])
				$diff_price = $product["price"]-$product["discounted_price"];

			# Corrections for Product Configurator module
			$product["pconf_price"] = $product["price"] = max(doubleval($product["options_surcharge"]), 0);
			$product["discounted_price"] = $product["price"] * $product["amount"] - $diff_price;
			foreach ($products as $k1=>$v1) {
				if ($v1["hidden"] == $product["cartid"]) {
					$product["pconf_price"] += price_format($v1["price"]);
				}
			}

			$product["pconf_display_price"] = $product["pconf_price"];
		}

		$product["subtotal"] = price_format($product["discounted_price"]);
		$product["display_price"] = price_format($product["price"]);
		$product["display_discounted_price"] = $product["discounted_price"];
		$product["display_subtotal"] = $product["subtotal"];

		$products[$k] = $product;

		if (!empty($active_modules["Special_Offers"])) {
			include $xcart_dir."/modules/Special_Offers/calculate_subtotal.php";
		}
		else {
			$subtotal += price_format($product["price"]) * $product["amount"];
			$discounted_subtotal += $product["subtotal"];
		}
	}

	$total = $subtotal;
	$display_subtotal = $subtotal;
	$display_discounted_subtotal = $discounted_subtotal;

	#
	# Enable shipping and taxes calculation if "apply_default_country" is ticked.
	#
	$calculate_enable_flag = true;

	if (empty($login)) {
		#
		# If user is not logged in
		#
		if ($config["General"]["apply_default_country"] == "Y") {
			$customer_info["s_country"] = $config["General"]["default_country"];
			$customer_info["s_state"] = $config["General"]["default_state"];
			$customer_info["s_zipcode"] = $config["General"]["default_zipcode"];
			$customer_info["s_city"] = $config["General"]["default_city"];
		}
		else {
			$calculate_enable_flag = false;
		}
	}

	if ($config["Shipping"]["disable_shipping"] != "Y" && $calculate_enable_flag || $cart["use_shipping_cost_alt"] == "Y") {
		#
		# Calculate shipping cost
		#
		if ($cart["use_shipping_cost_alt"] == "Y") {
			$shipping_cost = $cart["shipping_cost_alt"];
		}
		else {
			$shippings_ret = func_calculate_shippings($products, $shipping_id, $customer_info, $provider_for);
			#
			# Extract returned variables to global variables set:
			# $shipping_cost
			#
			extract($shippings_ret);
			unset($shippings_ret);
		}

		if (!empty($coupon_type) && $coupon_type == "free_ship") {
			#
			# Apply discount coupon 'Free shipping'
			#
			if (($single_mode) || ($provider_for == $discount_coupon_data["provider"])) {
				$coupon_discount = $shipping_cost;
				$shipping_cost = 0;
			}
		}
	}

	$display_shipping_cost = $shipping_cost;

	if ($calculate_enable_flag && !($customer_info["tax_exempt"] == "Y" && ($config["Taxes"]["enable_user_tax_exemption"] == "Y" || defined('XAOM')))) {
		#
		# Calculate taxes cost
		#
		$taxes = func_calculate_taxes($products, $customer_info, $shipping_cost, $provider_for);

		$total_tax = $taxes["total"];

		if ($config["Taxes"]["display_taxed_order_totals"] == "Y") {

			$_display_discounted_subtotal_tax = 0;
			if (is_array($taxes["taxes"])) {
				# Calculate the additional tax value if "display_including_tax"
				# option for tax is disabled (for $_display_discounted_subtotal)
				foreach ($taxes["taxes"] as $k=>$v)
					if ($v["display_including_tax"] != "Y")
						$_display_discounted_subtotal_tax += $v["tax_cost"];
			}

			$display_shipping_cost = $shipping_cost + $taxes["shipping"];
			$_display_subtotal = 0;
			$_display_discounted_subtotal = 0;
			if (is_array($products)) {
				foreach ($products as $k=>$v) {
					if (@$v["deleted"]) continue; # for Advanced_Order_Management module

					$v["display_price"] = $products[$k]["display_price"] = price_format($products[$k]["display_price"]);
					if (is_array($v["taxes"])) {
						# Correct $_display_subtotal if "display_including_tax"
						# option for the tax is disabled
						foreach ($v["taxes"] as $tn=>$tv) {
							if ($tv["display_including_tax"] == "N")
								$_display_subtotal += $tv["tax_value"];
						}
					}

					if (!empty($v["discount"]) || !empty($v["coupon_discount"])) {
						$subscription_flag = ( !empty($active_modules["Subscriptions"]) && $v["sub_plan"] ? false : true );
						$_taxes = func_tax_price($v["price"], $v["productid"], false, $v["discounted_price"], $customer_info, "", $subscription_flag);
						if ($v['discounted_price'] > 0)
							$products[$k]["display_discounted_price"] = price_format($_taxes["taxed_price"]);
					}
					else {
						$products[$k]["display_discounted_price"] = $v["display_price"] * $v["amount"];
					}

					$products[$k]["display_subtotal"] = $products[$k]["display_discounted_price"];
					$_display_discounted_subtotal += $products[$k]["display_subtotal"];
					if ($v["product_type"] == "C") {
						# Corrections for Product Configurator module
						$products[$k]["display_price"] = $_pconf_display_price = max(doubleval($products[$k]["options_surcharge"]), 0);
						$_display_subtotal += ($_pconf_display_price * $products[$k]["amount"]);
						$_pconf_taxes = array();
						foreach ($products as $k1=>$v1) {
							if (@$v1["deleted"]) continue; # for Advanced_Order_Management module

							if ($v1["hidden"] == $v["cartid"]) {
								$_pconf_display_price += price_format($v1["display_price"]);
								if (is_array($v1["taxes"])) {
									foreach ($v1["taxes"] as $_tax_name=>$_tax) {
										if (!isset($_pconf_taxes[$_tax_name])) {
											$_pconf_taxes[$_tax_name] = $_tax;
											$_pconf_taxes[$_tax_name]["tax_value"] = 0;
										}

										$_pconf_taxes[$_tax_name]["tax_value"] += $_tax["tax_value"];
									}
								}
							}
						}

						$products[$k]["taxes"] = $_pconf_taxes;
						$products[$k]["pconf_display_price"] = $_pconf_display_price;
					}
					else
						$_display_subtotal += $v["display_price"] * $v["amount"];
					
					if (!empty($active_modules["Subscriptions"]) && $products[$k]["sub_plan"] && $config["Taxes"]["display_taxed_order_totals"] == "Y") {
						$subscription_markup = $products[$k]["sub_days_remain"] * $products[$k]["sub_onedayprice"];
						$_display_subtotal += $subscription_markup;
						$products[$k]["display_price"] += $subscription_markup;
						if ($display_subtotal == $display_discounted_subtotal)
							$products[$k]["display_subtotal"] += $subscription_markup;
					}

				}

				if (empty($coupon_discount) && empty($discount))
					$display_discounted_subtotal = $_display_subtotal;
				else
					$display_discounted_subtotal = $_display_discounted_subtotal;

				$display_subtotal = $_display_subtotal;
			}
		}
	}

	#
	# Calculate Gift Certificates cost (purchased giftcerts)
	#
	if ((($single_mode) || (!$provider_for)) && ($giftcerts)) {
		foreach($giftcerts as $giftcert) {
			if (@$giftcert["deleted"]) continue; # for Advanced_Order_Management module

			$giftcerts_cost+=$giftcert["amount"];
		}
	}

	$subtotal += $giftcerts_cost;
	$display_subtotal += $giftcerts_cost;
	$discounted_subtotal += $giftcerts_cost;
	$display_discounted_subtotal += $giftcerts_cost;

	if ($discount > $display_subtotal)
		$discount = $display_subtotal;

	if ($coupon_discount > $display_subtotal)
		$coupon_discount = $display_subtotal;

	$display_shipping_cost = price_format($display_shipping_cost);
	$display_discounted_subtotal = price_format($display_discounted_subtotal);

	#
	# Calculate total
	#
	if ($config["Taxes"]["display_taxed_order_totals"] == "Y") {
		if ($config["Taxes"]["apply_discount_on_taxed_amount"] == "Y" && ($display_discounted_subtotal != $display_subtotal - $coupon_discount_orig - $discount_orig)) {
			$display_discounted_subtotal = $display_subtotal - $coupon_discount_orig - $discount_orig;
			  $coupon_discount = $coupon_discount_orig;
			  $discount = $discount_orig;
		}
		else {
			if ($discount > 0)
				$discount = $display_subtotal - ($display_discounted_subtotal + $coupon_discount);
			else
				$coupon_discount = $display_subtotal - ($display_discounted_subtotal + $discount);
		}
		$total = $display_discounted_subtotal + $display_shipping_cost;
	}
	else
		$total = $discounted_subtotal + $shipping_cost + $total_tax;

	$_products = array();
	foreach($products as $index=>$product) {
		foreach($product as $key=>$value)
			if (in_array($key, $product_keys))
				$_products[$index][$key] = $value;
	}

	$return = array(
		"total_cost" => price_format($total),
		"shipping_cost" => price_format($shipping_cost),
		"taxes" => $taxes["taxes"],
		"tax_cost" => price_format($taxes["total"]),
		"discount" => price_format($discount),
		"coupon" => $discount_coupon,
		"coupon_discount" => price_format($coupon_discount),
		"subtotal" => price_format($subtotal),
		"display_subtotal" => price_format($display_subtotal),
		"discounted_subtotal" => price_format($discounted_subtotal),
		"display_shipping_cost" => price_format($display_shipping_cost),
		"display_discounted_subtotal" => price_format($display_discounted_subtotal),
		"products" => $_products);

	if (!empty($active_modules["Special_Offers"])) {
		include $xcart_dir."/modules/Special_Offers/calculate_result.php";
	}

	return $return;
}

#
# This function calculates the payment method surcharge
#
function func_payment_method_surcharge ($total, $paymentid) {
	global $sql_tbl;

	$surcharge = 0;

	if (!empty($total))
		$surcharge = func_query_first_cell("SELECT IF (surcharge_type='$', surcharge, surcharge * $total / 100) as surcharge FROM $sql_tbl[payment_methods] WHERE paymentid='$paymentid'");

	return $surcharge;
}

#
# Generate products array in $cart
#
function func_products_in_cart($cart, $membershipid) {
	if (empty($cart) || empty($cart["products"]))
		return array();

	return func_products_from_scratch($cart["products"], $membershipid, false);
}

#
# Generate products array from scratch
#
function func_products_from_scratch($scratch_products, $membershipid, $persistent_products) {
	global $active_modules, $sql_tbl, $config, $xcart_dir;
	global $current_area, $store_language;

	$products = array();

	if (empty($scratch_products))
		return $products;

	$pids = array();
	foreach ($scratch_products as $product_data) {
		$pids[] = $product_data["productid"];
	}

	$int_res = func_query_hash("SELECT * FROM $sql_tbl[products_lng] WHERE code = '$store_language' AND productid IN ('".implode("','", $pids)."')", "productid", false);

	unset($pids);

	$hash = array();
	foreach ($scratch_products as $product_data) {

		$productid = $product_data["productid"];
		$cartid = $product_data["cartid"];
		$amount = $product_data["amount"];
		$variantid = $product_data["variantid"];
		if (!is_numeric($amount))
			$amount = 0;

		$options = $product_data["options"];
		$product_options = false;
		$variant = array();

		if (!empty($active_modules['Product_Options']) && !empty($options) && is_array($options)) {
			if (!func_check_product_options($productid, $options))
				continue;

			list($variant, $product_options) = func_get_product_options_data($productid, $options, $membershipid);

			if (empty($variantid) && isset($variant['variantid']))
				$variantid = $variant['variantid'];

			if ($config["General"]["unlimited_products"]=="N" && !$persistent_products) {
				if ((isset($variant['avail']) && $variant['avail'] < $amount) || ($variant['variantid'] != $variantid && !empty($variantid)))
					continue;
			}
		}

		if ($config["General"]["unlimited_products"]=="N" && !$persistent_products && empty($variant))
			$avail_condition = "($sql_tbl[products].avail>=".doubleval($amount)." OR $sql_tbl[products].product_type='C') AND ";

		$products_array = func_query_first("SELECT $sql_tbl[products].*, MIN($sql_tbl[pricing].price) as price, IF($sql_tbl[images_T].id IS NULL, '', 'Y') as is_thumbnail, $sql_tbl[images_T].image_path, $sql_tbl[images_T].image_x, $sql_tbl[images_T].image_y, IF($sql_tbl[images_P].id IS NULL, '', 'P') as is_pimage, $sql_tbl[images_P].image_path as pimage_path, $sql_tbl[images_P].image_x as pimage_x, $sql_tbl[images_P].image_y as pimage_y FROM $sql_tbl[pricing],$sql_tbl[products] LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[images_T].id = $sql_tbl[products].productid LEFT JOIN $sql_tbl[images_P] ON $sql_tbl[images_P].id = $sql_tbl[products].productid WHERE $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[products].forsale != 'N' AND $sql_tbl[products].productid='$productid' AND $avail_condition $sql_tbl[pricing].quantity<='$amount' AND $sql_tbl[pricing].membershipid IN('$membershipid', 0) AND $sql_tbl[pricing].variantid = '$variantid' GROUP BY $sql_tbl[products].productid ORDER BY $sql_tbl[pricing].quantity DESC");

		if ($products_array) {
			$products_array = func_array_merge($product_data, $products_array);

			#
			# If priduct's price is 0 then use customer-defined price
			#
			$free_price = false;
			if ($products_array["price"] == 0 && empty($products_array["slotid"])) {
				$free_price = true;
				$products_array["price"] = price_format($product_data["free_price"] ? $product_data["free_price"] : 0);
			}

			
			if (!empty($active_modules['Product_Options']) && $options) {
				if (!empty($variant) && $products_array['product_type'] != 'C') {
					unset($variant['price']);
					if (is_null($variant['pimage_path'])) {
						func_unset($variant, "pimage_path", "pimage_x", "pimage_y");
					} else {
						$variant['is_pimage'] = 'W';
					}
					$products_array = func_array_merge($products_array, $variant);
				}

				$hash_key = $productid."|".$products_array['variantid'];

				if ($config["General"]["unlimited_products"]=="N" && !$persistent_products && ($products_array['avail']-$hash[$hash_key]) < $amount && $products_array['product_type'] != 'C')
					continue;

				if ($product_options === false) {
					unset($product_options);
				} else {
					$variant['price'] = $products_array['price'];
					$products_array["options_surcharge"] = 0;
					if ($product_options) {
						foreach($product_options as $o) {
							$products_array["options_surcharge"] += ($o['modifier_type'] == '%' ? ($products_array['price']*$o['price_modifier']/100) : $o['price_modifier']);
						}
					}
				}
			}

			#
			# Get thumbnail's URL (uses only if images stored in FS)
			#
			$products_array['is_thumbnail'] = ($products_array['is_thumbnail'] == 'Y');
			if (!empty($products_array['pimage_path']) && !empty($products_array['is_pimage'])) {
				if ($products_array['is_pimage'] == 'P') {
					$products_array["pimage_url"] = func_get_image_url($products_array["productid"], 'P', $products_array['pimage_path']);
				} else {
					$products_array["pimage_url"] = func_get_image_url($products_array["variantid"], 'W', $products_array['pimage_path']);
				}

			} elseif ($products_array['is_thumbnail'] && !empty($products_array['image_path'])) {
				$products_array["pimage_url"] = func_get_image_url($products_array["productid"], 'T', $products_array['image_path']);

			} elseif (empty($products_array['is_pimage']) && !$products_array['is_thumbnail']) {
				$products_array["pimage_url"] = func_get_default_image("P");
				
			}

			$products_array["price"] += $products_array["options_surcharge"];

			if (!empty($active_modules["Product_Configurator"])) {
				include $xcart_dir."/modules/Product_Configurator/pconf_customer_price_modifier.php";
			}

			if ($current_area == "C" && $products_array["product_type"] != "C") {
				#
				# Calculate taxes and price including taxes
				#
				global $login;

				$products_array["taxes"] = func_get_product_taxes($products_array, $login);
			}

			if (!empty($active_modules["Special_Offers"])) {
				include $xcart_dir."/modules/Special_Offers/calculate_taxes_restore.php";
			}

			$products_array["total"] = price_format($amount*$products_array["price"]);
			$products_array["product_options"] = $product_options;
			$products_array["options"] = $options;
			$products_array["amount"] = $amount;
			$products_array["cartid"] = $cartid;

			$products_array["product_orig"] = $products_array["product"];

			if (isset($int_res[$productid])) {
				if (!empty($int_res["product"]))
					$products_array["product"] = stripslashes($int_res[$productid]["product"]);

				if (!empty($int_res["descr"]))
					$products_array["descr"] = stripslashes($int_res[$productid]["descr"]);

				if (!empty($int_res["fulldescr"]))
					$products_array["fulldescr"] = stripslashes($int_res[$productid]["fulldescr"]);

				func_unset($int_res, $productid);
			}

			if ($products_array["descr"] == strip_tags($products_array["descr"]))
				$products_array["descr"] = str_replace("\n", "<br />", $products_array["descr"]);

			if ($products_array["fulldescr"] == strip_tags($products_array["fulldescr"]))
				$products_array["fulldescr"] = str_replace("\n", "<br />", $products_array["fulldescr"]);

			$products[] = $products_array;

			$hash[$hash_key] += $amount;
		}
	}

	if (!empty($active_modules["Product_Configurator"])) {
		include $xcart_dir."/modules/Product_Configurator/pconf_customer_sort_products.php";
	}

	return $products;
}

#
# This function generates the unique cartid number
#
function func_generate_cartid($cart_products) {
	global $cart;

	if (empty($cart["max_cartid"]))
		$cart["max_cartid"] = 0;

	$cart["max_cartid"]++;

	return $cart["max_cartid"];
}

#
# Detectd ESD product(s) in cart
#
function func_esd_in_cart($cart) {
	if (!empty($cart['products'])) {
		foreach($cart['products'] as $p) {
			if (!empty($p['distribution'])) {
				return true;
			}
		}
	}

	return false;
}

#
# Calculate total amount of all products in cart. Used for cart validation
#
function func_get_cart_products_amount($products) {
	$amount = 0;
	if (!empty($products) && is_array($products)) {
		foreach ($products as $product) {
			$amount += $product['amount'];
		}
	}

	return $amount;
}

#
# Validate cart contents
#
function func_cart_is_valid($cart, $userinfo) {
	# test: all total amount should not change
	$current_amount = func_get_cart_products_amount($cart['products']);
	$validated_products = func_products_in_cart($cart, $userinfo['membershipid']);
	$validated_amount = func_get_cart_products_amount($validated_products);

	$is_valid = ($current_amount == $validated_amount);

	return $is_valid;
}

?>
