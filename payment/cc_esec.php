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
# $Id: cc_esec.php,v 1.22 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && !empty($HTTP_GET_VARS["ref-id"]) && !empty($HTTP_GET_VARS["message"]))
{
	require "./auth.php";

	$staerr = array(
        "00" => "successful approval",
        "01" => "refer to issuer",
        "02" => "refer to issuer's special conditions",
        "03" => "invalid merchant",
        "04" => "pickup card",
        "05" => "do not honour",
        "06" => "error",
        "07" => "pickup card, special conditions",
        "08" => "honour with ID (signature)(corresponds to 200 response)",
        "09" => "request in progress",
        "10" => "approved for partial amount",
        "11" => "approved VIP",
        "12" => "invalid transaction",
        "13" => "invalid amount",
        "14" => "invalid card number",
        "15" => "no such issuer",
        "16" => "approved, update track 3",
        "17" => "customer cancellation",
        "18" => "customer dispute",
        "19" => "re-enter transaction",
        "20" => "invalid response",
        "21" => "no action taken",
        "22" => "suspected malfunction",
        "23" => "unacceptable transaction fee",
        "24" => "file date not supported",
        "25" => "unable to locate record on file",
        "26" => "duplicate file update record, old record replaced",
        "27" => "file update field error",
        "28" => "file update file locked out",
        "29" => "file update not successful, contact acquirer",
        "30" => "format error",
        "31" => "bank not supported by switch",
        "32" => "completed partially",
        "33" => "expired card",
        "34" => "suspected fraud",
        "35" => "contact acquirer",
        "36" => "restricted card",
        "37" => "contact acquirer security",
        "38" => "allowable PIN retries exceeded",
        "39" => "no credit account",
        "40" => "request function not supported",
        "41" => "lost card",
        "42" => "no universal account",
        "43" => "stolen card",
        "44" => "no investment account",
        "51" => "insufficient funds",
        "52" => "no cheque account",
        "53" => "no savings account",
        "54" => "expired card",
        "55" => "incorrect PIN",
        "56" => "no card record",
        "57" => "transaction not permitted to cardholder",
        "58" => "transaction not permitted to terminal",
        "59" => "suspected fraud",
        "60" => "contact acquirer",
        "61" => "exceeds withdrawal amount limit",
        "62" => "restricted card",
        "63" => "security violation",
        "64" => "original amount incorrect",
        "65" => "exceeds withdrawal frequency limit",
        "66" => "contact acquirer security",
        "67" => "hard capture",
        "68" => "response received too late",
        "75" => "allowable number of PIN retries exceeded",
        "90" => "cutoff in progress",
        "91" => "issuer inoperative",
        "92" => "financial institution cannot be found",
        "93" => "transaction cannot be completed, violation of law",
        "94" => "duplicate transmission",
        "95" => "reconcile error",
        "96" => "system malfunction",
        "97" => "reconciliation totals have been reset",
        "98" => "MAC error",
        "99" => "reserved, will not be returned "
	);

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_GET_VARS["ref-id"]."'");

	preg_match("/^(\d{3})/U",$message,$out);$code = $out[1];

	if($code>=200 && $code<=299)
        $bill_output["code"] = 1;
	else
        $bill_output["code"] = 2;

	$bill_output["billmes"] = $message;

	if($HTTP_GET_VARS["auth-id"])
        $bill_output["billmes"].= " (Auth ID: ".$HTTP_GET_VARS["auth-id"].")";
	if($HTTP_GET_VARS["txn-id"])
        $bill_output["billmes"].= " (Txn ID: ".$HTTP_GET_VARS["txn-id"].")";
	if($HTTP_GET_VARS["eft-response"])
        $bill_output["billmes"].= " (EFT ".(empty($staerr[$HTTP_GET_VARS["eft-response"]]) ? "Code: ".$HTTP_GET_VARS["eft-response"] : "Response: ".$staerr[$HTTP_GET_VARS["eft-response"]]).")";    
        

	if($HTTP_GET_VARS["signature"])
        $bill_output["billmes"].= " (Signature: ".$HTTP_GET_VARS["signature"].")";
	
	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$esec_login = $module_params["param01"];
	$esec_prefix = $module_params["param03"];

	$ordr = $esec_prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	if($module_params["testmode"] == "N")
	{
		$test = "false";
        $first4 = 0+substr($userinfo["card_number"],0,4);
        if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="visa"; # VISA
        if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="mastercard"; # MasterCard
        if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="amex"; # AmericanExpress
        if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="amex"; # AmericanExpress
        if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="dinersclub"; # Diners
        if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="dinersclub"; # Diners
        if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="dinersclub"; # Diners
        if($first4>=3528 && $first4<=3589)$userinfo["card_type"]="jcb"; # JCB
	}
	else
	{
        $test = "true";
        $esec_login = "test";
        $userinfo["card_type"]="testcard";
        $userinfo["card_number"]=$module_params["testmode"]=="A"?"testsuccess":"testfailure";
	}

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://sec.aba.net.au/cgi-bin/service/authorise/<?php echo $esec_login; ?>" method=POST name=process>
	 <input type=hidden name=EPS_MERCHANT value="<?php echo htmlspecialchars($esec_login); ?>">
	 <input type=hidden name=EPS_REFERENCEID value="<?php echo htmlspecialchars($ordr); ?>">
	 <input type=hidden name=EPS_CARDNUMBER value="<?php echo htmlspecialchars($userinfo["card_number"]); ?>">
	 <input type=hidden name=EPS_CARDTYPE value="<?php echo htmlspecialchars($userinfo["card_type"]); ?>">
	 <input type=hidden name=EPS_EXPIRYMONTH value="<?php echo htmlspecialchars(0+substr($userinfo["card_expire"],0,2)); ?>">
	 <input type=hidden name=EPS_EXPIRYYEAR value="<?php echo htmlspecialchars(2000+substr($userinfo["card_expire"],2,2)); ?>">
	 <input type=hidden name=EPS_NAMEONCARD value="<?php echo htmlspecialchars($userinfo["card_name"]); ?>">
	 <input type=hidden name=EPS_AMOUNT value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
	 <input type=hidden name=EPS_CCV value="<?php echo htmlspecialchars($userinfo["card_cvv2"]); ?>">
	 <input type=hidden name=EPS_VERSION value="3">
	 <input type=hidden name=EPS_3DSECURE value="false">
	 <input type=hidden name=EPS_REDIRECT value="true">
	 <input type=hidden name=EPS_TEST value="<?php echo htmlspecialchars($test); ?>">
	 <input type=hidden name=EPS_RESULTURL value="<?php echo $current_location."/payment/cc_esec.php"; ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>eSec</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
