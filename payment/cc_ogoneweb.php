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
# $Id: cc_ogoneweb.php,v 1.14 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["oid"])
{
	require "./auth.php";

    $skey = $oid;
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

require($xcart_dir."/payment/sha1.php");

$pp_merch = $module_params["param01"];
$pp_secret = $module_params["param03"];
$pp_curr = $module_params["param04"];
$pp_test = ($module_params["testmode"]=='Y') ? "https://secure.ogone.com:443/ncol/test/orderstandard.asp" : "https://secure.ogone.com:443/ncol/prod/orderstandard.asp";
$ordr = $module_params["param06"].join("-",$secure_oid);


if(!$duplicate)
	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

$l = array(
	"US" => "en_US",
	"FR" => "fr_FR",
	"NL" => "nl_NL",
	"IT" => "it_IT",
	"DE" => "de_DE",
	"ES" => "es_ES",
	"NO" => "no_NO"
);

$post = "";
$post["PSPID"] = $pp_merch;
$post["orderID"] = $ordr;
$post["amount"] = (100*$cart["total_cost"]);
$post["currency"] = $pp_curr;
$post["EMAIL"] = $userinfo["email"];
$post["Owneraddress"] = $userinfo["b_address"];
$post["OwnerZip"] = $userinfo["b_zipcode"];
$post["language"] = ($l[$store_language] ? $l[$store_language] : "en_US");
#SHA-1(OrderId + Amount + Currency + PSPID + additional string)
$post["SHASign"] = sha1($ordr.(100*$cart["total_cost"]).$pp_curr.$pp_merch.$pp_secret);

$post["accepturl"] = $post["declineurl"] = $post["exceptionurl"] = $post["cancelurl"] = $http_location."/payment/cc_ogoneweb.php?oid=".$ordr;

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $pp_test; ?>" method=POST name=process>
<?php foreach($post as $k => $v)print "<input type=hidden name=".$k." value=\"".$v."\">\n"; ?>
<input type=submit>
        </form>
        <table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>Ogone</b> payment gateway...</td></tr>
        </table>
 </body>
</html>
<?php
}
exit;

?>

