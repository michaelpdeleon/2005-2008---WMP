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
# $Id: cc_paygate.php,v 1.13 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$curr = $module_params["param02"];
$pass = $module_params["param03"];

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="VISA";
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MasterCard";

$post = "";
$post[] = "MerchantID=".$module_params["param01"];
$post[] = "TransID=".$module_params["param04"].join("-",$secure_oid);
$post[] = "Amount=".(($curr=="JPY" || $curr=="LUF" || $curr=="SEK") ? 1 : 100 )*$cart["total_cost"];
$post[] = "Currency=".$curr;
$post[] = "CCNr=".$userinfo["card_number"];
$post[] = "CCexpiry=".(2000+substr($userinfo["card_expire"],2,2)).substr($userinfo["card_expire"],0,2);
$post[] = "CCCVC=".$userinfo["card_cvv2"];
$post[] = "CCBrand=".$userinfo["card_type"];
$post[] = "OrderDesc=".$config['Company']['company_name'];

$join = join("&",$post); $len = strlen($join);
$cipher = $blowfish->ctEncrypt($join, $len, $pass);

$post = "";
$post[] = "MerchantID=".$module_params["param01"];
$post[] = "Len=".strlen($join);
$post[] = "Data=".$cipher;

list($a,$return)=func_https_request("POST","https://www.netkauf.de:443/paygate/direct.aspx",$post);

# Len=125&Data=EC%F5%9E%C6%3E%9B%EE%74%DF%21%7E%27%FA%2E%19%4B%92%59%7C%73%95%BA%DB%AD%43%40%CD%FD%00%0D%D5%71%29%EE%FA%A0%BE%90%4A%7E%23%3A%CD%BE%5E%F9%8E%16%92%B1%60%25%64%DB%F2%C3%C7%51%73%A6%2C%48%49%62%A2%47%B8%A9%1E%A7%A5%44
preg_match("/Len=(.*)&/U",$return,$out);$len=$out[1];
preg_match("/&Data=(.*)$/U",$return,$out);$cipher=$out[1];

if($len>0 && strlen($cipher)>0)
{

$plain = $blowfish->ctDecrypt($cipher, $len, $password);$return = "&".$plain."&";

# PayID=a234b678e01f34567090e23d567890ce&XID=50f35e768edf34c4e090e23d567890ce&TransID=100000001&Status=OK&Description=OK&Code=0
if(preg_match("/&Status=AUTHORIZED&/U",$return,$out))
{
	$bill_output["code"] = 1;
	preg_match("/&PayID=(.*)&/U",$return,$out);
	if(!empty($out[1]))
		$bill_output["billmes"] = "PayID=".$out[1];
}
else
{
	$bill_output["code"] = 2;
	preg_match("/&Description=(.*)&/U",$return,$out);
	if(!empty($out[1]))
		$bill_output["billmes"] = $out[1];
}

preg_match("/&XID=(.*)&/U",$return,$out);
if(!empty($out[1]))
	$bill_output["billmes"].= " (XID=".$out[1].")";

}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $return;
}

$bill_output["cvvmes"].= "Not support";
$bill_output["avsmes"] = "Not support";

?>
