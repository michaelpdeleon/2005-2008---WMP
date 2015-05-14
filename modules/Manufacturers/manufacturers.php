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
# $Id: manufacturers.php,v 1.18.2.2 2006/07/18 08:56:38 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','image');

$location[] = array(func_get_langvar_by_name("lbl_manufacturers"), "");

#
# NOTES.
# 1. Only administrator can activate manufacturer and set up its position in
# the manufacturers list.
# 2. Provider can view the entire list of manufacturers but edit or delete only
# manufacturers created by the same provider.
# 3. If some manufacturer have assigned products of at least one provider that
# is not owner of this manufacturer, owner will not be able to delete that
# manufacturer.
#
$provider_condition = ($single_mode || $current_area == "A"?"":"AND provider='$login'");

$manufacturerid = intval($manufacturerid);

#
# Get the number of products that assigned to the manufacturer
# with different $provider (this need for checking permissions)
#
function func_manufacturer_is_used($manufacturerid, $provider) {
	global $sql_tbl;
	return func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[products] WHERE manufacturerid='$manufacturerid' AND provider!='$provider'");
}

if ($REQUEST_METHOD == "POST" || ($mode == "delete_image" && $manufacturerid)) {


	if ($mode == "details" && ($image_perms = func_check_image_storage_perms($file_upload_data, "M")) !== true) {
		# Check permissions
		$top_message = array(
			"content" => $image_perms['content'],
			"type" => "E"
		);

	} elseif ($mode == "details") {
	#
	# Modify manufacturer details
	#

		$orderby = intval($orderby);

		if ($orderby <= 0) {
		#
		# Generate the order by for manufacturer
		#
			$orderby = func_query_first_cell ("SELECT MAX(orderby) FROM $sql_tbl[manufacturers]") + 10;
		}

		if (!empty($manufacturerid)) {
			if (empty($manufacturer)) {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_empty");
				$top_message['type'] = 'E';
				func_header_location("manufacturers.php?manufacturerid=".$manufacturerid);

			} elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE manufacturer = '$manufacturer' AND manufacturerid != '$manufacturerid'")) {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_exist");
				$top_message['type'] = 'E';
				func_header_location("manufacturers.php?manufacturerid=".$manufacturerid);
  			}

		#
		# Update the manufacturer details
		#
			if (!empty($provider_condition))
			#
			# Check the permissions to update manufacturer details
			#
				$do_not_touch = (func_manufacturer_is_used($manufacturerid, $login) > 0);
			else
				$do_not_touch = false;

			$query_data = array(
				"url" => $url,
				"descr" => $descr
			);
			$query_data_lng = array(
				"manufacturerid" => $manufacturerid,
				"code" => $shop_language,
				"descr" => $descr
			);
			if (!$do_not_touch) {
				$query_data_lng['manufacturer'] = $manufacturer;
				if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE manufacturer = '$manufacturer'") == 0)
					$query_data['manufacturer'] = $manufacturer;
			}

			if ($shop_language != $config['default_admin_language']) {
				func_unset($query_data, "manufacturer", "descr");
			}

			if (empty($provider_condition)) {
				$query_data['avail'] = $avail;
				$query_data['orderby'] = $orderby;
			}

			func_array2update("manufacturers", $query_data, "manufacturerid='$manufacturerid' ".$provider_condition);
			func_array2insert("manufacturers_lng", $query_data_lng, true);

			$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_upd");

		}
		else {
		#
		# Add new manufacturer
		#
			if (empty($manufacturer)) {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_empty");
				$top_message['type'] = 'E';
				func_header_location("manufacturers.php?mode=add");
			} elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE manufacturer = '$manufacturer'")) {
				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_exist");
				$top_message['type'] = 'E';
				func_header_location("manufacturers.php?mode=add");
			} else {
				$query_data = array(
					"manufacturer" => $manufacturer,
					"avail" => $avail,
					"orderby" => $orderby,
					"provider" => $login,
					"descr" => $descr,
					"url" => $url
				);
				$manufacturerid = func_array2insert("manufacturers", $query_data);

				$query_data = array(
					"manufacturerid" => $manufacturerid,
					"code" => $shop_language,
					"manufacturer" => $manufacturer,
					"descr" => $descr
				);
				func_array2insert("manufacturers_lng", $query_data);

				$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_add");
			}
		}

		if (func_check_image_posted($file_upload_data, "M") && $manufacturerid > 0) {
			func_save_image($file_upload_data, "M", $manufacturerid);
		}

	}
	elseif ($mode == "delete" and !empty($to_delete) && is_array($to_delete)) {
	#
	# Delete selected manufacturers
	#
		$ids = func_query_column("SELECT manufacturerid FROM $sql_tbl[manufacturers] WHERE manufacturerid IN ('".implode("','", array_keys($to_delete))."') ".$provider_condition);
		if (!empty($ids)) {
			db_query("DELETE FROM $sql_tbl[manufacturers] WHERE manufacturerid IN ('".implode("','", $ids)."')");
			db_query("DELETE FROM $sql_tbl[manufacturers_lng] WHERE manufacturerid IN ('".implode("','", $ids)."')");
			db_query("UPDATE $sql_tbl[products] SET manufacturerid = 0 WHERE manufacturerid IN ('".implode("','", $ids)."')");
			func_delete_image($ids, "M");
			$top_message["content"] = func_get_langvar_by_name("msg_adm_manufacturer_del");
		}
	}
	elseif ($mode == "delete_image" && $manufacturerid) {
	#
	# Delete image of selected manufacturer
	#
		func_delete_image($manufacturerid, "M");
	}
	elseif ($mode == "update" and empty($provider_condition)) {
	#
	# Update manufacturers list
	#
		if (is_array($records)) {
			foreach ($records as $k=>$v) {
				$v["avail"] = (empty($v["avail"]) ? "N" : "Y");
				$v["orderby"] = intval($v["orderby"]);
				db_query("UPDATE $sql_tbl[manufacturers] SET avail='$v[avail]', orderby='$v[orderby]' WHERE manufacturerid='$k' $provider_condition");
			}
			$top_message["content"] = func_get_langvar_by_name("msg_adm_manufacturers_upd");
		}
	}


	$page_str = (!empty($page) ? "&page=$page" : "");

	func_header_location("manufacturers.php?manufacturerid=$manufacturerid".$page_str);
}



#
# Process the GET request
#

if ($mode == "add" or !empty($manufacturerid)) {
#
# Get the manufacturer data and display manufacturer details page
#
	$location[count($location)-1][1] = "manufacturers.php";

	if (!empty($manufacturerid)) {
		$manufacturer_data = func_query_first("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers_lng].manufacturerid = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[images_M].id = $sql_tbl[manufacturers].manufacturerid WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid'");

		if (empty($manufacturer_data)) {
			$top_message["content"] = func_get_langvar_by_name("msg_adm_err_manufacturer_not_exists");
			$top_message["type"] = "E";
			func_header_location("manufacturers.php");
		}
		else {
			$manufacturer_data["used_by_others"] = func_manufacturer_is_used($manufacturerid, $manufacturer_data["provider"]);
			$location[] = array($manufacturer_data["manufacturer"], "");
			$smarty->assign("manufacturer", $manufacturer_data);
			$smarty->assign("image", func_image_properties("M", $manufacturerid));
		}
	}
	else
		$location[] = array(func_get_langvar_by_name("lbl_add_manufacturer"), "");

	$smarty->assign("mode", "manufacturer_info");
}
else {
#
# Get and display the manufacturers list
#

	$total_items = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[manufacturers]");

	if ($total_items > 0) {

		#
		# Prepare the page navigation
		#
		$objects_per_page = $config["Manufacturers"]["manufacturers_per_page"];

		$total_nav_pages = ceil($total_items/$objects_per_page)+1;

		include $xcart_dir."/include/navigation.php";

		#
		# Get the manufacturers list
		#
		$manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, CONCAT($sql_tbl[customers].lastname,', ',$sql_tbl[customers].firstname) as provider_name, IF($sql_tbl[customers].login IS NULL,'','Y') as is_provider FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[manufacturers].provider=$sql_tbl[customers].login ORDER BY $sql_tbl[manufacturers].orderby, $sql_tbl[manufacturers].manufacturer LIMIT $first_page, $objects_per_page");

		if (is_array($manufacturers)) {
			foreach ($manufacturers as $k=>$v) {
				$manufacturers[$k]["products_count"] = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[products] WHERE manufacturerid='$v[manufacturerid]'");
				$manufacturers[$k]["used_by_others"] = func_manufacturer_is_used($v["manufacturerid"], $v["provider"]);
			}

			$smarty->assign("navigation_script","manufacturers.php?");
			$smarty->assign("manufacturers", $manufacturers);
			$smarty->assign("first_item", $first_page+1);
			$smarty->assign("last_item", min($first_page+$objects_per_page, $total_items));

		}

	}

	$smarty->assign("total_items",$total_items);

}

if (!empty($page))
	$smarty->assign("page", $page);

?>
