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
# $Id: category_products.php,v 1.5.2.1 2006/08/07 05:26:38 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";


$cat = intval($cat);

if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid='$cat'") == 0)
	func_header_location("categories.php");

#
# Assign page location
#
$location[] = array(func_get_langvar_by_name("lbl_categories_management"), "categories.php");

$location[] = array(func_get_langvar_by_name("lbl_category_products"), "");


$old_search_data = $search_data["products"];
$old_mode = $mode;

$search_data["products"] = array();
$search_data["products"]["categoryid"] = $cat;
$search_data["products"]["search_in_subcategories"] = "Y";
$search_data["products"]["category_main"] = "Y";
$search_data["products"]["category_extra"] = "Y";
if (!isset($sort))
	$sort = $search_data["products"]["sort_field"] = "orderby";
if (!isset($sort_direction))
	$search_data["products"]["sort_direction"] = 0;

$mode = "search";

include $xcart_dir."/include/search.php";

$search_data["products"] = $old_search_data;
$mode = $old_mode;

if (is_array($products)) {
	foreach ($products as $k=>$v) {
		$products[$k] = func_array_merge($v, func_query_first("SELECT main, orderby FROM $sql_tbl[products_categories] WHERE productid='$v[productid]' AND categoryid='$cat'"));
	}
	$smarty->assign("navigation_script","category_products.php?cat=$cat");
}

$smarty->assign("products",$products);

require "./location_ajust.php";

$smarty->assign("cat", $cat);

$smarty->assign("main","category_products");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
