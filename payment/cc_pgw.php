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
# $Id: cc_pgw.php,v 1.21.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$post = "";
$post[] = "account_token=".($module_params["testmode"]!="N"?"TEST":"").$module_params["param01"];
$post[] = "charge_type=SALE";
$post[] = "order_user_id=".$module_params["param03"].join("-",$secure_oid);
$post[] = "bill_address_one=".$userinfo["b_address"];
$post[] = "bill_address_two=".$userinfo["b_address_2"];
$post[] = "bill_city=".$userinfo["b_city"];
$post[] = "bill_company=".$userinfo["company"];
$post[] = "bill_country_code=".$userinfo["b_country"];
$post[] = "bill_customer_title=".$userinfo["b_title"];
$post[] = "bill_email=".$userinfo["email"];
$post[] = "bill_first_name=".$bill_firstname;
$post[] = "bill_last_name=".$bill_lastname;
$post[] = "bill_middle_name=";
$post[] = "bill_note=".preg_replace("/[^\w\d ]/", "", preg_replace("/&[\d]+;/", "", $config['Company']['company_name']));
$post[] = "bill_phone=".$userinfo["phone"];
$post[] = "bill_postal_code=".$userinfo["b_zipcode"];
$post[] = "bill_state_or_province=".$userinfo["b_state"];
#$post[] = "bayer_code=";
$post[] = "card_brand=".$userinfo["card_type"];
#$post[] = "cavv=";
$post[] = "charge_total=".$cart["total_cost"];
$post[] = "credit_card_number=".$userinfo["card_number"];
$post[] = "credit_card_verification_number=".$userinfo["card_cvv2"];
$post[] = "customer_ip_address=".$REMOTE_ADDR;
$post[] = "expire_month=".(0+substr($userinfo["card_expire"],0,2));
$post[] = "expire_year=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "order_customer_id=".$module_params["param03"].join("-",$secure_oid);
$post[] = "order_description=Order #".join("-",$secure_oid)."; customer: ".$userinfo['login'];
#$post[] = "reference_id=";
$post[] = "ship_address_one=".$userinfo["s_address"];
$post[] = "ship_address_two=".$userinfo["s_address_2"];
$post[] = "ship_city=".$userinfo["s_city"];
$post[] = "ship_company=".$userinfo["company"];
$post[] = "ship_country_code=".$userinfo["b_country"];
$post[] = "ship_customer_title=".$userinfo["s_title"];
$post[] = "ship_email=".$userinfo["email"];
$post[] = "ship_fax=".$userinfo["fax"];
$post[] = "ship_first_name=".$userinfo["s_firstname"];
$post[] = "ship_last_name=".$userinfo["s_lastname"];
$post[] = "ship_middle_name=";
$post[] = "ship_note=".$config['Company']['company_name'];
$post[] = "ship_phone=".$userinfo["phone"];
$post[] = "ship_postal_code=".$userinfo["s_zipcode"];
$post[] = "ship_state_or_province=".$userinfo["s_state"];
$post[] = "transaction_type=CREDIT_CARD";
$post[] = "version_id=PHP Plug v1.6.5";

list($a,$return)=func_https_request("POST","https://etrans.paygateway.com:443/TransactionManager",$post);

$ret = 0;
$mess = '';
if(preg_match("/response_code=(\d+)\n/U",$return,$out)) {
	$ret = $out[1];
}
if(preg_match("/response_code_text=(.+)\n/U",$return,$out)) {
	$mess = $out[1];
}
if($ret != 1) {
	$mess .= " Response code: ".$ret.";";
}
if(preg_match("/bank_transaction_id=(.+)\n/U",$return,$out)) {
	$mess .= " Bank transaction id: ".$out[1].";";
}
if(preg_match("/bank_approval_code=(.+)\n/U",$return,$out)) {
	$mess .= " Bank approval code: ".$out[1].";";
}
if(preg_match("/avs_code=(.+)\n/U",$return."\n",$out)) {
	$bill_output["avsmes"] = $out[1];
}
if(preg_match("/credit_card_verification_response=(.+)\n/U",$return."\n",$out)) {
	$bill_output["cvvmes"].= $out[1];
}
if(preg_match("/capture_reference_id=(.+)\n/",$return,$out)) {
	$mess .= " ReferenceID: ".$out[1].";";
}
if(preg_match("/order_id=(.+)\n/",$return,$out)) {
	$mess .= " OrderID: ".$out[1].";";
}

$bill_output["code"] = (($ret == 1)?1:2);
$bill_output["billmes"] = $mess;
?>
