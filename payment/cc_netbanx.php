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
# $Id: cc_netbanx.php,v 1.19.2.1 2006/06/21 12:08:13 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["oid"]))
{
	require "./auth.php";

#[oid] => xcart1
#[amount] => 10.01
#[netbanx_reference] => 1234567890ABCDEFGH

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");
	if(!empty($HTTP_POST_VARS["netbanx_reference"]))
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "NetBanx Reference: ".$HTTP_POST_VARS["netbanx_reference"];
	}
	else
	{
		$bill_output["code"] = 2;
	}
	
	$weblink=2;
	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
		if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

		$pp_merch = $module_params["param01"];
		$pp_url = $module_params["param02"];
		$ordr = $module_params["param03"].join("-",$secure_oid);
		if(!$duplicate)
			db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<body onLoad="document.process.submit();">
<form action="<?php echo $pp_url; ?>" method="POST" name="process">
<input type="hidden" name="oid" value="<?php echo htmlspecialchars($ordr); ?>">
<input type="hidden" name="merchantID" value="<?php echo htmlspecialchars($pp_merch); ?>">
<input type="hidden" name="amount" value="<?php echo $cart["total_cost"]; ?>">
<input type="hidden" name="curcode" value="<?php echo htmlspecialchars($module_params["param04"]); ?>">
</form>
<table width="100%" height="100%">
<tr>
	<td><img src="<?php echo $smarty->get_template_vars('ImagesDir'); ?>/netbanxlogo.gif" border="0" width="125" height="63" alt="" /></td>
</tr>
<tr>
	<td align="center" valign="middle">Please wait while connecting to <b>NetBanx</b> payment gateway...</td>
</tr>
</table>
</body>
</html>
<?php
}
exit;

?>
