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
# $Id: cc_mm.php,v 1.17 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$post = array();
$post[] = "requesttype=approvalonly";
$post[] = "transtype=sale";
$post[] = "merchantid=".$module_params["param01"];
$post[] = "bname=".$userinfo["card_name"];
$post[] = "ccnumber=".$userinfo["card_number"];
$post[] = "cvv2=".$userinfo["card_cvv2"];
$post[] = "expmo=".substr($userinfo["card_expire"],0,2);
$post[] = "expye=".substr($userinfo["card_expire"],2,2);
$post[] = "baddress1=".$userinfo["b_address"];
$post[] = "bstate=".$userinfo["b_state"];
$post[] = "bcity=".$userinfo["b_city"];
$post[] = "bcountry=".$userinfo["b_country"];
$post[] = "bzipcode=".$userinfo["b_zipcode"];
$post[] = "amount=".$cart["total_cost"];
$post[] = "invoice=".$module_params["param02"].join("-",$secure_oid);

list($a,$return) = func_https_request("POST","https://secure1.merchantmanager.com:443/ccgateway.asp",$post,"&","","application/x-www-form-urlencoded",$http_location."/payment/cc_mm.php");

# approved=N&msg=Your Credit Card was Declined: TEST MODE
# approved=N&msg=Your Credit Card was Declined: Request Declined: DECLINED

$retunr = trim($return);
if (strpos($return, "approved=") !== 0) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $return;
} else {
	$arr = array();
	parse_str($return, $arr);

	$bill_output["code"] = ($arr['approved'] == "Y") ? 1 : 2;
	$bill_output["billmes"] = $arr['msg'];
}
?>
