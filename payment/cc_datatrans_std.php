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
# $Id: cc_datatrans_std.php,v 1.1 2006/01/24 14:46:25 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && !empty($HTTP_GET_VARS["xref"])) {
	# FROM DATATRANS
	require "../top.inc.php";
	include $xcart_dir."/config.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$xref."'");

	if (!isset($HTTP_POST_VARS["errorCode"]) && isset($HTTP_POST_VARS["authorizationCode"])) {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = $responseMessage;
	} else {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = $errorMessage;
	}

	$_save_fields = array();
	foreach (array('status', 'pmethod', 'errorCode', 'uppTransactionId',
		'authorizationCode') as $_field) {
		if (isset($HTTP_POST_VARS[$_field])) {
			$_save_fields[] = $_field.': '.$HTTP_POST_VARS[$_field];
		}
	}

	if (!empty($_save_fields))
		$bill_output["billmes"] .= " (".implode(', ',$_save_fields).")";

	include $xcart_dir."/payment/payment_ccend.php";
}
else {
	# FROM CHECKOUT
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_refno = str_replace(" ","",$module_params["param04"]).join("-",$secure_oid);
	if (!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."')");

	$post["merchantId"] = $module_params["param01"];
	$post["refno"] = $pp_refno;
	$post["amount"] = 100*$cart["total_cost"]; # total amount in cents
	$post["currency"] = $module_params["param03"];
	$post["successUrl"] = $current_location.'/payment/cc_datatrans_std.php?xref='.$order_secureid;
	$post["errorUrl"] = $current_location.'/payment/cc_datatrans_std.php?xref='.$order_secureid;
	$post["cancelUrl"] = $xcart_catalogs['customer'].'/cart.php';
	$post["reqtype"] = "CAA"; # authorisation with immediate settlement, if the transaction is authorised
	if (in_array(strtolower($shop_language),array("de","en","fr")))
		$post["language"] = strtolower($shop_language);
	else
		$post["language"] = "en";
?>
<html>
<body onLoad="document.process.submit();">
<form action="https://www.datatrans.biz/upp/jsp/upStart.jsp" method="POST" name="process">
<?php
	foreach ($post as $_name=>$_value) {
		echo "<input type=\"hidden\" name=\"$_name\" value=\"".htmlspecialchars($_value)."\">\n";
	}
?>
</form>
<table width="100%" height="100%">
<tr><td align="center" valign="middle">Please wait while connecting to <b>DataTrans.biz</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}

exit;
?>
