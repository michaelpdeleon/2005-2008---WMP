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
# $Id: cc_vaultx.php,v 1.8 2006/01/11 06:56:23 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

set_time_limit(130);

$clid = $module_params["param01"];
$gateways = $module_params["param02"];
$gateport = $module_params["param03"];
$cert = $module_params["param04"];
$certpass = $module_params["param05"];
$exec = $module_params["param06"];
$prefix= $module_params["param07"];

# vaultx 
$execline = func_shellquote($exec)." \"".$gateways."\" \"".$gateport."\" \"".$clid."\" \"".$cert."\" \"".$certpass."\" \"".$cart["total_cost"]."\" \"".$userinfo["card_number"]."\" \"".$userinfo["card_expire"]."\" \"".$prefix.join("-",$secure_oid)."\"";

@exec($execline, $return); $return = join("",$return);

# Transaction : [OK]   | [Fail]
# Reference : [%s]
# AuthCode : [%s]
# ResponseText : [%s]
# ResponseCode : [%s]
# ErrorMessage : [%s]


if(preg_match("/Transaction : \[OK\]/U",$return))
{
	if(preg_match("/ResponseCode : \[00\]/U",$return,$out))
	{
		$bill_output["code"] = 1;
		preg_match("/AuthCode : \[(.*)\]/U",$return,$out);
		$bill_output["billmes"] = "AuthCode: ".$out[1];
	}
	else
	{
		$bill_output["code"] = 2;
		preg_match("/ResponseCode : \[(.*)\]/U",$return,$out);
		$bill_output["billmes"] = "[".$out[1]."]";

		preg_match("/ResponseText : \[(.*)\]/U",$return,$out);
		$bill_output["billmes"].= " ".$out[1];

	}

	preg_match("/Reference : \[(.*)\]/U",$return,$out);
	$bill_output["billmes"].= " (TranRef: ".$out[1].")";

}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Failed to connect with payment processor";
}

?>
