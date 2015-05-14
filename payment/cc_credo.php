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
# $Id: cc_credo.php,v 1.8 2006/01/11 06:56:22 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$errsta = array(
	"0000" => "Transaction received and approved.",
	"0001" => "Host unattainable.",
	"0010" => "Not valid Amount. The amount is too great.",
	"0012" => "Transaction ID Invalid.",
	"0013" => "Invalid Terminal.",
	"0014" => "Invalid Comerce.",
	"0017" => "System Malfunction.",
	"0018" => "Unsupported Transaccion.",
	"0020" => "Invalid Card.",
	"0021" => "Expired Card.",
	"9999" => "Received and declined Transaccin."
);

$vs_path = $module_params["param01"];
$vs_prx = $module_params["param02"];
$vs_cvc = $module_params["param03"];
$vs_avs = $module_params["param04"];

$post = "";
$post[] = "aut";
$post[] = $userinfo["card_number"];
$post[] = substr($userinfo["card_expire"],0,2);
$post[] = substr($userinfo["card_expire"],2,2);
$post[] = 100*$cart["total_cost"];
$post[] = func_shellquote($vs_prx.join("-",$secure_oid));
if($vs_cvc=="Y")
	$post[] = "-C".$userinfo["card_cvv2"];
if($vs_avs=="Y")
	$post[] = "-A".func_shellquote($userinfo["b_address"]." ".$userinfo["b_zipcode"]);

exec("cd ".func_shellquote($vs_path)." 2>&1;./newcli ".join(" ",$post)." 2>&1",$out); #aut 5412345678401234 12 05 25999 

list($code,$transid)=split("-",$out[0]); #0000-901234567890

$bill_output["code"] = ($code == "0000") ? 1 : 2;
$bill_output["billmes"] = ($errsta[$code] ? $errsta[$code] : join("",$out));
$bill_output["billmes"].= " (TransID: ".$transid.")";

?>
