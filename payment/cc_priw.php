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
# $Id: cc_priw.php,v 1.17 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];


if(!empty($HTTP_POST_VARS["RefNo"]) && !empty($HTTP_POST_VARS["TransID"]) && !empty($HTTP_POST_VARS["Auth"]))
{

require "./auth.php";

$avserr = array(
        "X" => "Exact match - 9 digit zip",
        "Y" => "Exact match - 5 digit zip",
        "A" => "Address match only",
        "W" => "9-digit zip match only",
        "Z" => "5-digit zip match only",
        "N" => "No address or zip match",
        "U" => "Address unavailable",
        "G" => "Non-U.S. Issuer",
        "R" => "Issuer system unavailable"
);

# [MerchantID] => 10011
# [TransID] => 2101395
# [TransType] => CC
# [RefNo] => x253
# [Auth] => 999999
# [AVSCode] => N
# [CVV2ResponseMsg] =>
# [Notes] => 

$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$RefNo."'");

if($Auth != "Declined")
{
	$bill_output["code"] = 1;
	$bill_output["billmes"].= "AuthCode: ".$Auth;
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $Auth.": ".$Notes;	
}

if($TransID)$bill_output["billmes"].= " (TransID: ".$TransID.")";
if($AVSCode)$bill_output["avsmes"] = empty($avserr[$AVSCode]) ? "AVS Code: ".$AVSCode : $avserr[$AVSCode];

require($xcart_dir."/payment/payment_ccend.php");

}
else
{
if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$ordr = $module_params["param03"].join("-",$secure_oid);
if(!$duplicate)
	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");


$post = array(
	"MerchantID"	=> $module_params["param01"],
	"RegKey"		=> $module_params["param02"],
	"Amount"		=> $cart["total_cost"],
	"AVSADDR"		=> $userinfo["b_address"],
	"AVSZIP"		=> $userinfo["b_zipcode"],
	"REFID"			=> $ordr,
	"RURL"			=> $http_location."/payment/cc_priw.php",
	"TransType"		=> "CC"
);

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://webservices.primerchants.com/billing/TransactionCentral/EnterTransaction.asp" method=POST name=process>
<?php
        if ($post)      
        foreach($post as $k=>$v)
         print "<input type=hidden name=\"".htmlspecialchars($k)."\" value=\"".htmlspecialchars($v)."\">\n";
?>
        </form>
        <table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>PRI</b> payment gateway...</td></tr>
        </table>
 </body>
</html>
<?php
exit;



}

?>
