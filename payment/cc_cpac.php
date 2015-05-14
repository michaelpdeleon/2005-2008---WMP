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
# $Id: cc_cpac.php,v 1.15 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && $HTTP_POST_VARS["idpedido"] && $HTTP_POST_VARS["huella"])
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["idpedido"]."'");
	$bill_output["code"] = 1;

	if(!empty($estado))			$bill_output["billmes"].= " (Estado: ".$estado.") ";
	if(!empty($diahora))		$bill_output["billmes"].= " (Diahora: ".$diahora.") ";
	if(!empty($label))			$bill_output["billmes"].= " (Label: ".$label.") ";

	$skey = $HTTP_POST_VARS["idpedido"];
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");
}
elseif($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["idpedido"])
{
	require "./auth.php";

	$skey = $HTTP_GET_VARS["idpedido"];
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{ 

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$_orderids = sprintf("%06d",$module_params ["param06"].$secure_oid[0]);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

$post = "";
$post[] = "tpv.jar";
$post[] = "tpv.cnf";
$post[] = "idpedido:".$_orderids;
$post[] = "importe:".$cart["total_cost"];
$post[] = "moneda:".$module_params ["param02"];
$post[] = "idioma:".$module_params ["param03"];

$run_me = "cd ".func_shellquote($module_params ["param01"]).";".func_shellquote($module_params ["param04"])." -cp tpv.jar CrURLtpv ".count($post)." ".join(" ",$post)." 2>&1; cd ".func_shellquote($xcart_dir);
@exec($run_me, $aaa);$return = $aaa[0];

?>
<html>
<body onLoad="javascript:self.location='<?php echo $return; ?>';">
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>LaCaixa</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
