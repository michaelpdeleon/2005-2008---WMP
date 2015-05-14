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
# $Id: cc_ccash.php,v 1.16 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('files');

@set_time_limit(100);

$prefix = $module_params["param01"];

if (!file_exists($xcart_dir."/payment/ccash.pl")) {
	func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound");
	exit;
}

$first4 = 0+substr($userinfo["card_number"],0,4);$userinfo["card_type"]="";
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="vs"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="mc"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="ax"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="ax"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="dc"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="dc"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="dc"; # Diners
if($first4==6011)$userinfo["card_type"]="ds"; # Discover
if($first4>=3528 && $first4<=3589)$userinfo["card_type"]="jb"; # JCB
if(!$userinfo["card_type"])$userinfo["card_type"]="ot";

# Execute perl script

$post = "";
$post[] = "order-id=".$prefix.join("-",$secure_oid);
$post[] = "card-number=".$userinfo["card_number"];
$post[] = "card-exp=".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2);
$post[] = "card-cic=".$userinfo["card_cvv2"];
$post[] = "amount=usd ".$cart["total_cost"];
$post[] = "card-name=".$userinfo["card_name"];
$post[] = "card-address=".$userinfo["b_address"];
$post[] = "card-city=".$userinfo["b_city"];
$post[] = "card-state=".$userinfo["b_state"];
$post[] = "card-zip=".$userinfo["b_zipcode"];
$post[] = "card-country=".$userinfo["b_country"];
$post[] = "card-type=".$userinfo["card_type"];

foreach($post as $i => $line)
{
	list($a,$b) = split("=",$line,2);
	$post[$i] = $a."=".addslashes($b);
}

$fnerr = func_temp_store("");
$perlbin = func_find_executable("perl",$config["General"]["perl_binary"]);

@exec(func_shellquote($perlbin)." ".func_shellquote($xcart_dir."/payment/ccash.pl")." \"".join("\" \"",$post)."\" 2>".func_shellquote($fnerr), $bill_output);
@unlink($fnerr);

list($bill_output["code"],$avs,$bill_output["billmes"]) = explode(",",$bill_output[0],3);

$avserr = array(
	"A" => "Address matches, ZIP code does not.",
	"E" => "Ineligible transaction.",
	"G" => "Address information unavailable for an international card.",
	"N" => "Neither address nor ZIP code matches.",
	"R" => "Retry (system unavailable or timed out).",
	"S" => "Card type not supported.",
	"U" => "Address information unavailable for the transaction.",
	"W" => "Nine-digit ZIP code matches, address does not.",
	"X" => "Exact match (nine-digit ZIP code and address).",
	"Y" => "Address and five-digit ZIP code match.",
	"Z" => "Five-digit ZIP code matches, address does not."
);

if($avs)
	$bill_output["avsmes"] = (($avserr[$avs]) ? $avserr[$avs] : "AVS Code: ".$avs);

#print_r($bill_output);
#exit;
?>
