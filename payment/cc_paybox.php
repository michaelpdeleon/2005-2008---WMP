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
# $Id: cc_paybox.php,v 1.14 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('files');
$paybox_script = $xcart_dir."/payment/bin/paybox.cgi";

putenv ("CONTENT_LENGTH=0");
putenv ("QUERY_STRING=0");
$ibs_site = $module_params["param01"];
$ibs_rang = $module_params["param02"];
$ibs_cmd = $module_params["param03"].join("-",$secure_oid);
$ibs_devise = $module_params["param04"];
$ibs_annule = $http_location."/payment/cc_paybox_result.php";
$ibs_effectue = $http_location."/payment/cc_paybox_result.php";
$ibs_refuse = $http_location."/payment/cc_paybox_result.php";
$ibs_paybox = "https://www.paybox.com/run/paiement3.cgi";
$ibs_retour = "montant:M;ref:R;numauto:A;transac:T";
$ibs_langue = $module_params["param05"];

if(!file_exists($paybox_script)) {
	func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound");
}

$fnerr = func_temp_store("");
$command_line = func_shellquote($paybox_script)." IBS_MODE=4 IBS_SITE=$ibs_site IBS_RANG=$ibs_rang IBS_TOTAL=".($cart["total_cost"]*100)." IBS_PORTEUR=".$userinfo["email"]." IBS_DATE=".date("d/m/Y H:i:s")." IBS_DEVISE=$ibs_devise IBS_CMD=$ibs_cmd IBS_LANGUE=$ibs_langue IBS_ANNULE=$ibs_annule IBS_EFFECTUE=$ibs_effectue IBS_REFUSE=$ibs_refuse IBS_PAYBOX=$ibs_paybox IBS_RETOUR='$ibs_retour' 2>".func_shellquote($fnerr);
@unlink($fnerr);

@exec($command_line, $output);

foreach($output as $k=>$v)
{
	if (eregi("PAYBOX INPUT ERROR \(code (.+)\)", $v, $pack))
	{
		$err = array(
"-1" => "Error in reading parameters via stdin (POST method). Error in HTTP reception",
"-2" => "Error of memory allocation. Not enough memory available on the e-commerce server",
"-3" => "Error in reading of parameters QUERY_STRING or CONTENT_LENGTH. HTTP error.",
"-4" => "IBS_RETOUR, IBS_ANNULE, IBS_REFUSE or IBS_EFFECTUE are too long (>150 characters)",
"-5" => "Error in opening file (if IBS_MODE contains 3). File non-existent, not found or access error",
"-6" => "Error in file format (if IBS_MODE contains 3). Badly constituted file, empty file or badly formatted line",
"-7" => "Obligatory variable missing (IBS_TOTAL,IBS_SITE,IBS_RANG,IBS_DEVISE,IBS_PORTEUR,IBS_CMD)",
"-8" => "One of the numerical variables contains a non-numerical character (amount,site,rank)",
"-9" => "IBS_SITE contains a site number which is not exactly made up of 7 characters.",
"-10" => "IBS_RANG contains a rank number which is not exactly made up of 2 characters.",
"-11" => "IBS_TOTAL has more than 10 or less than 3 numerical characters",
"-12" => "IBS_LANGUE or IBS_DEVISE contains a code which is not exactly made up of 3 characters",
"-13" => "IBS_CMD is empty / contains a reference whose length exceeds 256 characters.",
"-14" => "IBS_DEVISE is different from 250 (franc) and 978 (euro)",
"-15" => "IBS_LANGUE is different from FR, GBR and DEU",
"-16" => "IBS_PORTEUR does not contain a valid e-mail address"
);
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $err[$pack[1]];
		break;
	}
}

if($bill_output["code"] != 2)
{
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".$ibs_cmd."','".$XCARTSESSID."')");
	$output[0] = $output[1] = "";
	print join("",$output);
	exit();
}
?>
