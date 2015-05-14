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
# $Id: cc_slim.php,v 1.10.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$an_clientid = $module_params["param01"];
$an_siteid = $module_params["param02"];
$an_priceid = $module_params["param03"];
$an_pass = $module_params["param05"];
$an_prefix = $module_params["param04"];

$post[] = "transtype=SALE";
$post[] = "first_name=".$bill_firstname;
$post[] = "last_name=".$bill_lastname;
$post[] = "address=".$userinfo["b_address"];
$post[] = "city=".$userinfo["b_city"];
$post[] = "state=".$userinfo["b_state"];
$post[] = "zip=".$userinfo["b_zipcode"];
$post[] = "country=".$userinfo["b_country"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "email=".$userinfo["email"];
$post[] = "cardnumber=".$userinfo["card_number"];
$post[] = "expmonth=".substr($userinfo["card_expire"],0,2);
$post[] = "expyear=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "cvv2=".$userinfo["card_cvv2"];
$post[] = "amount=".$cart["total_cost"];
$post[] = "clientid=".$an_clientid;
$post[] = "siteid=".$an_siteid;
$post[] = "priceid=".$an_priceid;
$post[] = "password=".$an_pass;
$post[] = "clientip=".$REMOTE_ADDR;
$post[] = "client_transref=".$an_prefix.join("-",$secure_oid);

#print "<pre>";print_r($post);

list($a,$return) = func_https_request("POST","https://stats.slimcd.com:443/soft/cd_terminal_v2.asp",$post);

#"AXXXXXX","GID","YYYYYY","ZZZZZZ"
#print $return."<br />";

preg_match("/^\"(.)(.*)\",\"(.*)\",\"(.*)\",\"(.*)\"\s*$/U",$return,$ret);

if($ret[1]=='Y')
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = " AuthCode: ".$ret[2];
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = ($ret[1]=='N' ? "Declined" : "Error").": ".$ret[2]." ";
}


$bill_output["billmes"] .= "(Gatewat Identifier Definition: ".$ret[3].") ";
$bill_output["billmes"] .= "(GateID: ".$ret[4].") ";
$bill_output["billmes"] .= "(Identifier for the Bank: ".$ret[5].")";

#print_r($ret);
#print_r($bill_output);
#exit;

?>
