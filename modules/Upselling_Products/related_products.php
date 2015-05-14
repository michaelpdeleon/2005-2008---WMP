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
# $Id: related_products.php,v 1.24 2006/03/17 12:15:13 max Exp $
#
# This Module forms list of upsailing products
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

$avail_condition = "";
if ($config["General"]["unlimited_products"] == "N" && $config["General"]["disable_outofstock_products"] == "Y") {
	$avail_condition = "AND $sql_tbl[products].avail > 0";
}

$product_links = func_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].productcode, $sql_tbl[pricing].price AS price, IF ($sql_tbl[products_lng].product IS NOT NULL AND $sql_tbl[products_lng].product != '',$sql_tbl[products_lng].product, $sql_tbl[products].product) AS product FROM $sql_tbl[pricing], $sql_tbl[product_links], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[quick_prices], $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products_lng].productid = $sql_tbl[products].productid AND $sql_tbl[products_lng].code='$store_language' WHERE $sql_tbl[products].productid=$sql_tbl[product_links].productid2 AND $sql_tbl[product_links].productid1='$productid' AND $sql_tbl[pricing].priceid = $sql_tbl[quick_prices].priceid AND $sql_tbl[quick_prices].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].membershipid = '".intval($membershipid)."' AND $sql_tbl[products].forsale = 'Y' AND $sql_tbl[products_categories].productid = $sql_tbl[products].productid AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[products].productid != '$productid' $avail_condition GROUP BY $sql_tbl[products].productid ORDER BY $sql_tbl[product_links].orderby, product");

$smarty->assign("product_links",$product_links);
?>
