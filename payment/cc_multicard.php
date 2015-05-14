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
# $Id: cc_multicard.php,v 1.19.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["Proceed"]) && isset($HTTP_POST_VARS["order_num"]) && isset($HTTP_POST_VARS["user1"]))
{
	require "./auth.php";

#  'total_us_amount' => '',
#  'order_num' => '703100.3278936',
#  'user1' => '6',
#  'Proceed' => 'CONTINUE to AdultPremiumContent.com',

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["user1"]."'");

	$bill_output["code"] = 1;
	$bill_output["billmes"] = " OrderNumber: ".$HTTP_POST_VARS["order_num"];

	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$_orderids = $module_params ["param04"].join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");
?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://secure.multicards.com/cgi-bin/order2/processorder1.pl" method=POST name=process>
  
    <input type=hidden name=user1 value="<?php echo htmlspecialchars($_orderids); ?>">
    <input type=hidden name=mer_id value="<?php echo htmlspecialchars($module_params["param01"]); ?>">
	<input type=hidden name=mer_url_idx value="<?php echo htmlspecialchars($module_params["param02"]); ?>">
	<input type=hidden name=cust_name value="<?php echo htmlspecialchars($bill_name); ?>">
	<input type=hidden name=cust_email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=cust_phone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=cust_address1 value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=cust_zip  value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=cust_city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=cust_state value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=cust_country value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=cust_city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=pay_method value="creditcard">
	<input type=hidden name=agree2terms  value="1">
	<input type=hidden name=num_items value="1">
	<input type=hidden name=item1_desc value="Shopping cart">
	<input type=hidden name=item1_price value="<?php echo $cart["total_cost"]; ?>">
	<input type=hidden name=item1_qty value="1">
	<input type=hidden name=langcode value="<?php echo htmlspecialchars($module_params["param03"]); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>MultiCards</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php 
}
	exit;
?>

