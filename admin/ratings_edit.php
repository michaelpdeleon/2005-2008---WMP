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
# $Id: ratings_edit.php,v 1.15 2006/01/11 06:55:58 mclap Exp $
#
# This script allows administrator to browse thought templates tree
# and edit files (these files must be writable for httpd daemon).
#

require "./auth.php";
require $xcart_dir."/include/security.php";

#
# Ratings per page;
#
$objects_per_page = 25;

$location[] = array(func_get_langvar_by_name("lbl_edit_ratings"), "");

if ($REQUEST_METHOD == "POST") {
#
# Process the POST request
#
	if ($mode == "delete") {
	#
	# Delete ratings
	#
		if ($to_delete) {
			$deleted = false;
			foreach ($to_delete as $key=>$value) {
				db_query ("DELETE FROM $sql_tbl[product_votes] WHERE vote_id='$key'");
				$deleted = true;
			}
			if ($deleted)
				$top_message["content"] = func_get_langvar_by_name("msg_adm_ratings_del");
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_ratings_sel");
			$top_message["type"] = "W";
		}
	}
	
	if ($mode == "update") {
	#
	# Update ratings
	#
		if ($update_votes) {
			$updated = false;
			foreach ($update_votes as $key=>$value) {
				db_query ("UPDATE $sql_tbl[product_votes] SET vote_value='$value' WHERE vote_id='$key'");
				$updated = true;
			}
			if ($updated)
				$top_message["content"] = func_get_langvar_by_name("msg_adm_ratings_upd");
		}
	}

	func_header_location("ratings_edit.php?sortby=$sortby&sortorder=$orderby&productid=$productid&ip=".urlencode($ip)."&page=$page");

} # /if ($REQUEST_METHOD == "POST")

# sortorder & sortby
if ($sortorder != 0) {
	$sortorder = 1;
	$_sortorder = " DESC ";
} else {
	$sortorder = 0;
	$_sortorder = " ASC ";
}

if ($sortby == 'productcode')
	$_sortby = " $sql_tbl[products].productcode ";
elseif ($sortby == 'product')
	$_sortby = " $sql_tbl[products].product ";
elseif ($sortby == 'ip')
	$_sortby = " $sql_tbl[product_votes].remote_ip ";
elseif ($sortby == 'vote')
	$_sortby = " $sql_tbl[product_votes].vote_value ";
else {
	$sortby = 'productid';
	$_sortby = " $sql_tbl[product_votes].productid ";
}

if ($productid) {
	$condition = " AND $sql_tbl[product_votes].productid='$productid' ";
	$smarty->assign ("product", func_query_first ("SELECT product FROM $sql_tbl[products] WHERE productid='$productid'"));
} elseif ($ip)
	$condition = " AND $sql_tbl[product_votes].remote_ip='$ip' ";
else
	$condition = "";

$ratings_total = func_query ("SELECT * FROM $sql_tbl[product_votes] WHERE 1=1 $condition");

$total_nav_pages = ceil(count($ratings_total)/$objects_per_page)+1;
require $xcart_dir."/include/navigation.php";

$ratings = func_query ("SELECT $sql_tbl[product_votes].*, $sql_tbl[products].* FROM $sql_tbl[product_votes], $sql_tbl[products] WHERE $sql_tbl[product_votes].productid=$sql_tbl[products].productid $condition ORDER BY $_sortby $_sortorder LIMIT $first_page, $objects_per_page");

$smarty->assign("navigation_script", "ratings_edit.php?sortby=$sortby&orderby=$orderby&productid=$productid&ip=$ip");

$smarty->assign ("ratings", $ratings);
$smarty->assign ("sortby", $sortby);
$smarty->assign ("sortorder", $sortorder);
$smarty->assign ("invsortorder", !$sortorder);
$smarty->assign ("productid", $productid);
$smarty->assign ("ip", $ip);

$smarty->assign ("main", "ratings_edit");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
