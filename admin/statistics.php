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
# $Id: statistics.php,v 1.39 2006/04/07 11:28:00 svowl Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_session_register("date_range");

if (!in_array(@$mode, array("general","shop","toppaths","pagesviews","cartfunnel","logins","adaptives","search","users_online")))
	$mode = "general";

$location[] = array(func_get_langvar_by_name("lbl_statistics"), "statistics.php");

#
# Define data for the navigation within section
#
$dialog_tools_data["left"][] = array("link" => "statistics.php", "title" => func_get_langvar_by_name("lbl_general_statistics"));
if (!empty($active_modules["Advanced_Statistics"])) {
	$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=shop", "title" => func_get_langvar_by_name("lbl_shop_statistics"));
	$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=toppaths", "title" => func_get_langvar_by_name("lbl_top_paths_thru_site"));
	$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=pagesviews", "title" => func_get_langvar_by_name("lbl_top_pages_views"));
	$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=cartfunnel", "title" => func_get_langvar_by_name("lbl_shopping_cart_conversion_funnel"));
}

$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=logins", "title" => func_get_langvar_by_name("lbl_log_in_history"));
$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=adaptives", "title" => func_get_langvar_by_name("lbl_browser_settings"));
if (!empty($active_modules['Users_online']))
	$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=users_online", "title" => func_get_langvar_by_name("lbl_users_online"));
$dialog_tools_data["left"][] = array("link" => "statistics.php?mode=search", "title" => func_get_langvar_by_name("lbl_search_statistics"));

if ($user_account["flag"] != "FS")
	$dialog_tools_data["right"][] = array("link" => "general.php", "title" => func_get_langvar_by_name("lbl_summary"));


$ctime = time() + $config["Appearance"]["timezone_offset"];

$do_delete_login_history = ($mode == "logins" && (@$action == "delete" || @$action == "delete_all"));

if ($REQUEST_METHOD == "POST" && !$do_delete_login_history) {
	#
	# Save the date range
	#
	if (empty($StartMonth) || empty($StartDay) || empty($StartYear) || empty($EndMonth) || empty($EndDay) || empty($EndYear)) {
		$start_date = mktime(0,0,0,date("m",$ctime),date("d",$ctime),date("Y",$ctime));
		$end_date = $ctime;
	}
	else {
		$start_date = mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
		$end_date = mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
	}

	$date_range["start_date"] = $start_date;
	$date_range["end_date"] = $end_date;
	$date_range["refresh_end_date"] = "";
	
	if ($QUERY_STRING)
		$qry_string = "?$QUERY_STRING";
	
	func_header_location("statistics.php".$qry_string);
}

if (empty($date_range) || $date_range["refresh_end_date"] == "Y") {
	$date_range["start_date"] = mktime(0,0,0,date("m",$ctime),1,date("Y",$ctime));
	$date_range["end_date"] = mktime() + $config["Appearance"]["timezone_offset"];
	$date_range["refresh_end_date"] = "Y";
	x_session_save("date_range");
}

$start_date = $date_range["start_date"];
$end_date = $date_range["end_date"];

$smarty->assign("start_date", $start_date);
$smarty->assign("end_date", $end_date);

#
# Process GET-request
#

if ($mode == "general") {
	#
	# Collect statistics information
	#
	$location[] = array(func_get_langvar_by_name("lbl_general_statistics"));

	$statistics["clients"] = func_query_first_cell("select count(login) from $sql_tbl[customers] where usertype='C'");
	$statistics["providers"] = func_query_first_cell("select count(login) from $sql_tbl[customers] where usertype='P'");
	$statistics["products"] = func_query_first_cell("select count(productid) from $sql_tbl[products]");
	$statistics["categories"] = func_query_first_cell("select count(categoryid) from $sql_tbl[categories] where parentid='0'");
	$statistics["subcategories"] = func_query_first_cell("select count(categoryid) from $sql_tbl[categories] where parentid!='0'");
	$statistics["orders"] = func_query_first_cell("select count(orderid) from $sql_tbl[orders]");

	$first_login = "first_login+'".$config["Appearance"]["timezone_offset"]."'";
	$add_date = "add_date+'".$config["Appearance"]["timezone_offset"]."'";
	$date = "date+'".$config["Appearance"]["timezone_offset"]."'";

	$statistics["clients_last_month"] = func_query_first_cell("select count(login) from $sql_tbl[customers] where usertype='C' AND ($first_login>='$start_date' AND $first_login<='$end_date')");
	$statistics["providers_last_month"] = func_query_first_cell("select count(login) from $sql_tbl[customers] where usertype='P' AND ($first_login>='$start_date' AND $first_login<='$end_date')");
	$statistics["products_last_month"] = func_query_first_cell("select count(productid) from $sql_tbl[products] WHERE ($add_date>='$start_date' AND $add_date<='$end_date')");
	$statistics["orders_last_month"] = func_query_first_cell("select count(orderid) from $sql_tbl[orders] WHERE ($date>='$start_date' AND $date<='$end_date')");
}
else {
	$ss_date = "ss.date+'".$config["Appearance"]["timezone_offset"]."'";

	$date_condition = "($ss_date>='$start_date' AND $ss_date<='$end_date')";

	if ($mode == "shop" && $active_modules["Advanced_Statistics"]) {
		$location[] = array(func_get_langvar_by_name("lbl_shop_statistics"));
		include $xcart_dir."/modules/Advanced_Statistics/display_stats.php";
	}
	elseif (in_array($mode, array("toppaths","pagesviews","cartfunnel","logins"))) {
		include $xcart_dir.DIR_ADMIN."/atracking.php";
	}
	elseif($mode == 'adaptives') {
		$location[] = array(func_get_langvar_by_name("lbl_browser_settings"));
		$statistics = func_query("SELECT * FROM $sql_tbl[stats_adaptive]");
	}
	elseif($mode == 'users_online' && !empty($active_modules['Users_online'])) {
		$location[] = array(func_get_langvar_by_name("lbl_users_online"));
		include $xcart_dir."/modules/Users_online/stats.php";
	}
	elseif($mode == 'search') {
		$location[] = array(func_get_langvar_by_name("lbl_search_statistics"));
		include $xcart_dir.DIR_ADMIN."/stats_search.php";
	}
}

#
# Assign Smarty variables and show template
#
$smarty->assign("statistics", $statistics);
$smarty->assign("mode", $mode);
$smarty->assign("main", "statistics");

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
