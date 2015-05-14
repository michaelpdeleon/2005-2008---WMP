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
# $Id: cc_2checkoutcom.php,v 1.39.2.1 2006/06/15 10:10:48 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if (!empty($HTTP_POST_VARS["x_invoice_num"]) || !empty($HTTP_GET_VARS["x_invoice_num"])) {
	require "./auth.php";

	$tmp = func_query_first("SELECT sessionid,param1 FROM $sql_tbl[cc_pp3_data] WHERE ref='$x_invoice_num'");
	$bill_output["sessid"] = $tmp["sessionid"];

	$s = func_query_first("select param01,param03 from $sql_tbl[ccprocessors] where processor='cc_2checkoutcom.php'");

#x_response_code 1
#x_response_subcode 1
#x_response_reason_code 1
#x_auth_code 123456
#x_avs_code P
#x_trans_id 29378-1382296

	$bill_output["code"] = ($x_response_code==1 ? 1 : 2);

	if (!empty($x_2checked) && $x_2checked != "Y") $bill_output["code"] = 3;

	if(($x_amount!=$tmp["param1"] && $x_total!=$tmp["param1"]) || strtoupper(md5($s["param03"].$s["param01"].$x_trans_id.$tmp["param1"]))!=$x_MD5_Hash)
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "MD5 HASH is invalid!";
	}
	else $bill_output["billmes"] = "";
	if(!empty($x_auth_code))			$bill_output["billmes"].= " (AuthCode: ".$x_auth_code.") ";
	if(!empty($x_trans_id))				$bill_output["billmes"].= " (TransID: ".$x_trans_id.") ";
	if(!empty($x_response_subcode))		$bill_output["billmes"].= " (subcode/reasoncode: ".$x_response_subcode."/".$x_response_reason_code.") ";
	if(!empty($x_avs_code))				$bill_output["avsmes"]  = "AVS Code: ".$x_avs_code;

	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	if (!function_exists("func_2co_convert")) {
		function func_2co_convert($s) {
			return htmlspecialchars(str_replace("#"," ", $s));
		}
	}

	$_orderids = $module_params ["param02"].join("-",$secure_oid);
	$merchant = $module_params ["param01"];
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,param1) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','".$cart["total_cost"]."')");

# old URL: https://www.2checkout.com/cgi-bin/ccbuyers/purchase1s.2c
?>
<html>
<body onLoad="document.process.submit();">
<form action="https://www.2checkout.com/cgi-bin/Abuyers/purchase.2c" method="POST" name="process">
	<input type="hidden" name="x_login" value="<?php echo htmlspecialchars($merchant); ?>">
	<input type="hidden" name="x_amount" value="<?php echo $cart["total_cost"]; ?>">
	<input type="hidden" name="x_invoice_num" value="<?php echo func_2co_convert($_orderids); ?>">
	<input type="hidden" name="x_First_Name" value="<?php echo func_2co_convert($bill_firstname); ?>">
	<input type="hidden" name="x_Last_Name" value="<?php echo func_2co_convert($bill_lastname); ?>">
	<input type="hidden" name="x_Phone" value="<?php echo func_2co_convert($userinfo["phone"]); ?>">
	<input type="hidden" name="x_Email" value="<?php echo func_2co_convert($userinfo["email"]); ?>">
	<input type="hidden" name="x_Address" value="<?php echo func_2co_convert($userinfo["b_address"]); ?>">
	<input type="hidden" name="x_City" value="<?php echo func_2co_convert($userinfo["b_city"]); ?>">
	<input type="hidden" name="x_State" value="<?php echo func_2co_convert($userinfo["b_state"] ? $userinfo["b_state"] : "n/a"); ?>">
	<input type="hidden" name="x_Zip" value="<?php echo func_2co_convert($userinfo["b_zipcode"]); ?>">
	<input type="hidden" name="x_Country" value="<?php echo func_2co_convert($userinfo["b_country"]); ?>">
</form>
<table width=100% height=100%>
<tr><td align=center valign=middle>Please wait while connecting to <b>2checkout.com</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}
	exit;
?>
