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
# $Id: cc_pi_result.php,v 1.20 2006/01/11 06:56:22 mclap Exp $
#

require "./auth.php";

x_load('order');

#
#<%resultado=Request.QueryString("result")%>
#<%order=Request.QueryString("pszPurchorderNum")%>
#<%fecha=Request.QueryString("pszTxnDate")%>
#<%tipotrans=Request.QueryString("tipotrans")%>
#<%store=Request.QueryString("store")%>
#<% if resultado = 0 then %>
#<%codaprobacion=Request.QueryString("pszApprovalCode")%>
#<%idtrans=Request.QueryString("pszTxnID")%>
#<% else %>
#<%coderror=Request.QueryString("coderror")%>
#<%error=Request.QueryString("deserror")%>
#<%end if%>
#

if($REQUEST_METHOD == "GET")
{
	if(isset($result) && isset($pszPurchorderNum))
	{

# ACCEPT
#[result]=0
#[pszPurchorderNum]=38
#[pszTxnDate]=02/07/2003
#[tipotrans]=SSL
#[store]=PI00001537
#[pszApprovalCode]=123456
#[pszTxnID]=6500001
#[fpago]=000

# DECLINE
#[result]=2
#[pszPurchorderNum]=39
#[pszTxnDate]=02/07/2003
#[tipotrans]=SSL
#[store]=PI00001537
#[coderror]=180
#[deserror]=Operación Denegada
#[fpago]=000

		$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$pszPurchorderNum."'");

		if(!$result)
		{	$bill_output["code"] = 1;$bill_output["billmes"] = "(ApprovalCode: ".$pszApprovalCode."; TxnID: ".$pszTxnID.")";}
		else
		{	$bill_output["code"] = 2;$bill_output["billmes"] = "Declined (ErrorCode: ".$coderror."; Description: ".$deserror.")";}

		$skey = $pszPurchorderNum;
		require($xcart_dir."/payment/payment_ccmid.php");
		require($xcart_dir."/payment/payment_ccwebset.php");

	}
	elseif(isset($order) && isset($store))
	{
		$a = func_query_first("SELECT param1,param2,param3,param4,param5,sessionid,trstat FROM $sql_tbl[cc_pp3_data] WHERE ref='".$order."'");
		if (empty($a)) func_header_location($xcart_catalogs['customer'].'/error_message.php?access_denied&id=63');

		$sessid = $a['sessionid'];
		$trstat = $a['trstat'];
		unset($a['sessionid']);
		unset($a['trstat']);

		$oids = explode('|',$trstat);
		$status = array_shift($oids);

		x_session_id($sessid);
		x_session_register("cart");

		$pp_total = 0;
		$pp_count = 0;
		$pp_orders = array();
		foreach ($oids as $orderid) {
			$data = func_order_data($orderid);
			if (empty($data))
				continue;
			$pp_orders[] = $data;
			$pp_total += $data['order']['total'];
			$pp_count += count($data['products']);
		}

		print "M978".(100*$pp_total)."\n".$pp_count."\n";

		foreach ($pp_orders as $data) {
			foreach($data["products"] as $k)
				print $k["productid"]."\n".$k["product"]."\n".$k["amount"]."\n".(100*$k["price"])."\n";
		}
	}
}
?>
