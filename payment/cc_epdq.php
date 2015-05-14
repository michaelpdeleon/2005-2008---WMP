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
# $Id: cc_epdq.php,v 1.40 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["oid"])
{
	require "./auth.php";

	$skey = $HTTP_GET_VARS["oid"];
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	x_load('http');

	$merchant = $module_params["param01"];
	$clientid = $module_params["param02"];
	$phrase   = $module_params["param03"];
	$currency = $module_params["param04"];
	$auth     = $module_params["param05"];
	$cpi_logo = $module_params["param06"];
	$ordr = $module_params["param07"].join("-",$secure_oid);

	#the following parameters have been obtained earlier in the merchant's webstore: clientid, passphrase, oid, currencycode, total
	$params="clientid=".$clientid;
	$params.="&password=".$phrase;
	$params.="&oid=".$ordr;
	$params.="&chargetype=".$auth;
	$params.="&currencycode=".$currency;
	$params.="&total=".$cart["total_cost"];

	#perform the HTTP Post
	list($a1,$epdqdata,$a2)=func_http_post_request("secure2.epdq.co.uk", "/cgi-bin/CcxBarclaysEpdqEncTool.e", $params);

	if($epdqdata == "")
		{ func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound");exit; }

	$returnurl=$http_location."/payment/cc_epdq.php";
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdq.e" method=POST name=process>
    <?php print $epdqdata."\n"; ?>
    <input type=hidden name=merchantdisplayname value="<?php echo htmlspecialchars($merchant); ?>">
	<input type=hidden name=cpi_logo value="<?php echo htmlspecialchars($cpi_logo); ?>">
	<input type=hidden name=email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=baddr1 value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=baddr2 value="<?php echo htmlspecialchars($userinfo["b_address_2"]); ?>">
	<input type=hidden name=bcity value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=bcountry value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=bpostalcode value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=<?php echo htmlspecialchars(($userinfo["b_country"]=="US")?("bstate"):("bcountyprovince")); ?> value="<?php echo htmlspecialchars(($userinfo["b_country"]=="US")?($userinfo["b_state"]):($userinfo["b_statename"])); ?>">
	<input type=hidden name=<?php echo htmlspecialchars(($userinfo["s_country"]=="US")?("sstate"):("scountyprovince")); ?> value="<?php echo htmlspecialchars(($userinfo["s_country"]=="US")?($userinfo["s_state"]):($userinfo["s_statename"])); ?>">
	<input type=hidden name=saddr1 value="<?php echo htmlspecialchars($userinfo["s_address"]); ?>">
	<input type=hidden name=saddr2 value="<?php echo htmlspecialchars($userinfo["s_address_2"]); ?>">
	<input type=hidden name=scity value="<?php echo htmlspecialchars($userinfo["s_city"]); ?>">
	<input type=hidden name=spostalcode value="<?php echo htmlspecialchars($userinfo["s_zipcode"]); ?>">
	<input type=hidden name=scountry value="<?php echo htmlspecialchars($userinfo["s_country"]); ?>">
	<input type=hidden name=returnurl value="<?php echo $returnurl; ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>ePDQ</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
