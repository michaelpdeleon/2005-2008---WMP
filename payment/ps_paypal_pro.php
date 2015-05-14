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
# $Id: ps_paypal_pro.php,v 1.18.2.6 2006/07/29 09:42:26 max Exp $
#
# PayPal Website Payments Pro
#

if (!defined('XCART_START')) {
	require "./auth.php";

	$module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
}

x_session_register('cart');

$pp_locale_codes = array("AU","DE","FR","GB","IT","JP","US");
$pp_supported_charsets = array (
	"Big5", "EUC-JP", "EUC-KR", "EUC-TW", "gb2312", "gbk", "HZ-GB-2312",
	"ibm-862", "ISO-2022-CN", "ISO-2022-JP", "ISO-2022-KR", "ISO-8859-1",
	"ISO-8859-2", "ISO-8859-3", "ISO-8859-4", "ISO-8859-5", "ISO-8859-6",
	"ISO-8859-7", "ISO-8859-8", "ISO-8859-9", "ISO-8859-13", "ISO-8859-15",
	"KOI8-R", "Shift_JIS", "UTF-7", "UTF-8", "UTF-16", "UTF-16BE",
	"UTF-16LE", "UTF-32", "UTF-32BE", "UTF-32LE", "US-ASCII",
	"windows-1250", "windows-1251", "windows-1252", "windows-1253",
	"windows-1254", "windows-1255", "windows-1256", "windows-1257",
	"windows-1258", "windows-874", "windows-949", "x-mac-greek",
	"x-mac-turkish", "x-maccentraleurroman", "x-mac-cyrillic",
	"ebcdic-cp-us", "ibm-1047");

$pp_charset = func_query_first_cell("SELECT charset FROM $sql_tbl[countries] WHERE code='$shop_language'");
if (!in_array($pp_charset, $pp_supported_charsets)) {
	$pp_charset = "ISO-8859-1";
}

$pp_test = $module_params['testmode'];
$pp_username = $module_params['param01'];
$pp_password = $module_params['param02'];
$pp_currency = $module_params['param03'];
$pp_cert_file = $xcart_dir.'/payment/certs/'.$module_params['param04'];
$pp_signature = $module_params['param05'];
$pp_final_action = 'Sale'; # ($module_params['param05'] == 'S' ? 'Sale' : 'Authorization');
$pp_prefix = preg_replace("/[ '\"]+/","",$module_params['param06']);

$_location = ($res["protocol"] == "https") ? $https_location : $http_location;
$notify_url = $_location."/payment/ps_paypal.php?notify_from=pro";

$pp_dp_allowed = true;

$pp_dp_id = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].paymentid='$paymentid' AND $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid<>$sql_tbl[ccprocessors].paymentid AND $sql_tbl[payment_methods].active='Y'");
if (empty($pp_dp_id)) $pp_dp_allowed = false;

$pp_total = sprintf("%0.2f", $cart["subtotal"]);
$pp_use_cert = ($module_params['param07'] == 'C');
$pp_signature_txt = $pp_use_cert ? "" : "<Signature>".$pp_signature."</Signature>";

if ($pp_test == "N") {
	$pp_url = $module_params['param07'] == 'C' ? "https://api.paypal.com:443/2.0/" : "https://api-3t.paypal.com:443/2.0/";
	$pp_customer_url = "https://www.paypal.com";
} else {
	$pp_url = $module_params['param07'] == 'C' ? "https://api.sandbox.paypal.com:443/2.0/" : "https://api-aa.sandbox.paypal.com:443/2.0/";
	$pp_customer_url = "https://www.sandbox.paypal.com";
}

if ($REQUEST_METHOD == "GET" && $mode == "express") {
	# start express checkout
	x_session_register('paypal_token');

	$pp_return_url = $current_location.'/payment/ps_paypal_pro.php?mode=express_return';
	$pp_cancel_url = $xcart_catalogs['customer'].'/cart.php?mode=checkout';

	x_session_register('paypal_begin_express');
	$paypal_begin_express = false;
	x_session_save('paypal_begin_express');

	x_session_register("paypal_payment_id");
	x_session_register("paypal_mode");

	$paypal_payment_id = $payment_id;
	$paypal_mode = 'express';

	if (!empty($do_return) && !empty($paypal_token)) {
		$str_token = "<Token>$paypal_token</Token>";
	}

	$pp_locale_code = "US";
	if (in_array($shop_language, $pp_locale_codes)) {
		$pp_locale_code = $shop_language;
	}

	# send SetExpressCheckoutRequest to PayPal
	$request = <<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
		$pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI">
      <SetExpressCheckoutRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
          <ReturnURL>$pp_return_url</ReturnURL>
          <CancelURL>$pp_cancel_url</CancelURL>
          <PaymentAction>Authorization</PaymentAction>
		  $str_token
		  <LocaleCode>$pp_locale_code</LocaleCode>
        </SetExpressCheckoutRequestDetails>
      </SetExpressCheckoutRequest>
    </SetExpressCheckoutReq>
  </soap:Body>
</soap:Envelope>
EOT;
	$result = func_paypal_request($request);

	# receive SetExpressCheckoutResponse
	if ($result['success'] && !empty($result['Token'])) {
		$paypal_token = $result['Token'];
		# move to the PayPal
		func_header_location($pp_customer_url.'/webscr?cmd=_express-checkout&token='.$result['Token']);
	}

	$top_message['type'] = 'E';
	$top_message['content'] = $result['error']['ShortMessage'];
	func_header_location($pp_cancel_url);
}
else
if ($REQUEST_METHOD == "GET" && $mode == "express_return" && !empty($HTTP_GET_VARS['token'])) {
	# return from PayPal
	# send GetExpressCheckoutDetailsRequest
	$token = $HTTP_GET_VARS['token'];
	$request =<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
		$pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI">
      <GetExpressCheckoutDetailsRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <Token>$token</Token>
      </GetExpressCheckoutDetailsRequest>
    </GetExpressCheckoutDetailsReq>
  </soap:Body>
</soap:Envelope>
EOT;
	$result = func_paypal_request($request);

	$address = array (
		's_address' => preg_replace('![\s\n\r]+!s', ' ', $result['address']['Street1'])."\n".preg_replace('![\s\n\r]+!s', ' ', $result['address']['Street2']),
		's_city' => $result['address']['CityName'],
		's_county' => $result['address']['StateOrProvince'],
		's_state' => $result['address']['StateOrProvince'],
		's_country' => $result['address']['Country'],
		's_zipcode' => $result['address']['PostalCode']
	);

	x_session_register('login');
	x_session_register('login_type');

	if (!empty($login) && $login_type == 'C') {
		$profile_values = $address;

		foreach ($profile_values as $k=>$v) {
			$profile_values[$k] = addslashes($v);
		}
		func_array2update('customers', $profile_values, " login='".addslashes($login)."' AND usertype='C'");
	}
	else {
		x_load('crypt','user');

		#fill-in anonymous customer profile
		$_curtime = time();
		$pp_anon_user = array (
			'login' => func_generate_anonymous_username(),
			'usertype' => 'C',
			'password' => text_crypt($anonymous_password),
			'membershipid' => 0,
			'password_hint' => '',
			'password_hint_answer' => '',
			'title' => '', # unknown, unfortunally
			'firstname' => $result['FirstName'],
			'lastname' => $result['LastName'],
			'company' => '',
			'phone' => $result['ContactPhone'],
			'email' => $result['Payer'],
			'fax' => '',
			'url' => '',
			'first_login' => time(),
			'status' => 'Y',
			'referer' => @$RefererCookie,
			'pending_membershipid' => 0,
			'parent' => @$parent,
			'change_password' => '',
			'last_login' => $_curtime,
			'first_login' => $_curtime
		);

		foreach ($address as $k=>$v) {
			$pp_anon_user[$k] = $v;
			$pp_anon_user['b_'.substr($k,2)] = $v;
		}

		$profile_values = $pp_anon_user;

		foreach ($profile_values as $k=>$v) {
			$profile_values[$k] = addslashes($v);
		}

		# create new anonymous customer
		func_array2insert('customers', $profile_values);

		# make him logged in
		$login_type = 'C';
		$login = $pp_anon_user['login'];
		$logged = '';
		
		x_session_register("identifiers",array());
		$identifiers['C'] = array (
			'login' => $login,
			'login_type' => $login_type
		);

		if(!empty($active_modules['SnS_connector']))
			func_generate_sns_action("Login");
	}

	x_session_register("paypal_payment_id");
	x_session_register("paypal_express_details");
	$paypal_express_details = $result;

	func_header_location($xcart_catalogs['customer'].'/cart.php?paymentid='.$paypal_payment_id.'&mode=checkout');
}
else
if ($REQUEST_METHOD == 'POST' && $HTTP_POST_VARS["action"] == 'place_order' && $pp_dp_allowed) {
	# do DirectPayment
	$avs_codes = array (
		"A" => "Address Address only (no ZIP)",
		"B" => "International 'A'. Address only (no ZIP)",
		"C" => "International 'N'",
		"D" => "International 'X'. Address and Postal Code",
		"E" => "Not allowed for MOTO (Internet/Phone) transactions",
		"F" => "UK-specific X Address and Postal Code",
		"G" => "Global Unavailable",
		"I" => "International Unavailable",
		"N" => "None",
		"P" => "Postal Code only (no Address)",
		"R" => "Retry",
		"S" => "Service not Supported",
		"U" => "Unavailable",
		"W" => "Nine-digit ZIP code (no Address)",
		"X" => "Exact match. Address and five-digit ZIP code",
		"Y" => "Address and five-digit ZIP",
		"Z" => "Five-digit ZIP code (no Address)"
	);

	$cvv_codes = array (
		"M" => "Match",
		"N" => "No match",
		"P" => "Not Processed",
		"S" => "Service not Supported",
		"U" => "Unavailable",
		"X" => "No response"
	);

	include_once $xcart_dir."/include/cc_detect.php";

	$pp_total = sprintf("%0.2f", $cart["total_cost"]);

	$pp_cardtype = '';

	if (is_visa($userinfo["card_number"])) $pp_cardtype = "Visa";
	if (is_mc($userinfo["card_number"])) $pp_cardtype = "MasterCard";
	if (is_dc($userinfo["card_number"])) $pp_cardtype = "Discover";
	if (is_amex($userinfo["card_number"])) $pp_cardtype = "Amex";

	$payer = array();
	foreach ($userinfo as $k=>$v) {
		if (is_array($v)) continue;
		$payer[$k] = htmlspecialchars($v);
	}

	$payer_state = $payer['b_state'];

	if (!empty($REMOTE_ADDR)) $payer_ipaddress = $REMOTE_ADDR;
	else $payer_ipaddress = "127.0.0.1";

	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
	$pp_ordr = $pp_prefix.join("-",$secure_oid);

	$pp_exp_month = (int)substr($userinfo["card_expire"],0,2);
	$pp_exp_year = (2000+substr($userinfo["card_expire"],2,2));

	$s_name = "";
	if (!empty($payer['s_firstname'])) {
		$s_name = $payer['s_firstname'];
	} elseif (!empty($payer['firstname'])) {
		$s_name = $payer['firstname'];
	}

	if (!empty($payer['s_lastname'])) {
		$s_name .= (empty($s_name) ? "" : " ").$payer['s_lastname'];
	} elseif (!empty($payer['lastname'])) {
		$s_name .= (empty($s_name) ? "" : " ").$payer['lastname'];
	}

	if (!empty($s_name)) {
		$s_name = substr($s_name, 0, 32);
	}

	if (empty($payer['b_firstname'])) {
		$payer['b_firstname'] = empty($payer['firstname']) ? "Unknown" : $payer['firstname'];
	}
    if (empty($payer['b_lastname'])) {
        $payer['b_lastname'] = empty($payer['lastname']) ? "Unknown" : $payer['lastname'];
    }

	$request=<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
		$pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoDirectPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$pp_final_action</PaymentAction>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
            <ButtonSource>X-óart-DP</ButtonSource>
            <NotifyURL>$notify_url</NotifyURL>
			<ShipToAddress>
              <Name>$s_name</Name>
              <Street1>$payer[s_address]</Street1>
              <Street2>$payer[s_address_2]</Street2>
              <CityName>$payer[s_city]</CityName>
              <StateOrProvince>$payer[s_state]</StateOrProvince>
              <PostalCode>$payer[s_zipcode]</PostalCode>
              <Country>$payer[s_country]</Country>
            </ShipToAddress>
            <InvoiceID>$pp_ordr</InvoiceID>
            <Custom>$order_secureid</Custom>
          </PaymentDetails>
          <CreditCard>
            <CreditCardType>$pp_cardtype</CreditCardType>
            <CreditCardNumber>$payer[card_number]</CreditCardNumber>
            <ExpMonth>$pp_exp_month</ExpMonth>
            <ExpYear>$pp_exp_year</ExpYear>
            <CardOwner>
              <PayerStatus>verified</PayerStatus>
              <Payer>$payer[email]</Payer>
              <PayerName>
                <FirstName>$payer[b_firstname]</FirstName>
                <LastName>$payer[b_lastname]</LastName>
              </PayerName>
              <PayerCountry>$payer[b_country]</PayerCountry>
              <Address>
                <Street1>$payer[b_address]</Street1>
                <Street2>$payer[b_address_2]</Street2>
                <CityName>$payer[b_city]</CityName>
                <StateOrProvince>$payer_state</StateOrProvince>
                <Country>$payer[b_country]</Country>
                <PostalCode>$payer[b_zipcode]</PostalCode>
              </Address>
            </CardOwner>
            <CVV2>$payer[card_cvv2]</CVV2>
          </CreditCard>
          <IPAddress>$payer_ipaddress</IPAddress>
        </DoDirectPaymentRequestDetails>
      </DoDirectPaymentRequest>
    </DoDirectPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

	$result = func_paypal_request($request);

	$bill_output['code'] = 2;

	if ($result['success']) {
		$bill_output['code'] = 1;
		$bill_message = 'Accepted';
	}
	else {
		$bill_message = 'Declined';
	}

	$additional_fields = array();
	foreach (array('TransactionID') as $add_field) {
		if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
			$additional_fields[] = ' '.$add_field.': '.$result[$add_field];
	}

	if (!empty($additional_fields))
		$bill_message .= ' ('.implode(', ', $additional_fields).')';

	if (!empty($result['error'])) {
		$bill_message .= sprintf (
			" Error: %s (Code: %s, Severity: %s)",
			$result['error']['LongMessage'],
			$result['error']['ErrorCode'],
			$result['error']['Severity']);
	}

	$bill_output["billmes"] = $bill_message;

	if (isset($result['AVSCode'])) {
		$bill_output['avsmes'] = (empty($avs_codes[$result['AVSCode']]) ? "Code: ".$result['AVSCode'] : $avs_codes[$result['AVSCode']]);
	}

	if (isset($result['CVV2Code'])) {
		$bill_output['cvvmes'] = (empty($cvv_codes[$result['CVV2Code']]) ? "Code: ".$result['CVV2Code'] : $cvv_codes[$result['CVV2Code']]);
	}

	return;
}
else
if ($REQUEST_METHOD == 'POST' && $HTTP_POST_VARS["action"] == 'place_order') {
	$pp_total = sprintf("%0.2f", $cart["total_cost"]);

	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
	$pp_ordr = $pp_prefix.join("-",$secure_oid);

	# finish ExpressCheckout
	x_session_register("paypal_express_details");
	$request =<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
		$pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoExpressCheckoutPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$pp_final_action</PaymentAction>
          <Token>$paypal_express_details[Token]</Token>
          <PayerID>$paypal_express_details[PayerID]</PayerID>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
            <ButtonSource>X-óart-EC</ButtonSource>
            <NotifyURL>$notify_url</NotifyURL>
            <InvoiceID>$pp_ordr</InvoiceID>
            <Custom>$order_secureid</Custom>
          </PaymentDetails>
        </DoExpressCheckoutPaymentRequestDetails>
      </DoExpressCheckoutPaymentRequest>
    </DoExpressCheckoutPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

	$result = func_paypal_request($request);

	$bill_output['code'] = 2;

	if (!strcasecmp($result['PaymentStatus'],'Completed') || !strcasecmp($result['PaymentStatus'],'Processed')) {
		$bill_output['code'] = 1;
		$bill_message = 'Accepted';
	}
	else
	if (!strcasecmp($result['PaymentStatus'],'Pending')) {
		$bill_output['code'] = 3;
		$bill_message = 'Queued';
	}
	else {
		$bill_message = 'Declined';
	}

	$bill_message .= " Status: ".$result['PaymentStatus'];
	if (!empty($result['PendingReason']))
		$bill_message .= ' Reason: '.$result['PendingReason'];

	$additional_fields = array();
	foreach (array('TransactionID','TransactionType','PaymentType','GrossAmount','FeeAmount','SettleAmount','TaxAmount','ExchangeRate') as $add_field) {
		if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
			$additional_fields[] = ' '.$add_field.': '.$result[$add_field];
	}

	if (!empty($additional_fields))
		$bill_message .= ' ('.implode(', ', $additional_fields).')';

	if (!empty($result['error'])) {
		$bill_message .= sprintf (
			" Error: %s (Code: %s, Severity: %s)",
			$result['error']['LongMessage'],
			$result['error']['ErrorCode'],
			$result['error']['Severity']);
	}

	$bill_output["billmes"] = $bill_message;

	require $xcart_dir."/payment/payment_ccend.php";
}

function func_paypal_request($request, $regexp=false) {
	global $pp_url, $pp_cert_file, $pp_signature, $pp_use_cert;

	x_load('http');

	$post = explode("\n",$request);

	if ($pp_use_cert)
		list($headers, $response) = func_https_request("POST", $pp_url, $post, "", "", "text/xml", "", $pp_cert_file);
	else
		list($headers, $response) = func_https_request("POST", $pp_url, $post, "", "", "text/xml", "");

	if ($headers == "0") {
		return array(
			'success' => false,
			'error' => array('ShortMessage' => $response)
		);
	}

	$result = array (
		'headers' => $headers,
		'response' => $response
	);

	if (!empty($regexp)) {
		$matches = array();
		preg_match($regexp, $response, $matches);
		$result['matches'] = $matches;
	}

	#
	# Parse and fill common fields
	#
	$result['success'] = false;

	$ord_fields = array (
		'Ack',
		'TransactionID',
		'Token', # Note: expires after three hours (Express Checkout Integration Guide, p30)
		'AVSCode',
		'CVV2Code',
		'PayerID',
		'PayerStatus',
		'FirstName',
		'LastName',
		'ContactPhone',
		'TransactionType', # e.g. express-checokut
		'PaymentStatus', # e.g. Pending
		'PendingReason', # e.g. authorization
		'ReasonCode',
		'GrossAmount',
		'FeeAmount',
		'SettleAmount',
		'TaxAmount',
		'ExchangeRate'
	);

	foreach ($ord_fields as $field) {
		if (preg_match('!<'.$field.'[^>]+>([^>]+)</'.$field.'>!', $response, $out)) {
			$result[$field] = $out[1];
		}
	}

	if (!strcasecmp($result['Ack'], 'Success') || !strcasecmp($result['Ack'], 'SuccessWithWarning'))
		$result['success'] = true;

	if (preg_match('!<Payer(?:\s[^>]*)?>([^>]+)</Payer>!', $response, $out)) {
		$result['Payer'] = $out[1]; # e-mail address
	}

	if (preg_match('!<Errors[^>]*>(.+)</Errors>!', $response, $out_err)) {
		$error = array();

		if (preg_match('!<SeverityCode[^>]*>([^>]+)</SeverityCode>!', $out_err[1], $out))
			$error['SeverityCode'] = $out[1];

		if (preg_match('!<ErrorCode[^>]*>([^>]+)</ErrorCode>!', $out_err[1], $out))
			$error['ErrorCode'] = $out[1];

		if (preg_match('!<ShortMessage[^>]*>([^>]+)</ShortMessage>!', $out_err[1], $out))
			$error['ShortMessage'] = $out[1];

		if (preg_match('!<LongMessage[^>]*>([^>]+)</LongMessage>!', $out_err[1], $out))
			$error['LongMessage'] = $out[1];

		$result['error'] = $error;
	}

	if (preg_match('!<Address[^>]*>(.+)</Address>!', $response, $out)) {
		$out_addr = $out[1];
		$address = array();

		if (preg_match('!<Street1[^>]*>([^>]+)</Street1>!', $out_addr, $out))
			$address['Street1'] = $out[1];
		if (preg_match('!<Street2[^>]*>([^>]+)</Street2>!', $out_addr, $out))
			$address['Street2'] = $out[1];

		if (preg_match('!<CityName[^>]*>([^>]+)</CityName>!', $out_addr, $out))
			$address['CityName'] = $out[1];

		if (preg_match('!<StateOrProvince[^>]*>([^>]+)</StateOrProvince>!', $out_addr, $out))
			$address['StateOrProvince'] = $out[1];

		if (preg_match('!<Country[^>]*>([^>]+)</Country>!', $out_addr, $out))
			$address['Country'] = $out[1];

		if (preg_match('!<PostalCode[^>]*>([^>]+)</PostalCode>!', $out_addr, $out))
			$address['PostalCode'] = $out[1];

		if (preg_match('!<AddressOwner[^>]*>([^>]+)</AddressOwner>!', $out_addr, $out))
			$address['AddressOwner'] = $out[1];

		if (preg_match('!<AddressStatus[^>]*>([^>]+)</AddressStatus>!', $out_addr, $out))
			$address['AddressStatus'] = $out[1];

		$result['address'] = $address;
	}

	return $result;
}

?>
