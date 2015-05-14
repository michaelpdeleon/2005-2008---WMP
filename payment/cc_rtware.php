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
# $Id: cc_rtware.php,v 1.11.2.2 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$avserr = array(
	"A" => "Address (Street) matches, ZIP does not",
	"E" => "AVS error",
	"N" => "No Match on Address (Street) or ZIP",
	"P" => "AVS not applicable for this transaction",
	"R" => "Retry. System unavailable or timed out",
	"S" => "Service not supported by issuer",
	"U" => "Address information is unavailable",
	"W" => "9 digit ZIP matches, Address (Street) does not",
	"X" => "Exact AVS Match",
	"Y" => "Address (Street) and 5 digit ZIP match",
	"Z" => "5 digit ZIP matches, Address (Street) does not"
);

$cvverr = array(
	"M" => "Match",
	"N" => "No Match",
	"P" => "Not Processed",
	"S" => "Should have been present",
	"U" => "Issuer unable to process request"
);

$an_login = $module_params["param01"];
$an_password = $module_params["param02"];
$an_prefix = $module_params["param04"];
$an_curr = $module_params["param05"];

$post = array();
$post[] = "x_Login=".$an_login;
$post[] = "x_Tran_Key=".$an_password;
$post[] = "x_Version=3.1";
$post[] = "x_Test_Request=".($module_params["testmode"] == "N" ? "FALSE" : "TRUE");
$post[] = "x_Delim_Data=True";
$post[] = "x_Delim_Char=,";
$post[] = "x_Encap_Char=|";
$post[] = "x_ADC_URL=False";
$post[] = "x_First_Name=".$bill_firstname;
$post[] = "x_Last_Name=".$bill_lastname;
$post[] = "x_Address=".$userinfo["b_address"];
$post[] = "x_City=".$userinfo["b_city"];
$post[] = "x_State=".((!empty($userinfo["b_state"]))? $userinfo["b_state"] : "Non US");
$post[] = "x_Zip=".$userinfo["b_zipcode"];
$post[] = "x_Country=".$userinfo["b_country"];
$post[] = "x_Ship_To_First_Name=".$userinfo["s_firstname"];
$post[] = "x_Ship_To_Last_Name=".$userinfo["s_lastname"];
$post[] = "x_Ship_To_Address=".$userinfo["s_address"];
$post[] = "x_Ship_To_City=".$userinfo["s_city"];
$post[] = "x_Ship_To_State=".((!empty($userinfo["s_state"]))? $userinfo["s_state"] : "Non US");
$post[] = "x_Ship_To_Zip=".$userinfo["s_zipcode"];
$post[] = "x_Ship_To_Country=".$userinfo["s_country"];
$post[] = "x_Phone=".$userinfo["phone"];
$post[] = "x_Cust_ID=".$userinfo["login"];
$post[] = "x_Customer_IP=".$REMOTE_ADDR;
$post[] = "x_Email=".$userinfo["email"];
$post[] = "x_Merchant_Email=".$config["Company"]["orders_department"];
$post[] = "x_Invoice_Num=".$an_prefix.join("-",$secure_oid);
$post[] = "x_Amount=".$cart["total_cost"];
$post[] = "x_Currency_Code=".$an_curr;
$post[] = "x_Method=CC";
$post[] = "x_Type=auth_capture";
$post[] = "x_Card_Num=".$userinfo["card_number"];
$post[] = "x_Exp_Date=".$userinfo["card_expire"];
$post[] = "x_Card_Code=".$userinfo["card_cvv2"];

#print "<pre>";

list($a,$return) = func_https_request("POST","https://secure.rtware.net:443/gateway/transact.dll",$post);
$mass = split("\|,\|","|,".$return);

#|3|,|1|,|39|,|The supplied currency code is either invalid, not supported, not allowed for this merchant or doesn't have an exchange rate.|,|000000|,|P|,|0|,||,||,|26.56|,||,|auth_capture|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,|723CAF563B19FDC52ACDB6999AB876B7|,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||,||


#print $return;
#print_r($mass);

if($mass[1]==1)
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = " Approval Code: ".$mass[7];
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = ($mass[1]==2 ? "Declined" : "Error").": ";
	$bill_output["billmes"].= $mass[4]." (N ".$mass[3]." / Sub ".$mass[2].")";
}


if(!empty($mass[6]))
	$bill_output["avsmes"] = (empty($avserr[$mass[6]]) ? "Code: ".$mass[6] : $avserr[$mass[6]]);

if(!empty($mass[39]))
	$bill_output["cvvmes"] = (empty($cvverr[$mass[39]]) ? "Code: ".$mass[39] : $cvverr[$mass[39]]);

#print_r($bill_output);
#exit;

?>
