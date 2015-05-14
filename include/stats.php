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
# $Id: stats.php,v 1.18 2006/01/11 06:55:59 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

	$stats_info = array ();

	$result = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].login='$login'");
	$stats_info ["total_sales"] = $result;

	$result = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].login='$login' AND $sql_tbl[orders].status NOT IN ('C','P')");
	$stats_info ["unapproved_sales"] = $result;

	$result = func_query_first ("SELECT SUM($sql_tbl[partner_payment].commissions) AS numba FROM $sql_tbl[partner_payment], $sql_tbl[orders] WHERE $sql_tbl[partner_payment].orderid=$sql_tbl[orders].orderid AND $sql_tbl[partner_payment].login='$login' AND $sql_tbl[orders].status NOT IN ('C','P') AND $sql_tbl[partner_payment].paid!='Y'");
	$stats_info ["pending_commissions"] = ($result["numba"] ? $result["numba"] : "0.00");

	$result = func_query_first ("SELECT SUM($sql_tbl[partner_payment].commissions) AS numba FROM $sql_tbl[partner_payment], $sql_tbl[orders] WHERE $sql_tbl[partner_payment].orderid=$sql_tbl[orders].orderid AND $sql_tbl[partner_payment].login='$login' AND $sql_tbl[orders].status IN ('P','C') AND $sql_tbl[partner_payment].paid!='Y'");
	$stats_info ["approved_commissions"] = ($result["numba"] ? $result["numba"] : "0.00");

	$result = func_query_first ("SELECT SUM(commissions) AS numba FROM $sql_tbl[partner_payment] WHERE login='$login' AND paid='Y'");
	$stats_info ["paid_commissions"] = ($result["numba"] ? $result["numba"] : "0.00");

	$smarty->assign ("stats_info", $stats_info);
?>
