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
# $Id: subscriptions.php,v 1.26 2006/02/13 12:36:44 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

$location[] = array(func_get_langvar_by_name("lbl_subscriptions_management"), "");

function get_subscription_info($login) {
	global $sql_tbl;

	return func_query("SELECT sc.*,p.product,o.date,o.status,s.* FROM $sql_tbl[subscription_customers] AS sc, $sql_tbl[products] AS p, $sql_tbl[orders] AS o, $sql_tbl[subscriptions] AS s WHERE sc.productid=p.productid AND sc.orderid=o.orderid AND sc.productid=s.productid AND sc.login='$login'");
}

$sub_status = array("Active","Unsubscribed","Disabled");

if ($current_area != "C") {

	if ($REQUEST_METHOD == "POST") {

		if ($charge == "Y") {
			func_header_location($xcart_catalogs['admin']."/pay_subscriptions.php?subscriptionid=$subscriptionid&key=".$config["Subscriptions"]["subscriptions_key"]);
		}

		db_query("UPDATE $sql_tbl[subscription_customers] SET subscription_status='$subscription_status' WHERE subscriptionid='$subscriptionid'");
		func_header_location("orders.php?$QUERY_STRING");
	}

	if ($action == "listall") {
		$search_productid = $search_producttitle = $search_customerlogin = "";
		$action = "search";
	}

	if ($action == "search") {
		$productid_condition = (!empty($search_productid)?" AND s.productid='$search_productid'":"");
		$producttitle_condition = (!empty($search_producttitle)?" AND p.product LIKE '%$search_producttitle%'":"");
		$customerlogin_condition = (!empty($search_customerlogin)?" AND sc.login LIKE '%$search_customerlogin%'":"");

		$products = func_query("SELECT s.*, p.product FROM $sql_tbl[subscriptions] AS s, $sql_tbl[products] AS p WHERE s.productid=p.productid".$productid_condition.$producttitle_condition);

		if ($products) {
			foreach ($products as $product) {

				$pay_dates = Unserialize($product["pay_dates"]);
				$product["next_pay_date"] = "undefined";
				if ($pay_dates) {
					foreach ($pay_dates as $pay_date) {
						if ($pay_date < mktime(0,0,0,date("m"),date("d"),date("Y")))
							continue;

						$product["next_pay_date"] = strftime($config["Appearance"]["date_format"], $pay_date);
						break;
					}
				}

				$product["subscribers_num"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[subscription_customers] WHERE productid='$product[productid]'");

				$customers = func_query("SELECT sc.*, o.status, o.date FROM $sql_tbl[subscription_customers] AS sc, $sql_tbl[orders] AS o WHERE sc.orderid=o.orderid AND sc.productid='$product[productid]' ".$customerlogin_condition." ORDER BY sc.login");
				if ($customers) {
					foreach ($customers as $k1=>$customer) {
						$customers[$k1]["last_payed_order_status"] = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid='$customer[last_payed_orderid]'");
						$customers[$k1]["date"] = strftime($config["Appearance"]["date_format"], $customer["date"]+$config["Appearance"]["timezone_offset"]);
						$customers[$k1]["last_payed_date"] = strftime($config["Appearance"]["date_format"], $customer["last_payed_date"]+$config["Appearance"]["timezone_offset"]);
					}
				}

				$product["customers"] = $customers;
				$subscriptions[] = $product;
			}
		}
	}

	$smarty->assign("products_num", !empty($action)?count($subscriptions):"No");
	$smarty->assign("sub_status", $sub_status);
}
else {

	if ($REQUEST_METHOD == "POST") {

		if ($unsubscribe_info) {
			foreach ($unsubscribe_info as $k=>$v) {
				db_query("UPDATE $sql_tbl[subscription_customers] SET subscription_status='Unsubscribed' WHERE subscriptionid='$v' AND login='".addslashes($login)."'");
			}
		}

		$cnt = func_query_first_cell("SELECT count(*) FROM $sql_tbl[subscription_customers] WHERE login='".addslashes($login)."' AND subscription_status!='Unsubscribed'");
		if ($cnt > 0)
			func_header_location("orders.php?$QUERY_STRING");
		else
			func_header_location("home.php");
	}

	$subscriptions = get_subscription_info($login);

	for ($i = 0; $i < count($subscriptions); $i++) {
		$subscriptions[$i]["date"] = strftime($config["Appearance"]["date_format"], $subscriptions[$i]["date"]);
		$subscriptions[$i]["last_payed_date"] = strftime($config["Appearance"]["date_format"], $subscriptions[$i]["last_payed_date"]);
		$subscriptions[$i]["last_payed_orderstatus"] = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid='".$subscriptions[$i]["last_payed_orderid"]."'");
		$subscriptions[$i]["next_pay_date"] = "undefined";

		$pay_dates = unserialize($subscriptions[$i]["pay_dates"]);
		if ($pay_dates) {
			foreach ($pay_dates as $pay_date) {
				if ($pay_date < mktime(0,0,0,date("m"),date("d"),date("Y")))
					continue;

				$subscriptions[$i]["next_pay_date"] = strftime($config["Appearance"]["date_format"], $pay_date);
				break;
			}
		}
	}
}


$smarty->assign("main","subscriptions");
$smarty->assign("subscriptions_info",$subscriptions);

?>
