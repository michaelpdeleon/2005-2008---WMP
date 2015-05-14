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
# $Id: cc_trustcommerce.php,v 1.10 2006/03/18 08:21:38 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http','tests');

$decline = array(
	"decline" => "This is a 'true' decline, it almost always is a result of insufficient funds on the card.",
	"avs" => "AVS failed; the address entered does not match the billing address on file at the bank.",
	"cvv" => "CVV failed; the number provided is not the correct verification number for the card. (See section X for details on CVV.)",
	"call" => "The card must be authorized manually over the phone. You may choose to call the customer service number listed on the card and ask for an offline authcode, which can be passed in the offlineauthcode field.",
	"carderror" => "Card number is invalid, usually the result of a typo in the card number.",
	"authexpired" => "Attempt to postauth an expired (more than 7 days old) preauth.",
	"dailylimit" => "Daily limit in transaction count or amount as been reached.",
	"weeklylimit" => "Weekly limit in transaction count or amount as been reached.",
	"monthlylimit" => "Monthly limit in transaction count or amount as been reached."
);

$baddata = array (
	"missingfields" => "Some parameters required for this transaction type were not sent.",
	"extrafields" => "Parameters not allowed for this transaction type were sent.",
	"badformat" => "A field was improperly formatted, such as non-digit characters in a number field.",
	"badlength" => "A field was longer or shorter than the server allows.",
	"merchantcantaccept" => "The merchant can't accept data passed in this field. If the offender is 'cc', for example, it usually means that you tried to run a card type (such as American Express or Discover) that is not supported by your account. If it was 'currency', you tried to run a currency type not supported by your account.",
	"mismatch" => "Data in one of the offending fields did not cross-check with the other offending field."
);

$error = array (
	"cantconnect" => "Couldn't connect to the TrustCommerce gateway. Check your Internet connection to make sure it is up.",
	"dnsfailure" => "The TCLink software was unable to resolve DNS hostnames. Make sure you have name resolving ability on the machine.",
	"linkfailure" => "The connection was established, but was severed before the transaction could complete.",
	"failtoprocess" => "The bank servers are offline and unable to authorize transactions. Try again in a few minutes, or try a card from a different issuing bank."
);

$avserr = array(
	"A" => "Address (Street) matches, ZIP does not",
	"E" => "AVS error",
	"N" => "No Match on Address (Street) or ZIP",
	"P" => "AVS not applicable for this transaction",
	"R" => "Retry. System unavailable or timed out",
	"S" => "Service not supported by issuer",
	"U" => "Address information is unavailable",
	"W" => "9 digit ZIP matches, Address (Street) does not",
	"X" => "Exact AVS Match",
	"Y" => "Address (Street) and 5 digit ZIP match",
	"Z" => "5 digit ZIP matches, Address (Street) does not"
);

$cvverr = array(
	"M" => "Match",
	"N" => "No Match",
	"P" => "Not Processed",
	"S" => "Should have been present",
	"U" => "Issuer unable to process request"
);

$tc_custid = $module_params["param01"];
$tc_password = $module_params["param02"];
$tc_prefix = $module_params["param04"];
$tc_curr = $module_params["param05"];
$tc_avs = $module_params["param06"];
$tc_operator = $module_params["param07"];

$post["custid"] = $tc_custid;
$post["password"] = $tc_password;
$post["action"] = "sale";
if ($module_params["testmode"] != "N") $post["demo"]="y";
$post["address1"] = $userinfo["b_address"];
$post["city"] = $userinfo["b_city"];
$post["state"] = (!empty($userinfo["b_state"]))? $userinfo["b_state"] : "Non US";
$post["zip"] = $userinfo["b_zipcode"];
$post["country"] = $userinfo["b_country"];
$post["phone"] = $userinfo["phone"];
$post["email"] = $userinfo["email"];
$post["shipto_name"] = $userinfo["s_firstname"]." ".$userinfo["s_lastname"];
$post["shipto_address1"] = $userinfo["s_address"];
$post["shipto_city"] = $userinfo["s_city"];
$post["shipto_state"] = (!empty($userinfo["s_state"]))? $userinfo["s_state"] : "Non US";
$post["shipto_zip"] = $userinfo["s_zipcode"];
$post["shipto_country"] = $userinfo["s_country"];

$post["amount"] = $cart["total_cost"]*100;
$post["currency"] = $tc_curr;
$post["name"] = $userinfo["card_name"];
$post["cc"] = $userinfo["card_number"];
$post["exp"] = $userinfo["card_expire"];
$post["cvv"] = $userinfo["card_cvv2"];
$post["ticket"] = $tc_prefix.join("-",$secure_oid);
$post["operator"] = $tc_operator;

#
# Order details
#
$post["numitems"] = count($products);

$post["shippinghandling"] = $cart["shipping_cost"]*100;
if (is_array($products))
foreach($products as $k => $v ) {
	$n = $k + 1;
	$post["productcode".$n] = $v["productcode"] !="" ? $v["productcode"]:"#".$v["productid"];
	$post["quantity".$n] = $v["amount"];
	$post["price".$n] = $v["price"]*100;
}
if ($tc_avs == "Y") $post["avs"] = "y";

#
# Check the tclink librirary
#

$configured = test_trustcommerce();

#
# Use TCLink API
#
if ($configured)
	$result = tclink_send($post);
#
# Use HTTPS connection
#
elseif (test_active_bouncer()) {
	$http_post = "";
	foreach($post as $key=>$value)
		$http_post[] = $key."=".$value;

	list($a,$content) = func_https_request("POST","https://vault.trustcommerce.com:443/trans/", $http_post,'&');

	$result="";
	foreach (split("\n",$content) as $line) {
		list($key,$value) = split('=',$line);
		$result[$key] = $value;
	}
}
else
	$result["status"] = "notconfugured";

$bill_output["billmes"] = "";

switch($result["status"]) {
	case "notconfugured":
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "The TClink library is not configured (installed)";
		break;
	case "accepted":
	case "approved":
		$bill_output["code"] = 1;
		$bill_output["billmes"] = " (Trans ID: ".$result["transid"].") ";
		break;
	case "decline":
	case "rejected":
		$bill_output["code"] = 2;
		$bill_output["billmes"] = " Declined (Trans ID: ".$result["transid"]."): ".$decline[$result["declinetype"]]." ";
		if (!empty($result["avs"])) $bill_output["avsmes"] = "AVS Code: ".$avserr[$result["avs"]];
		if (!empty($result["cvv"])) $bill_output["cvvmes"] = "CVV Code: ".$cvverr[$result["cvv"]];
		break;
	case "error":
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Error : ". $error[$result["error"]];
		break;
	case "baddata":
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Error (bad data): ".$result["offenders"]." : ".$baddata[$result["error"]];
		break;
}

?>
