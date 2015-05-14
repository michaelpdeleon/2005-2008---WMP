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
# $Id: cc_egold.php,v 1.22 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_GET_VARS["ok"]) && isset($HTTP_POST_VARS["ORDER_NUM"]))
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ORDER_NUM."'");
	$bill_output["code"] = (($HTTP_GET_VARS["ok"]=="true") ? 1 : 2);

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$accid = $module_params ["param01"];
	$accname = $module_params ["param02"];
	$curr = $module_params ["param03"];
	$prefix = $module_params ["param03"];
	$ordr = $prefix.join("-",$secure_oid);

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.e-gold.com/sci_asp/payments.asp" method=POST name=process>
  	<input type=hidden name=PAYEE_ACCOUNT value="<?php echo htmlspecialchars($accid); ?>">
  	<input type=hidden name=PAYEE_NAME value="<?php echo htmlspecialchars($accname); ?>">
  	<input type=hidden name=PAYMENT_UNITS value="<?php echo htmlspecialchars($curr); ?>">
	<input type=hidden name=PAYMENT_METAL_ID value="0">
	<input type=hidden name=PAYMENT_URL value="<?php echo $http_location."/payment/cc_egold.php?ok=true"; ?>">
	<input type=hidden name=PAYMENT_URL_METHOD value="POST">
	<input type=hidden name=STATUS_URL value="<?php echo "mailto:".htmlspecialchars($config["Company"]["orders_department"]); ?>">
	<input type=hidden name=NOPAYMENT_URL value="<?php echo $http_location."/payment/cc_egold.php?ok=false"; ?>">
	<input type=hidden name=NOPAYMENT_URL_METHOD value="POST">
	<input type=hidden name=PAYMENT_AMOUNT value="<?php echo $cart["total_cost"]; ?>">
	<input type=hidden name=BAGGAGE_FIELDS value="ORDER_NUM">
	<input type=hidden name=ORDER_NUM value="<?php echo htmlspecialchars($ordr); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>E-Gold</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
