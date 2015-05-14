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
# $Id: cc_chargeit.php,v 1.29 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["result"] && $HTTP_POST_VARS["sid"] && $HTTP_POST_VARS["get_result"])
{

#  'result' => 'NOT CAPTURED',
#  'authorization' => 'Declined',
#  'reference_number' => '',
#  'reference_no' => '',
#  'amount' => '13.99',
#  'sid' => 'xcart526',
#  'userdef1' => '',
#  'userdef2' => '',
#  'userdef3' => '',
#  'userdef4' => '',
#  'userdef5' => 'rrf.ru/~sdg/eshop',
#  'get_result' => 'Declined',
#  'avs' => 'Address + 5-Digit Zip Code Match!',

	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$sid."'");

	if($HTTP_POST_VARS["result"] == "CAPTURED")
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = " (Reference No: ".$reference_no.")";
	}
	elseif($HTTP_POST_VARS["result"] == "NOT CAPTURED")
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $authorization;
	}
	else
		$bill_output["code"] = 0;

	if(!empty($avs))$bill_output["avsmes"] = $avs;

	$weblink = 1;
	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$accountid = $module_params["param01"];
	$url = "https://www.gochargeit.com/cgi-bin/remote".($module_params["testmode"] == "Y" ? "demo" : "").".cgi";
	$oid = $module_params["param03"].join("-",$secure_oid);

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($oid)."','".$XCARTSESSID."')");

	switch($userinfo["card_type"])
	{
		case "VISA":		$ctype="VISA";break;
		case "MC":			$ctype="MASTERCARD";break;
		case "AMEX":		$ctype="AMEX";break;
	}

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $url; ?>" method=POST name=process>
  	<input type=hidden name=F_CCNAME value="<?php echo htmlspecialchars($userinfo["card_name"]); ?>">
	<input type=hidden name=F_CCADDR value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=F_CCCITY value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=F_CCSTATE value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=F_CCZIP value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=F_DESC value="<?php echo str_replace('"', "'", $config['Company']['company_name']); ?> products">
	<input type=hidden name=F_EMAIL value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=CARD value="<?php echo htmlspecialchars($userinfo["card_number"]); ?>">
	<input type=hidden name=EXPIR value="<?php echo htmlspecialchars($userinfo["card_expire"]); ?>">
	<input type=hidden name=F_CCTYPE value="<?php echo htmlspecialchars($ctype); ?>">
	<input type=hidden name=AMOUNT value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
	<input type=hidden name=SID value="<?php echo htmlspecialchars($oid); ?>">
  	<input type=hidden name=TOTALLYREMOTE value=1>
	<input type=hidden name=REMOTEURL value="<?php echo $http_location."/payment/cc_chargeit.php"; ?>">
  	<input type=hidden name=RETURNURL value="<?php echo "http://www.gochargeit.com/".$accountid."/"; ?>chargelink.htm?returning">
  	<input type=hidden name=ROUTE value="<?php echo htmlspecialchars($accountid); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>GoChargeIt</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
