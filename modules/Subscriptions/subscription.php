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
# $Id: subscription.php,v 1.21 2006/04/05 09:58:06 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('taxes');

$subscription_periods = array("Annually"=>"365", "Quarterly"=>"90", "Monthly"=>"30", "Weekly"=>"7", "By Period"=>"45");

$subscription = array();

if (!function_exists("SubscriptionProducts")) {
function SubscriptionProducts($products) {
	global $subscription_periods, $subscription, $pay_period, $login, $sql_tbl;
	global $in_cart;

for ($i = 0; $i < count($products); $i++) {

$subscription = func_query_first("SELECT * FROM $sql_tbl[subscriptions] WHERE productid = '".$products[$i]["productid"]."'");

if ($subscription) {

	$pay_dates = array();
	if (!empty($subscription['pay_dates'])) {
		$pay_dates = unserialize($subscription['pay_dates']);
	}

    if (is_numeric($subscription["pay_period_type"])) {
        $pay_period = $subscription["pay_period_type"];
        $subscription["pay_period_type"] = "By Period";
    }   

    if ($subscription["pay_period_type"] == "Annually") {
        $days_in_year = (checkdate(2,29,date("Y"))?366:365);
        if (date("z") >= $subscription["days_as_period"])
            $subscription["days_remain"] = 0;
        else
            $subscription["days_remain"] = $days_in_year - date("z");
    } 
    elseif ($subscription["pay_period_type"] == "Quarterly") {
        if (date("m") >= 10) {
            $days_max = date("z",mktime(0,0,0,12,31,date("Y")));
        } elseif (date("m") >= 7) {
            $days_max = date("z",mktime(0,0,0,9,30,date("Y")));
        } elseif (date("m") >= 4) {
            $days_max = date("z",mktime(0,0,0,6,30,date("Y")));
        } else {
            $days_max = date("z",mktime(0,0,0,3,31,date("Y")));
        }
        $cday = $days_max - date("z");

        if ($cday >= $subscription["days_as_period"])
            $subscription["days_remain"] = 0;
        else
            $subscription["days_remain"] = $cday;
    }
    elseif ($subscription["pay_period_type"] == "Monthly") {
        if (checkdate(date("m"),31,date("Y")))
            $days_in_month = 31;
        elseif (checkdate(date("m"),30,date("Y")))
            $days_in_month = 30;
        elseif (checkdate(date("m"),29,date("Y")))
            $days_in_month = 29;
        else
            $days_in_month = 28;
        if (date("j") >= $subscription["days_as_period"])
            $subscription["days_remain"] = 0;
        else
            $subscription["days_remain"] = $days_in_month - date("j");
    }
    elseif ($subscription["pay_period_type"] == "Weekly") {
        $days_in_week = 7;
        if (date("w") >= $subscription["days_as_period"])
            $subscription["days_remain"] = 0;
        else
            $subscription["days_remain"] = $days_in_week - date("w");
    }
    elseif ($subscription["pay_period_type"] == "By Period") {
		$period = $pay_period;
		$last_date_days = 0;
		if (empty($pay_dates)) {
			$day_number = date("z");
			$days_in_year = (checkdate(2,29,date("Y")) ? 366 : 365);
			if ($period > $days_in_year)
				$period -= floor($period/$days_in_year)*$days_in_year;

			if ($period < $day_number)
				$last_date_days = floor($day_number/$period)*$period;
		} else {
			$day_number = floor((time()+date("Z"))/86400);
			foreach ($pay_dates as $pd) {
				$pd_number = ($pd+date("Z", $pd))/86400;
				if ($pd_number > $day_number) {
					if ($last_date_days > 0) {
						$period = $pd_number-$last_date_days;
					} else {
						$last_date_days = $pd_number-$period;
					}
					break;
				}
				$last_date_days = $pd_number;
			}
		}
		$days = $day_number-$last_date_days;

        if ($days >= $subscription["days_as_period"])
            $subscription["days_remain"] = 0;
        else
            $subscription["days_remain"] = $period - $days;
    }

	$p = array(
		"productid" => $products[$i]['productid'],
		"price" => $subscription['oneday_price'],
		"taxed_price" => $subscription['oneday_price']
	);

	if (!$in_cart) {
		$p["price"] = $p["taxed_price"] = $subscription["price_period"];
	}

	$subscription['taxes'] = func_get_product_taxes($p, $login);
	$subscription['taxed_oneday_price'] = $p['taxed_price'];
	$subscription['taxed_price_period'] = $p['taxed_price'];

    $products[$i]["catalogprice"] = $products[$i]["taxed_price"];
    $products[$i]["price"] = $products[$i]["catalogprice"] + $subscription["taxed_oneday_price"] * $subscription["days_remain"];

    if ($products[$i]["total"])
        $products[$i]["total"] = $products[$i]["price"] * $products[$i]["amount"];
    
    $products[$i]["subscription"] = $subscription;
    $products[$i]["sub_days_remain"] = $subscription["days_remain"];
    $products[$i]["sub_onedayprice"] = $subscription["taxed_oneday_price"];
    $products[$i]["sub_plan"] = $subscription["pay_period_type"];
    $products[$i]["sub_priceplan"] = $subscription["price_period"];
}   #if

} #for

return $products;

} # function
}

$products = SubscriptionProducts($products);

$smarty->assign("pay_period", $pay_period);
$smarty->assign("subscription", $subscription);
?>
