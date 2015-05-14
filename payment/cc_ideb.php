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
# $Id: cc_ideb.php,v 1.7 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

$vs_path_client = $module_params["param01"];
$vs_path_init = $module_params["param02"];
$vs_prj = $module_params["param03"];
$vs_curr = $module_params["param04"];
$vs_prefix = $module_params["param05"];

if(!file_exists($vs_path_client) || !file_exists($vs_path_init))
{	func_header_location($xcart_catalogs['customer']."/error_message.php?error_ccprocessor_notfound");exit;}

$post = "";
$post[] = "Project_Id=".$vs_prj;
$post[] = "Card_Expiry_Year=".substr($userinfo["card_expire"],2,2);
$post[] = "Card_Expiry_Month=".substr($userinfo["card_expire"],0,2);
$post[] = "Card_Number=".$userinfo["card_number"];
$post[] = "Name_On_Card=".$userinfo["card_name"];
$post[] = "Security_Code=".$userinfo["card_cvv2"];
$post[] = "Transaction_Amount=".(100*$cart["total_cost"]);
$post[] = "VAT_Amount=0";
$post[] = "Currency_Code=".$vs_curr;
$post[] = "Account=True";
$post[] = "LocalIpAddress=".$REMOTE_ADDR;
$post[] = "Connection_Test=False";
$post[] = "Invoice_Name=";
$post[] = "Invoice_Location=";
$post[] = "Transaction_Info=".$vs_prefix.join("-",$secure_oid);

# Execute iDeb client
$tmpfile=tempnam("../templates_c","ideb");
$fp = popen($vs_path_client." -f ".$vs_path_init." - > ".$tmpfile,"w"); fputs($fp,join("\n",$post)); pclose($fp);
$return = file("../templates_c/".$tmpfile);unlink("../templates_c/".$tmpfile);
preg_match("/^(..)(.*)$/",$return[0],$ret);

$bill_output["code"] = (($ret[1] == "AA") ? 1 : 2);
$bill_output["billmes"] = $ret[2];

#print "<pre>";print_r($bill_output);exit;

?>
