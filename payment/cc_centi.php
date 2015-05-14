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
# $Id: cc_centi.php,v 1.21 2006/01/19 15:25:24 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["x_response_code"]) && isset($HTTP_POST_VARS["x_trans_id"]))
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$x_invoice_num."'");

	if($HTTP_POST_VARS["x_response_code"] == 1)
	{	$bill_output["code"] = 1;
		$bill_output["billmes"] = "TransID=".$x_trans_id;
	}
	else
		$bill_output["code"] = 2;


	$weblink=1;
	require("payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_merch = $module_params["param01"];
	$pp_lang = $module_params["param02"];
	$_orderids = $module_params["param03"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");
	$url = $http_location."/payment/cc_centi.php";

?>
<html>
<body onLoad="document.process.submit();">
  <form method=post action="http://pay.centipaid.com/cart.php" name=process>
	<input type=hidden name=x_Login value="<?php echo htmlspecialchars($pp_merch); ?>">
	<input type=hidden name=x_Invoice_Num value="<?php echo htmlspecialchars($_orderids); ?>">
    <input type=hidden name=x_Amount value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
    <input type=hidden name=x_Lang value="<?php echo htmlspecialchars($pp_lang); ?>">
	<input type=hidden name=x_ADC_Relay_Response value="TRUE">
	<input type=hidden name=x_ADC_URL value="<?php echo htmlspecialchars($url); ?>">
	<input type=hidden name=x_Cust_ID value="<?php echo htmlspecialchars($userinfo["login"]); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>CentiPaid</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
