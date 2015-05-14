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
# $Id: ps_gcheckout.php,v 1.1.2.2 2006/07/29 09:29:29 max Exp $
#
# Google checkout
#

if (!defined('XCART_START')) {
	require "./auth.php";

	$module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'ps_gcheckout.php'");
	$is_standalone = true;
}

set_time_limit(86400);

x_load("payment");

if ($mode == 'continue' && !empty($skey)) {

	# Return from Google checkout
	require($xcart_dir."/payment/payment_ccview.php");
}

if ($is_standalone)
	exit;

x_load("http", "xml");

$module_params['param03'] = "USD";

$xml_url = $module_params['testmode'] == 'Y' ? "https://sandbox.google.com:443/cws/v2/Merchant/".$module_params['param01']."/request" : "https://checkout.google.com:443/cws/v2/Merchant/".$module_params['param01']."/request";

function hmac_sha1($data, $key) {
	$blocksize = 64;

	if (strlen($key) > $blocksize) {
		$key = pack('H*', sha1($key));
	}

	$key = str_pad($key, $blocksize, chr(0x00));
	$ipad = str_repeat(chr(0x36), $blocksize);
	$opad = str_repeat(chr(0x5c), $blocksize);
	$hmac = pack('H*', sha1(($key^$opad).pack('H*', sha1(($key^$ipad).$data))));

	return $hmac;
}

function google_encode($str) {
	return str_replace(array("&", "<", ">"), array("&#x26;", "&#x3c;", "&#x3e;"), $str);
}

$id = $order_secureid;

db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

$items = array();
if (!empty($products)) {
	foreach ($products as $p) {
		if (!empty($p['descr']))
			$p['descr'] = func_query_first_cell("SELECT descr FROM $sql_tbl[products] WHERE productid = '$p[productid]'");

		$p['product'] = google_encode($p['product']);
		$p['descr'] = google_encode($p['descr']);
		$items[] = <<<ITEM
				<item-name>$p[product]</item-name>
				<item-description>$p[descr]</item-description>
				<unit-price currency="$module_params[param03]">$p[price]</unit-price>
				<quantity>$p[amount]</quantity>
ITEM;
	}
}

if (!empty($cart["giftcerts"])) {
	foreach ($cart["giftcerts"] as $g) {
		$items[] = <<<ITEM
				<item-name>GIFT CERTIFICATE #$g[gcid]</item-name>
				<item-description>GIFT CERTIFICATE #$g[gcid]</item-description>
				<unit-price currency="$module_params[param03]">$g[amount]</unit-price>
				<quantity>1</quantity>
ITEM;

	}
}

if ($cart['tax_cost'] > 0) {
	$items[] = <<<ITEM
				<item-name>Taxes</item-name>
				<item-description>Taxes</item-description>
				<unit-price currency="$module_params[param03]">$cart[tax_cost]</unit-price>
				<quantity>1</quantity>
ITEM;
}

if ($cart['shipping_cost'] > 0) {
	$items[] = <<<ITEM
				<item-name>Shipping</item-name>
				<item-description>Shippings</item-description>
				<unit-price currency="$module_params[param03]">$cart[shipping_cost]</unit-price>
				<quantity>1</quantity>
ITEM;
}

$items = "\t\t\t<item>\n".implode("\t\t\t</item>\n\t\t\t<item>\n", $items)."\t\t\t</item>\n";

$cart_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">
	<shopping-cart>
		<merchant-private-data>
			<merchant-note>$id</merchant-note>
		</merchant-private-data>
		<items>$items</items>
	</shopping-cart>
	<checkout-flow-support>
		<merchant-checkout-flow-support>
			<edit-cart-url>$xcart_catalogs[customer]/cart.php?mode=checkout</edit-cart-url>
			<continue-shopping-url>$current_location/payment/ps_gcheckout.php?mode=continue&#x26;skey=$id</continue-shopping-url>
		</merchant-checkout-flow-support>
	</checkout-flow-support>
</checkout-shopping-cart>
XML;

$cart_xml = trim($cart_xml);

$auth = base64_encode($module_params['param01'].":".$module_params['param02']);

$h = array(
	"Authorization" => "Basic ".$auth,
	"Accept" => "application/xml"
);

list($headers, $return) = func_https_request("POST", $xml_url, array($cart_xml), "", "", "application/xml", "", "", "", $h);

$parse_errors = false;
$options = array(
	'XML_OPTION_CASE_FOLDING' => 1,
	'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
);
$parsed = func_xml_parse($return, $parse_errors, $options);

$redirect_url = func_array_path($parsed, "CHECKOUT-REDIRECT/REDIRECT-URL/0/#");

if ($redirect_url) {
	func_header_location($redirect_url);

} else {

	$error = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#");
	$bill_output['code'] = 2;
	if ($error)
		$bill_output['mess'] = "Error: ".($error ? $error : "Unknown");
	else
		$bill_output['mess'] = "Error: Unknown";
}

?>
