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

# $Id: cc_scopus.php,v 1.26 2006/01/11 06:56:22 mclap Exp $

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if($REQUEST_METHOD == "POST" && ($HTTP_POST_VARS['transId']=="getOrder" || $HTTP_GET_VARS['transId']=="getOrder"))
{
	require "./auth.php";

	$sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$numOrder."'");

	x_session_id($sessid);
	x_session_register("cart");

	print "<BEGIN_ORDER_DESCRIPTION><orderid>=(".$numOrder.")\n";
	foreach($products as $product)
	{
		print "<descritivo>=(".$product["product"].")\n";
		print "<quantidade>=(".$product["amount"].")\n";
		print "<unidade>=(".$product["productid"].")\n";
		print "<valor>=(".(100*$product["amount"]*$product["price"]).")\n";
	}

	if($cart["total_cost"]-$cart["subtotal"])
	{
		print "<adicional>=(adicional)\n";
		print "<valorAdicional>=(".(100*($cart["total_cost"]-$cart["subtotal"])).")\n";
	}
	print "<END_ORDER_DESCRIPTION>";

	exit;
}
elseif($REQUEST_METHOD == "POST" && ($HTTP_POST_VARS['transId']=="putAuth" || $HTTP_GET_VARS['transId']=="putAuth"))
{
	require "./auth.php";

	$line = "if=".$HTTP_POST_VARS["if"]."; ";
	$line.= "cod=".$HTTP_POST_VARS["cod"]."; ";
	$line.= "cctype=".$HTTP_POST_VARS["cctype"]."; ";
	$line.= "ccname=".$HTTP_POST_VARS["ccname"]."; ";
	$line.= "ccemail=".$HTTP_POST_VARS["ccemail"]."; ";
	$line.= "numparc=".$HTTP_POST_VARS["numparc"]."; ";
	$line.= "valparc=".$HTTP_POST_VARS["valparc"]."; ";
	$line.= "valtotal=".$HTTP_POST_VARS["valtotal"]."; ";
	$line.= "prazo=".$HTTP_POST_VARS["prazo"]."; ";
	$line.= "tipopagto=".$HTTP_POST_VARS["tipopagto"]."; ";
	$line.= "assinatura=".$HTTP_POST_VARS["assinatura"];

	$out = split("\*\*\*",chunk_split($line,250,"***"));

	db_query("update $sql_tbl[cc_pp3_data] set param1='".$out[0]."', param2='".$out[1]."',param3='".$out[2]."',param4='".$out[3]."',param5='".$out[4]."' where ref='".$numOrder."'");

	if(!$cod)
		print "<PUT_AUTH_OK>";
	else
		print "<ERRO>";
	exit;
}
elseif ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["numOrder"])
{
	require "./auth.php";

	$datas = func_query_first("select * from $sql_tbl[cc_pp3_data] where ref='".$numOrder."'");
	$bill_output["sessid"] = $datas["sessionid"];

	if($Transac=="statusreq" && $assinatura)
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = $datas["param1"].$datas["param2"].$datas["param3"].$datas["param4"].$datas["param5"];
	}
	elseif($cod && $errordesc)
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $errordesc." (".$cod.")";
	}

#	print_r($bill_output); exit;
	require($xcart_dir."/payment/payment_ccend.php");
}

else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$id = $module_params ["param01"];
	$test = ($module_params ["testmode"]!="N" ? "teste" : "");
	$ordr = $module_params ["param03"].join("-",$secure_oid);
	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="http<?php echo $test ? "" : "s"; ?>://mup<?php echo $test; ?>.comercioeletronico.com.br/sepsapplet/<?php echo $id; ?>/prepara_pagto.asp" method=POST name=process>
	<input type=hidden name=MerchantId value="<?php echo htmlspecialchars($id); ?>">
	<input type=hidden name=OrderId value="<?php echo htmlspecialchars($ordr); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>Scopus Tecnologia</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
