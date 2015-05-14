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
# $Id: cc_edinar.php,v 1.22 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && isset($HTTP_GET_VARS["ok"]) && isset($HTTP_GET_VARS["ordr"]))
{
	require "./auth.php";

    $bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ordr."'");
    $bill_output["code"] = (($HTTP_GET_VARS["ok"]=="true") ? 1 : 2);

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$accid = $module_params ["param01"];
	$curr = $module_params ["param02"];
	$prefix = $module_params ["param03"];
	$ordr = $prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.e-dinar.com/acct/n_sci.cgi" method=POST name=process>
  	<input type=hidden name=Payee_Account value="<?php echo htmlspecialchars($accid); ?>">
  	<input type=hidden name=PAY_IN value="<?php echo htmlspecialchars($curr); ?>">
  	<input type=hidden name=Memo value="<?php echo htmlspecialchars($ordr); ?>">
  	<input type=hidden name=confirm_method value="email">
	<input type=hidden name=confirm_email value="<?php echo htmlspecialchars($config["Company"]["orders_department"]); ?>">
	<input type=hidden name=success_url value="<?php echo $http_location."/payment/cc_edinar.php?ok=true&ord=".$ordr; ?>">
	<input type=hidden name=no_success_url value="<?php echo $http_location."/payment/cc_edinar.php?ok=false&ord=".$ordr; ?>">
	<input type=hidden name=Amount value="<?php echo $cart["total_cost"]; ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>E-Dinar</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
