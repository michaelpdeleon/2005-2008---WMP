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
# $Id: cc_psclient.php,v 1.11.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$vs_binary = $module_params["param01"];
$vs_curr = $module_params["param02"];

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="V"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="E"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="A"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="A"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="D"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="D"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="D"; # Diners

$post = "";
$post[] = "order_id=".$secure_oid[0];
$post[] = "amount=".$cart["total_cost"];
$post[] = "currency=".$vs_curr;
$post[] = "ttype=S";
$post[] = "Card_number=".$userinfo["card_number"];
$post[] = "Card_cvv2=".$userinfo["card_cvv2"];
$post[] = "Card_exp=".$userinfo["card_expire"];
$post[] = "Card_name=".$userinfo["card_name"];
$post[] = "Card_corporate=0";
$post[] = "Card_type=".$userinfo["card_type"];
$post[] = "first_name=".$bill_firstname;
$post[] = "last_name=".$bill_lastname;
$post[] = "Phone=".$userinfo["phone"];
$post[] = "Email=".$userinfo["email"];
$post[] = "customer_id=".$userinfo["login"];
$post[] = "Street=".$userinfo["b_address"];
$post[] = "country_code=".$userinfo["b_country"];
$post[] = "zip_code=".$userinfo["b_zipcode"];
$post[] = "City=".$userinfo["b_city"];
$post[] = "State=".$userinfo["b_state_text"];

$request = "";
foreach($post as $k=>$v)
{
	list($a,$b)=split("=",trim($v),2);
	$request.=" -".$a."=\"".addslashes($b)."\"";
}

#print "Binary execute: ".$vs_binary.$request."<hr />";

# Execute perl script
@exec($vs_binary.$request." 2>&1",$ret);

$return = $ret[0];

# Check result
list($code,$response) = split(":",$return,2);

if($code == "200")
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = "(TID: ".$response.")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $response." (Code: ".$code.")";
}

#print "<pre>";
#print_r($bill_output);
#print $return;
#exit;

?>
