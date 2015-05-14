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
# $Id: cc_eztp.php,v 1.5 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$pp_login = $module_params["param01"];
$pp_pass = $module_params["param02"];
$pp_curr = $module_params["param03"];
$pp_prefix = $module_params["param08"];

$post = "";
$post[] = "username=".$pp_login;
$post[] = "password=".$pp_pass;
$post[] = "format=csv";
$post[] = "cardholdername=".$userinfo["card_name"];
$post[] = "cardnumber=".$userinfo["card_number"];
$post[] = "cvv=".$userinfo["card_cvv2"];
$post[] = "expmonth=".substr($userinfo["card_expire"],0,2);
$post[] = "expyear=".substr($userinfo["card_expire"],2,2);
$post[] = "amount=".$cart["total_cost"];
$post[] = "address=".$userinfo["b_address"];
$post[] = "city=".$userinfo["b_city"];
$post[] = "state=".$userinfo["b_state"];
$post[] = "country=".$userinfo["b_country"];
$post[] = "zip=".$userinfo["b_zipcode"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "email=".$userinfo["email"];
$post[] = "reference=".$pp_prefix.join("-",$secure_oid);
$post[] = "transtype=sale";
$post[] = "ipaddress=".$REMOTE_ADDR;

list($a,$return) = func_https_request("POST","https://secure.eznp.com:443/eznp/transaction.php",$post);

$ret = split("\",\"","\",".$return.",\"");

if($ret[1]=="A" && $ret[2]=="000")
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = $ret[3];
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $ret[3]." (Code: ".$ret[2].")";
}

if(($ret[4]))
	$bill_output["billmes"].= " (TransID: ".$ret[4].")";


#print "<pre>";print_r($ret);print_r($bill_output); exit;

?>
