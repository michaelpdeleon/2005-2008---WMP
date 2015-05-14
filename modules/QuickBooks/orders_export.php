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
# $Id: orders_export.php,v 1.20 2006/01/11 06:56:17 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('crypt');

#
# Export orders (invoices, payments, customers info) to QuickBooks format
#
foreach ($orders_full as $key=>$value) {
	foreach ($value as $subkey => $subvalue) {
		if ($subkey == "details")
			$orders_full[$key][$subkey] = text_decrypt($orders_full [$key][$subkey]);

		if ($subkey == "product_options") {
			$orders_full[$key][$subkey] = strtr($orders_full[$key][$subkey], "\r\t", "");
			$orders_full[$key][$subkey] = str_replace("\n", "\\n", $orders_full[$key][$subkey]);
		}
		else
			$orders_full[$key][$subkey] = strtr($orders_full[$key][$subkey], "\r\n\t", " ");
	}

	$orders_full[$key]["shipping"] = func_query_first_cell("select shipping from $sql_tbl[shipping] where shippingid='".$value["shippingid"]."'");
	$orders_full[$key]["cost"] = price_format($value["price"] * $value["amount"]);
	$orders_full[$key]["b_statename"] = func_get_state($value["b_state"],$value["b_country"]);
	$orders_full[$key]["s_statename"] = func_get_state($value["s_state"],$value["s_country"]);
	$orders_full[$key]["b_countryname"] = func_get_country($value["b_country"]);
	$orders_full[$key]["s_countryname"] = func_get_country($value["s_country"]);
	if ($config["General"]["use_counties"] == "Y") {
		$orders_full[$key]["b_countyname"] = func_get_county($value["b_county"]);
		$orders_full[$key]["s_countyname"] = func_get_county($value["s_county"]);
	}

	$orders_full[$key]["tax_values"] = unserialize($value["taxes_applied"]);
	if ($value["giftcert_ids"]) {
		$tmp = array();
		foreach (split("\*", $value["giftcert_ids"]) as $v){
			if ($v) {
				list($giftcert_id, $giftcert_cost) = split(":", $v);
				$tmp[] = "GC#".$giftcert_id." (".$giftcert_cost.")";
			}
		}

		$orders_full[$key]["applied_giftcerts"] = join(", ",$tmp);
	}

	if (!empty($config["QuickBooks"]["qb_order_prefix"])) {
		$prefix = trim($config["QuickBooks"]["qb_order_prefix"]);
		if ($prefix != "") {
			$orders_full[$key]["orderid"] = $prefix.$orders_full[$key]["orderid"];
		}
	}

	if ($orders_full[$key]['coupon_discount'] > 0 && strstr($orders_full[$key]['coupon'], 'free_ship') ) {
		$orders_full[$key]['shipping_cost'] += $orders_full[$key]['coupon_discount'];
	}
}

$smarty->assign("orders", $orders_full);
func_display("modules/QuickBooks/orders_export_qb.tpl",$smarty);

?>
