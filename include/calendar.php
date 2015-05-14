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
# $Id: calendar.php,v 1.6 2006/01/11 06:55:58 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','product');

#
# Search of coincidence date_s and data in $pay_dates array (timestamp)
#
function is_date_pay($date_s) {
	global $pay_dates;

	if (is_array($pay_dates) && !empty($pay_dates))
		if (in_array($date_s, $pay_dates))
			return $date_s;

	return false;
}

function func_remove_year_dates($year, $dates) {
	$year_start = mktime(0,0,0,1,1,$year);
	$year_end = mktime(0,0,0,1,1,$year+1)-1;
	$result = array();

	if (is_array($dates) && !empty($dates)) {
		foreach ($dates as $date) {
			if ($date < $year_start || $date > $year_end)
				$result[] = $date;
		}
	}

	return $result;
}

$__ge_res = false;

$year = date("Y");
if (isset($current_year))
	$year = $current_year;

if ($REQUEST_METHOD == "POST") {

	# fetch pay dates
	$res = func_query_first_cell("SELECT s.pay_dates FROM $sql_tbl[subscriptions] as s, $sql_tbl[products] as p WHERE s.productid=p.productid AND s.productid='$productid'");

	$tmp = array();
	if (!empty($res)) {
		# remove dates of current year
		$tmp = func_remove_year_dates($year, unserialize($res));
	}

	$pay_dates_array = array_filter(array_unique(array_values($pay_dates_array)), "func_callback_empty");
	$tmp = func_array_merge($tmp, $pay_dates_array);
	sort($tmp);
	$query_data = array(
		"pay_dates" => addslashes(serialize($tmp))
	);

	func_array2update("subscriptions", $query_data, "productid='$productid'");

	if ($geid && $fields_subscription == 'Y') {
		while ($pid = func_ge_each($geid, 100, $productid)) {
			func_array2update("subscriptions", $query_data, "productid IN ('".implode("','", $pid)."')");
		}
	}

	func_header_location("calendar.php?productid=$productid&current_year=$year".(empty($geid) ? "" : "&geid=".$geid));
}

#
# Retrieve $pay_dates array from database or if it's empty filling it
#
$query = "select s.*, p.product from $sql_tbl[subscriptions] as s, $sql_tbl[products] as p where s.productid=p.productid and s.productid='$productid'";
$subscription_info = func_query_first($query);

$product_name = $subscription_info["product"];

$pay_type = is_numeric($subscription_info["pay_period_type"])?"By Period (".$subscription_info["pay_period_type"]." days)":$subscription_info["pay_period_type"];

$pay_dates = array();
if ($subscription_info["pay_dates"] && empty($reset) && empty($reset2nil)) {
	$pay_dates = Unserialize($subscription_info["pay_dates"]);
} elseif (!empty($reset2nil)) {
	$pay_dates = func_remove_year_dates($year, unserialize($subscription_info["pay_dates"]));
} else {
	$pay_dates = func_remove_year_dates($year, unserialize($subscription_info["pay_dates"]));
	switch($subscription_info["pay_period_type"]) {
        case "Annually":
		$pay_dates[] = mktime(0,0,0,1,1,$year);
		break;
        case "Quarterly":
		$pay_dates[] = mktime(0,0,0,1,1,$year);
		$pay_dates[] = mktime(0,0,0,4,1,$year);
		$pay_dates[] = mktime(0,0,0,7,1,$year);
		$pay_dates[] = mktime(0,0,0,10,1,$year);
		break;
        case "Monthly":
		for($i = 1; $i <= 12; $i++) {
			$pay_dates[] = mktime(0,0,0,$i,1,$year);  # for start of months
		}
		break;
        case "Weekly":
		$first_day = mktime(0,0,0,1,1,$year);
		$last_day = mktime(0,0,0,12,31,$year);
		for ($i = $first_day; $i <= $last_day; $i+=86400) {
			if (date("w",$i) == 1)
				$pay_dates[] = mktime(0,0,0,date("m",$i),date("d",$i),$year);
		}
		break;
        default: # By Period
		$first_day = time();
		$last_day = mktime(0,0,0,12,31,$year);
		for ($i = $first_day; $i <= $last_day; $i+=(86400*$subscription_info["pay_period_type"])) {
			$pay_dates[] = mktime(0,0,0,date("m",$i),date("d",$i),date("y",$i));
		}
	}
}

#
# Filling array of months for $year
#
$year_array = array();
$days_array = array();

for($mn = 0; $mn < 12; $mn++) {

	$month_array = array();
	$wnum = 0;
	$flag = false;
	$date_s = "";

	for ($day = 1; $day < 32 && checkdate($mn+1,$day,$year); $day++) {
		$date_s = mktime(0,0,0,$mn+1,$day,$year);
		$date_array = getdate($date_s);
		if (is_date_pay($date_s)) {
			$month_array[$date_array["wday"]][$wnum]["date"] = $date_s;
			$days_array[] = $date_s;
		} else {
			$days_array[] = "";
		}
		$month_array[$date_array["wday"]][$wnum]["day"] = $date_s;
		$month_array[$date_array["wday"]][$wnum]["dayofyear"] = $date_array["yday"];
		$flag = true;
		if ($date_array["wday"] == 6) {
			$wnum++;
			$flag = false;
		}
	}

	if ($flag)
		$wnum++;

	$year_tmp["wnum"] = $wnum;
	$year_tmp["month_array"] = $month_array;
	$year_tmp["month"] = mktime(0,0,0,$mn+1,1,$year);
	$year_array[] = $year_tmp;
}


$smarty->assign("year_array", $year_array);
$smarty->assign("days_array", $days_array);
$smarty->assign("current_date", mktime(0,0,0,date("m"),date("d"),date("y")));
$smarty->assign("product", $product_name);
$smarty->assign("productid", $productid);
$smarty->assign("pay_type", $pay_type);

$avail_years = array();
for ($y = $config["Company"]["start_year"]; $y <= $config["Company"]["end_year"]+1; $y++) {
	$avail_years[] = $y;
}
$smarty->assign("avail_years", $avail_years);
$smarty->assign("current_year", $year);

$smarty->assign("geid", $geid);
?>
