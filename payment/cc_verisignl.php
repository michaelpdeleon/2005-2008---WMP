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
# $Id: cc_verisignl.php,v 1.41.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["RESULT"]) && isset($HTTP_POST_VARS["RESPMSG"]))
{
	require "./auth.php";

#  'RESULT' => '12',
#  'AUTHCODE' => '',
#  'RESPMSG' => 'Declined',
#  'AVSDATA' => 'N',
#  'PNREF' => 'V63A28821903',
#  'INVOICE' => '514',
#  'CSCMATCH' => 'Y',

#  'RESULT' => '0',
#  'AUTHCODE' => '081PNI',
#  'RESPMSG' => 'Approved',
#  'AVSDATA' => 'XXN',
#  'PNREF' => 'V63A28822066',
#  'CSCMATCH' => 'Y',
#  'INVOICE' => '514',

	$skey = $INVOICE;
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_login = $module_params["param01"];
	$pp_partner = $module_params["param02"];
	$prefix = $module_params["param03"];
	$userinfo["phone"] = preg_replace("/[^0-9]/","",$userinfo["phone"]);

	$prods = "";
	foreach($products as $product)
		$prods.= str_replace('"', "'", $product["product"])."(".price_format($product["taxed_price"]*$product["amount"])."); ";

	if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0) {
		foreach ($cart["giftcerts"] as $tmp_gc)
			$prods.= "GIFT CERTIFICATE(".price_format($tmp_gc["amount"])."); ";
	}

	if(strlen($prods) > 200)
		$prods = substr($prods, 0, 200)."...";

	$_orderids = $prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

	if($module_params["param06"] == 'US') {
		$vs_host = "https://payments.verisign.com/payflowlink";
	} else {
		$vs_host = "https://payments.verisign.com.au/payflowlink";
	}

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $vs_host; ?>" method="POST" name="process">
	<input type=hidden name=login value="<?php echo htmlspecialchars($pp_login); ?>">
	<input type=hidden name=partner value="<?php echo htmlspecialchars($pp_partner); ?>">
	<input type=hidden name=amount value="<?php echo $cart["total_cost"]; ?>">
	<input type=hidden name=description value="<?php echo htmlspecialchars($prods); ?>">
	<input type=hidden name=type value=S>
	<input type=hidden name=orderform value=true>
	<input type=hidden name=method value=cc>
  	<input type=hidden name=name value="<?php echo htmlspecialchars($bill_name); ?>">
  	<input type=hidden name=nametoship value="<?php echo htmlspecialchars($userinfo["s_firstname"]." ".$userinfo["s_lastname"]); ?>">
	<input type=hidden name=email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=emailtoship value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=phone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=phonetoship value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=invoice value="<?php echo htmlspecialchars($_orderids); ?>">
	<input type=hidden name=address value="<?php echo htmlspecialchars($userinfo["b_address"]." ".$userinfo["b_address_2"]); ?>">
	<input type=hidden name=addresstoship value="<?php echo htmlspecialchars($userinfo["s_address"]." ".$userinfo["s_address_2"]); ?>">
	<input type=hidden name=city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=citytoship value="<?php echo htmlspecialchars($userinfo["s_city"]); ?>">
	<input type=hidden name=country value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=countrytoship value="<?php echo htmlspecialchars($userinfo["s_country"]); ?>">
	<input type=hidden name=state value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=statetoship value="<?php echo htmlspecialchars($userinfo["s_state"]); ?>">
	<input type=hidden name=zip value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=ziptoship value="<?php echo htmlspecialchars($userinfo["s_zipcode"]); ?>">
	<input type=hidden name=echodata value=true>
	<input type=hidden name=showconfirm value=false>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>VeriSign Payflow Link</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
