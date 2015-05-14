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
# $Id: cc_psigate.php,v 1.23.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["OrdNo"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$OrdNo."'");

	if(!empty($RefNo) && empty($Err))
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "RefNo: ".$RefNo." (Approval Code: ".$Code.")";
	}
	else
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $HTTP_GET_VARS["Err"];
	}

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$id = $module_params ["param01"];
	$expiry_month = substr($userinfo["card_expire"],0,2);
	$expiry_year = substr($userinfo["card_expire"],2,2);;
	$ordr = $module_params ["param02"].join("-",$secure_oid);
	$url = $http_location."/payment/cc_psigate.php";
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://order.psigate.com/psigate.asp" method=POST name=process>
	<input type=hidden name=Email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=Bcity value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=Bcountry value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=Bname value="<?php echo htmlspecialchars($bill_name); ?>">
	<input type=hidden name=Bzip value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=Bstate value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=Baddr1 value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=Phone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=MerchantID value="<?php echo htmlspecialchars($id); ?>">
	<input type=hidden name=Oid value="<?php echo htmlspecialchars($ordr); ?>">
	<input type=hidden name=Userid value="<?php echo htmlspecialchars($cart["login"]); ?>">
	<input type=hidden name=CardNumber value="<?php echo htmlspecialchars($userinfo["card_number"]); ?>">
	<input type=hidden name=ExpMonth value="<?php echo htmlspecialchars($expiry_month); ?>">
	<input type=hidden name=ExpYear value="<?php echo htmlspecialchars($expiry_year); ?>">
	<input type=hidden name=IP value="<?php echo htmlspecialchars($REMOTE_ADDR); ?>">
	<input type=hidden name=FullTotal value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
	<input type=hidden name=ChargeType value="1">
	<input type=hidden name=ThanksURL value="<?php echo htmlspecialchars($url); ?>">
	<input type=hidden name=NoThanksURL value="<?php echo htmlspecialchars($url); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>PS<i>i</i>Gate</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
