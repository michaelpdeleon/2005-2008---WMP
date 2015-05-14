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
# $Id: cc_multi.php,v 1.24.2.1 2006/06/15 10:10:49 max Exp $
#

function GetTcpinfo($psMsg,$IP, $Port, $psFront)
{		
	sleep(2);
	$mysocket = fsockopen($IP, $Port, $errno, $errstr, 10);
	if(isset($mysocket))
		{ fputs($mysocket,$psMsg."\n");
          while(!feof($mysocket))
          	$Ret = $Ret.fgets($mysocket,128);
          fclose($mysocket);
		}
	$pos = strpos($Ret,$psFront);
	if($pos>0)
    	$Ret = trim(substr($Ret,$pos + strlen($psFront) +1));
	return $Ret;
}

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["mpVar1"] && $HTTP_GET_VARS["mpOrder_ID"])
{
	require "./auth.php";

	$a = func_query("select * from $sql_tbl[cc_pp3_data] where ref='".$mpOrder_ID."'");
	$bill_output["sessid"]=$a["sessionid"];

	$sResult = GetTcpinfo("mpCheckTrans;.;".$a["param3"].";.;".$mpOrder_ID.";.;","multipay.net", "2229","System ready.");
	#;Status;currency;Amount;Payment type;Date and time; Firsttime; admin id;bankid

	$Items = split(";",$sResult);

	if ($Items[0] == "notfound" )
	{
		$bill_output["code"]=2;
		$bill_output["billmes"] = "Failed. Order not found";
	}
	elseif ($Items[1] == "B")
	{
		if($Items[2] != $a["param1"] || (0+$Items[3]) != (0+$a["param2"]))
		{
			$bill_output["code"] = 2;
			$bill_output["billmes"] = "Failed currency or/and amount";
		}

		if ($Items[6] == "Y")
		{
			$bill_output["code"] = 1;
			$bill_output["billmes"] = "(AdminID: ".$Items[7]."; BankID: ".$Items[8].")";
		}
		else
		{
			$bill_output["code"] = 2;
			$bill_output["billmes"] = "Order was checked before on ".$Items[6];
		}
	}
	else
	{	
			$bill_output["code"] = 2;
			$bill_output["billmes"] = "Transaction in progress (Code: ".$Items[1].")";
	}

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$sellerid = $module_params["param01"];
	$currency = $module_params["param02"];
	$adminid = $module_params["param03"];
	$allowid = strtoupper($module_params["param04"]);

	$description = " - ".$bill_name." / ".$login;
	$string = array();
	foreach ($products as $product)
		$string [] = " - ".$product["product"]." (".$product["price"]." x ".$product["amount"].")";

	if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
	foreach ($cart["giftcerts"] as $tmp_gc)
		$string [] = " - GIFT CERTIFICATE (".$tmp_gc["amount"]." x 1)";

	$ordr = $module_params["param05"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,param1,param2,param3,sessionid) VALUES ('".addslashes($ordr)."','".$currency."','".$cart["total_cost"]."','".$sellerid."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://multipay.net/cgi-bin/multipay.cgi?select" method=POST name=process>
  	<input type=hidden name=mpType value=1>
  	<input type=hidden name=mpAdministration_ID value="<?php echo htmlspecialchars($adminid); ?>">
  	<input type=hidden name=mpSeller_ID value="<?php echo htmlspecialchars($sellerid); ?>">
  	<input type=hidden name=mpOrder_ID value="<?php echo htmlspecialchars($ordr); ?>">
  	<input type=hidden name=mpDescription value="<?php echo htmlspecialchars($description); ?>">
  	<input type=hidden name=mpItems value="<?php echo htmlspecialchars(join("<br />",$string)); ?>">
	<input type=hidden name=mpCountry value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=mpCurrency value="<?php echo htmlspecialchars($currency); ?>">
	<input type=hidden name=mpSuccessURL value="<?php echo $http_location."/payment/cc_multi.php"; ?>">
	<input type=hidden name=mpVar1  value="response">
	<input type=hidden name=mpVar2  value="">
	<input type=hidden name=mpAmount value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
	<input type=hidden name=mpnaw_email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
  	<input type=hidden name=mpnaw_last value="<?php echo htmlspecialchars($userinfo["b_lastname"]); ?>">
  	<input type=hidden name=mpnaw_first value="<?php echo htmlspecialchars($bill_firstname); ?>">
	<input type=hidden name=mpnaw_street value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=mpnaw_number value="xxx">
	<input type=hidden name=mpnaw_zipcode value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=mpnaw_city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=mpnaw_country value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=mpnaw_telno value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=mpAllow value="<?php echo htmlspecialchars($allowid); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>MultiPay</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
