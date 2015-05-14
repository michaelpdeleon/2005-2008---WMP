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
# $Id: featured_products.php,v 1.22.2.1 2006/08/17 08:05:57 max Exp $
#
# Get featured products data and store it into $f_products array
# Get new products data and store it into $new_products array
#

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

#
# Select from featured products table
#

$user_account['membershipid'] = !empty($user_account['membershipid'])?$user_account['membershipid']:0;

$old_search_data = $search_data["products"];
$old_mode = $mode;
$old_page = $page;

$search_data["products"] = array();
$search_data["products"]["forsale"] = "Y";
$search_data["products"]["sort_condition"] = "$sql_tbl[featured_products].product_order";
$search_data["products"]['_']['inner_joins']['featured_products'] = array(
	"on" => "$sql_tbl[products].productid=$sql_tbl[featured_products].productid AND $sql_tbl[featured_products].avail='Y' AND $sql_tbl[featured_products].categoryid='".intval($cat)."'"
);

$REQUEST_METHOD = "GET";
$mode = "search";
include $xcart_dir."/include/search.php";

$search_data["products"] = $old_search_data;
x_session_save("search_data");
$mode = $old_mode;
$page = $old_page;
unset($old_search_data, $old_mode, $old_page);

if (!empty($active_modules["Subscriptions"])) {
    include_once $xcart_dir."/modules/Subscriptions/subscription.php";
}
$smarty->clear_assign("products");

$smarty->assign("navigation_script","home.php?cat=$cat&sort=$sort&sort_direction=$sort_direction");
$smarty->assign("f_products",$products);
$search_data = '';
$products = array();
unset($search_data, $products);
?>
