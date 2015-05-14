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
# $Id: cc_paypro.php,v 1.12 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

#  'MerchantOrderNo' => '1839456',
#  'TransactionNo' => '2751',
#  'AcquirerBankReceiptCode' => '000000002751',
#  'AcquirerBankResponseCode' => 'OK',
#  'Result' => 'OK',
#  'Amount' => '20',
#  'MerchantPrivateKey' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',


if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["Result"]) && isset($HTTP_POST_VARS["MerchantOrderNo"]))
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["MerchantOrderNo"]."'");
	$bill_output["code"] = $HTTP_POST_VARS["Result"]=="OK" ? 1 : 2;
	$bill_output["billmes"] = $HTTP_POST_VARS["Result"].": ".$AcquirerBankResponseCode;

	if(!empty($TransactionNo))		$bill_output["billmes"].= " (TransactionNo: ".$TransactionNo.") ";
	if(!empty($AcquirerBankReceiptCode))	$bill_output["billmes"].= " (AcquirerBankReceiptCode: ".$AcquirerBankReceiptCode.") ";

	$weblink=2;
	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant = $module_params ["param01"];
	$_orderids = $module_params ["param02"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

?>
<html>
<body  onLoad="document.process.submit();">
  <form action="https://www.paypro.co.nz/https/pay.aspx" method=POST name=process>
    <input type=hidden name=MerchantKey value="<?php echo $merchant; ?>">
	<input type=hidden name=PurchaseAmount value="<?php echo $cart["total_cost"]; ?>">
    <input type=hidden name=MerchantOrderNo value="<?php echo $_orderids; ?>">
    <input type=hidden name=Mode value="<?php echo ($module_params["testmode"] == "N" ? "P" : "T"); ?>">
	</form>
	<table width=100% height=100%>
	<tr><td align=center valign=middle>Please wait while connecting to <b>PayPro</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
