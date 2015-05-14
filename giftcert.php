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
# $Id: giftcert.php,v 1.13 2006/02/10 14:15:16 max Exp $
#

define("NUMBER_VARS", "amount");
require "./auth.php";

x_load('user');

if (empty($active_modules["Gift_Certificates"])) {
	func_header_location("home.php");
}

x_session_register("cart");

if (empty($mode)) $mode = "";

if (!$config["Gift_Certificates"]["min_gc_amount"])
	$config["Gift_Certificates"]["min_gc_amount"] = 0;

if (!$config["Gift_Certificates"]["max_gc_amount"])
	$config["Gift_Certificates"]["max_gc_amount"] = 0;

#
# Gift certificates module
#
if (!empty($gcid) && !empty($login)) {
	$gc_array = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");
	if (count($gc_array) == 0)
		$gc_array = "";

	$smarty->assign("gc_array", $gc_array);
}
elseif ($mode == "gc2cart" || $mode == "addgc2wl" || $mode == "preview") {
	$fill_error = (empty($purchaser) || empty($recipient));
	$amount_error = (($amount < $config["Gift_Certificates"]["min_gc_amount"]) || ($config["Gift_Certificates"]["max_gc_amount"] > 0 && $amount > $config["Gift_Certificates"]["max_gc_amount"]));

	#
	# Add GC to cart
	#
	if ($send_via == "E") {
		#
		# Send via Email
		#
		$fill_error = ($fill_error || empty($recipient_email));

		$giftcert = array (
			"purchaser" => stripslashes($purchaser),
			"recipient" => stripslashes($recipient),
			"message" => stripslashes($message),
			"amount" => $amount,
			"send_via" => $send_via,
			"recipient_email" => $recipient_email
		);
	}
	else {
		#
		# Send via Postal Mail
		#
		$has_states = (func_query_first_cell("SELECT display_states FROM $sql_tbl[countries] WHERE code = '".$recipient_country."'") == 'Y');
		$fill_error = ($fill_error || empty($recipient_firstname) || empty($recipient_lastname) || empty($recipient_address) || empty($recipient_city) || empty($recipient_zipcode) || (empty($recipient_state) && $has_states) || empty($recipient_country) || (empty($recipient_county) && $has_states && $config["General"]["use_counties"] == "Y"));

		if (empty($gc_template) || $config['Gift_Certificates']['allow_customer_select_tpl'] != 'Y') {
			$gc_template = $config['Gift_Certificates']['default_giftcert_template'];
		}
		else {
			$gc_template = stripslashes($gc_template);
		}

		$giftcert = array (
			"purchaser" => stripslashes($purchaser),
			"recipient" => stripslashes($recipient),
			"message" => stripslashes($message),
			"amount" => $amount,
			"send_via" => $send_via,
			"recipient_firstname" => stripslashes($recipient_firstname),
			"recipient_lastname" => stripslashes($recipient_lastname),
			"recipient_address" => stripslashes($recipient_address),
			"recipient_city" => stripslashes($recipient_city),
			"recipient_zipcode" => $recipient_zipcode,
			"recipient_county" => $recipient_county,
			"recipient_countyname" => func_get_county($recipient_county),
			"recipient_state" => $recipient_state,
			"recipient_statename" => func_get_state($recipient_state, $recipient_country),
			"recipient_country" => $recipient_country,
			"recipient_countryname" => func_get_country($recipient_country),
			"recipient_phone" => $recipient_phone,
			"tpl_file" => $gc_template
		);
	}

	#
	# If gcindex is empty - add
	# overwise - update
	#
	if (!$fill_error && !$amount_error) {
		if (!empty($active_modules["Gift_Certificates"]) && $mode == "addgc2wl") {
			include $xcart_dir."/modules/Wishlist/wishlist.php";
		}

		if ($mode == "preview") {
			$smarty->assign("giftcerts", array($giftcert));

			header("Content-Type: text/html");
			header("Content-Disposition: inline; filename=giftcertificates.html");

			$_tmp_smarty_debug = $smarty->debugging;
			$smarty->debugging = false;

			func_display("modules/Gift_Certificates/gc_customer_print.tpl",$smarty);
			$smarty->debugging = $_tmp_smarty_debug;
			exit;
		}

		if (isset($gcindex) && isset($cart["giftcerts"][$gcindex]))
			$cart["giftcerts"][$gcindex] = $giftcert;
		else
			$cart["giftcerts"][] = $giftcert;

		func_header_location("cart.php");
	}
}
elseif ($mode == "delgc") {
	#
	# Remove GC from cart
	#
	array_splice($cart["giftcerts"],$gcindex,1);
	func_header_location("cart.php");
}

require $xcart_dir."/include/categories.php";

if ($active_modules["Manufacturers"])
	include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

require $xcart_dir."/include/countries.php";
require $xcart_dir."/include/states.php";
if ($config["General"]["use_counties"] == "Y")
	include $xcart_dir."/include/counties.php";

if (empty($fill_error) && empty($amount_error)) {
	if ($action == "wl") {
		$smarty->assign("giftcert", unserialize(func_query_first_cell("SELECT object FROM $sql_tbl[wishlist] WHERE wishlistid='$gcindex'")));
		$smarty->assign("action", "wl");
		$smarty->assign("wlitem", $gcindex);
	}
	elseif (isset($gcindex) && isset($cart["giftcerts"][$gcindex])) {
		$smarty->assign("giftcert",@$cart["giftcerts"][$gcindex]);
	}
}
else {
	$smarty->assign("giftcert",$giftcert);
	$smarty->assign("fill_error",$fill_error);
	$smarty->assign("amount_error",$amount_error);
}

if (!empty($login))
	$smarty->assign("userinfo", func_userinfo($login, "C"));

$smarty->assign("min_gc_amount", $config["Gift_Certificates"]["min_gc_amount"]);
$smarty->assign("max_gc_amount", $config["Gift_Certificates"]["max_gc_amount"]);

x_session_save();

$smarty->assign("default_fields",
	array(
		"recipient_state" => array("avail" => "Y", "required" => "Y"),
		"recipient_country" => array("avail" => "Y", "required" => "Y")
	)
);

$smarty->assign("main","giftcert");

$location[] = array(func_get_langvar_by_name("lbl_gift_certificate", ""));

$smarty->assign('gc_templates', func_gc_get_templates($smarty->template_dir));

# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);
?>
