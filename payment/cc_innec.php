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
# $Id: cc_innec.php,v 1.8 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$pp_merch = $module_params["param01"];
$pp_pass = $module_params["param02"];
$pp_shift = $module_params["param03"];
$_orderids = join("-",$secure_oid);

$post = "";
$post[] = "target_app=WebCharge_v5.06";
$post[] = "response_mode=simple";
$post[] = "response_fmt=url_encoded";
#$post[] = "cardtype=".$userinfo["card_type"]; # !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$post[] = "delimited_fmt_include_fields=true";
$post[] = "delimited_fmt_field_delimiter==";
$post[] = "delimited_fmt_value_delimiter=|";
$post[] = "username=".$pp_merch;
$post[] = "pw=".$pp_pass;
$post[] = "trantype=sale";
$post[] = "ccname=".$userinfo["card_name"];
$post[] = "ccnumber=".$userinfo["card_number"];
$post[] = "month=".substr($userinfo["card_expire"],0,2);
$post[] = "year=".substr($userinfo["card_expire"],2,2);
$post[] = "fulltotal=".$cart["total_cost"];
$post[] = "trans_id=".$pp_shift.$_orderids;
$post[] = "baddress=".$userinfo["b_address"];
$post[] = "bcity=".$userinfo["b_city"];
$post[] = "bstate=".$userinfo["b_state"];
$post[] = "bzip=".$userinfo["b_zipcode"];
$post[] = "bcountry=".$userinfo["b_country"];
$post[] = "bphone=".$userinfo["phone"];
$post[] = "email=".$userinfo["email"];
$post[] = "ccidentifier1=".$userinfo["card_cvv2"];
$post[] = "ReceiptEmail=no";

list($a,$ret)=func_https_request("POST","https://transactions.innovativegateway.com:443/servlet/com.gateway.aai.Aai",$post);

$a = split("&",$ret);$ret = "";
if($a)foreach($a as $k){list($b,$c)=split("=",$k,2);$ret[strtolower(urldecode($b))]=strip_tags(urldecode($c));}
#print_r($ret);

#    [anatransid] => 426918
#    [trans_id] => 169
#    [direct] => upm
#    [ccnumber] => xxxxxxxxxxx
#    [ccname] => xxxxxxxx
#    [bcountry] => GB
#    [ordernumber] => 622
#    [bstate] => AB
#    [username] => gatewaytest
#    [bcity] => New York
#    [fulltotal] => 28.49
#    [bphone] => +4444444444447
#    [ccidentifier1] => 543
#    [amount] => 28.49
#    [avs] =>
#    [bzip] => 10001
#    [trans_fdco_talker_msec] => 651
#    [year] => 05
#    [trans_delta_msec] => 911
#    [contact_phone_number] =>
#    [upi_delta_msec] => 922
#    [clientip] => xxxxxxxxxxxxxx5
#    [cardtype] => VISA
#    [pw] => GateTest2002
#    [email] => sdg@rrf.ru
#    [baddress] => Goncharova str., 32
#    [error] => <ul><li>The card Issuing bank returned a "DECLINE" response to our processor indicating that the Bank will not allow the credit card submitted to be authorized for the amount requested. (error code 4001)</li></ul>
#    [pi_delta_msec] => 631
#    [approval] =>
#    [response_mode] => simple
#    [target_app] => WebCharge_v5.06
#    [response_fmt] => url_encoded
#    [month] => 02
#    [delimited_fmt_include_fields] => true
#    [trantype] => sale

$avserr = array(
	"X" => "Both the zip code (the AVS 9-digit) and the street address match.",
	"Y" => "Both the zip (the AVS 5-digit) and the street address match.",
	"A" => "The street address matches, but the zip code does not match.",
	"W" => "The 9-digit zip codes matches, but the street address does not match.",
	"Z" => "The 5-digit zip codes matches, but the street address does not match.",
	"N" => "Neither the street address nor the postal code matches.",
	"R" => "Retry, System unavailable (maybe due to timeout).",
	"S" => "Service not supported.",
	"U" => "Address information unavailable.",
	"E" => "Data not available/error invalid.",
	"G" => "Non-US card issuer that does not participate in AVS"
);


if(!empty($ret['approval']))
{
	$bill_output["code"]=1;
	$bill_output["billmes"] = $ret["approval"];
}
elseif(!empty($ret["error"]))
{
	$bill_output["code"]=2;
	$bill_output["billmes"] = $ret["error"];
}

if($ret["avs"])
	$bill_output["avsmes"] = (empty($avserr[$ret["avs"]]) ? "Error Code: ".$ret["avs"] : $avserr[$ret["avs"]]);
$bill_output["billmes"].= " (ANATransId: ".$ret['anatransid'].") ";

#print "<pre>";
#print_r($bill_output);
#exit;


?>
