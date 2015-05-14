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
# $Id: cc_censeo.php,v 1.18 2006/01/11 06:56:22 mclap Exp $
#

$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["R_Code"] && $HTTP_GET_VARS["Checksum"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_GET_VARS["T_Info"]."'");
	$secret = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_censeo.php'");
	
#  'R_Code' => '50',
#  'R_Message' => 'VAT_Amount_error_',
#  'T_Number' => '',
#  'Checksum' => '79735612d54a203fe5265cfd18e4ea05',
#  'P_Id' => 'std',
#  'T_Info' => 'xd42',

	if(md5($R_Code.$R_Message.$T_Number.$T_Info.$secret) == $Checksum)
	{
		$bill_output["code"] = $R_Code=="AA" ? 1 : 2;
		$bill_output["billmes"] = $R_Message." (code: ".$R_Code.")";

		if(!empty($T_Number))$bill_output["billmes"].= " (T_Number: ".$T_Number.") ";
	}
	else
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "MD5_Checksum_Error";
	}

	 #print_r($bill_output);$exit;
	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

	$p_url = $module_params["param01"];
	$p_key = $module_params["param02"];
	$p_pid = $module_params["param03"];
	$_orderids = $module_params ["param04"].join("-",$secure_oid);
	$p_currency = $module_params["param05"];
	$p_amount = 100*$cart["total_cost"];
	$p_pid = ($pid ? $pid : "std");
	$p_vat = 100*$cart["total_vat"];$p_vat = $p_vat ? $p_vat : "";
	$url = $http_location."/payment/cc_censeo.php";

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

	$md5 = md5($p_currency.$p_amount.$p_vat.$p_pid.$userinfo["card_number"].substr($userinfo["card_expire"],2,2).substr($userinfo["card_expire"],0,2).$userinfo["card_cvv2"].$userinfo["card_name"].$_orderids.$url.$p_key);

?>
<html>
<body onLoad="document.process.submit();">
<form action="https://www.censeo.se/nodialog/<?php echo $p_url; ?>" method="GET" name=process>
<input type=hidden name=C_Code value="<?php echo htmlspecialchars($p_currency); ?>">
<input type=hidden name=T_Amount value="<?php echo htmlspecialchars($p_amount); ?>">
<input type=hidden name=VAT_Amount value="<?php echo htmlspecialchars($p_vat); ?>">
<input type=hidden name=P_Id value="<?php echo htmlspecialchars($p_pid); ?>">


<input type=hidden name=C_Number value="<?php echo htmlspecialchars($userinfo["card_number"]); ?>">
<input type=hidden name=C_Year value="<?php echo htmlspecialchars(substr($userinfo["card_expire"],2,2)); ?>">
<input type=hidden name=C_Month value="<?php echo htmlspecialchars(substr($userinfo["card_expire"],0,2)); ?>">
<input type=hidden name=S_Code value="<?php echo htmlspecialchars($userinfo["card_cvv2"]); ?>">
<input type=hidden name=Name value="<?php echo htmlspecialchars($userinfo["card_name"]); ?>">

<input type=hidden name=T_Info value="<?php echo htmlspecialchars($_orderids); ?>">
<input type=hidden name=Return_URL value="<?php echo htmlspecialchars($url); ?>">
<input type=hidden name=Checksum value="<?php echo htmlspecialchars($md5); ?>">
</form>
<table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>Censeo</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php 
}
	exit;
?>
