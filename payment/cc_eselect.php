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
# $Id: cc_eselect.php,v 1.24.2.1 2006/06/15 10:10:49 max Exp $
#
# eSelect Plus DirectPost 3
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$post = array();
$post[] = "ps_store_id=".$module_params["param01"];
$post[] = "hpp_key=".$module_params["param02"];

$post[] = "charge_total=".price_format($cart["total_cost"]);
$post[] = "cc_num=".$userinfo["card_number"];
$post[] = "expMonth=".substr($userinfo["card_expire"],0,2);
$post[] = "expYear=".substr($userinfo["card_expire"],2,2);

$i = 0;
foreach($products as $product) {
	$i++;
	$post[] = "id$i=".substr($product["productcode"], 0, 10);
	$post[] = "description$i=".substr($product["product"], 0, 10);
	$post[] = "quantity$i=".$product["amount"];
	$post[] = "price$i=".price_format($product["price"]);
	$post[] = "subtotal$i=".price_format($product["price"]*$product["amount"]);
}

if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0) {
	foreach ($cart["giftcerts"] as $gc) {
		$i++;
		$post[] = "id$i=GC$i";
		$post[] = "description$i=GIFT CERTIFICATE";
		$post[] = "quantity$i=1";
		$post[] = "price$i=".price_format($gc["amount"]);
		$post[] = "subtotal$i=".price_format($gc["amount"]);
	}
}

$post[] = "cust_id=".$login;
$post[] = "order_id=".$module_params["param04"].join("-",$secure_oid);
$post[] = "lang=en-ca";
$post[] = "shipping_cost=".price_format($cart["total_cost"]-$cart["subtotal"]);

$post[] = "ship_first_name=".$userinfo['s_firstname'];
$post[] = "ship_last_name=".$userinfo['s_lastname'];
$post[] = "ship_company_name=".$userinfo['company'];
$post[] = "ship_address_one=".$userinfo['s_address'];
$post[] = "ship_city=".$userinfo['s_city'];
$post[] = "ship_state_or_province=".$userinfo['s_state'];
$post[] = "ship_postal_code=".$userinfo['s_zipcode'];
$post[] = "ship_country=".$userinfo['s_country'];
$post[] = "ship_phone=".$userinfo['phone'];
$post[] = "ship_fax=".$userinfo['fax'];

$post[] = "bill_first_name=".$bill_firstname;
$post[] = "bill_last_name=".$bill_lastname;
$post[] = "bill_company_name=".$userinfo['company'];
$post[] = "bill_address_one=".$userinfo['b_address'];
$post[] = "bill_city=".$userinfo['b_city'];
$post[] = "bill_state_or_province=".$userinfo['b_state'];
$post[] = "bill_postal_code=".$userinfo['b_zipcode'];
$post[] = "bill_country=".$userinfo['b_country'];
$post[] = "bill_phone=".$userinfo['phone'];
$post[] = "bill_fax=".$userinfo['fax'];

list($a,$return) = func_https_request("POST","https://".(($module_params["testmode"]=="Y") ? "esqa" : "www3").".moneris.com:443/HPPDP/index.php",$post);

$bill_output["cvvmes"] = "Not support";
$bill_output["avsmes"] = "Not support";

$tmp = explode("<br>", $return);
if (count($tmp) == 1) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Error: ".$return;

} else {
	$result = array();
	foreach($tmp as $v) {
		$pos = strpos($v, "=");
		if($pos === false)
			continue;
		$result[trim(substr($v, 0, $pos-1))] = trim(substr($v, $pos+1));
	}

	if ($result['result'] == "1" && intval($result['response_code']) < 50) {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "Approval code: ".$result['bank_approval_code']."; Transaction ID: ".$result['bank_transaction_id']."; Response code: ".$result['response_code'];

	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Decline: ".$result['message']."; Transaction ID: ".$result['bank_transaction_id']."; Response code: ".$result['response_code'];
	}
}

?>
