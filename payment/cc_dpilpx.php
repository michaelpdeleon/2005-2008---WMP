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
# $Id: cc_dpilpx.php,v 1.17 2006/01/11 06:56:22 mclap Exp $
#

@set_time_limit(100);

function decrypt_tripledes($data, $key) {
  $data = pack("H*", $data);
  $td = mcrypt_module_open('tripledes', '', 'ecb', '');
  $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
  mcrypt_generic_init($td, $key, $iv);
  $result = mdecrypt_generic($td, $data);
  mcrypt_generic_deinit($td);
  return $result;
}

function encrypt_tripledes($data, $key) {
  $td = mcrypt_module_open('tripledes', '', 'ecb', '');
  $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
  mcrypt_generic_init($td, $key, $iv);
  $result = mcrypt_generic($td, $data);
  mcrypt_generic_deinit($td);
  $tmp = unpack("H*", $result);	
  return array_pop($tmp);
}

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if (!empty($HTTP_GET_VARS['result']) || !empty($HTTP_POST_VARS['result'])) {

#<PxPay>
#<Success>1</Success>
#<StatusRequired>0</StatusRequired>
#<Retry>0</Retry>
#<TxnType>Purchase</TxnType>
#<AuthCode>074227</AuthCode>
#<AmountSettlement>39.50</AmountSettlement>
#<CurrencySettlement>NZD</CurrencySettlement>
#<MerchantReference>12</MerchantReference>
#<CardName>Visa</CardName>
#<CurrencyInput>NZD</CurrencyInput>
#<UserId>0000000000005944</UserId>
#<ResponseText>APPROVED</ResponseText>
#<TxnData1>test</TxnData1>
#<TxnData2>test,,RU</TxnData2>
#<TxnData3>12345</TxnData3>
#<CardHolderName>xxxxxxx xxxxxxx</CardHolderName>
#<EmailAddress>xxx@xxx.xxx</EmailAddress>
#<DpsTxnRef>0000000400932012</DpsTxnRef>
#<DpsBillingId></DpsBillingId>
#<BillingId></BillingId>
#<MerchantTxnId></MerchantTxnId>
#<TS>20040511074231</TS>
#</PxPay> 

	require "./auth.php";

	$pp_pass = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_dpilpx.php'");
	$return = decrypt_tripledes($result,$pp_pass);

	preg_match("/<MerchantReference>(.*)<\/MerchantReference>/i",$return,$ref);
	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ref[1]."'");

	$tmp = func_query_first_cell("SELECT param1 FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ref[1]."'");

	if(empty($tmp)) {

		if(preg_match("/<Success>1<\/Success>/i",$return)) {
			$bill_output["code"] = 1;
		} else {
			$bill_output["code"] = 2;
		}

		if(preg_match("/<ResponseText>(.+)<\/ResponseText>/i",$return,$out))
			$bill_output["billmes"] = $out[1];

		if(preg_match("/<AuthCode>(.+)<\/AuthCode>/i",$return,$out))
			$bill_output["billmes"].= " (AuthCode: ".$out[1].")";

		if(preg_match("/<UserId>(.+)<\/UserId>/i",$return,$out))
			$bill_output["billmes"].= " (UserId: ".$out[1].")";

		if(preg_match("/<DpsTxnRef>(.+)<\/DpsTxnRef>/i",$return,$out))
			$bill_output["billmes"].= " (DpsTxnRef: ".$out[1].")";

		if(preg_match("/<TS>(.+)<\/TS>/i",$return,$out))
			$bill_output["billmes"].= " (TS: ".$out[1].")";

		if(preg_match("/<MerchantTxnId>(.+)<\/MerchantTxnId>/i",$return,$out))
			$bill_output["billmes"].= " (MerchantTxnId: ".$out[1].")";

		$skey = $ref[1];
		require($xcart_dir."/payment/payment_ccmid.php");
		require($xcart_dir."/payment/payment_ccwebset.php");

	} else {

		$skey = $ref[1];
		require($xcart_dir."/payment/payment_ccview.php");

	}

} else {

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_id   = $module_params["param01"];
	$pp_pass = $module_params["param02"];
	$_orderids  = $module_params["param04"].join("-",$secure_oid);

	$script_url = $http_location."/payment/cc_dpilpx.php";

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

$xml = array();
$xml[]= "<Request>";
$xml[]= "<TxnType>Purchase</TxnType>";
$xml[]= "<AmountInput>".$cart["total_cost"]."</AmountInput>";
$xml[]= "<AppletType>PHPPxAccess</AppletType>";
$xml[]= "<AppletVersion>01.00.01</AppletVersion>";
$xml[]= "<InputCurrency>NZD</InputCurrency>";
$xml[]= "<MerchantReference>".$_orderids."</MerchantReference>";
$xml[]= "<TxnData1>".$userinfo["b_address"]."</TxnData1>";
$xml[]= "<TxnData2>".$userinfo["b_city"].",".$userinfo["b_state"].",".$userinfo["b_country"]."</TxnData2>";
$xml[]= "<TxnData3>Phone ".$userinfo["phone"]."</TxnData3>";
$xml[]= "<EmailAddress>".$userinfo["email"]."</EmailAddress>";
$xml[]= "<UrlFail>$script_url</UrlFail>";
$xml[]= "<UrlSuccess>$script_url</UrlSuccess>";
$xml[]= "</Request>";

	$xml =  join("",$xml);
	if(strlen($xml)%8) 
		$xml = str_pad($xml,strlen($xml)+8-strlen($xml)%8);

	func_header_location("https://www.payment.co.nz/pxpay/pxpay.asp?userid=$pp_id&request=".encrypt_tripledes($xml, $pp_pass)); exit;

}
?>
