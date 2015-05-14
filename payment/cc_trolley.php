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
# $Id: cc_trolley.php,v 1.16.2.1 2006/06/15 10:10:49 max Exp $
#

if (!empty($HTTP_GET_VARS['COUL_KEY']) || !empty($HTTP_POST_VARS['COUL_KEY'])) {
	require "./auth.php";

    $secret = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_trolley.php'");

    if ($COUL_KEY != md5($secret.$COUL_TXNID.$TXN_ID."YES") && $COUL_KEY != md5($secret.$COUL_TXNID.$TXN_ID."NO")) die("Access denied"); 
    $bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$TXN_ID."'");

$err_arr = array(
"0" => "Success",
"1" => "Unrecognized content set",
"2" => "No Key Sent",
"3" => "Keys don't match",
"10" => "Amount and method requested are not currently supported.",
"11" => "Badly formatted COST",
"12" => "Badly formatted CURRENCY",
"100" => "Success, zero cost item",
"345" => "Failed"
); 

    $bill_output["code"] = ($COUL_RC == 0 || $COUL_RC == 100) ? 1 : 2;
	$bill_output["billmes"] = $err_arr[$COUL_RC]; 
    
    require($xcart_dir."/payment/payment_ccend.php");
}
else {
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }
    $sid = $module_params["param04"];
    $cost = $cart["total_cost"];
    $currency = $module_params["param05"];
    $returnurl = $http_location."/payment/cc_trolley.php"; 
    $txt_id = $module_params["param09"].join("-",$secure_oid);

    if(!$duplicate)
        db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($txt_id)."','".$XCARTSESSID."')");

    $secret = $module_params["param02"];
    $key = md5($cost.$currency.$txt_id.$secret);
?>

<html>
<body onLoad="document.process.submit();">
  <form action="https://ccard.ipbill.com/gateway.php" method="GET" name=process>
    <input type=hidden name=SID value="<?php echo $sid; ?>">
    <input type=hidden name=COST value="<?php echo $cost; ?>">
    <input type=hidden name=CURRENCY value="<?php echo $currency; ?>">
    <input type=hidden name=METHOD value="CC">
    <input type=hidden name=PASS value="<?php echo $returnurl; ?>">
    <input type=hidden name=FAIL value="<?php echo $returnurl; ?>">
    <input type=hidden name=TXN_ID value="<?php echo $txt_id; ?>">
    <input type=hidden name=KEY value="<?php echo $key; ?>">
    <input type=hidden name=auto_name value="<?php echo $bill_name; ?>">
    <input type=hidden name=auto_email value="<?php echo $userinfo["email"]; ?>">
    <input type=hidden name=auto_post value="<?php echo $userinfo["s_zipcode"]; ?>">
    </form>
    <table width=100% height=100%>
     <tr><td align=center valign=middle>Please wait while connecting to <b>Trolley</b> payment gateway...</td></tr>
    </table>
 </body>
</html>
<?php
}
exit;
?>
