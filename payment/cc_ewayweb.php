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
# $Id: cc_ewayweb.php,v 1.20.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["ewayTrxnStatus"]))
{
	require "./auth.php";

# approved
#  'ewayTrxnStatus' => 'True',
#  'ewayTrxnNumber' => '',
#  'ewayTrxnReference' => '9999999',
#  'eWAYoption1' => '',
#  'eWAYoption2' => '',
#  'eWAYoption3' => 'TRUE',
#  'eWAYresponseCode' => '08',
#  'eWAYresponseText' => 'TRANSACTION APPROVED',
#  'ewayReturnAmount' => '$132.00',
#  'eWAYAuthCode' => '002644',

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["eWAYoption1"]."'");

	$bill_output["code"] = (preg_match("/^true$/i",$HTTP_POST_VARS["ewayTrxnStatus"]) ? 1 : 2);
	$bill_output["billmes"] = "";
	if(!empty($HTTP_POST_VARS["eWAYAuthCode"]))	$bill_output["billmes"].= " (ewayAuthCode: ".$HTTP_POST_VARS["eWAYAuthCode"].") ";
	if(!empty($HTTP_POST_VARS["ewayTrxnError"]))	$bill_output["billmes"].= " (ewayTrxnError: ".$HTTP_POST_VARS["ewayTrxnError"].") ";
	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$_orderids = $module_params ["param03"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");
?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.eway.com.au/gateway/payment.asp" method=POST name=process>
  
    <input type=hidden name=ewayCustomerID value="<?php echo htmlspecialchars($module_params ["param01"]); ?>">
	<input type=hidden name=ewayTotalAmount value="<?php echo htmlspecialchars(100*$cart["total_cost"]); ?>">
    <input type=hidden name=ewayCustomerInvoiceRef value="<?php echo htmlspecialchars($_orderids); ?>">
	<input type=hidden name=ewayCustomerFirstName value="<?php echo htmlspecialchars($bill_firstname); ?>">
	<input type=hidden name=ewayCustomerLastName value="<?php echo htmlspecialchars($bill_lastname); ?>">
	<input type=hidden name=ewayCustomerEmail value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=ewayCustomerAddress value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=ewayCustomerPostcode value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=ewayOption1 value="<?php echo htmlspecialchars($_orderids); ?>">
	<input type=hidden name=ewayOption3 value="<?php echo $module_params["testmode"]=="Y"?"TRUE":"FALSE";?>">
	<input type=hidden name=ewayURL value="<?php echo $http_location; ?>/payment/cc_ewayweb.php">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>eWay</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
