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
# $Id: cc_saferpay.php,v 1.21.2.4 2006/06/28 11:22:36 max Exp $
#

if (!empty($HTTP_GET_VARS['resp_code']) || !empty($HTTP_POST_VARS['resp_code'])) {
	define('USE_TRUSTED_GET_VARS',"DATA");

	require "./auth.php";

	$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_saferpay.php'");

	# Path to file 'saferpay' & configuration dir
	$safer_path = $module_params["param04"];
	$safer_config_path = $module_params["param06"];
	if ($safer_config_path == "")
		$safer_config_path = $safer_path;

	$exec_name = $safer_path.(X_DEF_OS_WINDOWS ? "saferpay.exe" : "saferpay");
	if (!file_exists($exec_name)) 
		func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound"); 

	$bill_output = array();
	$bill_output["sessid"] = func_query_first_cell("select sessionid from $sql_tbl[cc_pp3_data] where ref='".$ordr."'");

	if ($resp_code == "success" && !empty($DATA) && !empty($SIGNATURE)) {
# urldecode($DATA) = ...	
# <IDP MSGTYPE="PayConfirm" KEYID="1-0" ID="O3Y2rlA19lxdSAMUbdStA41rhn4b" TOKEN="PElEUCBNU0dUWVBFPSJUcmFuc2FjdGlvblRva2VuIiBJRD0iTzNZMnJsQTE5bHhkU0FNVWJkU3RBNDFyaG40YiIgQU1PVU5UPSI5ODAwIiBDVVJSRU5DWT0iQ0hGIiBLRVlJRD0iMS0wIi8+A==|079e9323301ec3c2ec0155aa8ec858c717a11d4bac86f12567c58ed33ff1896de755c338a6cb3aab5c54288ab1d75ba2543ad9a1d0d43793f399035917640200" VTVERIFY="(obsolete)" AMOUNT="9800" CURRENCY="CHF" PROVIDERID="90" PROVIDERNAME="Saferpay Test Card" ACCOUNTID="99867-94913159"/>
		$DATA = stripslashes($DATA);

		$fp = popen(func_shellquote($exec_name)." -payconfirm -p ".func_shellquote($safer_config_path)." -d \"".$DATA."\" -s \"".urldecode($SIGNATURE)."\" 2>&1", "r");
		$error = fgets($fp, 4096);
		pclose($fp);

		if ($error) {
			$bill_output["code"] = 2;
			preg_match("/ID=\"(.+)\"/U",$DATA,$id);
			$bill_output["billmes"] = $error." (ID: ".$id[1].")";

		} else {
			$bill_output["code"] = 1;
			$bill_output["billmes"] = " (DATA: ".$DATA.")";
		}

	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Control action: ".$resp_code;
	}

	require($xcart_dir."/payment/payment_ccend.php");

} else {

	if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

	$accoutid   = $module_params["param01"];
	$currency   = $module_params["param02"];
	$prefix     = $module_params["param03"];
	# Path to file 'saferpay' & configuration dir
	$safer_path = $module_params["param04"];
	$lang       = $module_params["param05"];
	$safer_config_path = $module_params["param06"];
	if ($safer_config_path == "")
		$safer_config_path = $safer_path;

	$exec_name = $safer_path.(X_DEF_OS_WINDOWS ? "saferpay.exe" : "saferpay");
	if (!file_exists($exec_name)) 
		func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound"); 


	$ordr = $prefix.join("-",$secure_oid);

	$self_url = $http_location."/payment/cc_saferpay.php?ordr=".$ordr."&resp_code=";

	$attr = array("NachName \"".$bill_firstname."\"",
		"VorName \"".$bill_lastname."\"",
		"Strasse \"".$userinfo["b_address"]."\"",
		"plz \"".$userinfo["b_zipcode"]."\"",
		"ort \"".$userinfo["b_city"]."\"",
		"HomeCountryId \"".$userinfo["b_city"]."\"",
#		"PAN \"".$userinfo["card_number"]."\"",
#		"EXP \"".$userinfo["card_expire"]."\"",
		"CURRENCY \"".$currency."\"",
		"ACCOUNTID \"".$accoutid."\"",
		"orderid \"".$ordr."\"",
		"AMOUNT ".(100*$cart["total_cost"]),
		"BACKLINK \"".$self_url."back\"",
		"FAILLINK \"".$self_url."fail\"",
		"SUCCESSLINK \"".$self_url."success\"",
		"DESCRIPTION \"Product(s)\"",
		"DELIVERY \"no\"",
		"LANGID \"".$lang."\""
	);

	$s_attr = "-a ".join(" -a ",$attr);

	$fp = popen(func_shellquote($exec_name)." -payinit -p ".func_shellquote($safer_config_path)." ".$s_attr." 2>&1", "r");
	$payinit_url = fgets($fp, 4096);
	pclose($fp);

	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

?>
<html>
<body onLoad="javascript: location.href='<?php print $payinit_url; ?>';">
<table width=100% height=100%>
<tr><td align=center valign=middle>Please wait while connecting to <b>Saferpay</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}
exit;

?>
