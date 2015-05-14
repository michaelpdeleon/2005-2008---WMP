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
# $Id: recommends.php,v 1.14.2.1 2006/06/01 11:25:16 max Exp $
#
# Recommends list
#

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

#
# Get products data for current category and store it into $products array
#
$avail_condition = "";
if ($config["General"]["unlimited_products"] == "N" && $config["General"]["disable_outofstock_products"] == "Y") {
	$avail_condition = " AND $sql_tbl[products].avail>0 ";
}

$lng_condition = " AND $sql_tbl[products_lng].code='$store_language'";
$query_ids = array();
if ($config["Recommended_Products"]["select_recommends_list_randomly"] == "Y") {
	$rnd = rand();
	$query_ids = func_query_column("SELECT $sql_tbl[products].productid FROM $sql_tbl[products] USE INDEX (fi,fia), $sql_tbl[categories] USE INDEX (ia), $sql_tbl[products_categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[products_categories].categoryid WHERE $sql_tbl[products].forsale='Y' AND $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' AND $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[categories].avail = 'Y' AND ($sql_tbl[category_memberships].membershipid IS NULL OR $sql_tbl[category_memberships].membershipid = '$user_account[membershipid]') ".$avail_condition." ORDER BY RAND(NOW()+$rnd) LIMIT ".$config["Recommended_Products"]["number_of_recommends"]);
}

if ($config["Recommended_Products"]["select_recommends_list_randomly"] == "Y" && count($query_ids) > 0) {
    $query = "SELECT $sql_tbl[products].* FROM $sql_tbl[products] WHERE $sql_tbl[products].productid IN ('".join("','",$query_ids)."')";
}
else {
    $query = "SELECT DISTINCT sp2.productid, $sql_tbl[products].* FROM $sql_tbl[stats_customers_products] as sp1, $sql_tbl[stats_customers_products] AS sp2, $sql_tbl[products], $sql_tbl[categories], $sql_tbl[products_categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[products_categories].categoryid WHERE sp1.productid='$productid' AND sp1.login=sp2.login AND sp2.productid!='$productid' AND $sql_tbl[products].productid=sp2.productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' AND $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[category_memberships].membershipid IN ('$user_account[membershipid]', 0) ".$avail_condition." ORDER BY $sql_tbl[products].product LIMIT ".$config["Recommended_Products"]["number_of_recommends"];
}

$recommends = func_query($query);

#
# Select international product descriptions
#
if (is_array($recommends)) {
	$ids = array();
	foreach ($recommends as $v) {
		$ids[] = $v['productid'];
	}
	$products_lng = func_query_hash("SELECT * FROM $sql_tbl[products_lng] WHERE productid IN ('".implode("','", $ids)."') $lng_condition", "productid", false);
	if (!empty($products_lng)) {
		foreach ($recommends as $k => $v) {
			if (isset($products_lng[$v['productid']])) {
				if (!empty($products_lng[$v['productid']]['product']))
					$recommends[$k]['product'] = $products_lng[$v['productid']]['product'];
				if (!empty($products_lng[$v['productid']]['descr']))
					$recommends[$k]['descr'] = $products_lng[$v['productid']]['descr'];
				if (!empty($products_lng[$v['productid']]['fulldescr']))
					$recommends[$k]['fulldescr'] = $products_lng[$v['productid']]['fulldescr'];
			}
		}
	}
	unset($products_lng, $ids);
}

$smarty->assign("recommends",$recommends);
?>
