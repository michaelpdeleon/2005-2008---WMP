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
# $Id: cc_pnpss.php,v 1.17.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["FinalStatus"] && $HTTP_GET_VARS["orderID"]) {
	require "./auth.php";

	$avserr = array(
		"A" => "Address matches, ZIP code does not. ",
		"B" => "Street address match for international transaction; postal code not verified. ",
		"C" => "Street & postal code not verified for international transaction. ",
		"D" => "Street & Postal codes match for international transaction. Both the five-digit postal zip code as well as the first five numerical characters contained in the address match for the international transaction. ",
		"E" => "Transaction is ineligible for address verification. ",
		"F" => "Street address & postal codes match for international transaction. (UK Only) ",
		"G" => "AVS not performed because the international issuer does not support AVS. ",
		"I" => "Address information not verified for international transaction. ",
		"M" => "Street address & postal codes match for international transaction. ",
		"N" => "Neither the ZIP nor the address matches. ",
		"P" => "Postal codes match for international transaction; street address not verified. ",
		"S" => "AVS not supported at this time. ",
		"R" => "Issuer's authorization system is unavailable, try again later. ",
		"U" => "Unable to perform address verification because either address information is unavailable or the Issuer does not support AVS. ",
		"W" => "Nine-digit zip match, address does not. The nine-digit postal zip code matches that stored at the VIC or card issuer's center. However, the first five numerical characters contained in the address do not match. ",
		"X" => "Exact match (nine-digit zip and address). Both the nine-digit postal zip code as well as the first five numerical characters contained in the address match. ",
		"Y" => "Address & 5-digit or 9-digit ZIP match. ",
		"Z" => "Either 5-digit or 9-digit ZIP matches, address does not. ",
		"0" => "Service Not Allowed. Generally associated with credit cards that are either not allowed to be used for any online transactions or are not allowed to be used for a specific classification of company. "
	);

	$cvverr = array(
		"M" => "Match ",
		"N" => "No Match ",
		"P" => "Not Processed ",
		"X" => "Cannot Verify (also used as a test response by some processors) ",
		"U" => "Unable To Verify ",
		"S" => "Unavailable For Verification "
	);

	$bill_output["sessid"] = func_query_first_cell("select sessionid from $sql_tbl[cc_pp3_data] where ref='".$HTTP_GET_VARS["orderID"]."'");

	if ($FinalStatus=="success") {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "(AuthCode: ".$HTTP_GET_VARS["auth-code"].")";
	} else {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $MErrMsg." (".$FinalStatus.($HTTP_GET_VARS["resp-code"] ? "/".$HTTP_GET_VARS["resp-code"] : "").")";
	}

	if(!empty($cvvresp))
		$bill_output["cvvmes"] = (empty($cvverr[$cvvresp]) ? "CVV Code: ".$cvvresp : $cvverr[$cvvresp]);

	$avscode = $HTTP_GET_VARS["avs-code"];
	if(!empty($avscode))
		$bill_output["avsmes"] = (empty($avserr[$avscode]) ? "AVS Code: ".$avscode : $avserr[$avscode]);

	if(!empty($IPaddress))
    	$bill_output["billmes"].= " (IP: ".$IPaddress.")";

	$weblink=2;
	require($xcart_dir."/payment/payment_ccend.php");

} else {

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_publisher = $module_params["param01"];
	$pp_host = $module_params["param03"];
	$ordr = $secure_oid[0]; #NUMBER only...

	if(!$duplicate)
		db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	$post = array();
	$post[] = "publisher-name=".$pp_publisher;
	$post[] = "publisher-email=".$config["Company"]["orders_department"];
	$post[] = "authtype=authpostauth";
	#$post[] = "dontsndmail=yes";
	$post[] = "card-amount=".$cart["total_cost"];
	$post[] = "card-name=".$bill_name;
	$post[] = "card-address1=".$userinfo["b_address"];
	$post[] = "card-city=".$userinfo["b_city"];
	$post[] = "card-state=".($userinfo["b_country"]=="US" ? $userinfo["b_state"] : "ZZ");
	if($userinfo["b_country"]!="US")
		$post[] = "card-prov=".$userinfo["b_statename"];
	$post[] = "card-zip=".$userinfo["b_zipcode"];
	$post[] = "card-country=".$userinfo["b_country"];
	$post[] = "email=".$userinfo["email"];
	$post[] = "phone=".$userinfo["phone"];
	$post[] = "address1=".$userinfo["s_address"];
	$post[] = "city=".$userinfo["s_city"];
	$post[] = "state=".($userinfo["s_country"]=="US" ? $userinfo["s_state"] : "ZZ");
	if($userinfo["s_country"]!="US")
		$post[] = "province=".$userinfo["s_statename"];
	$post[] = "country=".$userinfo["s_country"];
	$post[] = "orderID=".$ordr;
	$post[] = "app-level=".$module_params["param05"];

	$returnurl=$http_location."/payment/cc_pnpss.php";
	$post[] = "success-link=".$returnurl;
	$post[] = "badcard-link=".$returnurl;
	$post[] = "problem-link=".$returnurl;

?>
<html>
<body onLoad="document.process.submit();">
<form action="<?php echo "https://".$pp_host.":443/payment/pay.cgi"; ?>" method=POST name=process>
<?php
	foreach($post as $z) {
		list($k,$v) = split("=",$z,2);
		print "<input type=hidden name=\"".$k."\" value=\"".$v."\">\n";
	}
?>
<table width=100% height=100%>
<tr>
	<td align=center valign=middle>Please wait while connecting to <b>Plug'n'Pay</b> payment gateway...</td>
</tr>
</table>
</body>
</html>
<?php
}
exit;

?>
