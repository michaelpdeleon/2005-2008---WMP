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
# $Id: cc_netpay.php,v 1.14 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

# cc_netpay.php?PAYEE_NAME=sdg+test&PAYER_ACCOUNT=1001013&PAYEE_ACCOUNT=2179466&PAYMENT_BATCH_NUM=362481&PAYMENT_AMOUNT=0.010&PRODUCT_NAME=xxx13&EXTRA_INFO=&MEMO=sdg+was+here&COUPON_AMOUNT=&TIMESTAMP=20040518110037&MD5_DIGEST=1f5a12556e46c30af119c6d15eef87eb&Continue=Click+here+to+return+to+the+merchant+site.

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["TIMESTAMP"] && $HTTP_POST_VARS["MD5_DIGEST"] && $HTTP_POST_VARS["EXTRA_INFO"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["EXTRA_INFO"]."'");
	$answer = func_query_first_cell("SELECT param03 FROM $sql_tbl[ccprocessors] WHERE processor='cc_netpay.php'");

	#PAYMENT_AMOUNT:PAYER_ACCOUNT:PAYEE_ACCOUNT:MEMO:EXTRA_INFO:SECURITY_ANSWER:TXN_ID:PAYMENT_BATCH_NUM:TIMESTAMP 
	$md5 = md5(join(":",array($PAYMENT_AMOUNT,$PAYER_ACCOUNT,$PAYEE_ACCOUNT,$MEMO,$EXTRA_INFO,$answer,$TXN_ID,$PAYMENT_BATCH_NUM,$TIMESTAMP)));

	$bill_output["code"] = (($md5==$MD5_DIGEST) ? 1 : 2);
	if(strtolower($md5)!=strtolower($MD5_DIGEST))
		$bill_output["billmes"] = "Invalid MD5 hash";

	if(!empty($MEMO))				$bill_output["billmes"].= " (MEMO: ".$MEMO.") ";
	if(!empty($PAYER_ACCOUNT))		$bill_output["billmes"].= " (PAYER_ACCOUNT: ".$PAYER_ACCOUNT.") ";
	if(!empty($EXTRA_INFO))			$bill_output["billmes"].= " (EXTRA_INFO: ".$EXTRA_INFO.") ";
	if(!empty($COUPON_AMOUNT))		$bill_output["billmes"].= " (COUPON_AMOUNT: ".$COUPON_AMOUNT.") ";
	if(!empty($PAYMENT_BATCH_NUM))	$bill_output["billmes"].= " (PAYMENT_BATCH_NUM: ".$PAYMENT_BATCH_NUM.") ";
	if(!empty($TXN_ID))				$bill_output["billmes"].= " (TXN_ID: ".$TXN_ID.") ";
	if(!empty($MD5_DIGEST))			$bill_output["billmes"].= " (MD5_DIGEST: ".$MD5_DIGEST.") ";

	$skey = $HTTP_GET_VARS["EXTRA_INFO"];
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");

}
elseif ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["oid"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_GET_VARS["oid"]."'");
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Cancel";

	$skey = $HTTP_GET_VARS["oid"];
	require($xcart_dir."/payment/payment_ccend.php");

}
elseif ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["EXTRA_INFO"])
{
	require "./auth.php";

	$skey = $HTTP_GET_VARS["EXTRA_INFO"];require($xcart_dir."/payment/payment_ccview.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_id = $module_params["param01"];
	$pp_nm = $module_params["param02"];
	$_orderids = $module_params ["param04"].join("-",$secure_oid);
	$url = $http_location."/payment/cc_netpay.php";

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");


?>
<html>
<head>
<title>Order Form</title>
</head>
<body onLoad="document.process.submit();">
<form action="https://www.netpay.tv/cgi-bin/merchant/mpay.cgi" method="POST" name=process>
<input type=hidden name=PAYMENT_AMOUNT value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
<input type=hidden name=PAYEE_NAME value="<?php echo htmlspecialchars($pp_nm); ?>">
<input type=hidden name=PAYEE_ACCOUNT value="<?php echo htmlspecialchars($pp_id); ?>">
<input type=hidden name=STATUS_URL value="<?php echo htmlspecialchars($url); ?>">
<input type=hidden name=RETURN_URL value="<?php echo htmlspecialchars($url); ?>">
<input type=hidden name=CANCEL_URL value="<?php echo htmlspecialchars($url."?oid=".$_orderids); ?>">
<input type=hidden name=EXTRA_INFO value="<?php echo htmlspecialchars($_orderids); ?>">
</form>
<table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>NetPay</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php 
}
	exit;
?>
