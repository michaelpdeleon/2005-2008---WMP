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
# $Id: cc_intellipaycom.php,v 1.18.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$staerr = array(
	"A" => "Approved. The purchase has been authorized by the issuer. ",
	"S" => "Same. The DTS has detected a possible duplicate transaction.",
	"D" => "Declined. The authorizer has declined the purchase request. ",
	"R" => "Referred. The issuer has asked that you call the Voice Authorization center. ",
	"X" => "Expired. The card has expired. ",
	"E" => "Error. A data entry error of some kind has occurred. ",
	"U" => "Unknown. An unknown processor or issuer error has occurred. ",
	"F" => "Failure. A system failure of some kind has occurred. "
);

$avserr = array(
	"A" => " Address match only ",
	"W" => " 9-digit zip match only ",
	"X" => " Address & 9-digit zip match ",
	"Y" => " Address & 5-digit zip match ",
	"Z" => " 5-digit zip match only ",
	"N" => " No match on address or zip ",
	"U" => " Domestic address verification unavailable ",
	"G" => " Global (international) non-avs participant ",
	"R" => " Retry (system unavailable) ",
	"S" => " Service not supported by issuer ",
	"E" => " Error; service not available for this transaction "
);

$pp_merch = $module_params["param01"];
$pp_pass = $module_params["param02"];
$prefix = $module_params["param03"];

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="VI"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MC"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="AX"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="AX"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="DI"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="DI"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="DI"; # Diners
if($first4==6011)$userinfo["card_type"]="NO"; # Discover

$userinfo["phone"] = preg_replace("/[^0-9]/","",$userinfo["phone"]);

$post = "";
$post[] = "ADDRESS=".$userinfo["b_address"];
$post[] = "AMOUNT=".$cart["total_cost"];
$post[] = "CARDNUM=".$userinfo["card_number"];
$post[] = "CITY=".$userinfo["b_city"];
$post[] = "COUNTRY=".(($userinfo["b_country"]=="US")? "US" : $userinfo["b_countryname"]);
$post[] = "CUSTID=".$userinfo["login"];
$post[] = "DELIMCHARACTER=,";
$post[] = "DUPECHECK=Y";
$post[] = "EMAIL=".$userinfo["email"];
$post[] = "EXPDATE=".$userinfo["card_expire"];
$post[] = "INVOICE=".$prefix.join("-",$secure_oid);
$post[] = "LOGIN=".$pp_merch;
$post[] = "METHOD=".$userinfo["card_type"];
$post[] = "NAME=".$bill_name;
$post[] = "PASSWORD=".$pp_pass;
$post[] = "PHONE=".$userinfo["phone"];
$post[] = "RECEIPTFORMAT=NamedValueList";
$post[] = "REJECTAVSMISMATCH=".$module_params["param04"];
$post[] = "STATE=".$userinfo["b_state"];
$post[] = "TYPE=NA";
$post[] = "ZIP=".$userinfo["b_zipcode"];

list($a,$return)=func_https_request("POST","https://www.intellipay.net:443/LinkSmart/",$post);
$return.= "\n";

#RESPONSECODE=A
#AUTHCODE=000001
#DECLINEREASON=
#AVSDATA=Z
#TRANSID=C00 3517624

#RESPONSECODE=E
#AUTHCODE=
#DECLINEREASON,1,TAG=PHONE
#DECLINEREASON,1,ERRORCLASS=field.format.nonNumeric
#DECLINEREASON,1,PARAM1=+
#DECLINEREASON,1,PARAM2=
#DECLINEREASON,1,MESSAGE=Error in Field: Phone, Number contains characters that are not digits.
#AVSDATA=
#TRANSID=

preg_match("/RESPONSECODE=(.)\n/U",$return,$out);

if($out[1] == "A")
{
	$bill_output["code"] = 1;
	if(preg_match("/AUTHCODE=(.+)\n/U",$return,$out))
		$bill_output["billmes"].= " (AuthCode: ".$out[1].")";
	if(preg_match("/TRANSID=(.+)\n/U",$return,$out))
		$bill_output["billmes"].= " (TransID: ".$out[1].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = (empty($staerr[$out[1]]) ? "ResponseCode: ".$out[1] : $staerr[$out[1]]);
	$a = split("\n",$return);
	foreach($a as $line)
		if(preg_match("/DECLINEREASON,\d+,MESSAGE=(.*)$/U",$line,$out))
			$bill_output["billmes"].= " ".$out[1];
}

$bill_output["cvvmes"].= "Not support";
if(preg_match("/AVSDATA=(.)\n/U",$return,$out))
	$bill_output["avsmes"] = (empty($avserr[$out[1]]) ? "AVS Code: ".$out[1] : $avserr[$out[1]]);

#print_r($bill_output);
#exit;

?>
