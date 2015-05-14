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
# $Id: cc_usaepay.php,v 1.17.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_merch = $module_params["param01"];

$post = "";
$post[] = "UMkey=".$pp_merch;
if($module_params["testmode"]!="N")
	$post[] = "UMtestmode=1";
$post[] = "UMip=".$REMOTE_ADDR;
$post[] = "UMcommand=sale";
$post[] = "UMinvoice=".$module_params["param03"].join("-",$secure_oid);;
$post[] = "UMname=".$userinfo["card_name"];
$post[] = "UMstreet=".$userinfo["b_address"];
$post[] = "UMzip=".$userinfo["b_zipcode"];
$post[] = "UMbillname=".$bill_name;
$post[] = "UMbillcompany=".$userinfo["company"];
$post[] = "UMbillstreet=".$userinfo["b_address"];
$post[] = "UMbillcity=".$userinfo["b_city"];
$post[] = "UMbillstate=".$userinfo["b_state"];
$post[] = "UMbillzip=".$userinfo["b_zipcode"];
$post[] = "UMbillcountry=".$userinfo["b_country"];
$post[] = "UMbillphone=".$userinfo["phone"];
$post[] = "UMbillemail=".$userinfo["email"];
$post[] = "UMamount=".$cart["total_cost"];
$post[] = "UMcard=".$userinfo["card_number"];
$post[] = "UMexpir=".$userinfo["card_expire"];
$post[] = "UMcvv2=".$userinfo["card_cvv2"];
$post[] = "UMshipstreet=".$userinfo["s_address"];
$post[] = "UMshipcity=".$userinfo["s_city"];
$post[] = "UMshipcountry=".$userinfo["b_country"];
$post[] = "UMshipfname=".$userinfo["s_firstname"];
$post[] = "UMshiplname=".$userinfo["s_lastname"];
$post[] = "UMshipphone=".$userinfo["phone"];
$post[] = "UMshipzip=".$userinfo["s_zipcode"];
$post[] = "UMshipstate=".$userinfo["s_state"];

if(isset($cmpi_result)) {
	$post[] = "UMcardauth=true";
	$post[] = "UMxid=".$cmpi_result['Xid'];
	$post[] = "UMcavv=".$cmpi_result['Cavv'];
	$post[] = "UMeci=".intval($cmpi_result['EciFlag']);
}

list($a,$return)=func_https_request("POST","https://www.usaepay.com:443/gate.php",$post);
$return = $return."&";

#UMversion=2.3&UMstatus=Approved&UMauthCode=00000&UMrefNum=0&UMavsResult=Address%3A%20No%20Match%20%26%205%20Digit%20Zip%3A%20Match&UMcvv2Result=n%2Fa&UMresult=A&UMerror=&UMfiller=filled
#UMversion=2.3&UMstatus=Error&UMauthCode=000000&UMerror=Card%20Number%20was%20not%20between%2013%20and%2016%20digits&UMresult=E&UMfiller=filled

if(preg_match("/UMresult=A/",$return))
{
	$bill_output["code"] = 1;
	if(preg_match("/UMrefnum=(.*)&/U",$return,$out))
		$bill_output["billmes"] ="(".urldecode($out[1]).")";
}
else
{
	$bill_output["code"] = 2;
	if(preg_match("/UMerror=(.*)&/U",$return,$out))
		$bill_output["billmes"] =urldecode($out[1]);
}

if(preg_match("/UMavsResult=(.*)&/U",$return,$out))
	$bill_output["avsmes"] = urldecode($out[1]);

if(preg_match("/UMcvv2Result=(.*)&/U",$return,$out))
	$bill_output["cvvmes"].= urldecode($out[1]);

?>
