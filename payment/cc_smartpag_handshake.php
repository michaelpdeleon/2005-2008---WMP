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
# $Id: cc_smartpag_handshake.php,v 1.19.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD != "GET")
	exit;

require "./auth.php";

x_load('user');

$order_auth = $HTTP_GET_VARS["36948FFEF212F5E4"];
$oid = $HTTP_GET_VARS["91D4C3128BF7DA7F"];

$a = func_query_first("SELECT sessionid,param1,param2 FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");

x_session_id($a["sessid"]);
x_session_register("login");
x_session_register("login_type");
x_session_register("cart");

$userinfo = func_userinfo($login, $login_type);

$post = "";
$post["36948FFEF212F5E4"] 	= $order_auth;
$post["91D4C3128BF7DA7F"] 	= $oid;
$post["SPV"] 				= 100*$cart["total_cost"];
$post["SFRETE"]				= 100*($cart["total_cost"]-$cart["subtotal"]);
$post["Nome"] 				= $bill_name;
$post["CPF"]				= $userinfo["ssn"];
$post["FONE"]				= $userinfo["phone"];
$post["EMAIL"]				= $userinfo["email"];

$post["ENDERECO"]			= $userinfo["b_address"];
$post["CIDADE"]				= $userinfo["b_city"];
$post["ESTADO"]				= $userinfo["b_state"];
$post["CEP"]				= $userinfo["b_zipcode"];
$post["PAIS"]				= $userinfo["b_country"];

$post["ENDERECO_D"]			= $userinfo["s_address"];
$post["CIDADE_D"]			= $userinfo["s_city"];
$post["ESTADO_D"]			= $userinfo["s_state"];
$post["CEP_D"]				= $userinfo["s_zipcode"];
$post["PAIS_D"]				= $userinfo["s_country"];

$post["URLPOSTLOJA"]		= $http_location."/payment/cc_smartpag_final.php";
$post["URLRETORNOLOJA"]		= $http_location."/payment/cc_smartpag_return.php?oid=".$oid;
$post["PPAGAMENTO"]			= $a["pagamento"]; //"CREDIT CARD"; // ?????
$post["BANDEIRA"]			= $a["bandeira"];  //"2"; // MasterCard only
$post["SHOW_TELA_FINALIZACAO"] = 2;
$post["FRAME50URLBOTAO1"]	= 1; // switch to ZERO to disable SmartPag finalization screen
$post["ENVIA_EMAIL_CLIENTE"] = 0; // switch to ONE to sending email to customer

if ($products && is_array($products)) {
	# This key is required for restoring lost tranzaction
	foreach($products as $product) {
		$i++;
		$post["QTD_".$i]		= $product["amount"];
		$post["COD_".$i]		= $product["productid"];
		$post["DES_".$i]		= $product["product"];
		$post["VAL_".$i]		= 100*$product["price"];
	}

	$post["SITEM"]				= @count($products);
}
else {
	$post["SITEM"]		= 1;
	$post["QTD_1"]		= 1;
	$post["COD_1"]		= "xxx";
	$post["DES_1"]		= "Lost products";
	$post["VAL_1"]		= 100*$cart["subtotal"];
}

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://homolog.smartpag.com.br/index.asp" method=POST name=process>
		<?php  foreach($post as $k => $v) print "<input type=hidden name=\"".htmlspecialchars($k)."\" value=\"".htmlspecialchars($v)."\">\n"; ?>
  </form>
  <table width=100% height=100%>
        <tr><td align=center valign=middle>Redirecting to <b>SmartPag</b> server...</td></tr>
  </table>
</body>
</html>
