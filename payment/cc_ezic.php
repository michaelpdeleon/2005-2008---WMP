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
# $Id: cc_ezic.php,v 1.9.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_merch = $module_params["param01"];
$pp_siteid = $module_params["param02"];

$avserr = array(
	"X" => "Address and ZIP code match ",
	"Y" => "Address and ZIP code match ",
	"F" => "Address and ZIP code match ",
	"D" => "Address and ZIP code match ",
	"M" => "Address and ZIP code match ",
	"W" => "ZIP code match, address is wrong ",
	"Z" => "ZIP code match, address is wrong ",
	"A" => "Address match, ZIP code is wrong ",
	"B" => "Address match, ZIP code is wrong ",
	"P" => "Address match, ZIP code is wrong ",
	"N" => "No match, address and ZIP code are wrong ",
	"U" => "No data from issuer/banknet switch ",
	"R" => "System unable to process ",
	"S" => "Address verification not supported ",
	"E" => "Error, AVS not supported for your business",
	"?" => "Unrecognized (none of the above) response codes ",
	"_" => "No AVS data",
	"C" => "(Intl) Invalid address and ZIP format ",
	"I" => "(Intl) Address not verifiable ",
	"O" => "(Intl) No response from bank ",
	"G" => "(Intl) Global non-verifiable address "
);

$cvverr = array(
	"M" => "CVV2 match",
	"P" => "CVV2 not processed",
	"U" => "No CVV2 data from issuer",
	"_" => "No CVV2 data",
	"N" => "CVV2 does not match",
	"S" => "Card has CVV2, customer says it doesn't"
);


$post = array();
$post[] = "account_id=".$pp_merch;
$post[] = "pay_type=C";
$post[] = "tran_type=S";
$post[] = "site_tag=".$pp_siteid;
$post[] = "trans_id=".$module_params["param03"].join("-",$secure_oid);
$post[] = "card_number=".$userinfo["card_number"];
$post[] = "card_expire=".$userinfo["card_expire"];
$post[] = "card_cvv2=".$userinfo["card_cvv2"];
$post[] = "amount=".$cart["total_cost"];
$post[] = "tax_amount=".($cart["total_cost"]-$cart["shipping_cost"]);
$post[] = "ship_amount=".$cart["shipping_cost"];
$post[] = "bill_name1=".$bill_firstname;
$post[] = "bill_name2=".$bill_lastname;
$post[] = "bill_street=".$userinfo["b_address"];
$post[] = "bill_city=".$userinfo["b_city"];
$post[] = "bill_state=".$userinfo["b_state"];
$post[] = "bill_zip=".$userinfo["b_zipcode"];
$post[] = "ship_name1=".$userinfo["s_firstname"];
$post[] = "ship_name2=".$userinfo["s_lastname"];
$post[] = "ship_street=".$userinfo["s_address"];
$post[] = "ship_city=".$userinfo["s_city"];
$post[] = "ship_state=".$userinfo["s_state"];
$post[] = "ship_zip=".$userinfo["s_zipcode"];
$post[] = "cust_email=".$userinfo["email"];
$post[] = "cust_phone=".$userinfo["phone"];
$post[] = "cust_ip=".$REMOTE_ADDR;

list($a,$return)=func_https_request("POST","https://secure.ezic.com:1402/gw/sas/direct3.0",$post);

#HTTP/1.0 200 OK Declined
#CONNECTION
#close
#CONTENT-LENGTH
#83
#CONTENT-TYPE
#application/x-www-form-urlencoded
#-------
#auth_msg=DECLINE+005&auth_date=2003-10-30+05%3A56%3A50.0&status_code=0&trans_id=228

#HTTP/1.0 200 OK Approved
#CONNECTION
#close
#CONTENT-LENGTH
#125
#CONTENT-TYPE
#application/x-www-form-urlencoded
#-------
#auth_msg=TEST+APPROVED&avs_code=X&auth_date=2003-10-30+06%3A07%3A54.0&status_code=1&trans_id=229&auth_code=999999&cvv2_code=M

if (!empty($return)) {

	$a = split("&",$return);
	$ret = array();
	if (!empty($a)) {
		foreach ($a as $v) {
			list($b,$c) = split("=",$v);
			$ret[$b] = urldecode($c);
		}
	}

	$mess = $ret["auth_msg"];

	if ($ret["auth_date"])
		$mess .= " (AuthDate: ".$ret["auth_date"].")";

	if($ret["trans_id"])
		$mess .= " (TransID: ".$ret["trans_id"].")";

	if ($ret["status_code"] == 1 || $ret["status_code"] == 'I') {
		$bill_output["code"] = 1;

		if($ret["auth_code"])
			$mess .= " (Auth Code: ".$ret["auth_code"].")";
		$bill_output["billmes"] = $mess;
	
		if($ret["avs_code"])
			$bill_output["avsmes"] = $avserr[$ret["avs_code"]] ? $avserr[$ret["avs_code"]] : "AVS Code: ".$ret["avs_code"] ;

		if($ret["cvv2_code"])
			$bill_output["cvvmes"] = $cvverr[$ret["cvv2_code"]] ? $cvverr[$ret["cvv2_code"]] : "CVV2 Code: ".$ret["cvv2_code"] ;
	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $mess;
	}

} else {
	preg_match("/HTTP\/1\.0(.*)/",$a,$o);
	$mess = $o[1];
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $mess;
}

?>
