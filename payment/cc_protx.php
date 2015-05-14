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
# $Id: cc_protx.php,v 1.34.2.1 2006/06/15 10:10:49 max Exp $
#

function simpleXor($InString, $Key)
{
	$KeyList = array();
	$output = "";
	
	for($i=0;$i<strlen($Key);$i++)
		$KeyList[$i] = ord(substr($Key, $i, 1));

	for($i=0;$i<strlen($InString);$i++)
		$output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));

	return $output;
}


if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["crypt"])
{
	require "./auth.php";

	# [Status=OK&VendorTxCode=xcart324243&TxAuthNo=28566&AVSCV2=DATA NOT CHECKED&Amount=45.95&VPSTxID={00000033-0BF6-0001-0000-9687261DE084}]
	$pass = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_protx.php'");
	$crypt = str_replace(" ", "+", $crypt);
	$ret = "&".simpleXor(base64_decode($crypt),$pass)."&";

	preg_match("/&VendorTxCode=(.+)&/U",$ret,$sessid);
	$bill_output["sessid"] = func_query_first_cell("select sessionid from $sql_tbl[cc_pp3_data] where ref='".$sessid[1]."'");
	
	preg_match("/Status=(.+)&/U",$ret,$a);
	if($a[1] == "OK") {
		$bill_output["code"]=1;
		preg_match("/TxAuthNo=(.+)&/U",$ret,$authno);
		$bill_output["billmes"] = "AuthNo: ".$authno[1];
	} else {
		$bill_output["code"]=2;
		preg_match("/StatusDetail=(.+)&/U",$ret,$stat);
		$bill_output["billmes"] = "Status: ".$stat[1];
	}

	preg_match("/VPSTxID={(.+)}/U",$ret,$txid);
	if(!empty($txid[1]))
		$bill_output["billmes"].= " (TxID: {".$txid[1]."})";
	preg_match("/AVSCV2=(.*)&/U",$ret,$avs);
	if(!empty($avs[1]))
		$bill_output["billmes"].= " (AVS/CVV2: {".$avs[1]."})";


	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
		if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

		$pp_merch = $module_params["param01"];
		$pp_pass = $module_params["param02"];
		$pp_curr = $module_params["param03"];
		$pp_test = ($module_params["testmode"]!="N") ? "https://ukvpstest.protx.com/vps2form/submit.asp" : "https://ukvps.protx.com/vps2form/submit.asp";
		$pp_shift = preg_replace("/[^\w\d_-]/S", "", $module_params["param05"]);
		$_orderids = join("-",$secure_oid);
		if(!$duplicate)
			db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($pp_shift.$_orderids)."','".$XCARTSESSID."')");

		$crypt = "VendorTxCode=".$pp_shift.$_orderids."&";
		$crypt.= "Amount=".price_format($cart["total_cost"])."&";
		$crypt.= "Currency=".$pp_curr."&";
		$crypt.= "Description=Your Cart&";
		$crypt.= "SuccessURL=".$http_location."/payment/cc_protx.php&";
		$crypt.= "FailureURL=".$http_location."/payment/cc_protx.php&";
		$crypt.= "CustomerName=".$bill_name."&";
		$crypt.= "CustomerEMail=".$userinfo["email"]."&";
		$crypt.= "ContactNumber=".$userinfo["phone"]."&";
		$crypt.= "ContactFax=".$userinfo["fax"]."&";
		$crypt.= "VendorEMail=".$config["Company"]["orders_department"]."&";

		$shipping_address = array();
		$shipping_address[] = $userinfo["s_address"];
		if (!empty($userinfo["s_address_2"]))
			$shipping_address[] = $userinfo["s_address_2"];
		$shipping_address[] = $userinfo["s_city"];
		if (!empty($userinfo["s_countyname"]))
			$shipping_address[] = $userinfo["s_countyname"];
		$shipping_address[] = $userinfo["s_statename"].(($userinfo["s_statename"] == $userinfo["s_state"]) ? "" : " (".$userinfo["s_state"].")");
		$shipping_address[] = $userinfo["s_countryname"].(($userinfo["s_countryname"] == $userinfo["s_country"]) ? "" : " (".$userinfo["s_country"].")");
	
		$crypt.= "DeliveryAddress=".implode("\n", $shipping_address)."&";
		$crypt.= "DeliveryPostCode=".$userinfo["s_zipcode"]."&";
		
		$billing_address = array();
		$billing_address[] = $userinfo["b_address"];
		if (!empty($userinfo["b_address_2"]))
			$billing_address[] = $userinfo["b_address_2"];
		$billing_address[] = $userinfo["b_city"];
		if (!empty($userinfo["b_countyname"]))
			$billing_address[] = $userinfo["b_countyname"];
		$billing_address[] = $userinfo["b_statename"].(($userinfo["b_statename"] == $userinfo["b_state"]) ? "" : " (".$userinfo["b_state"].")");
		$billing_address[] = $userinfo["b_countryname"].(($userinfo["b_countryname"] == $userinfo["b_country"]) ? "" : " (".$userinfo["b_country"].")");
	
		$crypt.= "BillingAddress=".implode("\n", $billing_address)."&";
		$crypt.= "BillingPostCode=".$userinfo["b_zipcode"]."&";

		if(empty($products))
			$products = $cart['products'];
		$backet = @count($products)+@count($cart["giftcerts"]);
		if($products) {
			foreach($products as $product) {
				$backet.= ":".str_replace(":"," ",$product["product"]).":".$product["amount"].":".price_format($product["price"]).":::".price_format($product["amount"]*$product["price"]);
			}
		}

		if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0) {
			foreach ($cart["giftcerts"] as $tmp_gc) {
				$backet.= ":GIFT CERTIFICATE:1:".price_format($tmp_gc["amount"]).":::".price_format($tmp_gc["amount"]);
			}
		}
		$crypt.= "Basket=".$backet;

		$crypt = base64_encode(simpleXor($crypt,$pp_pass));

 
?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $pp_test; ?>" method=POST name=process>
	<input type=hidden name=VPSProtocol value="2.22">
	<input type=hidden name=Vendor value="<?php echo htmlspecialchars($pp_merch); ?>">
    <input type=hidden name=TxType value="PAYMENT">
    <input type=hidden name=Crypt value="<?php echo htmlspecialchars($crypt); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>Ptotx VSP Form</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
