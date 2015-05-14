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
# $Id: cc_cambist.php,v 1.12 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_merch = $module_params["param01"];
$pp_mname = $module_params["param02"];
$pp_test = ($module_params["testmode"]=='Y') ? "icvtest.pl" : "authorize.pl";

$post = "";
$post[] = "MerchantID=".$pp_merch;
$post[] = "MerchantName=".$pp_mname;
$post[] = "fulltotal=".$cart["total_cost"];
$post[] = "TransactionType=Sale";
$post[] = "MerchantEmail=".$config["Company"]["orders_department"];
$post[] = "customerid=".$login;
$post[] = "AVSVerify=Y";
$post[] = "UseCVV2=Y";
$post[] = "CVV2=".$userinfo["card_cvv2"];
$post[] = "CardTypesAccepted=Visa,MasterCard,Amex,Discover";
$post[] = "BillName=".$userinfo["card_name"];
$post[] = "BillStreet=".$userinfo["b_address"];
$post[] = "BillCity=".$userinfo["b_city"];
$post[] = "BillState=".$userinfo["b_state"];
$post[] = "BillZip=".$userinfo["b_zipcode"];
$post[] = "BillPhone=".$userinfo["phone"];
$post[] = "BillCountry=".$userinfo["b_country"];
$post[] = "BillEmail=".$userinfo["email"];
$post[] = "BillCreditCard=".$userinfo["card_number"];
$post[] = "ExpirationMonth=".substr($userinfo["card_expire"],0,2);
$post[] = "ExpirationYear=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "DirectResponse=Y";
$post[] = "form_action=AUTHORIZE PAYMENT";

list($a,$return)=func_https_request("POST","https://cambist.com:443/cgi-bin/".$pp_test,$post,"&","","",$current_location."/payment");
$return = "&".strtr($return,"\t","&")."&";

#Array
#    [0] => approved=N
#    [1] => approval_code=Invalid Card Number  Use 5419840000000003 only
#    [2] => AprCode=Demo32
#    [3] => AvsCode=Y
#    [4] => IcvCode=Y
#    [5] => Cvv2Code=

#    [0] => approved=Y
#    [1] => approval_code=Demo12
#    [2] => authentication=sdd/JrPojE4tY
#    [3] => AprCode=Demo12
#    [4] => AvsCode=Y
#    [5] => IcvCode=Y
#    [6] => Cvv2Code=M
#    [7] => AVSVerify=Y

if(preg_match("/approved=Y/",$return))
{
	$bill_output["code"] = 1;
	if(preg_match("/AprCode=(.*)&/U",$return,$out))
		$bill_output["billmes"] ="(AuthCode=".$out[1].")";
	if(preg_match("/authentication=(.*)&/U",$return,$out))
		$bill_output["billmes"].="(authentication=[".$out[1]."])";
}
else
{
	$bill_output["code"] = 2;
	if(preg_match("/approval_code=(.*)&/U",$return,$out))
		$bill_output["billmes"] =$out[1];
}

if(preg_match("/AvsCode=(.*)&/U",$return,$out))
{
$avsarr = array(
	"A"=>"Address matched, Zip not matched",
	"N"=>"Address not matched, Zip not matched",
	"W"=>"Address not matched, 5/9 Digit Zip matched",
	"X"=>"Address matched, 5/9 Digit Zip matched",
	"Y"=>"Address matched, 5 Digit Zip matched",
	"Z"=>"Address not matched, 5 Digit Zip matched",
	"R"=>"Retry, System Unavailable or timeout",
	"E"=>"Edit Error, AVS not available(?)",
	"G"=>"AVS Unavailable (Non-US Card)",
	"S"=>"AVS System Down",
	"U"=>"AVS Unavailable"
);
	$bill_output["avsmes"] = $avsarr[$out[1]];
}

if(preg_match("/Cvv2Code=(.*)&/U",$return,$out))
{
$cvvarr = array(
	"M"=>"Matched",
	"N"=>"Not Matched",
	"P"=>"Not Processed",
	"S"=>"Should be on card, but not so indicated",
	"U"=>"Issuer Not Certified and/or has not provided encryption key",
	"X"=>"No Response"
);
	$bill_output["cvvmes"].= $cvvarr[$out[1]];
}

?>
