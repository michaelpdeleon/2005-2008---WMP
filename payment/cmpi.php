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
# $Id: cmpi.php,v 1.26.2.3 2006/09/15 06:16:15 max Exp $
#
# Cardinal payment authentication
# Thin Client
#

if ( !defined('XCART_START') ) {
	require "./auth.php";

	if ($config['CMPI']['cmpi_enabled'] != 'Y') {
		header("Location: ../");
		die("Access denied");
	}

	$stand_alone = true;

} else {

	if ($config['CMPI']['cmpi_enabled'] != 'Y')
		func_header_location ("error_message.php?access_denied&id=71");

	$stand_alone = false;
}

x_load('http','order','payment','xml','tests');

x_session_register("cmpi_tid");
x_session_register("cmpi_spahf");
x_session_register("cmpi_env");

$sql_tbl["country_currencies"] = "xcart_country_currencies";
$sql_tbl["currencies"] = "xcart_currencies";

$timeout = 15;

# Save script enviroment
if (!$stand_alone) {
	$cmpi_env = array(
		"HTTP_POST_VARS" => $HTTP_POST_VARS,
		"products" => $products,
		"userinfo" => $userinfo,
		"cart" => $cart,
		"is_egoods" => $is_egoods,
		"order_details" => $order_details,
		"bill_output" => $bill_output,
		"orderids" => $orderids,
		"orderid" => $orderid,
		"bill_firstname" => $bill_firstname,
		"bill_lastname" => $bill_lastname,
		"bill_name" => $bill_name,
		"ship_firstname" => $ship_firstname,
		"ship_lastname" => $ship_lastname,
		"ship_name" => $ship_name
	);

} else {
	foreach ($cmpi_env as $var => $value) {
		$$var = $value;
		if ($var == 'HTTP_POST_VARS') {
			$reject = array_keys(get_defined_vars());
			$reject[] = "value2";
			$reject[] = "GLOBALS";
			$reject[] = "HTTP_GET_VARS";
			$reject[] = "HTTP_POST_VARS";
			$reject[] = "HTTP_SERVER_VARS"; 
			$reject[] = "HTTP_ENV_VARS";
			$reject[] = "HTTP_COOKIE_VARS";
			$reject[] = "HTTP_POST_FILES";
			foreach ($value as $var => $value2) {
				if (func_allowed_var($var))
					$$var = $value2;
			}
			unset($reject);
		}
	}
}

if (empty($orderids))
	$orderids = array($orderid);

if (empty($orderids) || !test_active_bouncer())
	return false;

# Get order(s) data
$order = array();
if (!empty($orderids) && is_array($orderids)) {
	foreach ($orderids as $orderid) {
		$tmp = func_order_data($orderid);

		if (empty($tmp))
			continue;

		if (empty($order)) {
			$order = $tmp;

		} else {
			$order['order']['total'] += $tmp['order']['total'];
			$order['products'] = func_array_merge($order['products'], $tmp['products']);
		}
	}
}

if (!isset($order['order']))
	return false;

$order['order']['desc'] = "Order #".implode(", ", $orderids)."; customer: ".$order['userinfo']['login'].";";
if (isset($force_userinfo)) {
	$order['order']['userinfo'] = func_array_merge($order['order']['userinfo'], $force_userinfo);
}

# Get CC data
$tmp = explode("\n", $order['order']['details']);
$details = array();
foreach ($tmp as $v) {
	$key = substr($v, 0, strpos($v,":"));
	$value = substr($v, strpos($v,":")+1);
	$details[trim($key)] = trim($value);
}
unset ($tmp);
if (isset($force_details)) {
	$details = func_array_merge($details, $force_details);
}
$hash = array();

# PayPal part
if ($order['order']['payment_method'] == 'PayPal') {
	$details['{CardNumber}'] = "PAYPAL";
	$details['{ExpDate}'] = "";
}

#
# cmpi_lookup method
#
if (!$stand_alone) {

	$cur = func_query_first_cell("SELECT code_int FROM $sql_tbl[currencies] WHERE code = '".$config['CMPI']['cmpi_currency']."'");
	$hash = array('CardinalMPI' => array(
		"MsgType" => "cmpi_lookup",
		"Version" => "1.5",
		"ProcessorId" => $config['CMPI']['cmpi_proseccorid'],
		"MerchantId" => $config['CMPI']['cmpi_merchantid'],
		"OrderNumber" => $order['order']['orderid'],
		"PurchaseAmount" => $config['General']['currency_symbol'].price_format($order['order']['total']),
		"RawAmount" => preg_replace("/\D/Ss","",$order['order']['total']),
		"PurchaseCurrency" => $cur,
		"PAN" => $details['{CardNumber}'],
		"PANExpr" => substr($details['{ExpDate}'], 2).substr($details['{ExpDate}'], 0, 2),
		"OrderDesc" => $order['order']['desc'],
		"UserAgent" => $HTTP_USER_AGENT,
		"BrowserHeader" => "*/*",
		"EMail" => $order['userinfo']['email'],
		"FirstName" => $order['userinfo']['b_firstname'],
		"LastName" => $order['userinfo']['b_lastname'],
		"Address1" => $order['userinfo']['b_address'],
		"Address2" => $order['userinfo']['b_address_2'],
		"City" => $order['userinfo']['b_city'],
		"State" => $order['userinfo']['b_state'],
		"PostalCode" => $order['userinfo']['b_zipcode'],
		"CountryCode" => $order['userinfo']['b_country'],
		"IPAddress" => func_get_valid_ip($REMOTE_ADDR)
	));
	$xml = func_hash2xml($hash);

	$t = time();
	list($header, $res) = func_https_request("POST", $config['CMPI']['cmpi_url'], array("cmpi_msg=".$xml), "&", "", "application/x-www-form-urlencoded", "","","","", $timeout);

	$res = func_xml2hash($res);
	$res = $res['CardinalMPI'];
	if (empty($res) && time()-$t >= $timeout) {
		$res = array("Enrolled" => "U", "ErrorDesc" => "HTTPS: Time out ($timeout)");
	}

	# Redirect customer to Cardinal commerce server
	if (empty($res)) {
		$bill_output['code'] = 4;
		$bill_output['billmes'] = "Authentication was not completed.  You will now be redirected back to the payment form to select another form of payment.";
		require $xcart_dir."/payment/payment_ccend.php";

	} elseif ($res['ErrorNo'] == 0 && $res['Enrolled'] == 'Y') {
		$cmpi_tid = $res['TransactionId'];
		$cmpi_spahf = $res['SPAHiddenFields'];
		x_session_register("cmpi_iframe_data");
		$cmpi_iframe_data['PaReq'] = $res['Payload'];
		$cmpi_iframe_data['TermUrl'] = $current_location."/payment/cmpi.php?".$XCART_SESSION_NAME."=".$XCARTSESSID."&from_frame";
		$cmpi_iframe_data['MD'] = $XCARTSESSID;
		$cmpi_iframe_data['ACSUrl'] = $res['ACSUrl'];
		if ($card_type == 'MC') {
			$type = func_get_langvar_by_name("lbl_cmpi_mcsc",false,false,true);

		} elseif ($card_type == 'JCB') {
			$type = func_get_langvar_by_name("lbl_cmpi_jcbjs",false,false,true);

		} else {
			$type = func_get_langvar_by_name("lbl_cmpi_vbv",false,false,true);
		}

		echo func_get_langvar_by_name("txt_cmpi_frame_customer_note",array("type" => $type), false, true)."<br /><br /><br />";
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td valign="top" align="center">
<iframe width="410" height="410" scrolling="no" marginwidth="0" marginheight="0" src="<?php echo $current_location."/payment/cmpi.php?".$XCART_SESSION_NAME."=".$XCARTSESSID."&iframe"; ?>" ></iframe>
	</td>
</tr>
</table>
<?php
		exit;
	}

	$cmpi_result = $res;
	unset($res);

	require_once $xcart_dir."/include/cc_detect.php";
	if (is_visa($details['{CardNumber}'])) {
		$cmpi_result['EciFlag'] = 6;

	} elseif (is_mc($details['{CardNumber}'])) {
		$cmpi_result['EciFlag'] = 1;
	}

	require $xcart_dir."/payment/".basename($module_params["processor"]);
	require $xcart_dir."/payment/payment_ccend.php";
	exit;

#
# Display IFRAME content
#
} elseif (isset($iframe)) {

	x_session_register("cmpi_iframe_data");

	if (empty($cmpi_iframe_data)) {
		header("Location: ../");
		die("Access denied");
	}

?>
<html>
<body onload="javascript: document.frm.submit();">
<form name="frm" action="<?php echo $cmpi_iframe_data['ACSUrl']; ?>" method="POST">
<input type="hidden" name="PaReq" value="<?php echo $cmpi_iframe_data['PaReq']; ?>">
<input type="hidden" name="TermUrl" value="<?php echo $cmpi_iframe_data['TermUrl']; ?>">
<input type="hidden" name="MD" value="<?php echo $cmpi_iframe_data['MD']; ?>">
</form>
</body>
</html>
<?php

#
# Return from IFRAME
#
} elseif (isset($close_frame)) {

	require $xcart_dir."/include/payment_wait.php";
	x_session_unregister("cmpi_env");
	x_session_register("cmpi_result");

	if (empty($cmpi_result)) {
		header("Location: ../");
		die("Access denied");
	}

	$module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='".$paymentid."'");
	x_session_register("secure_oid");
	x_session_register("secure_oid_cost");

	# ... last checkout step
	if ((($cmpi_result['PAResStatus'] != 'Y' && $cmpi_result['PAResStatus'] != 'A') || $cmpi_result['SignatureVerification'] != "Y") && empty($cmpi_result['ErrorDesc'])) {
		$bill_output['code'] = 4;
		if ($cmpi_result['PAResStatus'] == 'Y' && $cmpi_result['SignatureVerification'] == "N") {
			$bill_output['billmes'] = "Authentication could not be completed.  You will now be redirected back to the payment form to select another form of payment.";
		} elseif ($cmpi_result['PAResStatus'] == 'N' && $cmpi_result['SignatureVerification'] == "Y") {
			$bill_output['billmes'] = "Authentication failed.  You will now be redirected back to the payment form to select another form of payment.";
		} elseif ($cmpi_result['PAResStatus'] == 'U' && $cmpi_result['SignatureVerification'] == "Y") {
			$bill_output['billmes'] = "Authentication could not be completed.  You will now be redirected back to the payment form to select another form of payment.";
		} elseif (!isset($cmpi_result['PAResStatus'])) {
			$bill_output['billmes'] = "Authentication was not completed.  You will now be redirected back to the payment form to select another form of payment.";
		}

		require $xcart_dir."/payment/payment_ccend.php";
	}

	# ... payment module
	if (!empty($module_params["processor"])) {
		require $xcart_dir."/include/cc_detect.php";
		if (empty($cmpi_result['EciFlag'])) {
			if (is_visa($details['{CardNumber}'])) {
				$cmpi_result['EciFlag'] = 6;
			} elseif (is_mc($details['{CardNumber}'])) {
				$cmpi_result['EciFlag'] = 1;
			}
		}
		require $xcart_dir."/payment/".basename($module_params["processor"]);
		require $xcart_dir."/payment/payment_ccend.php";

		# ... customer interface
 	} else {
		func_header_location($current_location.DIR_CUSTOMER."/cart.php?mode=order_message&orderids=".func_get_urlencoded_orderids($orderids));
	}

#
# cmpi_authenticate method
#
} elseif (isset($from_frame)) {

	$hash = array('CardinalMPI' => array(
		"MsgType" => "cmpi_authenticate",
		"Version" => "1.5",
		"ProcessorId" => $config['CMPI']['cmpi_proseccorid'],
		"MerchantId" => $config['CMPI']['cmpi_merchantid'],
		"TransactionId" => $cmpi_tid,
		"PAResPayload" => $PaRes
	));
	$xml = func_hash2xml($hash);

	$t = time();
	list($header, $res) = func_https_request("POST",$config['CMPI']['cmpi_url'],array("cmpi_msg=".$xml), "&", "", "application/x-www-form-urlencoded", "","","","", $timeout);
	$res = func_xml2hash($res);
	$res = $res['CardinalMPI'];
	if (empty($res) && time()-$t >= $timeout) {
		$res = array("ErrorDesc" => "HTTPS: Time out ($timeout)");
	}

	if (!empty($cmpi_spahf))
		$res['SPAHiddenFields'] = $cmpi_spahf;

	# Generate inner transaction status
	if ($res['ErrorNo'] == 0) {
		if ($res['PAResStatus'] == 'Y')
			$res['PAResStatusDesc'] = "Successful authentication. Cardholder successfully authenticated with their Card Issuer.";
		elseif ($res['PAResStatus'] == 'A')
			$res['PAResStatusDesc'] = "Attempts authentication. Cardholder authentication was attempted.";
		elseif ($res['PAResStatus'] == 'N')
			$res['PAResStatusDesc'] = "Failed authentication. Cardholder failed to successfully authenticate with their Card Issuer.";
		elseif ($res['PAResStatus'] == 'U')
			$res['PAResStatusDesc'] = "Authentication unavailable. Authentication with the Card Issuer was unavailable.";
		else
			$res['PAResStatusDesc'] = "Inner error";
	}

	$res['TransactionID'] = $cmpi_tid;
	$res['SPAHiddenFields'] = $cmpi_spahf;
	x_session_unregister("cmpi_tid");
	x_session_unregister("cmpi_spahf");

	# Generate common transaction status
	$res['status'] = ($res['ErrorNo'] == 0 && ($res['PAResStatus'] == 'Y' || $res['PAResStatus'] == 'A')&& $res['SignatureVerification'] == 'Y') ? "Y" : "";
	x_session_register("cmpi_result");
	$cmpi_result = $res;
	unset($res);

	# Redirect to ...
	echo "<script type=\"text/javascript\">
<!--
window.parent.location = '".$current_location."/payment/cmpi.php?".$XCART_SESSION_NAME."=".$XCARTSESSID."&close_frame=close_frame';
-->
</script>";
	exit;
}
?>
