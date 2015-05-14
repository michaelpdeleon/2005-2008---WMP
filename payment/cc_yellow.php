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
# $Id: cc_yellow.php,v 1.15.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["txtOrderIDShop"] && $HTTP_POST_VARS["txtPayMet"])
{
	require "./auth.php";


	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["txtOrderIDShop"]."'");
	$bill_output["code"] = 1;

	if(!empty($txtPayMet))			$bill_output["billmes"].= " (Method: ".$txtPayMet.") ";
	if(!empty($txtTransactionID))	$bill_output["billmes"].= " (TransactionID: ".$txtTransactionID.") ";

	$skey = $HTTP_POST_VARS["txtOrderIDShop"];
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");
}
elseif($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["oid"])
{
	require "./auth.php";

	$skey = $HTTP_GET_VARS["oid"];
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant = $module_params ["param01"];
	$url = $module_params ["param02"];
	$curr = $module_params ["param03"];
	$lang = $module_params ["param04"];
	
	$_orderids = $module_params ["param06"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body>
  <form action="<?php echo $url; ?>" method=POST name=process>
  
    <input type=hidden name=txtShopId value="<?php echo $merchant; ?>">
	<input type=hidden name=txtOrderTotal value="<?php echo $cart["total_cost"]; ?>">
    <input type=hidden name=txtOrderIDShop value="<?php echo $_orderids; ?>">
    <input type=hidden name=txtLangVersion value="<?php echo $lang; ?>">
    <input type=hidden name=txtArtCurrency value="<?php echo $curr; ?>">
    <input type=hidden name=DeleveryPaymentType value="immediate">
    <input type=hidden name=txtShopPara value="<?php echo "oid=".$_orderids; ?>">

	<input type=hidden name=txtBFirstName value="<?php echo addslashes($bill_firstname);?>">
	<input type=hidden name=txtBLastName value="<?php echo addslashes($bill_lastname);?>">
	<input type=hidden name=txtBAddr1 value="<?php echo $userinfo["b_address"];?>">
	<input type=hidden name=txtBZipCode value="<?php echo $userinfo["b_zipcode"];?>">
	<input type=hidden name=txtBCity value="<?php echo $userinfo["b_city"];?>">
	<input type=hidden name=txtBCountry value="<?php echo $userinfo["b_country"];?>">
	<input type=hidden name=txtBTel value="<?php echo $userinfo["phone"];?>">
	<input type=hidden name=txtBEmail value="<?php echo $userinfo["email"];?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle><a href="javascript:document.process.submit();">Please click the link to connecting to <b>yellowpay</b> payment gateway...</a></td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
