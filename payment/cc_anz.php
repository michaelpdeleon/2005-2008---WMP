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
# $Id: cc_anz.php,v 1.14 2006/01/11 06:56:22 mclap Exp $
#
# Server-Hosted payment
#

if (!isset($HTTP_GET_VARS['vpc_TxnResponseCode'])) {

	$post = array();
	$post['vpc_AccessCode'] = $module_params["param02"];
	$post['vpc_Amount'] = str_replace(".", "", price_format($cart["total_cost"]));
	$post['vpc_Command'] = "pay";
	$post['vpc_Locale'] = "en";
	$post['vpc_MerchTxnRef'] = $module_params["param04"].join("-",$secure_oid);
	$post['vpc_Merchant'] = $module_params["param01"];
	$post['vpc_OrderInfo'] = substr("Order #".join("-",$secure_oid), 0, 34);
	$post['vpc_ReturnURL'] = $http_location."/payment/cc_anz.php?".$XCART_SESSION_NAME."=".$XCARTSESSID;
	$post['vpc_Version'] = "1";

	$md5_value = $module_params["param03"];
	foreach($post as $k => $v) {
		$md5_value .= $v;
	}
	$post['vpc_SecureHash'] = strtoupper(md5($md5_value));

?>
<html>
<head>
	<title>Order Form</title>
</head>
<body onLoad="document.process.submit();">
<form action="https://migs.mastercard.com.au/vpcpay" method="GET" name="process">
<?php
	foreach ($post as $k => $v) {
		?><input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>">
<?php
	}
?>
</form>
<table width="100%" height="100%">
<tr>
	<td align="center" valign="middle">Please wait while connecting to <b>ANZ eGate Server-Hosted</b> payment gateway...</td>
</tr>
</table>
</body>
</html>
<?php
	exit;

} else {
	require "./auth.php";

	$bill_output = array();	
	$bill_output["sessid"] = $XCARTSESSID;
	if ($vpc_TxnResponseCode == "0") {
		$bill_output['code'] = 1;
		$bill_output['billmes'] = "Approved. Transaction ID: $vpc_TransactionNo;";
	} else {
		$bill_output['code'] = 2;
		$bill_output['billmes'] = "Declined: Result code: $vpc_TxnResponseCode / $vpc_AcqResponseCode; Message: $vpc_Message; Transaction ID: $vpc_TransactionNo;";
	}

	require($xcart_dir."/payment/payment_ccend.php");
}
?>
