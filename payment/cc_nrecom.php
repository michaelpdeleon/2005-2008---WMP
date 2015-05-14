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
# $Id: cc_nrecom.php,v 1.10 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_merch = $module_params["param01"];
$pp_pass = $module_params["param02"];

$post = "";
$post[] = "LOGIN=".$pp_merch."/".$pp_pass;
$post[] = "COMMAND=purchase";
$post[] = "AMOUNT=".$cart["total_cost"];
$post[] = "COMMENT=OID:".join("-",$secure_oid).";CardHolder:".$userinfo["card_name"];
$post[] = "CCNUM=".$userinfo["card_number"];
$post[] = "CCEXP=".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2);

list($a,$return)=func_https_request("POST","https://4tknox.au.com:443/cgi-bin/themerchant.au.com/ecom/external2.pl",$post);
$return = "&".strtr($return,"\n","&")."&";

# declined
# .
# result=0           # 0 and -1 = failed; 1 - approved
# card_type=05
# settlement_date=20030228
# status=declined
# card_desc=VISA
# response_text=CARD NOT VALID
# txn_ref=0302282313434545
# bank_ref=002248
# response_code=31

if(preg_match("/&status=approved&/i",$return) && preg_match("/&result=1&/i",$return))
{
	$bill_output["code"] = 1;
	if(preg_match("/authentication=(.*)&/U",$return,$out))
		$bill_output["billmes"] ="(authentication=[".$out[1]."])";
}
else
{
	$bill_output["code"] = 2;
	if(preg_match("/response_text=(.*)&/U",$return,$out))
		$bill_output["billmes"] =$out[1];
}

preg_match("/bank_ref=(.*)&/U",$return,$out); $bill_output["billmes"].="(Bank ref=".$out[1].")";
preg_match("/txn_ref=(.*)&/U",$return,$out);  $bill_output["billmes"].="(Txn=".$out[1].")";
$bill_output["cvvmes"].= "Not support";
$bill_output["avsmes"] = "Not support";

?>
