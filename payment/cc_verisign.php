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
# $Id: cc_verisign.php,v 1.32.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('files','tests');

$vs_user = $module_params["param01"];
$vs_vendor = $module_params["param02"];
$vs_partner = $module_params["param03"];
$vs_pwd = $module_params["param04"];
if($module_params["param06"] == 'US') {
	$vs_host = ($module_params["testmode"] != "N")?"test-payflow.verisign.com":"payflow.verisign.com";
} else {
	$vs_host = ($module_params["testmode"] != "N")?"payflow-test.verisign.com.au":"payflow.verisign.com.au";
}
$testlive_prefix = ($module_params["testmode"] != "N") ? "test" : "live";

if(!test_payflow())
	func_header_location($xcart_catalogs['customer']."/error_message.php?error_ccprocessor_notfound");

$post = array(
	"USER" => $vs_user,
	"VENDOR" => $vs_vendor,
	"PARTNER" => $vs_partner,
	"PWD" => $vs_pwd,
	"TRXTYPE" => "S",
	"TENDER" => "C",
	"ACCT" => $userinfo["card_number"],
	"EXPDATE" => $userinfo["card_expire"],
	"AMT" => $cart["total_cost"],
	"CVV2" => $userinfo["card_cvv2"],
	"STREET" => $userinfo["b_address"]." ".$userinfo["b_address_2"],
	"ZIP" => $userinfo["b_zipcode"],
	"FIRSTNAME" => $bill_firstname,
	"LASTNAME" => $bill_lastname,
	"CITY" => $userinfo["b_city"],
	"STATE" => $userinfo["b_state"],
	"EMAIL" => $userinfo["email"],
	"PONUM" => substr(join("",$secure_oid), 0, 17),
	"COMMENT1" => substr($module_params["param05"].$testlive_prefix.join("-",$secure_oid), 0, 128)
);

foreach ($post as $pkey => $pval) {
	$pval = preg_replace("![ \n\r\t]+!", " ", $pval);
	if (preg_match("![&=]!", $pval))
		$pval = $pkey."[".strlen($pval)."]=".$pval;
	else
		$pval = $pkey."=".$pval;
		
	$post[$pkey] = $pval;
}

# Execute VeriSign PayFlow Pro client

putenv("LD_LIBRARY_PATH=".getenv("LD_LIBRARY_PATH").":".$xcart_dir."/payment/lib");
putenv("PFPRO_CERT_PATH=".$xcart_dir."/payment/certs");
$tmpfile = func_temp_store("");
@exec(func_shellquote($xcart_dir."/payment/bin/pfpro")." $vs_host 443 ".func_shellquote(join("&",$post))." 2>".func_shellquote($tmpfile), $bill_output);
@unlink($tmpfile);


$return = "&".$bill_output[0]."&";
# Check AVS result
$a = array();
$erravs = array(
"Y" => "match",
"N" => "not match"
);

if(preg_match("/IAVS=([YNX])/",$return,$out))
	$a[] = "iAVS ".(($out[1]=="X") ? ("cannot be determined") : ( ($out[1]=="Y") ? ("international") : ("USA") ));

if(preg_match("/AVSADDR=([YNX])/",$return,$out))
	$a[] = ($out[1] == "X") ? "Bank does not support AVS" : "Street address ".$erravs[$out[1]];

if(preg_match("/AVSZIP=([YNX])/",$return,$out))
	$a[] = ($out[1] == "X") ? "Bank does not support AVS" : "ZIP code ".$erravs[$out[1]];

$bill_output["avsmes"] = join(" :: ",$a);

# Check result
if(preg_match("/PNREF=(.*)&/U",$return,$out))
	$pnref = $out[1];

$err = array(
"-1" => "Failed to connect to host",
"-2" => "Failed to resolve hostname",
"-5" => "Failed to initialize SSL context",
"-6" => "Parameter list format error: & in name",
"-7" => "Parameter list format error: invalid [ ] name length clause",
"-8" => "SSL failed to connect to host",
"-9" => "SSL read failed",
"-10" => "SSL write failed",
"-11" => "Proxy authorization failed",
"-12" => "Timeout waiting for response",
"-13" => "Select failure",
"-14" => "Too many connections",
"-15" => "Failed to set socket options",
"-20" => "Proxy read failed",
"-21" => "Proxy write failed",
"-22" => "Failed to initialize SSL certificate",
"-23" => "Host address not specified",
"-24" => "Invalid transaction type",
"-25" => "Failed to create a socket",
"-26" => "Failed to initialize socket layer",
"-27" => "Parameter list format error: invalid [ ] name length clause",
"-28" => "Parameter list format error: name",
"-29" => "Failed to initialize SSL connection",
"-30" => "Invalid timeout value",
"-31" => "The certificate chain did not validate, no local certificate found",
"-32" => "The certificate chain did not validate, common name did not match URL",
"-99" => "Out of memory",
"1" => "User authentication failed",
"2" => "Invalid tender type. Your merchant bank account does not support the following credit card type that was submitted.",
"3" => "Invalid transaction type. Transaction type is not appropriate for this transaction. For example, you cannot credit an authorization-only transaction.",
"4" => "Invalid amount format",
"5" => "Invalid merchant information. Processor does not recognize your merchant account information. Contact your bank account acquirer to resolve this problem.",
"7" => "Field format error. Invalid information entered. See RESPMSG.",
"8" => "Not a transaction server",
"9" => "Too many parameters or invalid stream",
"10" => "Too many line items",
"11" => "Client time-out waiting for response",
"12" => "Declined. Check the credit card number and transaction information to make sure they were entered correctly. If this does not resolve the problem, have the customer call the credit card issuer to resolve.",
"13" => "Referral. Transaction was declined but could be approved with a verbal authorization from the bank that issued the card. Submit a manual Voice Authorization transaction and enter the verbal auth code.",
"19" => "Original transaction ID not found. The transaction ID you entered for this transaction is not valid. See RESPMSG.",
"20" => "Cannot find the customer reference number",
"22" => "Invalid ABA number",
"23" => "Invalid account number. Check credit card number and re-submit.",
"24" => "Invalid expiration date. Check and re-submit.",
"25" => "Invalid Host Mapping. Transaction type not mapped to this host",
"26" => "Invalid vendor account",
"27" => "Insufficient partner permissions",
"28" => "Insufficient user permissions",
"29" => "Invalid XML document. This could be caused by an unrecognized XML tag or a bad XML format that cannot be parsed by the system.",
"30" => "Duplicate transaction",
"31" => "Error in adding the recurring profile",
"32" => "Error in modifying the recurring profile",
"33" => "Error in canceling the recurring profile",
"34" => "Error in forcing the recurring profile",
"35" => "Error in reactivating the recurring profile",
"36" => "OLTP Transaction failed",
"50" => "Insufficient funds available in account",
"99" => "General error. See RESPMSG.",
"100" => "Transaction type not supported by host",
"101" => "Time-out value too small",
"102" => "Processor not available",
"103" => "Error reading response from host",
"104" => "Timeout waiting for processor response. Try your transaction again.",
"105" => "Credit error. Make sure you have not already credited this transaction, or that this transaction ID is for a creditable transaction. (For example, you cannot credit an authorization.)",
"106" => "Host not available",
"107" => "Duplicate suppression time-out",
"108" => "Void error. See RESPMSG. Make sure the transaction ID entered has not already been voided. If not, then look at the Transaction Detail screen for this transaction to see if it has settled. (The Batch field is set to a number greater than zero if the transaction has been settled). If the transaction has already settled, your only recourse is a reversal (credit a payment or submit a payment for a credit).",
"109" => "Time-out waiting for host response",
"111" => "Capture error. Only authorization transactions can be captured.",
"112" => "Failed AVS check. Address and ZIP code do not match. An authorization may still exist on the cardholder's account.",
"113" => "Cannot exceed sales cap. For ACH transactions only.",
"113" => "Merchant sale total will exceed the cap with current transaction",
"114" => "Card Security Code (CSC) Mismatch. An authorization may still exist on the cardholder's account.",
"115" => "System busy, try again later",
"116" => "VPS Internal error - Failed to lock terminal number",
"117" => "Failed merchant rule check. An attempt was made to submit a transaction that failed to meet the security settings specified on the VeriSign Manager Security Settings page. See VeriSign Manager User's Guide.",
"118" => "Invalid keywords found in string fields",
"122" => "Merchant sale total will exceed the credit cap with current transaction. ACH transactions only.",
"125" => "Fraud Protection Services Filter - Declined by filters",
"126" => "Fraud Protection Services Filter - Flagged for review by filters",
"127" => "Fraud Protection Services Filter - Not processed by filters",
"128" => "Fraud Protection Services Filter - Declined by merchant after being flagged for review by filters",
"1000" => "Generic host error. See RESPMSG. This is a generic message returned by your credit card processor. The message itself will contain more information describing the error.",
"1001" => "Buyer Authentication Service unavailable",
"1002" => "Buyer Authentication Service - Transaction timeout",
"1003" => "Buyer Authentication Service - Invalid client version",
"1004" => "Buyer Authentication Service - Invalid timeout value",
"1011" => "Buyer Authentication Service unavailable",
"1012" => "Buyer Authentication Service unavailable",
"1013" => "Buyer Authentication Service unavailable",
"1014" => "Buyer Authentication Service - Merchant is not enrolled for Buyer Authentication Service (3-D Secure). To enroll, log in to VeriSign Manager, click Security, and then click the Buyer Authentication Service banner on the page.",
"1021" => "Buyer Authentication Service - Invalid card type",
"1022" => "Buyer Authentication Service - Invalid or missing currency code",
"1023" => "Buyer Authentication Service - Merchant has not activated buyer authentication for this card type",
"1041" => "Buyer Authentication Service - Validate Authentication failed: missing or invalid PARES",
"1042" => "Buyer Authentication Service - Validate Authentication failed: PARES format is invalid",
"1043" => "Buyer Authentication Service - Validate Authentication failed: Cannot find successful Verify Enrollment",
"1044" => "Buyer Authentication Service - Validate Authentication failed: Signature validation failed for PARES",
"1045" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid amount in PARES",
"1045" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid amount in PARES",
"1046" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid acquirer in PARES",
"1047" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid Merchant ID in PARES",
"1048" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid card number in PARES",
"1049" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid currency code in PARES",
"1050" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid XID in PARES",
"1051" => "Buyer Authentication Service - Validate Authentication failed: Mismatched or invalid order date in PARES",
"1052" => "Buyer Authentication Service - Validate Authentication failed: This PARES was already validated for a previous Validate Authentication transaction"
);

if(preg_match("/RESULT=(.*)&/U",$return,$out))
	$result = $out[1];

if(preg_match("/CVV2MATCH=([YNX])/",$return,$out))
	$bill_output["cvvmes"].= (($out[1]=="X") ? ("Not Supported") : ( ($out[1]=="Y") ? ("Match") : ("Not Match") ));

if($result == "0")
{
	$bill_output["code"] = 1;

	if(preg_match("/AUTHCODE=(.*)&/U",$return,$out))
		$bill_output["billmes"] = "(AuthCode: ".$out[1].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = (empty($err[$result]) ? "Result: ".$result : $err[$result]);
}

if(!empty($bill_output["billmes"]))
	$bill_output["billmes"].= " (PNREF = ".$pnref.")";


#print "<pre>";
#print_r($bill_output);
#print $return;
#exit;

?>
