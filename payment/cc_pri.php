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
# $Id: cc_pri.php,v 1.10 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$mid = $module_params["param01"];
$regkey = $module_params["param02"];

$post = "";
$post[] = "MerchantID=".$mid;
$post[] = "RegKey=".$regkey;
$post[] = "Amount=".$cart["total_cost"];
$post[] = "CardHolderName=".$userinfo["card_name"];
$post[] = "CardNumber=".$userinfo["card_number"];
$post[] = "Expiration=".$userinfo["card_expire"];
$post[] = "Address=".$userinfo["b_address"];
$post[] = "ZipCode=".$userinfo["b_zipcode"];
$post[] = "RefID=".$module_params["param03"].join("-",$secure_oid);
$post[] = "TrackData=".$config['Company']['company_name'];

#print_r($post);

list($a,$return) = func_https_request("POST","https://webservices.primerchants.com:443/creditcard.asmx/CreditCardSale",$post);

#print "<html>";
#print $return;

#<BankCardDebitStatus ... />
#  <Status>Declined</Status>
#  <Amount>14.25</Amount>
#  <SettledDate>0001-01-01T00:00:00.0000000-08:00</SettledDate>
#  <RefID>xcart72</RefID>
#  <TransID>34</TransID>
#  <PostedDate>2003-07-04T02:17:07.9769168-07:00</PostedDate>
#  <Message>test transaction</Message>
#</BankCardDebitStatus>

# test data for auth.response
#    [0] => MerchantID=10011
#    [1] => RegKey=1234
#    [2] => Amount=0.9
#    [3] => CardHolderName=firstname lastname
#    [4] => CardNumber=4242424242424242
#    [5] => Expiration=1010
#    [6] => Address=somestreet str., 32
#    [7] => ZipCode=10001
#    [8] => RefID=someref
#    [9] => TrackData=X-Cart

# <BankCardDebitStatus ... />
#  <Status>Authorized</Status>
#  <AuthCode>Test00</AuthCode>
#  <Amount>0.1</Amount>
#  <SettledDate>0001-01-01T00:00:00.0000000-08:00</SettledDate>
#  <RefID>123</RefID>
#  <TransID>38</TransID>
#  <PostedDate>2003-07-04T02:39:51.6276756-07:00</PostedDate>
#  <Message>test transaction</Message>
#  </BankCardDebitStatus>

$avserr = array(
	"X" => "Exact match - 9 digit zip",
	"Y" => "Exact match - 5 digit zip",
	"A" => "Address match only",
	"W" => "9-digit zip match only",
	"Z" => "5-digit zip match only",
	"N" => "No address or zip match",
	"U" => "Address unavailable",
	"G" => "Non-U.S. Issuer",
	"R" => "Issuer system unavailable"
);

preg_match("/<Status>(.*)<\/Status>/",$return,$ret);
$bill_output["billmes"] = $ret[1].": ";

if($ret[1] == "Authorized" || $ret[1] == "Settled")
{
	$bill_output["code"] = 1;
	preg_match("/<AuthCode>(.*)<\/AuthCode>/",$return,$out);
	$bill_output["billmes"].= $out[1];
}
else
{
	$bill_output["code"] = 2;
	preg_match("/<Message>(.*)<\/Message>/",$return,$out);
	$bill_output["billmes"].= $out[1];
}

if(preg_match("/<TransID>(.*)<\/TransID>/",$return,$out))
	$bill_output["billmes"].= " (TransID: ".$out[1].")";

if(preg_match("/<AVSCode>(.*)<\/AVSCode>/",$return,$out))
	$bill_output["avsmes"] = empty($avserr[$out[1]]) ? "AVS Code: ".$out[1] : $avserr[$out[1]];

#print_r($bill_output);
#exit;

?>
