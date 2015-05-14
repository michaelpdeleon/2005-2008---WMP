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
# $Id: func.php,v 1.1.2.1 2006/10/13 10:41:19 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

#
# Check discount
# Discount coupons
# Status: A - active, D - disabled, U - used
#
function func_is_valid_coupon ($coupon) {
	global $cart, $products, $single_mode, $sql_tbl, $login, $anonymous_login;

	$my_coupon = func_query_first("SELECT * FROM $sql_tbl[discount_coupons] WHERE coupon='$coupon' AND status='A' AND expire>".time());
	if (!$my_coupon)
		return 1;
	if (!$single_mode) {
		$products_providers = func_get_products_providers ($products);
		if (!in_array ($my_coupon["provider"], $products_providers))
			return 2;
	}

	if ($my_coupon["per_user"] == "Y") {
		if (empty($login) || $anonymous_login)
			return 1;
		$_times_used = func_query_first_cell("SELECT times_used FROM $sql_tbl[discount_coupons_login] WHERE coupon='$coupon' AND login='$login'");
		if (intval($_times_used) >= $my_coupon["times"])
			return 5;
	}

	$cart["coupon_type"] = $my_coupon["coupon_type"];

	if ($my_coupon["coupon_type"] == "percent" && $my_coupon["discount"] > 100) {
		return 1;
	}
	if ($my_coupon["productid"] > 0) {
		$found = false;

		foreach ($products as $value) {
			if ((!$single_mode) && ($my_coupon["provider"] != $value["provider"]))
				next;

			if ($value["productid"] == $my_coupon["productid"])
				$found = true;
		}

		return ($found ? 0 : 4);
	} elseif ($my_coupon["categoryid"] > 0) {
		$found = false;

		$category_ids[] = $my_coupon["categoryid"];

		if ($my_coupon["recursive"] == "Y") {
			$categoryid_path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid='$my_coupon[categoryid]'");
			$tmp = db_query("SELECT categoryid FROM $sql_tbl[categories] WHERE categoryid_path LIKE '$categoryid_path/%'");
			while($row = db_fetch_array($tmp))
				$category_ids[] = $row["categoryid"];
		}

		if (!is_array($products))
			return 4;

		foreach ($products as $value) {
			if (!$single_mode && $my_coupon["provider"] != $value["provider"])
				continue;
			$product_categories = func_query("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$value[productid]'");
			$is_valid_product = false;
			foreach ($product_categories as $k=>$v) {
				if (in_array($v["categoryid"], $category_ids)) {
					$is_valid_product = true;
					break;
				}
			}
			if ($is_valid_product) {
				$found = true;
				break;
			}
		}

		return ($found ? 0 : 4);
	} else {
		$total = 0;

		if (!empty($products) && is_array($products)) {
			foreach ($products as $value) {
				if (($single_mode) || ((!$single_mode) && ($my_coupon["provider"] == $value["provider"])))
					$total += $value["price"]*$value["amount"];
			}
		}

		if ($total < $my_coupon["minimum"])
			return 3;
		else
			return 0;
	}

	return 0;
}

?>
