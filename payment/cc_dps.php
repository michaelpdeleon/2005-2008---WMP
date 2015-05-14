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
# $Id: cc_dps.php,v 1.12 2006/02/03 11:33:57 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$post = "";
$post[] = "<Txn>";
$post[] = "<PostUsername>".$module_params["param01"]."</PostUsername>";
$post[] = "<PostPassword>".$module_params["param02"]."</PostPassword>";
$post[] = "<TxnType>Purchase</TxnType>";
$post[] = "<CardHolderName>".$userinfo["card_name"]."</CardHolderName>";
$post[] = "<CardNumber>".$userinfo["card_number"]."</CardNumber>";
$post[] = "<Cvc2>".$userinfo["card_cvv2"]."</Cvc2>";
$post[] = "<Amount>".$cart["total_cost"]."</Amount>";
$post[] = "<DateExpiry>".$userinfo["card_expire"]."</DateExpiry>";
$post[] = "<MerchantReference>".$module_params["param03"].join("-",$secure_oid)."</MerchantReference>";
$post[] = "<InputCurrency>".$module_params["param04"]."</InputCurrency>";
$post[] = "</Txn>";

list($a,$return)=func_https_request("POST","https://www.payment.co.nz:443/pxpost.asp",$post,"");

#<Txn><Transaction success="1" reco="00" responsetext="APPROVED">
#		<Authorized>1</Authorized>
#		<MerchantReference>xcart6</MerchantReference>
#		<Cvc2></Cvc2>
#		<CardName>Visa</CardName>
#		<Retry>0</Retry>
#		<StatusRequired>0</StatusRequired>
#		<AuthCode>174714</AuthCode>
#		<Amount>45.95</Amount>
#		<CurrencyId>840</CurrencyId>
#		<InputCurrencyId>840</InputCurrencyId>
#		<InputCurrencyName>USD</InputCurrencyName>
#		<CurrencyRate>1.00</CurrencyRate>
#		<CurrencyName>USD</CurrencyName>
#		<CardHolderName>SHABAEV D.G.</CardHolderName>
#		<DateSettlement>20030404</DateSettlement>
#		<TxnType>Purchase</TxnType>
#		<CardNumber>4242424242424242    </CardNumber>
#		<DateExpiry>0204</DateExpiry>
#		<ProductId>-1</ProductId>
#		<AcquirerDate>20030404</AcquirerDate>
#		<AcquirerTime>174714</AcquirerTime>
#		<AcquirerId>9000</AcquirerId>
#		<Acquirer>Test</Acquirer>
#		<TestMode>1</TestMode>
#		<CardId>2</CardId>
#		<CardHolderResponseText>APPROVED</CardHolderResponseText>
#		<CardHolderHelpText>The Transaction was approved</CardHolderHelpText>
#		<CardHolderResponseDescription>The Transaction was approved</CardHolderResponseDescription>
#		<MerchantResponseText>APPROVED</MerchantResponseText>
#		<MerchantHelpText>The Transaction was approved</MerchantHelpText>
#		<MerchantResponseDescription>The Transaction was approved</MerchantResponseDescription>
#		<UrlFail></UrlFail>
#		<UrlSuccess></UrlSuccess>
#		<AcquirerPort>999999999999-99999999</AcquirerPort>
#		<AcquirerTxnRef>2870</AcquirerTxnRef>
#		<DpsTxnRef>0000000300499e3d</DpsTxnRef>
#		<TransactionId>00499e3d</TransactionId><PxHostId>00000003</PxHostId>
#	</Transaction><ReCo>00</ReCo><ResponseText>APPROVED</ResponseText><HelpText>The Transaction was approved</HelpText><Success>1</Success></Txn>

preg_match("/<Cvc2>(.*)<\/Cvc2>/",$return,$out);
if(!empty($out[1]))$bill_output["cvvmes"].= $out[1];

$bill_output["avsmes"] = "Not support";

preg_match("/<Success>(.*)<\/Success>/",$return,$succ);
preg_match("/<ResponseText>(.*)<\/ResponseText>/",$return,$resptext);
preg_match("/<HelpText>(.*)<\/HelpText>/",$return,$helptext);
$bill_output["billmes"] = $resptext[1].":".$helptext[1];

if($succ[1] == "1")
{
	preg_match("/<AuthCode>(.*)<\/AuthCode>/",$return,$out2);
	$bill_output["code"] = 1; $bill_output["billmes"].= " (AuthCode: ".$out2[1].")";
}
else
{
	$bill_output["code"] = 2;
}

preg_match("/<DpsTxnRef>(.*)<\/DpsTxnRef>/",$return,$out);
$bill_output["billmes"].= " (DpsTxnRef: ".$out[1].")";

?>
