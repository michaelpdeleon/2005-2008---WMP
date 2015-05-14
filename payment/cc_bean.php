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
# $Id: cc_bean.php,v 1.10.2.2 2006/06/15 10:10:49 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

$an_login = $module_params["param01"];
$an_prefix = $module_params["param04"];

$post[] = "merchant_id=".$an_login;
$post[] = "trnOrderNumber=".$an_prefix.join("-",$secure_oid);
$post[] = "trnType=P"; # P - purchase; PA - pre-auth...
$post[] = "trnCardOwner=".$userinfo["card_name"];
$post[] = "trnCardNumber=".$userinfo["card_number"];
$post[] = "trnExpMonth=".substr($userinfo["card_expire"],0,2);
$post[] = "trnExpYear=".substr($userinfo["card_expire"],2,2);
$post[] = "trnCardCvd=".$userinfo["card_cvv2"];
$post[] = "errorPage=".$https_location.DIR_CUSTOMER."/home.php";

$post[] = "ordName=".$bill_name;
$post[] = "ordEmailAddress=".$userinfo["email"];
$post[] = "ordPhoneNumber=".$userinfo["phone"];
$post[] = "ordAddress1=".$userinfo["b_address"];
$post[] = "ordCity=".$userinfo["b_city"];
$post[] = "ordProvince=".(strlen($userinfo["b_state"])!=2 ? "--" : $userinfo["b_state"]);
$post[] = "ordPostalCode=".$userinfo["b_zipcode"];
$post[] = "ordCountry=".$userinfo["b_country"];

$post[] = "shipAddress1=".$userinfo["s_address"];
$post[] = "shipCity=".$userinfo["s_city"];
$post[] = "shipProvince=".(strlen($userinfo["s_state"])!=2 ? "--" : $userinfo["s_state"]);
$post[] = "shipPostalCode=".$userinfo["s_zipcode"];
$post[] = "shipCountry=".$userinfo["s_country"];
$post[] = "shipPhoneNumber=".$userinfo["phone"];

$post[] = "trnAmount=".$cart["total_cost"];


list($a,$return) = func_https_request("POST","https://www.beanstream.com:443/scripts/process_transaction.asp",$post);
preg_match("/location:?[\t \n]?(https?.*\?)(.*)\n/mi",$a,$ret);

parse_str($ret[2],$mass);

#    [trnId] => 10000008
#    [messageId] => 1
#    [messageText] => Approved
#    [authCode] => TEST
#    [responseType] => T
#    [trnAmount] => 601.00
#    [trnDate] => 5/4/2004 3:05:20 AM
#    [trnOrderNumber] => 210
#    [trnLanguage] => eng
#    [trnCustomerName] => Dmitriy Shabaev
#    [trnEmailAddress] => xxx@Ryyy.zzz
#    [trnPhoneNumber] => +555-123-123

$bill_output["billmes"] = $mass["messageText"].$mass["errorMessage"];

if($mass["messageId"]==1 && !empty($mass["authCode"]))
{
	$bill_output["code"] = 1;
	$bill_output["billmes"].= " (authCode: ".$mass["authCode"].")";
}
else
	$bill_output["code"] = 2;

if(!empty($mass["trnId"]))
	$bill_output["billmes"].= " (TrnID: ".$mass["trnId"].")";

if($mass["avsMessage"])
	$bill_output["avsmes"] = $mass["avsMessage"]." (Code: ".$mass["avsResult"].")";

#print "<pre>";print_r($mass);
#print_r($bill_output); exit;

?>
