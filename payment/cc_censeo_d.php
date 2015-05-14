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
# $Id: cc_censeo_d.php,v 1.18 2006/01/11 06:56:22 mclap Exp $
#

$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["R_Code"] && $HTTP_POST_VARS["Checksum"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["T_Info"]."'");
	$secret = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_censeo_d.php'");
	
#  'R_Code' => '50',
#  'R_Message' => 'VAT_Amount_error_',
#  'T_Number' => '',
#  'Checksum' => '79735612d54a203fe5265cfd18e4ea05',
#  'S_Code' => '345',
#  'NameOnCard' => 'shabaev dmitiry',
#  'P_Id' => 'std',
#  'Card_type' => 'VISA',
#  'T_Info' => 'xd42',
#  'ExpMonth' => '02',
#  'submit1' => 'Fortsätt',
#  'Amount' => 'USD 74,98',
#  'CardNumber' => '4242424242424242',
#  'ExpYear' => '05',

	if(md5($R_Code.$R_Message.$T_Number.$T_Info.$secret) == $Checksum)
	{
		$bill_output["code"] = $R_Code=="AA" ? 1 : 2;
		$bill_output["billmes"] = $R_Message." (code: ".$R_Code.")";

		if(!empty($T_Number))$bill_output["billmes"].= " (T_Number: ".$T_Number.") ";
		if(!empty($CardNumber) &&  $bill_output["code"]==1)
			$bill_output["billmes"].= " (CardInfo: ".$NameOnCard." :: ".$Card_type." xxxx".substr($CardNumber,-4,4)." :: ".$ExpMonth."/".$ExpYear.") ";
	}
	else
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "MD5_Checksum_Error";
	}

	require($xcart_dir."/payment/payment_ccend.php");

	}
else
{ 
	if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

	$p_url = ($module_params["param01"] ? $module_params["param01"]."/" : "");
	$p_key = $module_params["param02"];
	$p_pid = $module_params["param03"];
	$_orderids = $module_params ["param04"].join("-",$secure_oid);
	$p_currency = $module_params["param05"];
	$p_amount = 100*$cart["total_cost"];
	$p_pid = ($pid ? $pid : "std");
	$p_vat = 100*$cart["total_vat"];$p_vat = $p_vat ? $p_vat : "";
	$url = $https_location."/payment/cc_censeo_d.php";

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

	$fingerprint = md5($_orderids.$url.$p_currency.$p_amount.$p_vat.$p_pid.$p_key);

?>
<html>
<body onLoad="document.process.submit();">
<form action="https://www.censeo.se/dialog/<?php echo $p_url; ?>kontoform.asp" method="POST" name=process>
<input type=hidden name=C_Code value="<?php echo htmlspecialchars($p_currency); ?>">
<input type=hidden name=T_Amount value="<?php echo htmlspecialchars($p_amount); ?>">
<input type=hidden name=VAT_Amount value="<?php echo htmlspecialchars($p_vat); ?>">
<input type=hidden name=P_Id value="<?php echo htmlspecialchars($p_pid); ?>">
<input type=hidden name=T_Info value="<?php echo htmlspecialchars($_orderids); ?>">
<input type=hidden name=Return_URL value="<?php echo htmlspecialchars($url); ?>">
<input type=hidden name=Checksum value="<?php echo htmlspecialchars($fingerprint); ?>">
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
