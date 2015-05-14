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
# $Id: cc_securepay.php,v 1.8 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$avserr = array(
    "A" => "Address (Street) matches, Zip does not.",
    "E" => "AVS Error",
    "G" => "Issuing bank does not subscribe to the AVS system.",
    "N" => "no match on Address or Zip Code.",
    "R" => "Retry, system unavailable or timed out.",
    "S" => "Service not supported by issuer.",
    "U" => "Address information unavailable.",
    "W" => "9 digit Zip matches, Address does not.",
    "X" => "Exact AVS match.",
    "Y" => "Address and 5 digit zip code match.",
    "Z" => "5 digit Zip Code matches, Address does not."
);

$an_login = $module_params["param01"];
$an_prefix = $module_params["param02"];

$post[] = "Amount=".$cart["total_cost"];
$post[] = "Merch_ID=".$an_login;
$post[] = "Email=".$userinfo["email"];
$post[] = "Tr_Type=SALE";
$post[] = "CC_Method=DataEntry";
$post[] = "Name=".$userinfo["card_name"];
$post[] = "CC_NUMBER=".$userinfo["card_number"];
$post[] = "Month=".substr($userinfo["card_expire"],0,2);
$post[] = "Year=".substr($userinfo["card_expire"],2,2);
$post[] = "Street=".$userinfo["b_address"];
$post[] = "City=".$userinfo["b_city"];
$post[] = "State=".$userinfo["b_state"];
$post[] = "Zip=".$userinfo["b_zipcode"];
$post[] = "AVSREQ=1";

#print "<pre>";
#print_r($post);

list($a,$return) = func_https_request("POST","https://www.securepay.com:443/secure1/index.asp",$post);

# N,Not Approved,Error %3D204%2DInvalid Bank Number,Not Available,001,END
list($Return_Code,$Approv_Num,$Card_Response,$AVS_Response,$VoidRecNum,$temp)=split(",",$return);

	$bill_output["code"] = (($Return_Code=="Y" && $Approv_Num!="Not Approved") ? 1 : 2);

	if(!empty($Card_Response))
		$bill_output["billmes"] = urldecode($Card_Response);
	if($bill_output["code"]==1)
		$bill_output["billmes"].= " (Approval Code: ".$Approv_Num.")";
	if(!empty($VoidRecNum))
		$bill_output["billmes"].= " (VoidRecNum: ".$VoidRecNum.")";

	if($AVS_Response)
		$bill_output["avsmes"] = (!empty($avserr[$AVS_Response]) ? $avserr[$AVS_Response] : "AVS Response Code: ".$AVS_Response);

#print $return."<br />";
#print_r($bill_output);
#exit;

?>
