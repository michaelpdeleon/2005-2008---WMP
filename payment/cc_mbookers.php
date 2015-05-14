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
# $Id: cc_mbookers.php,v 1.18.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && !empty($HTTP_GET_VARS["status"]) && !empty($HTTP_GET_VARS["oid"]))
{
	require "./auth.php";

	$a = func_query_first("select * from $sql_tbl[cc_pp3_data] where ref='".$oid."'");
	$bill_output["sessid"] = $a["sessionid"];
	$bill_output["code"] = (!empty($a["param1"]) ? $a["param1"] : 2);

	if($status == "nok")
		$bill_output['billmes'] = "Cancelled";
	if(!empty($a["param1"]))
		$bill_output['billmes'] = $a["param2"];
	
	$skey = $oid;
	require($xcart_dir."/payment/payment_ccend.php");
}
elseif($REQUEST_METHOD == "POST" && !empty($HTTP_POST_VARS["status"]) && !empty($HTTP_POST_VARS["transaction_id"]) && !empty($HTTP_POST_VARS["merchant_id"]) && !empty($HTTP_POST_VARS["mb_transaction_id"]) )
{
#  'status' => '2',
#  'md5sig' => 'CA47626B3B238B269079E41C5DC1D4BA',
#  'merchant_id' => '261449',
#  'pay_to_email' => 'hmayers@maysys.com',
#  'mb_amount' => '1.5',
#  'mb_transaction_id' => '1046368',
#  'currency' => 'CAD',
#  'amount' => '1.50',
#  'transaction_id' => 'tst7',
#  'pay_from_email' => 'hmayers@maysys.com',
#  'mb_currency' => 'CAD',

	require "./auth.php";
	
	$transactionstatus = ($status==2 ? 1 : ($status==-2 ? 2 : 3));
	$addinfo = "MBookersTransactionID: ".$mb_transaction_id."; MerchantID: ".$merchant_id."; Pay from Email: ".$pay_from_email;
	db_query("update $sql_tbl[cc_pp3_data] set param1='".$transactionstatus."', param2='".$addinfo."' where ref='".$transaction_id."'");

}
else
{
if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$ordr = $module_params["param04"].join("-",$secure_oid);
$url = $http_location."/payment/cc_mbookers.php";
$urla = "?oid=".$ordr."&status=";

$post = array(
"pay_to_email" => $module_params["param01"],
"transaction_id" => $ordr,
"return_url" => $url.$urla."ok",
"cancel_url" => $url.$urla."nok",
"status_url" => $url,
"language" => $module_params["param03"],
"pay_from_email" => $userinfo['email'],
"amount" => $cart['total_cost'],
"currency" => $module_params["param02"],
"firstname" => $bill_firstname,
"lastname" => $bill_lastname,
"address" => $userinfo["b_address"],
"postal_code" => $userinfo["b_zipcode"],
"city" => $userinfo["b_city"],
"state" => $userinfo["b_state"],
"country" => $userinfo["b_country"],
"detail1_description" => "Cart:",
"detail1_text" => "Product(s)"
);

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.moneybookers.com/app/payment.pl" method=POST name=process>
  <?php
  	if($post)foreach($post as $k => $v)print "<input type=\"hidden\" name=".htmlspecialchars($k)." value=\"".htmlspecialchars($v)."\">\n";
  ?>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>MoneyBookers.com</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
