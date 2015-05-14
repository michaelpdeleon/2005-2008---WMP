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
# $Id: search_users.php,v 1.1.2.1 2006/06/01 06:30:58 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$condition = array();
$where = array();

if (!empty($data["usertype"])) {
	# Search by usertype...
	$where[] = "usertype='".$data["usertype"]."'";

	if ($data["usertype"] == "C") {
		# Search by customer registration type...
		if ($data["registration_type"] == "1") {
			$where[] = "login LIKE '".addslashes($anonymous_username_prefix)."%'";

		} elseif ($data["registration_type"] == "2") {
			$where[] = "login NOT LIKE '".addslashes($anonymous_username_prefix)."%'";
		}
	}

}

if (!empty($data["membershipid"])) {
	# Search by membershipid...
	if (preg_match("/pending_membership/i", $data["membershipid"]))
		$where[] = "$sql_tbl[customers].membershipid != $sql_tbl[customers].pending_membershipid";
	else
		$where[] = "$sql_tbl[customers].membershipid = '".$data["membershipid"]."' ";
}

if (!empty($data["substring"])) {

	# Search for substring in some fields...

	if (!empty($data["by_username"]))
		$condition[] = "login LIKE '%".$data["substring"]."%'";

	if (!empty($data["by_firstname"]))
		$condition[] = "firstname LIKE '%".$data["substring"]."%'";

	if (!empty($data["by_lastname"]))
		$condition[] = "lastname LIKE '%".$data["substring"]."%'";

	if (preg_match("/^(.+)(\s+)(.+)$/", $data["substring"], $found) && !empty($data["by_firstname"]) && !empty($data["by_lastname"]))
		$condition[] = "firstname LIKE '%".$found[1]."%' AND lastname LIKE '%".$found[3]."%'";

	if (!empty($data["by_email"]))
		$condition[] = "email LIKE '%".$data["substring"]."%'";

	if (!empty($data["by_company"]))
		$condition[] = "company LIKE '%".$data["substring"]."%'";

	$where[] = "(".implode(" OR ", $condition).")";
}

if (!empty($data["phone"])) {
	# Search by phone...
	$alt_phone = preg_replace("/[- ]/", "", $data["phone"]);
	$where[] = "(phone LIKE '%".$data["phone"]."%' OR phone LIKE '%$alt_phone%' OR fax LIKE '%".$data["phone"]."%' OR fax LIKE '%$alt_phone%')";
}

if (!empty($data["url"])) {
	# Search by web site url...
	$where[] = "url LIKE '%".$data["url"]."%'";
}

$address_condition = "";
if (!empty($data["address_type"])) {

	# Search by address...

	if (!empty($data["city"]))
		$address_condition .= "PREFIX_city LIKE '%".$data["city"]."%'";

	if (!empty($data["state"]))
		$address_condition .= "PREFIX_state='".$data["state"]."'";

	if (!empty($data["country"]))
		$address_condition .= "PREFIX_country='".$data["country"]."'";

	if (!empty($data["zipcode"]))
		$address_condition .= "PREFIX_zipcode LIKE '%".$data["zipcode"]."%'";

	if ($data["address_type"] == "B" || $data["address_type"] == "Both")
		$where[] = preg_replace("/PREFIX_(city|state|country|zipcode)/", "b_\\1", $address_condition);

	if ($data["address_type"] == "S" || $data["address_type"] == "Both")
		$where[] = preg_replace("/PREFIX_(city|state|country|zipcode)/", "s_\\1", $address_condition);

}

#
# Search by first or/and last login date condition
#
$compare_date_fields = array();
if (!empty($data["registration_date"]))
	$compare_date_fields[] = "first_login";

if (!empty($data["last_login_date"]))
	$compare_date_fields[] = "last_login";

if (!empty($compare_date_fields)) {
	$end_date = mktime();

	# ...dates within specified period
	if ($data["date_period"] == "C") {
		$start_date = $data["start_date"];
		$end_date = $data["end_date"];
	}
	# ...dates within this month
	else {
		if ($data["date_period"] == "M")
			$start_date = mktime(0,0,0,date("n",$end_date),1,date("Y",$end_date));
		elseif ($data["date_period"] == "D")
			$start_date = mktime(0,0,0,date("n",$end_date),date("j",$end_date),date("Y",$end_date));
		elseif ($data["date_period"] == "W") {
			$first_weekday = $end_date - (date("w",$end_date) * 86400);
			$start_date = mktime(0,0,0,date("n",$first_weekday),date("j",$first_weekday),date("Y",$first_weekday));
		}

	}
	foreach ($compare_date_fields as $k=>$v) {
		$where[] = "$sql_tbl[customers].$v >= '$start_date'";
		$where[] = "$sql_tbl[customers].$v <= '$end_date'";
	}
}

if (!empty($active_modules["Simple_Mode"])) {
	$where[] = "$sql_tbl[customers].usertype != 'A'";
}


$sort_string = "";
if (!empty($data["sort_field"])) {

	# Sort the search results...

	$direction = ($data["sort_direction"] ? "DESC" : "ASC");
	switch ($data["sort_field"]) {
		case "username":
			$sort_string = " ORDER BY login $direction";
			break;

		case "name":
			$sort_string = " ORDER BY lastname $direction, firstname $direction";
			break;

		case "last_login":
			$sort_string = " ORDER BY last_login $direction, login";
			break;

		case "usertype":
		case "email":
			$sort_string = " ORDER BY ".$data["sort_field"]." $direction";
	}
}

$search_condition = empty($where) ? "" : (" WHERE ".implode(" AND ", $where));

#
# Calculate the number of rows in the search results
#
$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers]".$search_condition);

if ($total_items == 0)
	return false;


# Export all found users
if ($data['_get_sql_query']) {
	$sql_query = "SELECT $sql_tbl[customers].login FROM $sql_tbl[customers] ".$search_condition;
	return true;
}

if (!empty($data['_objects_per_page'])) {
	#
	# Prepare the page navigation
	#
	$page = $data["page"];
	$objects_per_page = $data['_objects_per_page'];
	$total_nav_pages = ceil($total_items/$objects_per_page)+1;

	include $xcart_dir."/include/navigation.php";

	$sort_string .= " LIMIT $first_page, $objects_per_page";
}

#
# Perform the SQL query and getting the search results
#
$users = func_query("SELECT $sql_tbl[customers].*, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[customers] LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language'".$search_condition.$sort_string);

if (is_array($users)) {
	#
	# Correct the search results...
	#
	foreach($users as $k => $v) {
		if (!empty($v["last_login"]))
			$users[$k]["last_login"] += $config["Appearance"]["timezone_offset"];

		if (!empty($users[$k]["first_login"]))
			$users[$k]["first_login"] += $config["Appearance"]["timezone_offset"];

		if ($v["usertype"] == "P" && !$single_mode)
			$users[$k]["products"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE provider='".addslashes($v["login"])."'");
	}
}

?>
