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
# $Id: cc_securetrading.php,v 1.21.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["stauthcode"]) {
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["orderref"]."'");
	$bill_output["code"] = ($stauthcode=="DECLINED" ? 2 : 1);
	$bill_output["billmes"] = "(Transaction Ref: $streference) (ST Confidence: $stconfidence)";

	require($xcart_dir."/payment/payment_ccend.php");
}
else {
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$sc_merchant = $module_params ["param01"];
	$sc_orderids = $module_params ["param02"].join("-",$secure_oid);
	$sc_currency = $module_params ["param03"];
	$returnurl = $http_location."/payment/cc_securetrading.php";
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($sc_orderids)."','".$XCARTSESSID."')");
?>
<html>
<body onLoad="document.process.submit();">
<form ACTION="https://securetrading.net/authorize/form.cgi" METHOD=POST NAME="process">
<input TYPE=hidden NAME="amount" VALUE="<?php echo round($cart["total_cost"]*100);?>">
<input TYPE=hidden NAME="orderref" VALUE="<?php echo htmlspecialchars($sc_orderids); ?>">
<input TYPE=hidden NAME="orderinfo" VALUE="Default order information">
<input TYPE=hidden NAME="name" VALUE="<?php echo htmlspecialchars($bill_name);?>">
<input TYPE=hidden NAME="address" VALUE="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
<input TYPE=hidden NAME="town" VALUE="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
<input TYPE=hidden NAME="county" VALUE="<?php echo htmlspecialchars($userinfo["b_state"]?$userinfo["b_state"]:"n/a");?>">
<input TYPE=hidden NAME="country" VALUE="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
<input TYPE=hidden NAME="postcode" VALUE="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
<input TYPE=hidden NAME="telephone" VALUE="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
<input TYPE=hidden NAME="fax" VALUE="">
<input TYPE=hidden NAME="email" VALUE="<?php echo htmlspecialchars($userinfo["email"]); ?>">
<input TYPE=hidden NAME="url" VALUE="<?php echo $xcart_catalogs['customer'].'/cart.php'; ?>">
<input TYPE=hidden NAME="currency" VALUE="<?php echo htmlspecialchars($sc_currency); ?>">
<input TYPE=hidden NAME="requiredfields" VALUE="name,email">
<input TYPE=hidden NAME="merchant" VALUE="<?php echo htmlspecialchars($sc_merchant); ?>">
<input TYPE=hidden NAME="merchantemail" VALUE="<?php echo htmlspecialchars($config["Company"]["orders_department"]); ?>">
<input TYPE=hidden NAME="customeremail" VALUE="1">
<input TYPE=hidden NAME="settlementday" VALUE="1">
<input TYPE=hidden NAME="callbackurl" VALUE="1">
<input TYPE=hidden NAME="failureurl" VALUE="1">
</form>
<table WIDTH=100% HEIGHT=100%>
<tr><td ALIGN=CENTER VALIGN=MIDDLE>Please wait while connecting to <b>SECURETRADING.com</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}
	exit;
?>
