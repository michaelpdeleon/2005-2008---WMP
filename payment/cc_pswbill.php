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
# $Id: cc_pswbill.php,v 1.15.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && $HTTP_GET_VARS["TransactionID"] && $HTTP_GET_VARS["User1"])
{
	require "./auth.php";

	$skey = $HTTP_GET_VARS["User1"];
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{
		if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

        $merchant = $module_params ["param01"];
        $productcode = $module_params ["param02"];
		$ordr = $module_params ["param09"].join("-",$secure_oid);

		if(!$duplicate)
			db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://secure.pswbilling.com/cgi-bin/form.cgi" method=GET name=process>

	<input type=hidden name=Amount value="<?php echo 100*$cart["total_cost"]; ?>">
	<input type=hidden name=Template value="checkout">
    <input type=hidden name=AccountID value="<?php echo $merchant; ?>">
    <input type=hidden name=FirstName value="<?php echo $bill_firstname; ?>">
    <input type=hidden name=LastName value="<?php echo $bill_lastname; ?>">
    <input type=hidden name=email value="<?php echo $userinfo["email"]; ?>">
    <input type=hidden name=Address1 value="<?php echo $userinfo["b_address"]; ?>">
    <input type=hidden name=City value="<?php echo $userinfo["b_city"]; ?>">
    <input type=hidden name=State value="<?php echo $userinfo["b_state"]; ?>">
    <input type=hidden name=Zip value="<?php echo $userinfo["b_zipcode"]; ?>">
    <input type=hidden name=IPAddress value="<?php echo $REMOTE_ADDR; ?>">
	<input type=hidden name=User1 value="<?php echo $ordr; ?>">
	<input type=hidden name=ProductCode value="<?php echo $productcode; ?>">
        </form>
        <table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>PSW Billing</b> payment gateway...</td></tr>
        </table>
 </body>
</html>
<?php
}
exit;

?>
