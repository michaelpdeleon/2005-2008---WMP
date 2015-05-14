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
# $Id: cc_jettis.php,v 1.11.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

if (!function_exists('jettis_encrypt')) {
	function jettis_encrypt($input,$encr=1) {
		static $encryptionKeys = array(5, 233, 15, 299, 0, 29, 249, 177, 176, 200, 18, 155, 211, 234);
		$encryptionKeysLength = count($encryptionKeys);

		$listResult = unpack("C*", $input);

		$split = $encr ? chr(0) : '&';
		$c = "";$index = 0;
		foreach ($listResult as $place) {
			$v = ($place ^ $encryptionKeys[$index]) & 255;
			$c .= ($v == 0 ? $split : chr($v));
			$index++; if($index >= $encryptionKeysLength)$index = 0;
		}

		return $c;
	}
}

$jettis_message = array(
    0 => "Success",
    1 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
    2 => "Please go back and enter your credit card number.",
    3 => "Please go back and enter your first name.",
    4 => "Please go back and enter your last name.",
    5 => "Please go back and enter the zip code that appears on your credit card statement.",
    6 => "Please go back and enter the month of your expiration date.",
    7 => "Please go back and enter the year of your expiration date.",
    8 => "Please go back and select a username.",
    9 => "Please go back and confirm your password.",
   10 => "Please go back and read the terms and conditions.",
   11 => "We're sorry, but your age does not meet the minimum required by law to access this site.",
   12 => "Please enter your city.",
   13 => "Please go back and enter your state.",
   14 => "Your username must be 2-8 characters long.",
   15 => "We're sorry, but your username may only contain letters and numbers.  Please go back and remove any other characters.",
   16 => "For security purposes, your password must be 6-8 characters long.",
   17 => "Please be sure to enter your password correctly in both fields.",
   18 => "For security reasons, your password must be different than your username.",
   19 => "Please enter a valid email address.",
   20 => "Please enter your street address.",
   21 => "Please enter a numeric phone number (eg. 8185551212).",
   22 => "You have entered an invalid credit card number.  Please go back and enter a valid credit card number.",
   23 => "You have entered an invalid credit card expiration date.  Please go back and enter a valid expiration date.",
   24 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   25 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   26 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   27 => "The username you have selected is already in use.  Please select another username.",
   28 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   29 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   30 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   31 => "You have entered an invalid credit card number.  Please go back and enter a valid credit card number.",
   32 => "Unable to find version or out-of-date version.",
   33 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   34 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   35 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   36 => "Please go back and select the Country from which you are accessing this site.",
   37 => "Please go back and select a different type of credit card.",
   38 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   39 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   40 => "We're sorry, but your password may only contain letters and numbers.  Please go back and remove any other characters.",
   41 => "You have already subscribed to this product.  Please go back and select another product.",
   42 => "You have already purchased this trial product.  Please go back and select another product.",
   43 => "Too many consecutive errors",
   44 => "Please go back and enter a password.",
   45 => "We're sorry, but your zip code may not contain any quotes. Please go back and remove them.",
   46 => "We're sorry, but the state field may not contain any quotes.  Please go back and remove them.",
   47 => "We're sorry, but the street may not contain any quotes.  Please go back and remove them.",
   48 => "Please go back and choose your country.",
   49 => "The country code may only have numbers.  Please go back and try again.",
   50 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   51 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   52 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   53 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   54 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   55 => "Your first name may only be between 1 and 30 letters long.  Please go back and try again.",
   56 => "Your last name may only be between 1 and 50 letters long.  Please go back and try again.",
   57 => "Your zip code may only be between 1 and 15 characters long.  Please go back and try again.",
   58 => "Your city may only be between 1 and 30 characters long.  Please go back and try again.",
   59 => "Your state may only be between 1 and 30 characters long.  Please go back and try again.",
   60 => "Your email address may only be between 1 and 80 characters long.  Please go back and try again.",
   61 => "Your street address may only be between 1 and 80 characters long.  Please go back and try again.",
   62 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   63 => "Credit Limit exceeded. Please go back and try another credit card.",
   64 => "Credit Limit exceeded. Please go back and try another credit card.",
   65 => "Credit Limit exceeded. Please go back and try another credit card.",
   66 => "Credit Limit exceeded. Please go back and try another credit card.",
   67 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   68 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   69 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   70 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   71 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   72 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   73 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   74 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
   75 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   76 => "Credit Card BIN Exclusion",
   77 => "Email Exclusion",
   78 => "IP not reversible",
   79 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   80 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
   81 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error
you are encountering.",
   82 => "Mail Zip Code Exclusion",
   83 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  101 => "We are currently unable to process your credit card.  Please go back and try again.",
  102 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  103 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  104 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  105 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  106 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  108 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  110 => "You have entered an invalid credit card number.  Please go back and enter a valid credit card number.",
  111 => "You have entered an invalid credit card expiration date.  Please go back and enter a valid expiration date.",
  112 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  113 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  114 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  115 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  116 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  117 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  118 => "Merchant bank is temporarily down.",
  119 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  120 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  121 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  122 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  123 => "This account has been declined by the bank.",
  124 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  125 => "We are currently unable to process your credit card.  Please go back and try another credit card.",
  126 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  127 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  128 => "The expiration date you have provided is expired.  Please go back and enter the correct expiration date.",
  130 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  131 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  140 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  141 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  142 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  150 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  151 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  160 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  161 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  162 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  170 => "You have entered an invalid zip code.  Please go back and enter the zip code which appears on your credit card statement.  Thank You",
  171 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  172 => "AVS Void Failure.",
  180 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  181 => "Your CVV2 field is incorrect.  Please go back and correct it",
  182 => "Original transaction date field is incorrect",
  198 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  199 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+INTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  200 => " ",
  201 => " ",
  202 => " ",
  203 => " ",
  204 => " ",
  205 => " ",
  206 => " ",
  300 => "This check number has already been used.  Please go back and enter a valid check number.",
  301 => "Your license number is invalid.  Please go enter the correct value and try again.",
  400 => "You have entered an invalid bank routing number.  Please go back and enter a valid bank routing number.",
  401 => "Please enter your bank name.",
  402 => "We're sorry, but the bank name may not contain any quotes.  Please go back and remove them.",
  403 => "Please go back and enter your bank account number.",
  404 => "Please go back and enter a valid bank account number",
  405 => "Please go back and enter your bank routing number",
  406 => "Please go back and enter a valid bank routing number",
  407 => "Please go back and enter the check number",
  408 => "Error -- Please go back and try again.  If you continue to experience problems please send an email to <+EXTERNAL_EMAIL> along with an explanation of the error you are encountering.",
  999 => "Unknown Error",
);

$jettis_longDesc = array(
    0 => "Success",
    1 => "Missing Product ID, Price, or Merchant ID",
    2 => "Missing CC Number",
    3 => "Missing First Name",
    4 => "Missing Last Name",
    5 => "Missing Zip Code",
    6 => "Missing Expiration Month",
    7 => "Missing Expiration Year",
    8 => "Missing Username",
    9 => "Missing Verify Password",
   10 => "The Agree Terms data is missing or not agreed",
   11 => "The user did not verifiy their age, or the age is missing",
   12 => "Missing City",
   13 => "Missing State",
   14 => "The username is too long or too short",
   15 => "There is a space or other invalid character in the username",
   16 => "The password is too long or too short",
   17 => "The Verify Password field is different than the original password",
   18 => "The password and the username are the same",
   19 => "The email address has some unexpected characters or formating.",
   20 => "Neither of the address fields are filled out",
   21 => "The phone number has some unexpected characters or formating",
   22 => "The credit card failed a simple check for validity.",
   23 => "The expiration date is not in the right format",
   24 => "We found this user in the Fraud Database",
   25 => "Missing parameters (Internal Jettis Error)",
   26 => "Database Error (Internal Jettis Error)",
   27 => "This username already is taken",
   28 => "The product, merchant or price is invalid, or this merchant is not allowed to sell this product",
   29 => "Unknown (Internal Jettis Error)",
   30 => "Invalid Proc (Internal Jettis Error)",
   31 => "Invalid Credit Card Number",
   32 => "The Version is missing or is wrong",
   33 => "The Country Code is invalid",
   34 => "This credit card is issues from a bank in a high-fraud country.",
   35 => "This user claims to come from a country which is high-fraud",
   36 => "The country that the user claims to live in does not match the country we found from their IP Address",
   37 => "No Account (Unknown)",
   38 => "This IP Address is in our fraud database.",
   39 => "We are unable to connect to the external password maintance program, or we are getting errors back from the program.",
   40 => "There is a space or other invalid character in the password",
   41 => "The customer alreadu has a membership",
   42 => "The customer hit our velocity check - they recently bought a membership",
   43 => "Too many consecutive errors, please try again at a later time",
   44 => "Missing Password",
   45 => "There are quotes in the zip code",
   46 => "There are quotes in the state",
   47 => "There are quotes in the street",
   48 => "Missing Country",
   49 => "The country can only contain numbers",
   50 => "Missing Quantity",
   51 => "The quantity may only contain numbers",
   52 => "Missing IP Address",
   53 => "The IP Address is not in the right format",
   54 => "The Merchant Text Area has quotes",
   55 => "The First Name must be between 1 and 30 letters long",
   56 => "The Last Name must be between 1 and 50 letters long",
   57 => "The zip must be between 1 and 15 characters long",
   58 => "The city must be between 1 and 30 characters long",
   59 => "The state must be between 1 and 30 characters long",
   60 => "The email address must be between 1 and 80 characters long.",
   61 => "The street address must be between 1 and 80 characters long.",
   62 => "The merch text area must be 80 or less characters long.",
   63 => "Quantity Limit Per Day Exceeded",
   64 => "Amount Limit Per Day Exceeded",
   65 => "Quantity Limit Per Month Exceeded",
   66 => "Amount Limit Per Month Exceeded",
   67 => "The credit card number does not match the credit card number of the  original bill",
   68 => "The price of this credit does not match the price of the original buill.",
   69 => "The Merch ID is mismatched from the Merch ID of the original bill.",
   70 => "The Prod ID for this credit does not match the Merch ID for the original bill.",
   71 => "The Bill Item ID is missing or invalid.",
   72 => "The Prod ID is missing or invalid.",
   73 => "The Merch ID is missing or invalid.",
   74 => "This user may be fradulent.",
   75 => "This Bill Item has already been credited, auto-credited, or charge-backed.",
   76 => "Credit Card BIN Exclusion",
   77 => "Email Exclusion",
   78 => "IP not reversible",
   79 => "The Bill ID is missing or invalid.",
   80 => "This authorization has already been settled.  Do not attempt to settle again.",
   81 => "The account number has some unexpected characters or formating.",
   82 => "Mail Zip Code Exclusion",
   83 => "Missing IP Code",
  101 => "We are unable to contact the bank",
  102 => "Invalid Request (Internal Jettis Error)",
  103 => "Incomplete (Internal Jettis Error)",
  104 => "Memory Allocation (Internal Jettis Error)",
  105 => "Bugcheck (Internal Jettis Error)",
  106 => "Inhibited (Internal Jettis Error)",
  108 => "Reject (Internal Jettis Error)",
  110 => "Credit Card Number (Internal Jettis Error)",
  111 => "The Expiration Date is incorrect",
  112 => "Prefix (Internal Jettis Error)",
  113 => "Amount (Internal Jettis Error)",
  114 => "We do not have a link with the bank",
  115 => "SENO (Internal Jettis Error)",
  116 => "Invalid Merchant Number",
  117 => "Request (Internal Jettis Error)",
  118 => "Merchant Bank Down",
  119 => "Preauth Unavailable",
  120 => "This card is declined",
  121 => "This card is in the bank's fraud database",
  122 => "This card is declined",
  123 => "Unknown decline (Internal Jettis Error)",
  124 => "This card is in the bank's fraud database",
  125 => "This card is overlimit",
  126 => "This transaction is less than allowed",
  127 => "Pin Error (Internal Jettis Error)",
  128 => "This credit card is expiried",
  130 => "Batch Unbalanced (Internal Jettis Error)",
  131 => "Batch Unopened (Internal Jettis Error)",
  140 => "Control Invalid (Internal Jettis Error)",
  141 => "Control Readonly (Internal Jettis Error)",
  142 => "Control Bad (Internal Jettis Error)",
  150 => "Duplicate Address (Internal Jettis Error)",
  151 => "Unknown Address (Internal Jettis Error)",
  160 => "Duplicate Merchant Number (Internal Jettis Error)",
  161 => "Merchant Busy (Internal Jettis Error)",
  162 => "This merchant is inhibited from making transactions",
  170 => "The address does not match",
  171 => "AVS Unmatched Void (Internal Jettis Error)",
  172 => "AVS Void Failure (Internal Jettis Error)",
  180 => "Invalid IP code",
  181 => "Invalid CVV2",
  182 => "Invalid Original Transaction Date",
  198 => "Can't connect to transaction server (Internal Jettis Error)",
  199 => "Unrecognized (Internal Jettis Error)",
  200 => " ",
  201 => " ",
  202 => " ",
  203 => " ",
  204 => " ",
  205 => " ",
  206 => " ",
  300 => "This is a duplicate attempt to process a check that has already been submitted",
  301 => "Drivers License ID is invalid",
  400 => "The ACH bank routing number failed a simple check for validity.",
  401 => "The Bank Name field is not filled out",
  402 => "There are quotes in the bank name",
  403 => "Missing bank account number",
  404 => "The account number has some unexpected characters or formating",
  405 => "Missing bank routing number",
  406 => "The bank routing number has some unexpected characters or formatting",
  407 => "The check number is missing",
  408 => "Unsupported Transaction Type",
  999 => "Unknown Error",
);


if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

	$j_login = $module_params["param01"];
	$j_pid = $module_params["param02"];
	$j_test = ($module_params["param03"] == "Y" ? "test" : "live");
	$j_prefix = $module_params["param04"];

$post = array();
$post[]="PRICE=".$cart["total_cost"];
$post[]="PRODUCT_ID=".$j_pid;
$post[]="QTY=1";
$post[]="MERCHANT_ID=".$j_login;
$post[]="CC_NUMBER=".$userinfo["card_number"];
$post[]="CVV2=".$userinfo["card_cvv2"];
$post[]="FIRST_NAME=".$bill_firstname;
$post[]="LAST_NAME=".$bill_lastname;
$post[]="ADDR_ZIP=".$userinfo["b_zipcode"];
$post[]="CC_EXP_MONTH=".substr($userinfo["card_expire"],0,2);
$post[]="CC_EXP_YEAR=".(2000+substr($userinfo["card_expire"],2,2));
$post[]="TERMS_AGREE=Y";
$post[]="CHECK_AGE=Y";
$post[]="VERSION=1.0";
$post[]="REMOTE_ADDR=".$REMOTE_ADDR;
$post[]="AUTH_STAT=B";
$post[]="EMAIL=".$userinfo["email"];
$post[]="ADDR_STREET_1=".$userinfo["b_address"];
$post[]="ADDR_CITY=".$userinfo["b_city"];
$post[]="ADDR_STATE=".$userinfo["b_state"];
$post[]="ADDR_COUNTRY=".func_query_first_cell("SELECT code_N3 FROM $sql_tbl[countries] WHERE code='".$userinfo["b_country"]."'");

$fp = fsockopen("purch-".$j_test.".billingservices.com","6333",$errno, $errstr, 30);
if (!$fp) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Cant connect ($errno)";
}
else {
	if ($post) {
		$post_str = "";
		foreach($post as $k => $v) {
			list($a,$b) = split("=",trim($v),2);
			$post_str .= $a.chr(0).$b.chr(0);
		}
	}

	fputs($fp, jettis_encrypt($post_str));
	$p = split('&',jettis_encrypt(fgets($fp,4096),0));
	fclose($fp);

	for ($i=0;$i<count($p);$i+=2) {
		if ($p[$i])
			$return[$p[$i]] = $p[$i+1];
	}

	if ($return['RESULT_MAIN']=='0') {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "Success (AuthCode: ".$return['AUTHORIZATION_CODE'].")";
	}
	else {
		$bill_output["code"] = 2;
		$errs = split(",",$return['RESULT_MAIN']);
		foreach ($errs as $v) {
			$msg = trim($jettis_longDesc[$v]);
			$bill_output["billmes"] .= ($msg ? $msg : "Code: ".$v);
		}
	}
}

?>
