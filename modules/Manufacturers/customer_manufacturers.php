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
# $Id: customer_manufacturers.php,v 1.12 2006/01/11 06:56:12 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$manufacturers_menu = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] USE INDEX (avail) WHERE avail = 'Y'");
if ($manufacturers_menu > 0) {
	if ($config["Manufacturers"]["manufacturers_limit"] > 0)
		$smarty->assign("show_other_manufacturers", $manufacturers_menu>$config["Manufacturers"]["manufacturers_limit"]);
	$manufacturers_menu = func_query("SELECT $sql_tbl[manufacturers].*, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] USE INDEX (avail) LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' WHERE avail = 'Y' ORDER BY orderby, manufacturer".(($config["Manufacturers"]["manufacturers_limit"] > 0) ? " LIMIT ".$config["Manufacturers"]["manufacturers_limit"] : ""));
	$smarty->assign("manufacturers_menu", $manufacturers_menu);
}

?>
