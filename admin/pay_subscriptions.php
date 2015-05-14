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
# $Id: pay_subscriptions.php,v 1.46.2.6 2006/07/31 10:04:05 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/get_language.php";
require $xcart_dir."/include/cc_detect.php";

x_load('crypt','files','mail');

#
# Access verification
# (see section "Modifying and Deleting Products" in X-Cart manual for more details)
#

$is_admin_logged = !empty($login) && ($login_type=="A" || ($active_modules["Simple_Mode"] && $login_type=="P"));

if (!empty($REQUEST_METHOD) && !$is_admin_logged && (!isset($HTTP_GET_VARS['key']) || empty($HTTP_GET_VARS['key']) || $HTTP_GET_VARS['key'] != $config["Subscriptions"]["subscriptions_key"]) ) {
	func_header_location("error_message.php?access_denied&id=12");
}

$date_format_short = "m/d/Y";
$date_format_long  = "m/d/Y H:i:s";
#
# Enable auto addition of the new pay period for product subscription
#
$prolongate_subscriptions = "Y";

$logfile = $var_dirs["log"]."/pay_subscriptions.log";

#
# Authorize.Net CC processing module
#
function cc_processing($subscriptionid)
{

    global $store_cc, $config, $str, $sql_tbl;
	global $date_format_short;
	global $xcart_dir;
	global $active_modules, $single_mode;

    #
    # Set data for cc_processing
    #
    $userinfo = func_query_first("select sc.*, c.* from $sql_tbl[subscription_customers] as sc, $sql_tbl[customers] as c where sc.login=c.login and sc.subscriptionid='$subscriptionid'");

	$userinfo["card_number"] = text_decrypt($userinfo["card_number"]);
	if (is_null($userinfo["card_number"])) {
		x_log_flag("log_decrypt_errors", "DECRYPT", " Could not decrypt the field 'Card number' for the user ".$username, true);
	}
	$userinfo["card_cvv2"] = text_decrypt($userinfo["card_cvv2"]);
	if (is_null($userinfo["card_number"])) {
		x_log_flag("log_decrypt_errors", "DECRYPT", " Could not decrypt the field 'Card CVV2' for the user ".$username, true);
	}

	$bill_firstname = empty($userinfo['b_firstname']) ? $userinfo['firstname'] : $userinfo['b_firstname'];
	$bill_lastname = empty($userinfo['b_lastname']) ? $userinfo['lastname'] : $userinfo['b_lastname'];
	$bill_name = $bill_firstname;
	if (!empty($bill_lastname))
		$bill_name .= (empty($bill_firstname) ? "" : " ").$bill_lastname;

	$ship_firstname = empty($userinfo['s_firstname']) ? $userinfo['firstname'] : $userinfo['s_firstname'];
	$ship_lastname = empty($userinfo['s_lastname']) ? $userinfo['lastname'] : $userinfo['s_lastname'];
	$ship_name = $ship_firstname;
	if (!empty($ship_lastname))
		$ship_name .= (empty($ship_firstname) ? "" : " ").$ship_lastname;

    $product = func_query_first("select p.*, s.* from $sql_tbl[products] as p, $sql_tbl[subscriptions] as s where p.productid=s.productid and s.productid='$userinfo[productid]'");

    $cart["total_cost"] = $product["price_period"];

    #
    # Insert into orders
    #

	$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='".$config["active_subscriptions_processor"]."'");
	$payment_method = "Recurring billing (".$module_params["module_name"].")";

	db_query("INSERT INTO $sql_tbl[orders] (login, total, subtotal, shipping_cost, shippingid, tax, discount, coupon, coupon_discount, date, status, payment_method, flag, title, firstname, lastname, company, b_address, b_city, b_state, b_country, b_zipcode, s_address, s_city, s_state, s_country, s_zipcode, phone, fax, email) VALUES ('".addslashes($userinfo["login"])."', '".addslashes($product["price_period"])."', '".addslashes($product["price_period"])."','0', '0', '0', '0', '0', '0', '".time()."', 'I', '".addslashes($payment_method)."', 'N', '".addslashes($userinfo["title"])."', '".addslashes($userinfo["firstname"])."', '".addslashes($userinfo["lastname"])."', '".addslashes($userinfo["company"])."', '".addslashes($userinfo["b_address"])."', '".addslashes($userinfo["b_city"])."', '".addslashes($userinfo["b_state"])."', '".addslashes($userinfo["b_country"])."', '".addslashes($userinfo["b_zipcode"])."', '".addslashes($userinfo["s_address"])."', '".addslashes($userinfo["s_city"])."', '".addslashes($userinfo["s_state"])."', '".addslashes($userinfo["s_country"])."', '".addslashes($userinfo["s_zipcode"])."', '".addslashes($userinfo["phone"])."', '".addslashes($userinfo["fax"])."', '".addslashes($userinfo["email"])."')");
	$orderid = db_insert_id();
	$secure_oid = array($orderid);

    #
    # Include needed cc_processing module
    #
	global $HTTP_SERVER_VARS;
	$REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	if (empty($REMOTE_ADDR)) {
		$REMOTE_ADDR = '127.0.0.1';
	}

	require $xcart_dir."/payment/".basename($module_params["processor"]);

    if ($bill_output["code"] == 1) {
        $order_status = "P";
    } else {
		$order_status = "F";
		$orderstatus_set = "Disabled";
	}

	if ($userinfo["orderid"]) {
		$order_details = func_query_first_cell("SELECT details FROM $sql_tbl[orders] WHERE orderid = '".$userinfo["orderid"]."' AND login = '".$userinfo["login"]."'");
		$order_details_crypt_type = func_get_crypt_type($order_details);
		$order_details = text_decrypt($order_details)."\n";
        if ($order_details === false || is_null($order_details)) {
            $order_details = func_get_langvar_by_name("txt_this_data_encrypted");
        	if (is_null($order_details) && ($order_details_crypt_type != 'C' || func_get_crypt_key("C") !== false))
				x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt order details for the order ".$userinfo["orderid"], true);
		}
	}
	$order_details .= $bill_output["billmes"];

    #
    # Update order status
    #
	$query_data = array(
		"status" => addslashes($order_status),
		"details" => addslashes(text_crypt($order_details))
	);
	func_array2update("orders", $query_data, "orderid = '$orderid'");

    #
    # Insert into order details
    #
	$extra_data = array();
	if ($userinfo["orderid"])
		$extra_data = @unserialize(func_query_first_cell("SELECT extra_data FROM $sql_tbl[order_details] WHERE orderid='".$userinfo["orderid"]."' AND productid = '".$product['productid']."'"));
	$extra_data["display"]["price"] = price_format($product['price_period']);
	$extra_data["display"]["discounted_price"] = price_format($product['price_period']);
	$extra_data["display"]["subtotal"] = price_format($product['price_period']);

	$query_data = array(
		"orderid" => $orderid,
		"productid" => $product['productid'],
		"amount" => 1,
		"price" => $product['price_period'],
		"provider" => addslashes($product["provider"]),
		"extra_data" => addslashes(serialize($extra_data))
	);
	$product['itemid'] = func_array2insert("order_details", $query_data);

	$query_data = array(
		"last_payed_date" => time(),
		"last_payed_orderid" => $orderid,
	);
	if (!empty($orderstatus_set))
		$query_data['subscription_status'] = $orderstatus_set;
	func_array2update("subscription_customers", $query_data, "subscriptionid = '$subscriptionid'");

	if (!empty($active_modules['XAffiliate'])) {
		$partner = func_query_first_cell("SELECT login FROM $sql_tbl[partner_payment] WHERE orderid = '$userinfo[orderid]' AND affiliate = ''");
		$product['price'] = $cart["total_cost"];
		$products = array($product);
		$current_order = array("provider" => $product['provider']);
		include $xcart_dir."/include/partner_commission.php";
	}
    $str .= "    order id:........".$orderid."\n";
    $str .= "    pay date:........".date($date_format_short)."\n";
    $str .= "    login:...........$userinfo[login]\n";
    $str .= "    email:...........$userinfo[email]\n";
    $str .= "    first name:......$userinfo[firstname]\n";
    $str .= "    last name:.......$userinfo[lastname]\n";
    $str .= "    billing address:.$userinfo[b_address]\n";
    $str .= "    zip code:........$userinfo[b_zipcode]\n";
    $str .= "    card number:.....$userinfo[card_number]\n";
    $str .= "    card expire:.....$userinfo[card_expire]\n";
    $str .= "    total cost:......$product[price_period]\n";
    $str .= "          Status:....$order_status / $bill_output[billmes]\n\n";

    return $result;
}

$curdate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

$str = "-------------------------------------------------------------\n";
$str .= date($date_format_long,time())." - script launching time\n";


$subs_pg_avail = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ccprocessors] WHERE processor = '".$config["active_subscriptions_processor"]."'");
if ($subs_pg_avail < 1) {
	$str .= "\n".func_get_langvar_by_name("txt_subscription_no_payment_warn", array(), false, true)."\n";

} elseif (!empty($subscriptionid)) {

    #
    # Get info about subsription products
    #
    $product = func_query_first("SELECT p.productid, p.product FROM $sql_tbl[subscription_customers] as sc, $sql_tbl[products] as p WHERE sc.subscriptionid = '$subscriptionid' AND sc.productid = p.productid");

    $str .= "#$product[productid] $product[product]:\n";
    cc_processing($subscriptionid);

} else {

	#
	# Get info about subsription products
	#
	$products = func_query("SELECT $sql_tbl[subscriptions].*, $sql_tbl[products].product FROM $sql_tbl[subscriptions], $sql_tbl[products] WHERE $sql_tbl[subscriptions].productid = $sql_tbl[products].productid");

	if ($products) {

		foreach ($products as $k => $product) {

			$pay_dates = unserialize($product["pay_dates"]);
			$next_pay_date = "";
			if (!$pay_dates)
				continue;

			foreach ($pay_dates as $pay_date) {
				if ($pay_date == $curdate) {
					$next_pay_date = $pay_date;
					break;
				}
			}

			if (!$next_pay_date)
				continue;


			$str .= "#$product[productid] $product[product]:\n";

			# Get info about customers-subsribers
			$customers = func_query("SELECT $sql_tbl[orders].status, $sql_tbl[subscription_customers].* FROM $sql_tbl[subscription_customers], $sql_tbl[orders], $sql_tbl[subscriptions] WHERE $sql_tbl[subscription_customers].orderid = $sql_tbl[orders].orderid AND $sql_tbl[subscription_customers].productid = '$product[productid]' AND $sql_tbl[subscription_customers].productid = $sql_tbl[subscriptions].productid");

			if (!empty($customers)) {
				foreach ($customers as $customer) {
					if (($customer["status"] == "P" || $customer["status"] == "C") && !in_array($customer["subscription_status"], array("Unsubscribed","Disabled")) && (empty($subscriptionid) || $customer["subscriptionid"] == $subscriptionid))
						cc_processing($customer["subscriptionid"]);
				}
			}

			if ($prolongate_subscriptions != "Y")
				continue;

			$lastdate = max($pay_dates);
			$newdate = 0;
			$l_year = intval(date("Y", $lastdate));
			$l_month = date("n", $lastdate);
			$l_day = date("j", $lastdate);
			switch ($product["pay_period_type"]) {
				case "Annually":
					$l_year++;
					$l_month = 1;
					$l_day = 1;
					break;

				case "Quarterly":
					$l_quart = floor(($l_month-1) / 3);
					if (++$l_quart > 3) {
						$l_quart = 0;
						$l_year++;
					}
					$l_month = ($l_quart*3)+1;
					$l_day = 1;
					break;

				case "Monthly":
					$l_month++;
					if ($l_month > 12) {
						$l_month = 1;
						$l_year++;
					}
					$l_day = 1;
					break;

				case "Weekly":
					$last_weekday = date("w", $lastdate);
					$l_day += (8 - $last_weekday) % 8;
					break;

				default: # By Period
					$newdate = $lastdate + 86400 * $product["pay_period_type"];
			}

			if (!$newdate)
				$newdate = mktime(0, 0, 0, $l_month, $l_day, $l_year);
			#
			# Subscription prolongation
			#
			if ($newdate != $lastdate && $lastdate - $curdate < $newdate - $lastdate) {
				$str .= "  Subscription for product is prolongated:\n";
				$str .= "     new pay date:...".date($date_format_short, $newdate)."\n\n";
				$pay_dates[] = $newdate;
				sort($pay_dates);
				func_array2update("subscriptions", array("pay_dates" => addslashes(serialize($pay_dates))), "productid = '$product[productid]'");
			}
        }
    }
}

$mail_smarty->assign ("str", $str);
if ($config['Subscriptions']['eml_recurring_notification'] == 'Y')
	func_send_mail($config["Company"]["orders_department"], "mail/recurring_notification_subj.tpl", "mail/recurring_notification_admin.tpl", $config["Company"]["orders_department"], true, true);

$fp = func_fopen($logfile, "a+", true);
fwrite($fp, $str);
fclose($fp);
@chmod($logfile, 0666);

echo "<pre><tt>$str</tt></pre>";

?>
