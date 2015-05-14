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
# $Id: cc_worldpay.php,v 1.43.2.2 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && !empty($HTTP_POST_VARS["cartId"]) && !empty($HTTP_POST_VARS["transStatus"]))
{
	require "./auth.php";

# rawAuthMessage
# transStatus (Y for Success)
# transId

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$cartId."'");
	$bill_output["code"] = (($transStatus == "Y") ? 1 : 2);
	
	if(!empty($rawAuthMessage))
		$bill_output["billmes"] = $rawAuthMessage;
	if(!empty($transId))
		$bill_output["billmes"].= " (TransId: ".$transId.")";

	$weblink = 1;
	echo "<wpdisplay item=banner><br>\n";
	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$worldpay_login = $module_params["param01"];
	$worldpay_curr = $module_params["param02"];
	$worldpay_modes = array("A"=>100, "D"=>101, "N"=>0);
	$worldpay_test = $worldpay_modes[$module_params["testmode"]];
	$worldpay_prefix = $module_params["param04"];

	$ordr = str_replace(" ", "", $worldpay_prefix).join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	$fields = array(
		"instId" => $worldpay_login,
		"cartId" => $ordr,
		"amount" => $cart["total_cost"],
		"currency" => $worldpay_curr,
		"testMode" => $worldpay_test,
		"desc" => "Order #".$ordr,
		"name" => $bill_name,
		"tel" => $userinfo["phone"],
		"email" => $userinfo["email"],
		"address" => $userinfo["b_address"]." ".$userinfo["b_address_2"].", ".$userinfo["b_city"].", ".$userinfo["b_statename"].", ".$userinfo["b_countryname"],
		"postcode" => $userinfo["b_zipcode"],
		"country" => $userinfo["b_country"]
	);
	func_create_payment_form("https://select.worldpay.com/wcc/purchase", $fields, "WorldPay.com");

}
exit;

?>
