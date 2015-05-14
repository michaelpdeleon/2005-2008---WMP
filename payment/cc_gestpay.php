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
# $Id: cc_gestpay.php,v 1.28 2006/01/11 06:56:22 mclap Exp $
#

if ($HTTP_POST_VARS["mode"]=="import_passwords") {
	require "../top.inc.php";
	require $xcart_dir.DIR_ADMIN."/auth.php";
	require $xcart_dir."/include/security.php";

	function func_import_gestpay_passwords ($file, $type) {
		global $file_temp_dir, $sql_tbl;

		x_load('files');

		$file = func_move_uploaded_file($file);
		if ($file === false) return;

		if ($fp = func_fopen($file,"rt",true))
		{
			while (!feof ($fp))
			{	$pass = fgets ($fp, 33); $pass = trim($pass);
				if ($pass)db_query ("INSERT INTO $sql_tbl[cc_gestpay_data] (value, type) VALUES ('$pass', '$type')");
			}

			fclose ($fp);
		}
		@unlink ($file);
	}

	if ($delete_all == "Y") db_query ("DELETE FROM $sql_tbl[cc_gestpay_data]");
	if ($ric) func_import_gestpay_passwords ("ric", "C");
	if ($ris) func_import_gestpay_passwords ("ris", "S");

	func_header_location($xcart_catalogs['admin']."/cc_processing.php?cc_processor=GestPay&mode=update&imported");
	exit;
}

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if (!isset($QUERY_STRING))
	$QUERY_STRING = $HTTP_SERVER_VARS["QUERY_STRING"];

if ($REQUEST_METHOD == "GET" && $QUERY_STRING)
{
	require "./auth.php";

#	if($cc_gestpay_debug)
#	{
#		$responses = array ();
#		if ($QUERY_STRING)
#		{
#			$_responses = explode ("*", $b);
#			if (!empty($_responses))
#			{
#				foreach ($_responses as $value)
#				{
#					$r = explode ("=", $value);
#					$k = $r[0];
#					$v = $r[sizeof($r)-1];
#					$responses [$k] = $v;
#				}
#			}
#		}
#		$result = $responses ["PAY1_TRANSACTIONRESULT"];
#		$otp = $responses ["PAY1_OTP"];
#		print_r($HTTP_GET_VARS);exit;
#
#	} else
#	{
		$result = $HTTP_GET_VARS["a"];
		$otp = $HTTP_GET_VARS["c"];
		$key = $HTTP_GET_VARS["b"];
#	}

	$res = func_query_first("SELECT * FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='S'");
	if($res)db_query("DELETE FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='S'");

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$key."'");

	if(!preg_match("/^ko$/i",$result) && !empty($res))
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = " (AuthCode: ".$result.")";
	}
	else
		$bill_output["code"] = 2;

	require $xcart_dir."/payment/payment_ccend.php";
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant_id = $module_params["param01"];
	$currency = $module_params["param02"];
	$terminal_id = $module_params["param03"];
	$ordr = $module_params ["param04"].join("-",$secure_oid);
	if($terminal_id<1)$terminal_id=1;
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".$ordr."','".$XCARTSESSID."')");

	# Fetch OTP here and Delete OTP from the database if exeist
	$otp = func_query_first_cell("SELECT value FROM $sql_tbl[cc_gestpay_data] WHERE type='C' LIMIT 1");
	if ($otp) db_query ("DELETE FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='C'");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://ecomm.sella.it/gestpay/pagam.asp" name=process>
  	<input type=hidden name=a value="<?php echo htmlspecialchars($merchant_id); ?>">
<?php
#	if ($cc_gestpay_debug)
#	{
#		print "<input type=hidden name=b value=\"PAY1_OTP=".$otp."*P1*PAY1_AMOUNT=".round($cart["total_cost"]*$terminal_id,2)."*P1*PAY1_UICCODE=".$currency."*P1*PAY1_SHOPTRANSACTIONID=".$ordr."\">";
#	} else {
?>
	<input type=hidden name=b value="<?php echo round($cart["total_cost"]*$terminal_id,2) ?>">
	<input type=hidden name=c value="<?php echo htmlspecialchars($otp); ?>">
	<input type=hidden name=d value="<?php echo htmlspecialchars($ordr); ?>">
<?php
#	}
?>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>GestPay</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
