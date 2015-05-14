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
# $Id: cc_basia.php,v 1.13 2006/01/11 06:56:22 mclap Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

function make_basia_request($a)
{
	global $pp_path;
	$post = "";
	$post.= sprintf("%04d",$a["transaction"]);
	$post.= sprintf("%06d",$a["currency"]);
	$post.= sprintf("%09d",$a["merchantid"]);
	$post.= "00"; # reserved1
	$post.= sprintf("%06d",$a["order_num"]);
	$post.= date("Ymd"); #date: CCYYMMDD
	$post.= date("His"); #date:HHMMSS
	$post.= sprintf("%019d",($a["card_number"]=0)); # NOT used!!!
	$post.= sprintf("%04d",($a["card_expire"]=0)); # NOT used!!!
	$post.= sprintf("%012d",(100*$a["amount"]));
	$post.= sprintf("%02d",0); # 0 for requests
	$post.= sprintf("%06d",$a["approval_code"]);
	$post.= sprintf("%019d",$a["cust_ref1"]);
	$post.= sprintf("%019d",$a["cust_ref2"]);
	$post.= sprintf("%025d",0); #reserver2
	$post.= sprintf("%04d",($a["card_cvv2"]=0)); # NOT used!!!
	$post.= sprintf("%01d",$a["card_type"]); # card type
	$post.= sprintf("%034d",0); #reserved3
	$post.= sprintf("%01d",$a["vbv_flag"]); # VbVisa (1/yes; 0/no)
	$post.= sprintf("%08d",0); # terminal/reserved
	$post.= sprintf("%02d",0); # ShopN/reserved
	$post.= "2"; #payment type (2/credit card;3/debit card;4/asiaWallet)
	$post.= "3"; #settlement type (1/auto;2/manual;3/e-settle)
	$post.= sprintf("%016d",0); #auth.code/ NOT used!!!

	exec(func_shellquote($pp_path)."/encrypt ".$post." 2>&1",$out); $post = $out[0];

	return $post;
}

function parse_basia_response($resp)
{
	global $pp_path;

	exec(func_shellquote($pp_path)."/decrypt ".$resp." 2>&1",$out); $resp = $out[0];

	if (preg_match("/^(\d{4})(\d{6})(\d{9})(\d{2})(\d{6})(\d{8})(\d{6})(\d{19})(\d{4})(\d{12})(\d{2})(\d{6})(\d{19})(\d{19})(\d{25})(\d{4})(\d{1})(\d{34})(\d{1})(\d{8})(\d{2})(\d{1})(\d{1})(\d{16})$/",$resp,$out))
	{

	$a["transaction"] = $out[1];
	$a["currency"] = $out[2];
	$a["merchantid"] = $out[3];
	#$post.= "00"; # reserved1
	$a["order_num"] = $out[5];
	$a["date"] = $out[6];
	$a["time"] = $out[7];
	$a["card_number"] = $out[8];
	$a["card_expire"] = $out[9];
	$a["amount"] = $out[10];
	$a["response_code"] = $out[11];
	$a["approval_code"] = $out[12];
	$a["cust_ref1"] = $out[13];
	$a["cust_ref2"] = $out[14];
	#$post.= sprintf("%025d",0); #reserver2
	$a["card_cvv2"] = $out[16];
	$a["card_type"] = $out[17];
	#$post.= sprintf("%034d",0); #reserved3
	$a["vbv_flag"] = $out[19];
	$a["terminal"] = $out[20];
	$a["shop"] = $out[21];
	$a["payment"] = $out[22];
	$a["settlement"] = $out[23];
	$a["auth_code"] = $out[24];

	return $a;
	}
	else return $resp;
}

if($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["boamsg"]))
{
	require "./auth.php";

	$err = array(
	"01" => "Declined",
	"02" => "Card expired",
	"03" => "Invalid Merchant",
	"12" => "Invalid Transaction",
	"13" => "Invalid Amount",
	"14" => "Invalid Credit Card",
	"21" => "No Action Taken",
	"30" => "Format Error",
	"51" => "Incufficient Funds",
	"55" => "Time Out",
	"75" => "Allowable Number of PIN entry tries Exceeded",
	"83" => "Unable to Verify PIN",
	"84" => "Incorrect CVV",
	"91" => "Issuer or Switch Inoperative",
	"95" => "Invalid Authentication",
	"99" => "System is Unavailable"
	);

	$carderr = array(
	"1" => "VISA",
	"2" => "MasterCard"
	);

}

if($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["boamsg"]) && empty($HTTP_POST_VARS["boaref"]))
{
	x_load('order');

	$pp_path = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_basia.php'");
	$resp = parse_basia_response($HTTP_POST_VARS["boamsg"]);

	if($resp["transaction"]=="0410")$bill_output["billmes"] = "-- REVERSAL --\n";
	if($resp["transaction"]=="0510")$bill_output["billmes"] = "-- SETTLEMENT --\n";

	if($resp["response_code"]=="00" && $resp["transaction"]=="0410")
	{
		$bill_output["code"] = "D"; # reversal
	}
	elseif($resp["response_code"]=="00" && $resp["transaction"]=="0510")
	{
		$bill_output["code"] = "P"; # settlement
	}
	else
	{
		$bill_output["code"] = "F";
		$bill_output["billmes"].= (empty($err[$resp["response_code"]]) ? "Resp.Code: ".$resp["response_code"] : $err[$resp["response_code"]]);
	}

	if($resp["approval_code"])
		$bill_output["billmes"].= " (Approval Code: ".$resp["approval_code"].")";
	if($resp["amount"])
		$bill_output["billmes"].= " (Amount: ".$resp["amount"].")";
	if($resp["card_type"])
		$bill_output["billmes"].= " (CardType: ".(empty($carderr[$resp["card_type"]]) ? "Code: ".$resp["card_type"] : $carderr[$resp["card_type"]]).")";
	if($resp["vbv_flag"])
		$bill_output["billmes"].= " [Card enrolled by VbV]";

	func_change_order_status((0+$resp["order_num"]), $bill_output["code"], $bill_output["billmes"]);
	print "Ok";exit;
}
elseif($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["boamsg"]) && isset($HTTP_POST_VARS["boaref"]) && isset($HTTP_POST_VARS["orderno"]) && isset($HTTP_POST_VARS["rescode"]))
{
	$skey = $HTTP_POST_VARS["boaref"];
	require($xcart_dir."/payment/payment_ccview.php");
}
elseif ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS["boamsg"]) && isset($HTTP_POST_VARS["boaref"]))
{
	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_POST_VARS["boaref"]."'");
	$pp_path = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_basia.php'");

	$resp = parse_basia_response($HTTP_POST_VARS["boamsg"]);

	if($resp["response_code"]=="00" && $resp["transaction"]=="0110")
	{
		$bill_output["code"] = 3;
	}
	else
	{
		$bill_output["code"] = 2;
		$bill_output["billmes"] = (empty($err[$resp["response_code"]]) ? "Resp.Code: ".$resp["response_code"] : $err[$resp["response_code"]]);
	}

	if($resp["approval_code"])
		$bill_output["billmes"].= " (Approval Code: ".$resp["approval_code"].")";
	if($resp["card_type"])
		$bill_output["billmes"].= " (CardType: ".(empty($carderr[$resp["card_type"]]) ? "Code: ".$resp["card_type"] : $carderr[$resp["card_type"]]).")";
	if($resp["vbv_flag"])
		$bill_output["billmes"].= " [Card enrolled by VbV]";

	$skey = $HTTP_POST_VARS["boaref"];
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_login = $module_params["param01"];
	$pp_path = $module_params["param02"];
	$pp_curr = $module_params["param03"];
	$pp_prefix = preg_replace("/\D/","",$module_params["param04"]);

	$_orderids = $secure_oid[0];
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

	$post = array();
	$post["transaction"] = "100";
	$post["currency"] = $pp_curr;
	$post["merchantid"] = $pp_login;
	$post["order_num"] = $_orderids;
	$post["amount"] = $cart["total_cost"];# = "1.25";
	$request = make_basia_request($post);

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www2.boa.co.th/boapayment/boapayment.php" method=POST name=process>
	<input type=hidden name=boamsg value="<?php echo $request; ?>">
	<input type=hidden name=boaref value="<?php echo $_orderids; ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>Bank of Asia</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
