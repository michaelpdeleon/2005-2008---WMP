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
# $Id: cc_anz_mh.php,v 1.3 2006/01/11 06:56:22 mclap Exp $
#
# Merchant-Hosted payment
#
if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$post = array();
$post[] = "vpc_Version=1";
$post[] = "vpc_Command=pay";
$post[] = "vpc_MerchTxnRef=".$module_params["param04"].join("-",$secure_oid);
$post[] = "vpc_AccessCode=".$module_params["param02"];
$post[] = "vpc_Merchant=".$module_params["param01"];
$post[] = "vpc_OrderInfo=Order #".join("-",$secure_oid)."; customer: ".$userinfo['login'];
$post[] = "vpc_Amount=".str_replace(".", "", (string)$cart["total_cost"]);
$post[] = "vpc_CardNum=".$userinfo["card_number"];
$post[] = "vpc_CardExp=".substr($userinfo["card_expire"], 2,2).substr($userinfo["card_expire"], 0,2);

list($a,$return) = func_https_request("POST","https://migs.mastercard.com.au:443/vpcdps",$post);

$return = explode("&", $return);
$result = array();
foreach ($return as $v) {
	$pos = strpos($v, "=");
	if ($pos !== false) {
		$result[substr($v, 0, $pos)] = trim(urldecode(substr($v, $pos+1)));
	}
}

if ($result['vpc_TxnResponseCode'] == "0") {
	$bill_output['code'] = 1;
	$bill_output['billmes'] = "Approved. Transaction ID: $result[vpc_TransactionNo];";
} else {
	$bill_output['code'] = 2;
	$bill_output['billmes'] = "Declined: Result code: $result[vpc_TxnResponseCode] / $result[vpc_AcqResponseCode]; Message: $result[vpc_Message]; Transaction ID: $result[vpc_TransactionNo];";
}

?>
