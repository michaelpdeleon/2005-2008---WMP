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
# $Id: cc_payweb.php,v 1.12 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["PAYGATEID"] && $HTTP_POST_VARS["ORDERNO"] && $HTTP_POST_VARS["CHECKSUM"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["ORDERNO"]."'");

#  'PAYGATEID' => '10011013800',
#  'ORDERNO' => '161',
#  'TRANSTYPE' => 'A',
#  'TRANSSTATUS' => '2',
#  'RESULTCODE' => '990020',
#  'AUTHCODE' => '00000000',
#  'AMOUNT' => '10.02',
#  'DESC' => 'Auth Declined',
#  'TID' => '1931223',
#  'CHECKSUM' => '15010',

#0	Not Done
#1	Approved
#2	Declined
#3	Paid
#4	Refunded
#5	Received by PayGate

	for($t=$i=0;$i<strlen($PAYGATEID);$i++)
		$t += substr($PAYGATEID,$i,1);
	$sum = 100*$AMOUNT*$t;

	for($t=$i=0;$i<strlen($RESULTCODE);$i++)
		$t += substr($RESULTCODE,$i,1);
	$sum = $sum - $t;

	$bill_output["billmes"] = "";
	if($sum!=$CHECKSUM)
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "CHECKSUM mismatched! ";
	}
	elseif($TRANSSTATUS==="1" || $TRANSSTATUS==="3")
		$bill_output["code"] = 1;
	elseif($TRANSSTATUS==="2")
		$bill_output["code"] = 2;
	elseif($TRANSSTATUS==="0" || $TRANSSTATUS==="5")
		$bill_output["code"] = 3;

	if(!empty($DESC))				$bill_output["billmes"].= $DESC;
	if(!empty($AUTHCODE))			$bill_output["billmes"].= " (AuthCode: ".$AUTHCODE.") ";
	if(!empty($TID))				$bill_output["billmes"].= " (TransID: ".$TID.") ";
	if(!empty($TRANSSTATUS))		$bill_output["billmes"].= " (TRANSSTATUS/RESULTCODE: ".$TRANSSTATUS."/".$RESULTCODE.") ";

	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant = $module_params ["param01"];
	$_orderids = $module_params ["param02"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

	for($t=$i=0;$i<strlen($merchant);$i++)
		$t += substr($merchant,$i,1);
	$sum = 100*$cart["total_cost"]*$t;

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.paygate.co.za/payweb/request.asp" method=POST name=process>
	<input type=hidden name=PAYGATEID value="<?php echo $merchant; ?>">
	<input type=hidden name=ORDERNO value="<?php echo $_orderids; ?>">
	<input type=hidden name=AMOUNT value="<?php echo $cart["total_cost"]; ?>">
	<input type=hidden name=RETURNURL value="<?php echo $http_location."/payment/cc_payweb.php";?>">
	<input type=hidden name=CHECKSUM value="<?php echo $sum;?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>PayWeb</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
