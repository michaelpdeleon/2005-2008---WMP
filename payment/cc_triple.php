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
# $Id: cc_triple.php,v 1.12.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$pp_merch = $module_params["param01"];
$pp_curr = $module_params["param02"];
$pp_lang = $module_params["param03"];
$pp_test = ($module_params["testmode"]=='Y') ? "test" : "www";
$pp_shift = $module_params["param05"];
$_orderids = join("-",$secure_oid);
$pp_days = $module_params["param07"];

if(!$duplicate)
        db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($pp_shift.$_orderids)."','".$XCARTSESSID."')");

$post = "";
$post[] = "command=new_payment_cluster";
$post[] = "merchant_name=".$pp_merch;
$post[] = "merchant_transaction_id=".$pp_shift.$_orderids;
$post[] = "description=".$config['Company']['company_name']." customer";
$post[] = "profile=".$module_params["param06"];
$post[] = "client_id=".$userinfo["login"];
$post[] = "price=".$cart["total_cost"];
$post[] = "cur_price=".$pp_curr;
$post[] = "client_email=".$userinfo["email"];
$post[] = "client_firstname=".$bill_firstname;
$post[] = "client_lastname=".$bill_lastname;
$post[] = "client_address=".$userinfo["b_address"];
$post[] = "client_zip=".$userinfo["b_zipcode"];
$post[] = "client_city=".$userinfo["b_city"];
$post[] = "client_language=".$pp_lang;
$post[] = "client_country=".$userinfo["b_country"];
$post[] = "days_pay_period=".$pp_days;

#print "<pre>";
#print_r($post);

list($a,$ret)=func_https_request("POST","https://".$pp_test.".tripledeal.com:443/ps/com.tripledeal.paymentservice.servlets.PaymentService",$post);

#print "<hr />".$a."<hr />".$return;
#exit;

#<new_payment_cluster>
#	<id value="350006357"/>
#</new_payment_cluster>
#
#<new_payment_cluster>
#	<errorlist>
#		<error msg="client_id_incorrect"/>
#	</errorlist>
#</new_payment_cluster>

preg_match("/<id value=\"(.+)\"\/>/U",$ret,$id);$clusterid = $id[1];
if(empty($clusterid))
{
	preg_match("/<error msg=\"(.+)\"\/>/U",$ret,$err);$err = $err[1];

	if(empty($err))
		$bill_output["code"]=0;
	else
	{
		$bill_output["code"]=2;
		$bill_output["billmes"] = $err;
	}
}
else
{

db_query("update ".$sql_tbl[cc_pp3_data]." set param1='".$clusterid."', param2='".$clusterkey."' where ref='".$pp_shift.$_orderids."'");

$post["command"]					= "show_payment_cluster";
$post["merchant_name"]				= $pp_merch;
$post["merchant_transaction_id"]	= $pp_shift.$_orderids;
$post["payment_cluster_id"]			= $clusterid;
$post["client_language"]			= $pp_lang;
$post["return_url_success"]=$post["return_url_canceled"]=$post["return_url_pending"]=$post["return_url_error"] = "http://".$xcart_http_host.$xcart_web_dir."/payment/cc_triple_return.php?ordr=".$pp_shift.$_orderids;

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo "https://".$pp_test.".tripledeal.com:443/ps/com.tripledeal.paymentservice.servlets.PaymentService"; ?>" method=POST name=process>
<?php
        if ($post)
        foreach($post as $k=>$v)
         print "<input type=hidden name='$k' value='$v'>\n";
?>
        </form>
        <table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>Triple Deal</b> payment gateway...</td></tr>
        </table>
 </body>
</html>
<?php
exit;

}

?>
