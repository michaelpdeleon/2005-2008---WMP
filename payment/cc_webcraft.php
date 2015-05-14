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
# $Id: cc_webcraft.php,v 1.7 2006/01/11 06:56:23 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

/*
Connection
The web server will be required to initiate an HTTPS request from a fixed IP address to URL:
https://www.webcraft.com.mt:443/merchantservices/gateway/process.asp

Request Parameters
The following required parameters must be sent with your request using either GET or POST:

username : provided by Webcraft
password : provided by Webcraft
orderno : your order reference (25 chars)
cardno : credit card number (numeric, no spaces)
expiry : card expiry date (YYMM)
amount : transaction value in selected currency (no decimal)

Response Parameters
Upon submitting your request the gateway will respond within the specified
maximum response time with a response string contained in the body of the
HTTPS response. The format of the response string is as follows:

ORDERNO=123&AUTHCODE=&ERROR=1&MESSAGE=INVALID LOGIN

Examples
Sample Request (using GET):
https://www.webcraft.com.mt/merchantservices/gateway/process.asp?username=myuser&password=mypass&orderno=123&cardno=12343567891011&expiry=0512&amount=1000
Sample Response:
ORDERNO=123&AUTHCODE=W99999&ERROR=0&MESSAGE=AUTH CODE:W99999

Test:
https://www.webcraft.com.mt:443/merchantservices/dummy/dummyyes.asp
generates a successful transaction response for testing purposes (dummy only)

https://www.webcraft.com.mt:443/merchantservices/dummy/dummyno.asp
generates a failed transaction response for testing purposes (dummy only)
*/

# Error indicator levels:
#
$response_error1_1_3 = array(
	0 => 'no errors, funds blocked',
	1 => 'transaction failed (declined by bank)',
	2 => 'transaction failed due to communication problem (try again)'
);

$response_error1_1_2 = array(
	0 => 'No errors, funds blocked',
	1 => 'Unspecified error',
	2 => 'Invalid Transaction Type',
	4 => 'Card Scheme not recognized',
	5 => 'Card Scheme not accepted',
	6 => 'Invalid card number (lcd)',
	7 => 'Invalid card number length',
	8 => 'Invalid card number (pcd)',
	9 => 'Card expired',
	10 => 'Card not yet valid',
	11 => 'Invalid card service code',
	12 => 'Field Missing / Wrong format',
	15 => 'Account number does not exist',
	16 => 'Value exceeds ceiling limit',
	21 => 'EFT system not configured',
	28 => 'General transaction error',
	30 => 'Unspecified error',
	37 => 'Invalid / missing expiry date',
	38 => 'Invalid / missing issue number',
	39 => 'Invalid / missing start date',
	40 => 'Purchase / refund value bad or missing',
	54 => 'Card on hot list',
	80 => 'Service Error'
);

switch ($module_params["param04"]) {
	case '1.1.2':
		$response_error = $response_error1_1_2;
		break;
	case '1.1.3':
		$response_error = $response_error1_1_3;
		break;
	default:
		$response_error = $response_error1_1_3;
}

$request_login = $module_params["param01"];
$request_password = $module_params["param02"];
$request_prefix = $module_params["param09"];


if ( (empty($userinfo["card_number"]) || empty($userinfo["card_expire"])) || strlen($userinfo["card_expire"]) != 4 ) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Error: Check the credit card information.";
}else {
	# Format is YYMM (but we get as MMYY from form)
	$userinfo["card_expire"] = substr($userinfo["card_expire"],2,2) . substr($userinfo["card_expire"],0,2);

	$post[] = "username=".$request_login;
	$post[] = "password=".$request_password;
	$post[] = "orderno=".$request_prefix.join("-",$secure_oid);
	$post[] = "cardno=".$userinfo["card_number"];
	$post[] = "expiry=".$userinfo["card_expire"];
	$post[] = "amount=".ceil($cart["total_cost"]*100); # Only in cents

	switch($module_params["testmode"])
	{
		case "A": $url = "https://www.webcraft.com.mt:443/merchantservices/dummy/dummyyes.asp";break;
		case "D": $url = "https://www.webcraft.com.mt:443/merchantservices/dummy/dummyno.asp";break;
		default : $url = "https://www.webcraft.com.mt:443/merchantservices/gateway/process.asp";break;
	}

	list($a,$return) = func_https_request("POST", $url, $post);

	$mass = @explode("&", $return);
	$response_array = array();
	if (!empty($mass) && is_array($mass)) {
		foreach ($mass as $v) {
			$key = "";
			$value = "";
			list($key, $value) = @explode("=", $v);
			if ($key && isset($value)) {
				$response_array[$key] = $value;
			}
		}
	}

	if (!empty($response_array) && is_array($response_array)) {
		if($response_array["ERROR"] == 0)
		{
			$bill_output["code"] = 1;
			$bill_output["billmes"] = $response_error[$response_array["ERROR"]] . ". " . $response_array["MESSAGE"];
		}
		else
		{
			$bill_output["code"] = 2;
			$bill_output["billmes"] = "Error: " . $response_error[$response_array["ERROR"]] . ".";
			$bill_output["billmes"].= " (Reason message: " . str_replace("_", " ", trim($response_array["MESSAGE"])) . ")";
		}
	}else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Error: Invalid URL array.";
	}


}
?>
