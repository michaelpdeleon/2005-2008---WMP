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
# $Id: cc_cardia2.php,v 1.17 2006/01/11 06:56:22 mclap Exp $
#
# API version: 2.2
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && isset($HTTP_GET_VARS["ok"]) && isset($HTTP_GET_VARS["oid"])) {
	require "./auth.php";

	x_load('http');

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");
	$token = func_query_first_cell("SELECT param01 FROM $sql_tbl[ccprocessors] WHERE processor='cc_cardia2.php'");

	$str = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:si=\"http://soapinterop.org/xsd\" xmlns:nu=\"http://testuri.org\">
<SOAP-ENV:Body>
<ReturnTransactionStatus xmlns=\"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx\">
	<merchantToken>".$token."</merchantToken>
	<merchantReference>".$oid."</merchantReference>
</ReturnTransactionStatus>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>";

	list($a,$return) = func_https_request("POST","https://secure.cardia.no:443/Service/Card/Transaction/1.2/Transaction.asmx",array($str), "","","text/xml","","","",array("SOAPAction: \"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx/ReturnTransactionStatus\""));

	preg_match("/<StatusCode>(\d+)<\/StatusCode>/",$return,$status);
	preg_match("/<ResponseCode>(\d+)<\/ResponseCode>/",$return,$resp);

	$err1 = array(
		"1" => "Transaction is approved",
		"2" => "Transaction not approved",
		"3" => "No transaction registered",
		"4" => "General error",
		"5" => "Transaction is approved, but voided afterwards."
	);

	$err2 = array(
		"0" => "Success",
		"6" => "Invalid card number",
		"7" => "Card is hotlisted (known card used for fraud)",
		"17" => "Card is expired",
		"18" => "Invalid expiry date",
		"19" => "Cardnumber is not valid",
		"22" => "Cardtype is not registered for merchant",
		"25" => "Card can not be used for internet purchases",
		"26" => "Card can not be used for internet purchases",
		"27" => "Card can not be used for internet purchases",
		"28" => "Card can not be used for internet purchases",
		"29" => "Card can not be used for internet purchases",
		"31" => "Card can not be used for internet purchases",
		"45" => "Amount exceeds allowed amount",
		"46" => "Amount is below minimum amount",
		"54" => "Card can not be used for internet purchases",
		"69" => "The bank do not authorise the payment (authorisation not OK)",
		"74" => "Card is rejected",
		"75" => "Not authorised"
	);

	$bill_output["billmes"] = $err1[$status[1]];

	if ($status[1] == 1) {
		$bill_output["code"] = 1;
	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"].= ": ".(empty($err2[$resp[1]]) ? "Resp.Code: ".$resp[1] : $err2[$resp[1]]);
	}

	require($xcart_dir."/payment/payment_ccend.php");

} else {

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	x_load('http');

	$_orderids = $module_params["param08"].join("-",$secure_oid);

	$err = "";
	if(!$duplicate) {

		$url = $http_location."/payment/cc_cardia2.php";

		$str = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:si=\"http://soapinterop.org/xsd\" xmlns:nu=\"http://testuri.org\">
<SOAP-ENV:Body>
<PrepareTransaction xmlns=\"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx\">
	<merchantToken>".$module_params["param01"]."</merchantToken>
	<applicationIdentifier></applicationIdentifier>
	<store>".$module_params["param02"]."</store>
	<orderDescription>Order#".$_orderids."</orderDescription>
	<merchantReference>".$_orderids."</merchantReference>
	<currencyCode>".$module_params["param03"]."</currencyCode>
	<successfulTransactionUrl>".htmlspecialchars($url."?ok=1&oid=".$_orderids)."</successfulTransactionUrl>
	<unsuccessfulTransactionUrl>".htmlspecialchars($url."?ok=0&oid=".$_orderids)."</unsuccessfulTransactionUrl>
	<authorizedNotAuthenticatedUrl></authorizedNotAuthenticatedUrl>
	<amount>".str_replace(",",".",$cart["total_cost"])."</amount>
	<skipFirstPage>".$module_params["param04"]."</skipFirstPage>
	<skipLastPage>".$module_params["param05"]."</skipLastPage>
	<isOnHold>".$module_params["param06"]."</isOnHold>
	<useThirdPartySecurity>".$module_params["param07"]."</useThirdPartySecurity>
	<paymentMethod>3000</paymentMethod>
</PrepareTransaction>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>";

		$str = str_replace("\t", "", $str);
		$str = str_replace("\n", "", $str);
		list($a,$return) = func_https_request("POST","https://secure.cardia.no:443/Service/Card/Transaction/1.2/Transaction.asmx",array($str), "","","text/xml","","","",array("SOAPAction: \"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx/PrepareTransaction\""));

		if (preg_match("/Address>([^<]+)<\/Address/",$return,$addr) && preg_match("/ReferenceGuid>([^<]+)<\/ReferenceGuid/",$return,$gid)) {
			$addr = $addr[1];
			$gid = $gid[1];
			db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,param1) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','".$gid."')");
		} elseif(preg_match("/Error>([^<]+)<\/Error/",$return,$preg)) {
			$err = $preg[1];
		}

	} else {
		$gid = func_query_first_cell("SELECT param1 FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$_orderids."'");
	}

	if (!empty($gid) && !empty($addr)) {
?>
<html>
<body onLoad="document.process.submit();">
<form action="<?php echo $addr; ?>" name="process" method="GET">
<input type="hidden" name="guid" value="<?php echo $gid; ?>">
</form>
<table width="100%" height="100%">
<tr>
	<td align="center" valign="middle">Please wait while connecting to <b>Cardia Shop 2.2</b> payment gateway...</td>
</tr>
</table>
</body>
</html>
<?php
	exit;

	} elseif (!empty($err)) {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Declined: ".$err;
	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Declined: PrepareTransaction failed.";
	}
}

?>
