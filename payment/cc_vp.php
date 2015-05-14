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
# $Id: cc_vp.php,v 1.18 2006/01/11 06:56:23 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

#    [VPResponseCode] => 05
#    [VPTransactionUnique] => x183
#    [VPMessage] => CARD DECLINED
#    [VPBillingAddress] => sdflksj dfks dflk
#    [VPBillingName] => Shabaev D.G.
#    [VPBillingPostCode] => GT4 43F
#    [VPBillingEmail] => sdg@rrf.ru

if ($REQUEST_METHOD == "GET" && !empty($HTTP_GET_VARS["VPResponseCode"]) && !empty($HTTP_GET_VARS["VPMessage"]) && !empty($HTTP_GET_VARS["VPTransactionUnique"]))
{
	require "./auth.php";

$err = array(
	"00" => "Authorised",
	"02" => "Card Referred",
	"04" => "Keep card decline",
	"05" => "Card declined",
	"30" => "Exception"
);

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$VPTransactionUnique."'");
	$bill_output["code"] = (($VPResponseCode == "00") ? 1 : 2);
	
	$bill_output["billmes"] = $VPMessage." (".($err[$VPResponseCode] ? $err[$VPResponseCode] : "Code: ".$VPResponseCode).")";

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$vp_login = $module_params["param01"];
	$vp_pass = $module_params["param02"];
	$vp_url = $module_params["param03"];
	$vp_curr = $module_params["param04"];
	$vp_prefix = $module_params["param05"];
	$cntr = func_query_first_cell("SELECT code_N3 FROM $sql_tbl[countries] WHERE code='".$userinfo["b_country"]."'");

	$ordr = $vp_prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $vp_url; ?>" method=POST name=process>
	 <input type=hidden name=VPMerchantPassword value="<?php echo htmlspecialchars($vp_pass); ?>">
	 <input type=hidden name=VPMerchantID value="<?php echo htmlspecialchars($vp_login); ?>">
	 <input type=hidden name=VPAmount value="<?php echo 100*$cart["total_cost"]; ?>">
	 <input type=hidden name=VPCountryCode value="<?php echo $cntr; ?>">
	 <input type=hidden name=VPCurrencyCode value="<?php echo htmlspecialchars($vp_curr); ?>">
	 <input type=hidden name=VPTransactionUnique value="<?php echo htmlspecialchars($ordr); ?>">
	 <input type=hidden name=VPOrderDesc value="<?php echo htmlspecialchars($ordr); ?>">
	 <input type=hidden name=VPCallBack value="<?php echo $http_location; ?>/payment/cc_vp.php">

	 <input type=hidden name=VPMailingAddress value="<?php echo htmlspecialchars($userinfo["b_address"]." ".$userinfo["b_address_2"].", ".$userinfo["b_city"]); ?>">
	 <input type=hidden name=VPMailingPostCode value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	 <input type=hidden name=VPMailingEmail value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	 <input type=hidden name=VPMailingPhoneNumber value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>VelocityPay</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
