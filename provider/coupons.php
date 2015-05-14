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
# $Id: coupons.php,v 1.34.2.1 2006/05/04 12:36:11 max Exp $
#

define("NUMBER_VARS", "minimum_new,discount_new");
require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_coupons"), "");

include $xcart_dir."/include/categories.php";

#
# Use this condition when single mode is disabled
#
$provider_condition = ($single_mode ? "" : "AND provider='$login'");

if ($REQUEST_METHOD == "POST") {

	if ($mode=="delete") {
		#
		# Delete selected coupons
		#
		if (is_array($posted_data)) {
			$deleted = false;
			foreach ($posted_data as $coupon=>$v) {
				if (empty($v["to_delete"]))
					continue;

				db_query("delete from $sql_tbl[discount_coupons] where coupon='$coupon' $provider_condition");
				$deleted = true;
			}

			if ($deleted)
				$top_message["content"] = func_get_langvar_by_name("msg_discount_coupons_del");
		}
	}

	if ($mode == "update") {
		#
		# Update discount table
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $coupon=>$v) {
				db_query("UPDATE $sql_tbl[discount_coupons] SET status='$v[status]' WHERE coupon='$coupon' $provider_condition");
			}

			$top_message["content"] = func_get_langvar_by_name("msg_discount_coupons_upd");
		}
	}
	
	if ($mode == "add") {
		#
		# Add new coupon
		#

		# Generate timestamp
		$expire_new = mktime(0,0,0,$new_Month,$new_Day,$new_Year)-$config["Appearance"]["timezone_offset"];

		$recursive = ($recursive ? "Y" : "N");
		$per_user = ($per_user ? "Y" : "N");
		if ($how_to_apply_p != "N")
			$how_to_apply_p = "Y"; # Apply discount once per order

		if (!in_array($how_to_apply_c, array("Y", "N1", "N2")))
			$how_to_apply_c = "Y"; # Apply discount once per order

		$apply_category_once = $apply_product_once = "N";
		switch ($apply_to) {
		case '':
		case 'any':
			$productid_new=0;
			$categoryid_new=0;
			break;
		case 'product':
			$categoryid_new=0;
			$apply_product_once = $how_to_apply_p;
			break;
		case 'category':
			$productid_new=0;
			if ($how_to_apply_c == "Y") {
				$apply_product_once = $apply_category_once = "Y";
			}
			elseif ($how_to_apply_c == "N1") {
				$apply_product_once = "N";
				$apply_category_once = "N";
			}
			else {
				$apply_product_once = "Y";
				$apply_category_once = "N";
			}
			break;
		}

		if (empty($coupon_new) || ($discount_new <= 0 && $coupon_type_new != 'free_ship') || ($discount_new > 100 && $coupon_type_new == 'percent') || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons] WHERE coupon='$coupon_new'") > 0) {
			$coupon_data = $HTTP_POST_VARS;
			$coupon_data["expire_new"] = $expire_new;
			x_session_register("coupon_data");
			$top_message["content"] = func_get_langvar_by_name("msg_err_discount_coupons_add");
			$top_message["type"] = "E";
		}
		else {
			db_query("INSERT INTO $sql_tbl[discount_coupons] (coupon, discount, coupon_type, minimum, times, per_user, expire, status, provider, productid, categoryid, recursive, apply_category_once, apply_product_once) VALUES ('$coupon_new', '$discount_new', '$coupon_type_new', '$minimum_new', '$times_new', '$per_user', '$expire_new', '$status_new', '$login', '$productid_new', '$categoryid_new', '$recursive', '$apply_category_once', '$apply_product_once')");
			$top_message["content"] = func_get_langvar_by_name("msg_discount_coupons_add");
		}
	}
	
	func_header_location("coupons.php");
}

$coupons = func_query("SELECT *, (expire + ".doubleval($config["Appearance"]["timezone_offset"]).") as expire FROM $sql_tbl[discount_coupons] WHERE 1 $provider_condition");

if (x_session_is_registered("coupon_data")) {
	x_session_register("coupon_data");
	$smarty->assign("coupon_data", $coupon_data);
	x_session_unregister("coupon_data");
}

if (!empty($coupons))
	$smarty->assign("coupons", $coupons);
$smarty->assign("main","coupons");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("provider/home.tpl",$smarty);
?>
