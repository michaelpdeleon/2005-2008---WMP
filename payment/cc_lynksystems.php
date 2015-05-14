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
# $Id: cc_lynksystems.php,v 1.9.2.1 2006/06/15 10:10:49 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

$avsmes = array (
	'X'=>'Exact match for nine-digit zip and street address',
	'Y'=>'Exact match for five-digit zip and street address',
	'A'=>'Exact match for street address but not zip code',
	'W'=>'Exact match for nine-digit zip but not street address',
	'Z'=>'Exact match for five-digit zip but not address',
	'N'=>'No match',
	'U'=>'AVS Unavailable',
	'G'=>'AVS Unavailable (non-US card)',
	'R'=>'System unable to process',
	'S'=>'AVS not supported',
	'E'=>'AVS not supported',
	'B'=>'AVS not performed',
	'Q'=>'Unknown response from bank'
);

$cvvmes = array (
	'M'=>'CVV2/CVC2 Match',
	'N'=>'CVV2/CVC2 Did Not Match',
	'P'=>'Not Processed',
	'S'=>'Card does not have CVV2/CVC2 Value',
	'U'=>'Not Supported by Issuer'
);

/*$decline_reasons = array (
	"01"=>"Call for Authorization (referral for Voice Auth)",
	"02"=>"Call for Authorization (referral for Voice Auth)",
	"03"=>"Invalid Merchant",
	"04"=>"Pick Up Card",
	"05"=>"Do Not Honor",
	"06"=>"Edit Error (usually means a card is not accepted at the location - i.e. trying to charge an Amex card when you don't accept Amex cards)",
	"07"=>"Pick Up Card",
	"12"=>"Invalid Processing Code",
	"13"=>"Invalid Amount",
	"14"=>"Invalid Card Number",
	"15"=>"No Such Issuer",
	"19"=>"RFI Error (Reenter Transaction)",
	"30"=>"Format Error",
	"39"=>"No Credit Account",
	"40"=>"Requested Function Not Supported",
	"41"=>"Pick Up Card (Lost Card)",
	"42"=>"Pick Up Card (Stolen Card)",
	"43"=>"Stolen Card",
	"51"=>"Insufficient Funds",
	"52"=>"No Checking Account",
	"53"=>"No Savings Account",
	"54"=>"Expired Card",
	"55"=>"Incorrect PIN",
	"56"=>"No Card Record",
	"57"=>"Transaction not Permitted to Cardholder",
	"58"=>"Transaction not Permitted to Terminal",
	"61"=>"Exceeds Withdrawal Amount Limit",
	"62"=>"Restricted Card",
	"63"=>"Security Violation",
	"64"=>"Exceeds Withdrawal Frequency Limit",
	"65"=>"Exceeds Withdrawal Count Limit",
	"68"=>"Response Received Too Late (Time Out from Issuer)",
	"75"=>"Allowable number of PIN tries Exceeded",
	"76"=>"Invalid/Nonexistent "To" Account Specified",
	"77"=>"Invalid/Nonexistent "From" Account Specified",
	"78"=>"Invalid/Nonexistent Account Specified (General)",
	"80"=>"Invalid Date",
	"83"=>"Unable to verify PIN",
	"91"=>"Issuer INC or switch is inoperative",
	"92"=>"Financial institution facility not found for routing",
	"93"=>"Transaction Can Not Be Completed",
	"96"=>"System Malfunction",

);
*/

$lynk_merchant_id	= $module_params['param01'];
$lynk_store_id		= $module_params['param02'];
$lynk_terminal_id	= $module_params['param03'];
$script = ($module_params['testmode'] == 'Y') ? 'testPmt' : 'Pmt';

// SVCType=Authorize&Signature=1&Storeid=400001&MerchantID=542929001000041&TerminalId=LK117709&cardnumber=4446661234567892&expirationdate=1203&EntryMode=1&Amount=1.00
$lynk_request[] = 'SvcType=Sale';
$lynk_request[] = 'CustOrderId='.$module_params['param04'].join("-",$secure_oid);
$lynk_request[] = 'FirstName='.$bill_firstname;
$lynk_request[] = 'LastName='.$bill_lastname;
$lynk_request[] = 'StreetAddress='.$userinfo['b_address'];
$lynk_request[] = 'City='.$userinfo['b_city'];
$lynk_request[] = 'State='.$userinfo['b_state'];
$lynk_request[] = 'Zip='.$userinfo['b_zipcode'];
$lynk_request[] = 'Country='.$userinfo['b_country'];
$lynk_request[] = 'Email='.$userinfo['email'];
$lynk_request[] = 'PaymentBrand='.$userinfo['card_type'];
$lynk_request[] = 'CardNumber='.$userinfo['card_number'];
$lynk_request[] = 'ExpirationDate='.$userinfo['card_expire'];
$lynk_request[] = 'CVV2='.$userinfo['card_cvv2'];

$lynk_request[] = 'ShipToStreetAddress='.$userinfo['s_address'];
$lynk_request[] = 'ShipToCity='.$userinfo['s_city'];
$lynk_request[] = 'ShipToState='.$userinfo['s_state'];
$lynk_request[] = 'ShipToZipCode='.$userinfo['s_zipcode'];
$lynk_request[] = 'ShipToCountry='.$userinfo['s_country'];

$lynk_request[] = 'StoreID='.$lynk_store_id;
$lynk_request[] = 'MerchantID='.$lynk_merchant_id;
$lynk_request[] = 'TerminalID='.$lynk_terminal_id;
$lynk_request[] = 'OrderDesc='.$config['Company']['company_name'];
$lynk_request[] = 'Amount='.$cart['total_cost'];

#print_r($lynk_request);

list($a,$return) = func_https_request("POST", "https://sundev.lynk-systems.com:443/".$script, $lynk_request);
parse_str($return, $res);

#print $return;
# TransactionStatus=1&ErrorCode=ERROR_AUTH_DECLINED&ErrorMsg=Card declined, Response Code : 12, Reason returned : INVALID TRANSACTION
# TransactionStatus=0&OrderId=4483036&AVSResponse=N&ApprovalCode=000784&CVV2Response=N

if((int)$res['TransactionStatus'] === 0) {
	$bill_output["code"] = 1;
	$bill_output["billmes"] = " Approval Code: ".$res['ApprovalCode'];
} else {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $res['ErrorMsg']." (ErrorCode: ".$res['ErrorCode'].")";
}

if (!empty($res['AVSResponse'])) {
	$bill_output["avsmes"] =  (empty($avsmes[$res['AVSResponse']])) ? 'AVSResponse: '.$res['AVSResponse'] : $avsmes[$res['AVSResponse']];
}

if (!empty($res['CVV2Response'])) {
	$bill_output["cvvmes"] =  (empty($cvvmes[$res['CVV2Response']])) ? 'CVV2Response: '.$res['CVV2Response'] : $cvvmes[$res['CVV2Response']];
}

#print_r($bill_output);
#exit;
?>
