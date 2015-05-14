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
# $Id: cc_triple_return.php,v 1.11 2006/01/11 06:56:23 mclap Exp $
#

require "./auth.php";

x_load('http');

$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_triple.php'");

$a = func_query_first("select param1,sessionid from $sql_tbl[cc_pp3_data] where ref='$ordr'");
$clusterid = $a["param1"];
$bill_output["sessid"] = $a["sessionid"];

$pp_merch = $module_params["param01"];
$pp_test = ($module_params["testmode"]=='Y') ? "test" : "www";

$post = "";
$post[] = "command=status_payment_cluster";
$post[] = "merchant_name=".$pp_merch;
$post[] = "merchant_transaction_id=".$ordr;
$post[] = "payment_cluster_id=".$clusterid;
$post[] = "report_type=xml_std";

list($a,$ret)=func_https_request("POST","https://".$pp_test.".tripledeal.com:443/ps/com.tripledeal.paymentservice.servlets.PaymentService",$post);

#<status_payment_cluster>
#  <status>
#    <payment_cluster_process>paid</payment_cluster_process>
#    <last_partial_payment_process>paid</last_partial_payment_process>
#    <last_partial_payment_method>tripledeal-ipaygwv2-mc-ssl</last_partial_payment_method>
#    <payout_process>started</payout_process>
#    <meta_considered_safe>true</meta_considered_safe>
#    <meta_charged_back>N</meta_charged_back>
#    <meta_amount_received>paid</meta_amount_received>
#  </status>
#</status_payment_cluster>

#<status_payment_cluster>
#  <status>
#    <payment_cluster_process>started</payment_cluster_process>
#    <last_partial_payment_process></last_partial_payment_process>
#    <last_partial_payment_method></last_partial_payment_method>
#    <payout_process>new</payout_process>
#    <meta_considered_safe>false</meta_considered_safe>
#    <meta_charged_back>N</meta_charged_back>
#    <meta_amount_received>none</meta_amount_received>
#  </status>
#</status_payment_cluster>

preg_match("/<payment_cluster_process>(.+)<\/payment_cluster_process>/U",$ret,$out1);
preg_match("/<meta_amount_received>(.+)<\/meta_amount_received>/U",$ret,$out2);
preg_match("/<payout_process>(.+)<\/payout_process>/U",$ret,$out3);
preg_match("/<last_partial_payment_method>(.+)<\/last_partial_payment_method>/U",$ret,$out4);

$bill_output["billmes"] = "Payment cluster process: ".$out1[1]."; Meta amount received: ".$out2[1]."; Payout process: ".$out3[1]."; Method: ".$out4[1];

if($out1[1]=="paid" && $out2[1]=="paid")
{
	$bill_output["code"] = 1;
}
elseif($out4[1]=="banktransfer-nl")
{
	$bill_output["code"] = 3;
}
else
{
	$bill_output["code"] = 2;
}

require($xcart_dir."/payment/payment_ccend.php");

?>
