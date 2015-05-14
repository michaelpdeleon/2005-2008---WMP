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
# $Id: cc_pp3.php,v 1.13 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$url = $module_params["param01"];
$id = $module_params["param02"];
$curr = $module_params["param03"];
# currency code which mantisse equal zero, else two
$mantisse2=array("0056"=>"1","0300"=>"1","0380"=>"1","0442"=>"1","0724"=>"1","0392"=>"1");

$ref=$module_params["param04"].join("-",$secure_oid);
if(!$duplicate)
	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,param1,param2,param3,sessionid) VALUES ('".addslashes($ref)."', '".$cart["total_cost"]."', '".substr($curr,1)."', 'I', '".$XCARTSESSID."')");

$expiry_month = substr($userinfo["card_expire"],0,2);
$expiry_year = substr($userinfo["card_expire"],2,2);;

?>
<html>
<body onLoad="document.process.submit();">
  <form action="<?php echo $url;?>" method=POST name=process>
	<input type=hidden name=merchantID value="<?php echo $id; ?>">
	<input type=hidden name=lang value="EN">
	<input type=hidden name=currency value="<?php echo $curr; ?>">
	<input type=hidden name=APACScommand value="NewPayment">
	<input type=hidden name=amount value="<?php echo $cart["total_cost"]*(($mantisse2[$curr])?(1):(100)); ?>">
	<input type=hidden name=merchantRef value="<?php echo $ref; ?>">
  </form>
  <table width=100% height=100%>
	<tr><td align=center valign=middle>Please wait while connecting to <b>ProxyPay<sup>3</sup></b> server...</td></tr>
  </table>
</body>
</html>
<?php
exit;

?>
