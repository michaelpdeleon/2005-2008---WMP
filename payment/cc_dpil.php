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
# $Id: cc_dpil.php,v 1.16 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_id   = $module_params["param01"];
$pp_pass = $module_params["param02"];
$pp_test = $module_params["testmode"];
$prefix  = $module_params["param04"];

$post = array();
#$post[] = "DPIAccountNum=".$pp_id;
$post[] = "ePayAccountNum=".$pp_id;
$post[] = "password=".$pp_pass;
$post[] = "transactionCode=32";
$post[] = "orderNum=".$prefix.join("-",$secure_oid);
#$post[] = "customerNum=".sprintf("%015d",($secure_oid));
$post[] = "transactionAmount=".$cart["total_cost"];
$post[] = "cardHolderZip=".strtoupper($userinfo["b_zipcode"]);
$post[] = "cardHolderName=".strtoupper($userinfo["card_name"]);
$post[] = "cardHolderAddress=".strtoupper($userinfo["b_address"]);
$post[] = "cardHolderCity=".strtoupper($userinfo["b_city"]);
$post[] = "cardHolderState=".strtoupper($userinfo["b_state"]);
$post[] = "cardHolderPhone=".strtoupper($userinfo["phone"]);
$post[] = "cardHolderEmail=".strtoupper($userinfo["email"]);
$post[] = "eCommerce=Y";
$post[] = "testTransaction=".$pp_test;
$post[] = "cardAccountNum=".$userinfo["card_number"];
$post[] = "expirationDate=".$userinfo["card_expire"];
$post[] = "CVV2=".$userinfo["card_cvv2"];

#list($a,$return)=func_https_request("POST","https://www.dpisecure.com:443/dpilink/authpd.asp",$post);
list($a,$return)=func_https_request("POST","https://epaysecure.transfirst.com:443/elink/authpd.asp",$post);
$return = split("\|",$return);
$TSC = $return[10];

# Check CVV2

$errcvv = array(
	"M" => "CVV2 Match",
	"N" => "CVV2 Does Not Match",
	"P" => "CVV2 Not Processed",
	"S" => "Merchant has indicated the CVV2 is not present on the card",
	"U" => "Issuer is not certified and/or has not provided Visa Encryption Keys"
);
$bill_output["cvvmes"].= $errcvv[$return[23]];

# Check AVS
$erravs = array(
	"A" => "Address match, zip does not match",
	"E" => "Not a mail / phone order",
	"N" => "Address and zip do not match",
	"R" => "Issuer system unavailable",
	"S" => "Service not supported",
	"U" => "Address information unavailable",
	"W" => "Nine digit zip match, no address match",
	"X" => "Address and nine digit zip match",
	"Y" => "Address and five digit zip match",
	"Z" => "Five digit zip match, no address match"
);
$bill_output["avsmes"] = $erravs[$return[21]];

if(($TSC == "TO") || ($TSC == "00")) {
	$bill_output["code"] = 1;
} else {
	$errarr=array(
		"01" => "Refer to issuer",
		"02" => "Refer to issuer-Special condition",
		"03" => "Invalid merchant ID",
		"04" => "Pick up card",
		"05" => "Do not honor",
		"06" => "General error",
		"06" => "General error",
		"07" => "Pick up card-Special condition",
		"13" => "Invalid Amount",
		"14" => "Invalid card number",
		"15" => "No such issuer",
		"19" => "Re-enter transaction",
		"21" => "Unable to back out transaction",
		"28" => "File is temporarily unavailable",
		"39" => "No credit account",
		"41" => "Pickup card-Lost",
		"43" => "Pickup card-Stolen",
		"51" => "Insufficient funds",
		"54" => "Expired card",
		"57" => "Trans. Not permitted-Card",
		"61" => "Exceeds withdrawal limit",
		"62" => "Invalid service code, restricted",
		"65" => "Activity limit exceeded",
		"76" => "Unable to locate, no match",
		"77" => "Inconsistent data, rev. or repeat",
		"78" => "No account",
		"80" => "Invalid date",
		"85" => "No reason to decline",
		"91" => "Issuer or switch is unavailable",
		"93" => "Violation, cannot complete",
		"96" => "System malfunction",
		"98" => "No matching transaction to Void",
		"L0" => "An error occurred - Contact DPI Account Executive.",
		"L1" => "Invalid or missing account number",
		"L2" => "The Password is missing or invalid",
		"L3" => "Expiration Date is not formatted correctly",
		"L4" => "Reference number not found",
		"L6" => "The Order Number is required but missing",
		"L7" => "Transaction Code must be either 30 for Authorization Only, or 32 for Authorize and Settle",
		"L8" => "The Network Connection timed out due to a communication error.",
		"L14" => "Invalid Card Number",
		"S5" => "Settlement Request submitted for transaction that was previously settled.",
		"S6" => "Settlement Request was submitted for a transaction that was not authorized.",
		"S7" => "Settlement Request was submitted for a transaction that was declined.",
		"V6" => "Transaction Type must equal 40 (Settlement) or 32 (Authorize & Settle)",
		"V7" => "Void Request was submitted for a transaction that was declined.",
		"V8" => "Void Request was submitted for a transaction that was previously voided.",
		"V9" => "Void Request was submitted for a transaction that was already posted.  Must submit a Credit Request."
	);
	$bill_output["code"] = 2;
	$bill_output["billmes"] = empty($errarr[$TSC]) ? "TransactionCode: ".$TSC : $errarr[$TSC];
}

if($return[13]!="")
	$bill_output["billmes"].= " (Unique Reference Number: ".$return[13].")";

if($return[14]!="")
	$bill_output["billmes"].= " (Authorization Response Code: ".$return[14].")";

if($return[15]!="")
	$bill_output["billmes"].= " (Authorization Source Code: ".$return[15].")";

if($return[16]!="")
	$bill_output["billmes"].= " (Authorization Characteristic Indicator: ".$return[16].")";

if($return[17]!="")
	$bill_output["billmes"].= " (Transaction ID: ".$return[17].")";

?>
