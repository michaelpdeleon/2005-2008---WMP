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
# $Id: cc_easypay.php,v 1.16 2006/01/11 06:56:22 mclap Exp $
#

$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD =="GET" && !empty($HTTP_GET_VARS["TM_RefNo"]))
{
#  'TM_MCode' => '10820031988',
#  'TM_RefNo' => '111-5',
#  'TM_Currency' => 'USD',
#  'TM_DebitAmt' => '1979.00',
#  'TM_Status' => 'YES',
#  'TM_ErrorMsg' => '',
#  'TM_PaymentType' => '3',
#  'TM_ApprovalCode' => '888888',

	require "./auth.php";

$paytyp = array(
	"3" => "VISA",
	"2" => "MAster Card",
	"23" => "JSB",
	"5" => "AMEX",
	"40" => "eNETS",
	"41" => "iQB"
);

	list($ordr,$a) = split("-",$TM_RefNo);
	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ordr."'");

	if($TM_Status=="YES")
	{
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "ApprovalCode: ".$TM_ApprovalCode;
	}
	else
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = $TM_ErrorMsg;
	}

	$bill_output["billmes"].=" (RefNo: $TM_RefNo)";
	$bill_output["billmes"].=" (PaymentType: ".(($paytyp[$TM_PaymentType])?($paytyp[$TM_PaymentType]):($TM_PaymentType)).")";

	$skey = $ordr;
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");

}
elseif ($REQUEST_METHOD == "GET" && !empty($HTTP_GET_VARS["oid"]))
{

	require "./auth.php";

	$skey = $oid;
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pay_login = $module_params["param01"];
	$pay_curr = $module_params["param02"];
	$pay_test = ($module_params["testmode"]=="Y")?"http://gw02.telemoney.com.sg":"https://cart.telemoneyworld.com";
	$pay_prefix = $module_params["param04"];

	$ordr = $pay_prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $pay_test; ?>/~project/easypay/integrate.php" method=POST name=process>
	 <input type=hidden name=mid value="<?php echo $pay_login;?>">
	 <input type=hidden name=ref value="<?php echo $ordr; ?>">
	 <input type=hidden name=amt value="<?php echo $cart["total_cost"]; ?>">
	 <input type=hidden name=cur value="<?php echo $pay_curr; ?>">
	 <input type=hidden name=returnurl value="http://<?php echo $http_location; ?>/payment/cc_easypay.php?oid=<?php echo $ordr; ?>">
	 <input type=hidden name=statusurl value="http://<?php echo $http_location; ?>/payment/cc_easypay.php">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>EasyPay</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
