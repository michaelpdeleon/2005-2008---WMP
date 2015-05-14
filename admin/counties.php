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
# $Id: counties.php,v 1.5 2006/01/11 06:55:57 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

if ($config["General"]["use_counties"] != "Y") {
	func_header_location("states.php");
}

$location[] = array(func_get_langvar_by_name("lbl_counties_management"), "");

#
# Counties per page
#
$objects_per_page = 25;

$state = func_query_first("SELECT * FROM $sql_tbl[states] WHERE stateid='$stateid'");

if (!empty($state)) {
	$state["country"] = func_query_first_cell("SELECT $sql_tbl[languages].value FROM $sql_tbl[languages] WHERE $sql_tbl[languages].name = CONCAT('country_', '$state[country_code]')");
}
else {
	func_header_location("states.php");
}

if ($REQUEST_METHOD == "POST") {
	if ($mode == "delete") {
		#
		# Delete the selected states
		#
		if (is_array($selected)) {
			foreach ($selected as $countyid=>$v) {
				db_query("DELETE FROM $sql_tbl[counties] WHERE countyid='$countyid'");
			}
			$top_message["content"] = func_get_langvar_by_name("msg_adm_counties_del");
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_counties_del");
			$top_message["type"] = "W";
		}
	}

	if ($mode == "update") {
		#
		# Update states
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $countyid=>$v) {
				db_query ("UPDATE $sql_tbl[counties] SET county='$v[county]' WHERE countyid='$countyid'");
			}

			$top_message["content"] = func_get_langvar_by_name("msg_adm_counties_upd");
		}
	}
	
	if ($mode == "add") {
		#
		# Add new state
		#
		if (!empty($new_county_name) && $stateid > 0) {
			db_query ("REPLACE INTO $sql_tbl[counties] (stateid, county) VALUES ('$stateid', '$new_county_name')");
			$top_message["content"] = func_get_langvar_by_name("msg_adm_county_add");
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_county_add");
			$top_message["type"] = "W";
		}
	}

	func_header_location("counties.php?stateid=$stateid".(!empty($page)?"&page=$page":""));
}

$total_items_in_search = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[counties] WHERE stateid='$stateid'");

if ($total_items_in_search > 0) {
	#
	# Navigation code
	#
	$total_nav_pages = ceil($total_items_in_search/$objects_per_page)+1;
	
	require $xcart_dir."/include/navigation.php";
	
	$smarty->assign("navigation_script","counties.php?stateid=$stateid");
	
	$counties = func_query ("SELECT * FROM $sql_tbl[counties] WHERE stateid='$stateid' ORDER BY county LIMIT $first_page, $objects_per_page");

	$smarty->assign ("counties", $counties);
}

$smarty->assign("stateid", $stateid);
$smarty->assign("state", $state);

$smarty->assign("main", "counties_edit");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>