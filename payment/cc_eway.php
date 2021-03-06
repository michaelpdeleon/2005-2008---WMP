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
# $Id: cc_eway.php,v 1.18.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

@set_time_limit(100);

$pp_login = $module_params["param01"];
$pp_test = ($module_params["testmode"]=="N")?(""):("TRUE");
$script = ($module_params["testmode"]=="N")?("gateway_cvn/xmlpayment.asp"):("gateway_cvn/xmltest/TestPage.asp");

$post = "";
$post[] = "<ewaygateway>";
$post[] = "<ewayCustomerID>".$pp_login."</ewayCustomerID>";
$post[] = "<ewayTotalAmount>".(100*$cart["total_cost"])."</ewayTotalAmount>";
$post[] = "<ewayCustomerFirstName>".$bill_firstname."</ewayCustomerFirstName>";
$post[] = "<ewayCustomerLastName>".$bill_lastname."</ewayCustomerLastName>";
$post[] = "<ewayCustomerEmail>".$userinfo["email"]."</ewayCustomerEmail>";
$post[] = "<ewayCustomerAddress>".$userinfo["b_address"]."</ewayCustomerAddress>";
$post[] = "<ewayCustomerPostcode>".$userinfo["b_zipcode"]."</ewayCustomerPostcode>";
$post[] = "<ewayCustomerInvoiceDescription>".$descr."</ewayCustomerInvoiceDescription>";
$post[] = "<ewayCustomerInvoiceRef>".$module_params["param03"].join("-",$secure_oid)."</ewayCustomerInvoiceRef>";
$post[] = "<ewayCardHoldersName>".$userinfo["card_name"]."</ewayCardHoldersName>";
$post[] = "<ewayCardNumber>".$userinfo["card_number"]."</ewayCardNumber>";
$post[] = "<ewayCardExpiryMonth>".substr($userinfo["card_expire"],0,2)."</ewayCardExpiryMonth>";
$post[] = "<ewayCardExpiryYear>".substr($userinfo["card_expire"],2,2)."</ewayCardExpiryYear>";
$post[] = "<ewayTrxnNumber></ewayTrxnNumber>";
$post[] = "<ewayOption1></ewayOption1>";
$post[] = "<ewayOption2></ewayOption2>";
$post[] = "<ewayOption3>".$pp_test."</ewayOption3>";
$post[] = "<ewayCVN>".$userinfo["card_cvv2"]."</ewayCVN>";
$post[] = "</ewaygateway>";

list($a,$return)=func_https_request("POST","https://www.eway.com.au:443/".$script,$post,"","","text/xml");

#<ewayResponse>
#	<ewayTrxnError>A9,INVALID CARD NUMBER. Data Sent:4111111111111111</ewayTrxnError>
#	<ewayTrxnStatus>False</ewayTrxnStatus>
#	<ewayTrxnNumber>10016</ewayTrxnNumber>
#	<ewayTrxnOption1></ewayTrxnOption1>
#	<ewayTrxnOption2></ewayTrxnOption2>
#	<ewayTrxnOption3>TRUE</ewayTrxnOption3>
#   <ewayAuthCode></ewayAuthCode>
#   <ewayReturnAmount>11998</ewayReturnAmount>
#	<ewayTrxnReference></ewayTrxnReference>
#</ewayResponse>

$bill_output["avsmes"] = "Not support";

preg_match("/<ewayTrxnStatus>(.*)<\/ewayTrxnStatus>/",$return,$out);

if($out[1] == "True")
{	preg_match("/<ewayAuthCode>(.*)<\/ewayAuthCode>/",$return,$out);
	$bill_output["code"] = 1; $bill_output["billmes"] = $out[1]; }
else
{	preg_match("/<ewayTrxnError>(.*)<\/ewayTrxnError>/",$return,$out);
	$bill_output["code"] = 2; $bill_output["billmes"] = $out[1]; }

preg_match("/<ewayTrxnNumber>(.*)<\/ewayTrxnNumber>/",$return,$out);
$bill_output["billmes"].= " (TrnxNum=".$out[1].")";

?>
