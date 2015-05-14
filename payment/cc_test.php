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
# $Id: cc_test.php,v 1.20 2006/01/11 06:56:23 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["process"]) && isset($HTTP_POST_VARS["status"]))
{
	require "./auth.php";

	$sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");

	$bill_output["code"]=$HTTP_POST_VARS["status"];
	$bill_output["billmes"]=($reason ? $reason : "Reason did not set");
	$bill_output["sessid"]=$sessid;

	require("payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$_orderids = $module_params["param02"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");
?>

<html>
<body>
<table width="100%" height="80%" border="0">
<form method="post" action="cc_test.php" name="process">
<tr>
	<td align="center" valign="middle">
Please select desire result...<br /><br />
	<table border="0" width="50%">
	<tr>
		<td colspan="2" bgcolor="#b0b0b0" align="right"><b>MerchantID</b>: [<?php echo htmlspecialchars($module_params["param01"]); ?>] | <b>OrderID</b>: [<?php echo htmlspecialchars($_orderids); ?>] | <b>Amount</b>: [<?php echo htmlspecialchars($cart["total_cost"]); ?>] &nbsp;</td>
	</tr>
	<tr>
		<td align="right">Status:</td>
		<td><select name="status">
			<option value="0">Error</option>
			<option value="1">Approved</option>
			<option value="2">Declined</option>
		</select></td>
	</tr>
	<tr>
		<td align="right">Reason:</td>
		<td><input type="test" size="30" name="reason" value=""></td>
	</tr>
	</table>
	<input type="hidden" name="process" value="xcarttest">
	<input type="hidden" name="oid" value="<?php echo htmlspecialchars($_orderids); ?>">
	<input type="submit" value="Submit data">
	</td>
</tr>
</form>
</table>
</body>
</html>
<?php
	exit;
}
?>
