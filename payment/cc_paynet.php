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
# $Id: cc_paynet.php,v 1.13 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_payid = $module_params["param01"];
$pp_curr = $module_params["param02"];
$pp_cert = $module_params["param04"];

$post = "";
$post[] = "type=encode";
$post[] = "agrnr=".$pp_payid;
$post[] = "ccnr=".$userinfo["card_number"];
$post[] = "ccex=".$userinfo["card_expire"];
$post[] = "ccode=".$userinfo["card_cvv2"];


list($a,$return)=func_https_request("POST","https://www.paynet.no:8210/if2",$post,"&","","application/x-www-form-urlencoded","",$pp_cert,$pp_cert);
#print "<pre><hr color=red>".$a."<hr />".$return;

$a = urldecode(trim($return));
preg_match("/cced=([^&]*)/",$a,$b);
$pp_cced = $b[1];
preg_match("/errm=([^&]*)/",$a,$b);preg_match("/errh=([^&]*)/",$a,$c);
$error_ = $b[1].($c[1] ? ": ".$c[1] : "");

if(!$error_)
{
	$post = "";
	$post[] = "type=sale";
	$post[] = "totam=".$cart["total_cost"];
	$post[] = "curry=".$pp_curr;
	$post[] = "agrnr=".$pp_payid;
	$post[] = "utref=".$module_params["param03"].join("-",$secure_oid);
	$post[] = "cced=".$pp_cced;
	list($a,$return)=func_https_request("POST","https://www.paynet.no:8210/if2",$post,"&","","application/x-www-form-urlencoded","",$pp_cert,$pp_cert);
	#print "<pre><hr color=green>".$a."<hr />".$return;

	$a = urldecode(trim($return));
	preg_match("/[^u]tref=([^&]*)/",$a,$b);
	$pp_tref = $b[1];
	preg_match("/errm=([^&]*)/",$a,$b);preg_match("/errh=([^&]*)/",$a,$c);
	$error_ = $b[1].($c[1] ? ": ".$c[1] : "");
	if(preg_match("/authnr=([^&]+)/",$a,$b))
		$pp_authnr = $b[1];

}

if(!$error_ && $pp_tref)
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = "(TRef: ".$pp_tref.") (AuthNr: ".$pp_authnr.")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $error_;
}


$bill_output["avsmes"] = "Not support";
$bill_output["cvvmes"].= "Not support";

#print_r($bill_output);
#exit;

?>
