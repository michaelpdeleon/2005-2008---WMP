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
# $Id: cc_egateway.php,v 1.14.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if (isset($HTTP_GET_VARS["ref"])) {
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_GET_VARS["ref"]."'");
	$bill_output["code"] = $bill_output["sessid"]==$XCARTSESSID?1:2;

	require($xcart_dir."/payment/payment_ccend.php");
}
else {
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$eg_invoice = $module_params ["param03"].join("-",$secure_oid);
	$post["MerchantID"] = $module_params ["param01"];
	$post["InvoiceNumber"] = $eg_invoice;
	$post["FromIdentifier"] = $module_params ["param02"];
	$post["TotalAmount"] = $cart["total_cost"];
	$post["SuccessURL"] = $http_location.'/payment/cc_egateway.php?ref='.urlencode($eg_invoice);
	$post["Name"] = $bill_name;
	$post["Email"] = $userinfo["email"];

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".$eg_invoice."','".$XCARTSESSID."')");
?>

<html>
<body onLoad="document.process.submit();">
<form action="https://secure.egateway.com.au/payment_accept.aspx" method=POST name=process>
<?php foreach ($post as $k => $v) { ?>
<input type=hidden name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
<?php } ?>	
</form>
<table width=100% height=100%>
<tr><td align=center valign=middle>Please wait while connecting to <b>E-Gateway</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}
	exit;
?>
