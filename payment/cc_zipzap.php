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
# $Id: cc_zipzap.php,v 1.10 2006/01/11 06:56:23 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$zipzap_login = $module_params["param01"];
$zipzap_prefix = $module_params["param02"];
$zipzap_curr = $module_params["param03"];

$post = "";
$post[] = "CARDNUM=".$userinfo["card_number"];
$post[] = "EXPIRY=".substr($userinfo["card_expire"],2,2).substr($userinfo["card_expire"],0,2);
$post[] = "AMOUNT=".$cart["total_cost"];
$post[] = "CURRENCY=".$zipzap_curr;
$post[] = "TYPE=P";
$post[] = "TEMPLATE=9999";
$post[] = "ORDERNO=".$zipzap_prefix.join("-",$secure_oid);
$post[] = "EMAIL=".$userinfo["email"];
$post[] = "OPS_ID=".$zipzap_login;

list($a,$return) = func_https_request("POST","https://zipzap.zipzap.co.nz:443/servlets/zipzap",$post);

#print $return;
#  0        1                                                              2     3       4    5
#Declined|Time Out - no response was received within the time out period|31.32|xcart544|11190|USD
$resp = split("\|",$return);

$bill_output["code"] = ($resp[0]=="Approved") ? 1 : 2;
$bill_output["billmes"] = $resp[0].": ".$resp[1]." (SeqNumber: ".$resp[4].")";

#print_r($bill_output);
#exit;

?>
