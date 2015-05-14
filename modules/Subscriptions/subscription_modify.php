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
# $Id: subscription_modify.php,v 1.21 2006/04/05 09:58:06 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

$subscription_periods = array("Annually"=>"365", "Quarterly"=>"90", "Monthly"=>"30", "Weekly"=>"7", "By Period"=>"45");

if ($REQUEST_METHOD=="POST" && !empty($product_info)) {

	#
	# Update subscription
	#
	if ($mode=="subscription_modify") {
		$pay_period_type = (($subscription["pay_period_type"] == 'By Period') ? intval($pay_period) : $subscription["pay_period_type"]);
		if ($subscription["pay_period_type"] == 'By Period' && !$pay_period_type) {
			$subscription["pay_period_type"] = "Annually";
			$pay_period_type = $subscription["pay_period_type"];
		}

		$query_data = array(
			"productid" => $productid,
			"pay_period_type" => $pay_period_type,
			"price_period" => $subscription['price_period'],
			"oneday_price" => $subscription['oneday_price'],
			"days_as_period" => $subscription['days_as_period']
		);

		func_array2insert("subscriptions", $query_data, true);

		if ($geid && $fields['subscription'] == 'Y') {
			while ($pid = func_ge_each($geid, 1, $productid)) {
				$query_data['productid'] = $pid;
				func_array2insert("subscriptions", $query_data, true);
			}
		}

		$top_message["content"] = func_get_langvar_by_name("msg_adm_product_subscr_upd");
		$top_message["type"] = "I";

	    func_refresh("subscr");

	#
	# Delete subscription
	#
	} elseif ($mode == "subscription_delete") {

		if ($geid && $fields['subscription'] == 'Y') {
			while ($pid = func_ge_each($geid, 100)) {
				db_query("DELETE FROM $sql_tbl[subscriptions] WHERE productid IN ('".implode("','", $pid)."')");
			}
		} else {
			db_query("DELETE FROM $sql_tbl[subscriptions] WHERE productid = '$productid'");
		}

		$top_message["content"] = func_get_langvar_by_name("msg_adm_product_subscr_del");
		$top_message["type"] = "I";

	    func_refresh("subscr");
	}
}

#
# Prepare data for Product subscription management page
#
$subscription = func_query_first("SELECT * FROM $sql_tbl[subscriptions] WHERE productid='$productid'");

if (is_numeric($subscription["pay_period_type"])) {
    $pay_period = $subscription["pay_period_type"];
    $subscription["pay_period_type"] = "By Period";
}

$smarty->assign("pay_period", $pay_period);
$smarty->assign("subscription", $subscription);
$smarty->assign("subscription_periods",$subscription_periods);

?>
