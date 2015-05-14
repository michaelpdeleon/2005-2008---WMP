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
# $Id: cc_payzip_result.php,v 1.20 2006/01/11 06:56:22 mclap Exp $
#

@set_time_limit(100);

require "./auth.php";

x_load('http');

if (empty($OrderID)) $OrderID = $orderid;

$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_payzip.php'");
$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$OrderID."'");
$skey = $OrderID;

$pp_login = $module_params["param01"];
$pp_pass = $module_params["param02"];
$test = $module_params["testmode"]!="N"?"testapi/":"";

$post = array();
$post[] = "<PAYZIP_XML><REQUEST>";
$post[] = "<Pin>".$pp_pass."</Pin>";
$post[] = "<AccID>".$pp_login."</AccID>";
$post[] = "<OrderID>".$OrderID."</OrderID>";
$post[] = "<Function>GET_ORDER_STATUS</Function>";
$post[] = "</REQUEST></PAYZIP_XML>";

list($a,$return)=func_https_request("POST","https://www.payzip.net:443/".$test."api/apixml.asp",$post,"");

#<PAYZIP_XML>
#      <RESPONSE>
#             <Function>GET_ORDER_STATUS</Function>
#             <Result>OK</Result>
#             <ResultCode>0</ResultCode>
#             <Message>Transaction Successful</Message>
#             <AccID>973008</AccID>
#             <OrderID>20030522081906</OrderID>
#             <OrderStatus>3</OrderStatus>
#             <StatusDate>20030520094500</StatusDate>
#             <Reference>849</Reference>
#             <PayAttempts>1</PayAttempts>
#             <TID>200672</TID>
#             <PaidOn>5/20/2003 4:32:00 AM</PaidOn>
#             <PaidBy>1</PaidBy>
#             <PaymentType>VISA</PaymentType>
#             <Description>Test Purchase</Description>
#             <Currency>USD</Currency>
#             <ProductTotal>10000</ProductTotal>
#             <VATRateTotal>0</VATRateTotal>
# 		 	  <AmountTotal>10000</AmountTotal>
#  	 		  <CustomerName>John Doe</CustomerName>
#             <EMailAddress>john@somewhere.com</EMailAddress>
#     </RESPONSE>
#</PAYZIP_XML>

$ordsts = array(
    "0" => "Open",
    "1" => "Authorized",
    "2" => "Cancelled",
    "3" => "Paid",
    "4" => "Refunded ",
    "5" => "Charge back"
);

if(preg_match("/<Result>OK<\/Result>/",$return))
{
	$bill_output["code"] = 1;

	preg_match("/<OrderStatus>(.*)<\/OrderStatus>/",$return,$out);
	$bill_output["billmes"] = "OrderStatus: ".(($ordsts[$out[1]]) ?  $ordsts[$out[1]] : "Code ".$out[1])."";
	if ($out[1] != '3' && $module_params["testmode"] != 'Y') {
		$bill_output["code"] = 2;
		preg_match("/<Message>(.*)<\/Message>/",$return,$out);
		$bill_output["billmes"] = $out[1]." ".$bill_output["billmes"];
	}

	preg_match("/<ApprovalCode>(.*)<\/ApprovalCode>/",$return,$out);
	if($out[1])$bill_output["billmes"] .= " (ApprovalCode: ".$out[1].")";
}
else
{
	$bill_output["code"] = 2;

	preg_match("/<Message>(.*)<\/Message>/",$return,$out);
	$bill_output["billmes"] = $out[1];

	preg_match("/<ResultCode>(.*)<\/ResultCode>/",$return,$out);
	if($out[1])$bill_output["billmes"] .= " (ResultCode: ".$out[1].")";
}

preg_match("/<Reference>(.*)<\/Reference>/",$return,$out);
if($out[1])$bill_output["billmes"] .= " (Reference: ".$out[1].")";

preg_match("/<TID>(.*)<\/TID>/",$return,$out);
if($out[1])$bill_output["billmes"] .= " (TID: ".$out[1].")";

require($xcart_dir."/payment/payment_ccend.php");

?>
