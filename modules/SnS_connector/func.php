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
# $Id: func.php,v 1.26.2.4 2006/08/14 10:47:45 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
#
# Functions for SnS connector module
#

define('PERSONALIZE_CLIENT_ID', 'personal_client_id');

function func_generate_sns_action($action, $param = false, $is_pure = false) {
	global $sql_tbl, $config, $HTTP_COOKIE_VARS, $active_modules, $sns_collector_path_url, $current_location, $sns_ext_actions;

	x_load('http','user');

	$actions = array("AddToCart","DeleteFromCart","CartChanged","FeaturedProductSelected","BestsellerSelected","Order","Register","Login","FillContactForm","SiteSearch","AdvancedSearch","ViewLegalInfo","ViewHelp","AddToWishList","WriteReview");

	if (empty($action) || empty($sns_collector_path_url) || (!in_array($action, $actions) && !isset($sns_ext_actions[$action])))
		return false;

	$host = parse_url($sns_collector_path_url."/event.".$config['SnS_connector']['sns_script_extension']);
	if (empty($host['host']) || empty($host['path']))
		return false;

	$ts = time();

	$post = array();
	$chain = array($action);
	foreach ($chain as $action) {
		$cpost = "";
		switch ($action) {
		case "AddToCart":
		case "DeleteFromCart":
			if ($param === false) {
				global $productid;
			}
			else {
				$productid = $param;
			}

			$tmp = func_sns_select_product($productid);
			if (empty($tmp))
				return false;

			if ($is_pure) {
				$cpost = $tmp;
				break;
			}

			$post[] = "name=$action&".$tmp;
			$action = "CartChanged";
			/* FALL-THROUGH */
		case "CartChanged":
			global $cart;

			$cpost = "itemsCount=".((empty($cart['products']) || !is_array($cart['products'])) ? 0 : intval(@count($cart['products'])))."&total=".price_format($cart['total_cost']);
			break;
		case "BestsellerSelected":
		case "AddToWishList":
		case "FeaturedProductSelected":
			if ($param === false) {
				global $productid;
			}
			else {
				$productid = $param;
			}

			$cpost = func_sns_select_product($productid);
			if (empty($cpost))
				return false;
			break;
		case "Order":
			if ($param === false) {
				global $orderid;
			}
			else {
				$orderid = $param;
			}

			x_load('order');

			$order = func_order_data($orderid);
			if (empty($order) || !in_array($order['order']['status'], array("P","C")) || (empty($order['products']) && empty($order['giftcerts'])))
				return false;

			$ts = $order['order']['date']-$config["Appearance"]["timezone_offset"];
			if (!empty($order['products'])) {
				foreach ($order['products'] as $i) {
					$tmp = func_sns_select_product($i['productid']);
					if (!empty($tmp)) {
						$cpost[] = "profile_id=".urlencode($order['userinfo']['login'])."&orderId=$orderid&total=".round($i['amount']*$i['price'], 2)."&billing_country=".urlencode($order['userinfo']['b_countryname'])."&billing_city=".urlencode($order['userinfo']['b_city'])."&billing_company=".urlencode($order['userinfo']['company'])."&billing_fax=".urlencode($order['userinfo']['fax'])."&billing_phone=".urlencode($order['userinfo']['phone'])."&billing_address=".urlencode($order['userinfo']['b_address'])."&billing_state=".urlencode($order['userinfo']['b_statename'])."&billing_zipcode=".urlencode($order['userinfo']['b_zipcode'])."&billing_firstname=".urlencode($order['userinfo']['firstname'])."&billing_lastname=".urlencode($order['userinfo']['lastname'])."&email=".urlencode($order['userinfo']['email'])."&quantity=$i[amount]&".$tmp;
					}
				}
			}

			if (!empty($order['giftcerts'])) {
				foreach ($order['giftcerts'] as $i) {
					$cpost[] = "profile_id=".urlencode($order['userinfo']['login'])."&orderId=$orderid&total=".$i['amount']."&billing_country=".urlencode($order['userinfo']['b_countryname'])."&billing_city=".urlencode($order['userinfo']['b_city'])."&billing_company=".urlencode($order['userinfo']['company'])."&billing_fax=".urlencode($order['userinfo']['fax'])."&billing_phone=".urlencode($order['userinfo']['phone'])."&billing_address=".urlencode($order['userinfo']['b_address'])."&billing_state=".urlencode($order['userinfo']['b_statename'])."&billing_zipcode=".urlencode($order['userinfo']['b_zipcode'])."&billing_firstname=".urlencode($order['userinfo']['firstname'])."&billing_lastname=".urlencode($order['userinfo']['lastname'])."&email=".urlencode($order['userinfo']['email'])."&quantity=1&productId=$i[gcid]&productName=GIFT CERTIFICATE&categoryName=";
				}
			}
			break;
		case "Register":
			global $uname, $usertype;

			$userinfo = func_userinfo($uname, $usertype);
			/* FALL-THROUGH */
		case "Login":
			if (!isset($userinfo)) {
				global $login, $login_type;
				$userinfo = func_userinfo($login, $login_type);
			}

			$cpost = "profile_id=$userinfo[login]&billing_country=".urlencode($userinfo['b_country'])."&billing_city=".urlencode($userinfo['b_city'])."&billing_company=".urlencode($userinfo['company'])."&billing_fax=".urlencode($userinfo['fax'])."&billing_phone=".urlencode($userinfo['phone'])."&billing_address=".urlencode($userinfo['b_address'])."&billing_state=".urlencode($userinfo['b_state'])."&billing_zipcode=".urlencode($userinfo['b_zipcode'])."&billing_firstname=".urlencode($userinfo['firstname'])."&billing_lastname=".urlencode($userinfo['lastname'])."&email=".urlencode($userinfo['email']);
			break;
		case "FillContactForm":
			global $contact, $body;

			$cpost = "billing_country=".urlencode($contact['b_country'])."&billing_city=".urlencode($contact['b_city'])."&billing_company=".urlencode($contact['company'])."&billing_fax=".urlencode($contact['fax'])."&billing_phone=".urlencode($contact['phone'])."&billing_address=".urlencode($contact['b_address'])."&billing_state=".urlencode($contact['b_state'])."&billing_zipcode=".urlencode($contact['b_zipcode'])."&billing_firstname=".urlencode($contact['firstname'])."&billing_lastname=".urlencode($contact['lastname'])."&email=".urlencode($userinfo['email'])."&enquiry=".urlencode($body);
			break;
		case "SiteSearch":
			global $posted_data;

			$cpost = "searchPhrase=".urlencode($posted_data['substring']);
			break;
		case "AdvancedSearch":
			global $posted_data;

			$post[] = "name=SiteSearch&searchPhrase=".urlencode($posted_data['substring']);
			$cat = "";
			if (!empty($posted_data["categoryid"])) {
				$ids = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid = '$posted_data[categoryid]'");
				$ids = explode("/", $ids);
				$cat = array();
				foreach ($ids as $catid)
					$cat[] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid = '$catid'");
				$cat = implode("/", $cat);
			}

			$cpost = "searchPhrase=".urlencode($posted_data['substring'])."&categoryName=".urlencode($cat);
			break;
		case "ViewLegalInfo":
			if ($param === false) {
				global $section;
				$cpost = "pageName=".urlencode($section);
			}
			else {
				$cpost = "pageName=".urlencode($param);
			}
			break;
		case "ViewHelp":
			global $current_location, $REQUEST_URI;

			$tmp = parse_url($current_location);
			$cpost = "pageUrl=".urlencode($tmp['scheme']."://".$tmp['host'].$REQUEST_URI);
			break;
		case "WriteReview":
			global $review_message, $productid;

			$cpost = func_sns_select_product($productid);
			if (empty($cpost))
				return false;

			$cpost .= "&reviewText=".urlencode(stripslashes($review_message));
			break;

		default:
			if (isset($sns_ext_actions[$action]) && !empty($sns_ext_actions[$action]) && function_exists($sns_ext_actions[$action])) {
				if (!$sns_ext_actions[$action]($cpost, $param))
					continue;
			} else {
				continue;
			}
		}

		if (is_array($cpost)) {
			foreach ($cpost as $cp) {
				$post[] = "name=$action&".$cp;
			}
		}
		else {
			$post[] = "name=$action&".$cpost;
		}
	}

	if (empty($post))
		return false;

	$static_post = "clientId=".func_get_sns_client_id()."&sessionId=$HTTP_COOKIE_VARS[personal_session_id]&timestamp=".$ts."&shopDisplayName=".urlencode($config['SnS_connector']['sns_shop_display_name'])."&passphrase=".urlencode($config['SnS_connector']['sns_passphrase'])."&site=".urlencode($current_location);
	foreach ($post as $k => $v) {
		if (empty($v)) {
			unset($post[$k]);
			continue;
		}

		$post[$k] = $static_post."&".urlencode("actions[$k]")."=".urlencode($v);
	}

global $xcart_dir;
$fp = fopen($xcart_dir."/var/snsn.log", "a");
fwrite($fp, date("m/d/Y H:i:s ").implode("&",$post)."\n\n");
fclose($fp);

	list($head, $res) = func_http_post_request($host['host'], $host['path'], implode("&",$post));

	return (strpos($head['ERROR'],"200") !== false && strpos($res,"External event registered") !== false);
}

#
#
#
function func_sns_select_product($productid) {
	global $sql_tbl;

	$tmp = func_query_first("SELECT $sql_tbl[products].product, $sql_tbl[categories].categoryid_path FROM $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products].productid = '$productid' AND $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid");
	if (empty($tmp))
		return false;

	$cats = explode("/", $tmp['categoryid_path']);
	$cat = array();
	foreach ($cats as $v) {
		$cat[] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid = '$v'");
	}

	return "productId=$productid&productName=".urlencode($tmp['product'])."&categoryName=".urlencode(implode("/", $cat));
}

function func_get_sns_client_id() {
	global $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS;

	$client_id = (int)$HTTP_COOKIE_VARS[constant('PERSONALIZE_CLIENT_ID')];
	if (!empty($client_id)) {
		return $client_id;
	}

	$remote_addr = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	$forwarded_for = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
	if (!empty($forwarded_for)) {
		$remote_addr = substr($forwarded_for.", ".$remote_addr, 0, 255);
	}

	$accept_language = $HTTP_SERVER_VARS["HTTP_ACCEPT_LANGUAGE"];
	$user_agent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];

	return func_xor(func_xor(crc32($remote_addr),crc32($accept_language)), crc32($user_agent));
}

function func_sns_exec_actions(&$sns_actions) {
	if (empty($sns_actions))
		return false;

	foreach ($sns_actions as $a => $v) {
		foreach ($v as $v2) {
			func_generate_sns_action($a, $v2);
		}
	}
	$sns_actions = array();
}
?>
