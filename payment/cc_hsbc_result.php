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
# $Id: cc_hsbc_result.php,v 1.14.2.1 2006/07/05 11:41:26 max Exp $
#
  
if ($HTTP_SERVER_VARS["REQUEST_METHOD"]=="POST") {
	require "./auth.php";

$errarr = array(
	"1"	=> "The user cancelled the transaction.",
	"2"	=> "The processor declined the transaction for an unknown reason.",
	"3"	=> "The transaction was declined because of a problem with the card. For example, an invalid card number or expiration date was specified.",
	"4"	=> "The processor did not return a response.",
	"5"	=> "The amount specified in the transaction was either too high or too low for the processor.",
	"6"	=> "The specified currency is not supported by either the processor or the card.",
	"7"	=> "The order is invalid because the order ID is a duplicate.",
	"8"	=> "The transaction was rejected by FraudShield.",
	"9"	=> "The transaction was placed in Review state by FraudShield.1",
	"10"	=> "The transaction failed because of invalid input data.",
	"11"	=> "The transaction failed because the CPI was configured incorrectly.",
	"12"	=> "The transaction failed because the Storefront was configured incorrectly.",
	"13"	=> "The connection timed out.",
	"14"	=> "The transaction failed because the cardholders browser refused a cookie.",
	"15"	=> "The customers browser does not support 128-bit encryption.",
	"16"	=> "The CPI cannot communicate with the Secure ePayment engine."
);

        $bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$OrderId."'");

        if($CpiResultsCode)
        {
                $bill_output["code"] = ($CpiResultsCode == 9 || $CpiResultsCode == 8) ? 3 : 2;
                $bill_output["billmes"] = empty($errarr[$CpiResultsCode]) ? "CpiResultsCode: ".$CpiResultsCode : $errarr[$CpiResultsCode];
        }
        else
        {
                $bill_output["code"] = 1;
                $bill_output["billmes"] = "Ok";
        }

	$skey = $HTTP_POST_VARS["OrderId"];
	require($xcart_dir."/payment/payment_ccmid.php");
	require($xcart_dir."/payment/payment_ccwebset.php");
}

?>
