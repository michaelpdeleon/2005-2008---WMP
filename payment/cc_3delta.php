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
# $Id: cc_3delta.php,v 1.25 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD)) 
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["Status"] && $HTTP_GET_VARS["TranNum"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$TranNum."'");

$sterr = array(
	"V" => "Voided",
	"C" => "Partial Credit",
	"M" => "Forced Authorization",
	"D" => "Declined",
	"F" => "Failed",
	"E" => "Exists"
);

#    [ReturnCode] => 51
#    [AuthMsg] => Unable to validate user.
#    [Status] => F
#    [AuthCode] => 
#    [TotalAmt] => 43595
#    [TranNum] => 167
#    [OrigTran] => 
#    [CCType] => VISA
#    [AVS] => 
#    [PCode] => 

	if($Status == "A")
	{	$bill_output["code"] = 1;
		$bill_output["billmes"] = "AuthCode: ".$AuthCode."; PCode: ".$PCode;
		$bill_output["avsmes"] = $AVS;
	}
	else
	{	$bill_output["code"] = 2;
		$bill_output["billmes"] = $sterr[$Status]." (AuthMsg: ".$AuthMsg." [ReturnCode=".$ReturnCode."])";
	}
	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant = $module_params["param01"];
	$ord = $module_params["param03"].join("-",$secure_oid);
	$test = "https://www.".($module_params ["testmode"]=="Y" ? "ec-zonedemo.com/secure/extern/" : "eclinx.com/secure/external/")."ExternalAuth.asp";
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) values ('".addslashes($ord)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $test; ?>" method=POST name=process>
  	<input type=hidden name=URL value="<?php echo $http_location; ?>/payment/cc_3delta.php">
    <input type=hidden name=MerchId value="<?php echo htmlspecialchars($merchant); ?>">
	<input type=hidden name=TotalAmt value="<?php echo 100*$cart["total_cost"]; ?>">
	<input type=hidden name=AuthType value="B">
	<input type=hidden name=DbCr value="D">
	<input type=hidden name=NameOnCard value="<?php echo htmlspecialchars($userinfo["card_name"]); ?>">
	<input type=hidden name=CVV value="<?php echo htmlspecialchars($userinfo["card_cvv2"]); ?>">
	<input type=hidden name=CreditCardNo value="<?php echo htmlspecialchars($userinfo["card_number"]); ?>">
	<input type=hidden name=ExpireMM value="<?php echo htmlspecialchars(substr($userinfo["card_expire"],0,2)); ?>">
	<input type=hidden name=ExpireYY value="<?php echo htmlspecialchars(substr($userinfo["card_expire"],2,2)); ?>">
	<input type=hidden name=ShipToStreet value="<?php echo htmlspecialchars($userinfo["s_address"]); ?>">
	<input type=hidden name=ShipToZip value="<?php echo htmlspecialchars($userinfo["s_zipcode"]); ?>">
	<input type=hidden name=TranNum value="<?php echo htmlspecialchars($ord); ?>">
<?php
	$backet = count($products); $lines=""; $i=0;
	foreach($products as $product)
	{
		$lines.= "\t<input type=hidden name=ItemQty".(++$i)." value=\"".$product["amount"]."\">\n";
		$lines.= "\t<input type=hidden name=ItemAmt".$i." value=\"".(100*$product["price"])."\">\n";
		$lines.= "\t<input type=hidden name=ItemDesc".$i." value=\"".htmlspecialchars($product["product"])."\">\n";
		$lines.= "\t<input type=hidden name=ItemPartNo".$i." value=\"".$i."\">\n";
	}
	if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
	foreach($cart["giftcerts"] as $tmp_gc)
	{
		$lines.= "\t<input type=hidden name=ItemQty".(++$i)." value=\"1\">\n";
		$lines.= "\t<input type=hidden name=ItemAmt".$i." value=\"".(100*$tmp_gc["amount"])."\">\n";
		$lines.= "\t<input type=hidden name=ItemDesc".$i." value=\"GIFT CERTIFICATE\">\n";
		$lines.= "\t<input type=hidden name=ItemPartNo".$i." value=\"".$i."\">\n";
	}
	print "\t<input type=hidden name=NumberOfItems value=".$backet.">\n".$lines;
?>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>3Delta Sytems</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
