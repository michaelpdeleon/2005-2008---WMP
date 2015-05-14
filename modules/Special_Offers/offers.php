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
# $Id: offers.php,v 1.20.2.4 2006/10/11 12:42:29 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('backoffice','image');

if (empty($mode) || !in_array($mode, array("create","modify","delete","update","conditions","bonuses","status","promo")))
	$mode = "";

$available_types = array();

#
# Conditions:
# S - Customer orders a certain product or products 
# T - Cart subtotal exceeds a certain amount 
# M - Customer has a certain membership 
# B - Customer has a certain amount of bonus points 
# Z - Customer comes from a specific geographic location
$available_types['conditions'] = array('S','T','M','B','Z');

#
# Bonuses:
# D - Give a discount 
# B - Give bonus points
# S - Give free shipping
# N - Offer products for free
# M - Offer membership
$available_types['bonuses'] = array('D','B','S','N','M');


function func_get_default_values ($mode) {
	global $available_types;

	$items = array_flip($available_types[$mode]);
	foreach ($items as $k=>$v) {
		$items[$k] = '';
	}

	return $items;
}

function func_promo_update() {
	global $offerid, $offer_lng, $new_offer_lng, $file_upload_data, $offer_lng_code;
	global $sql_tbl;

	if (!is_array($offer_lng)) $offer_lng = array();

	if (func_check_image_posted($file_upload_data, "S")) {
		$code = "US";
		$id = $offerid;
		if (preg_match("/^(\w{2})(\d*)$/", $file_upload_data['S']['id'], $match)) {
			$code = $match[1];
			if (empty($id)) {
				$id = $match[2];
			}
		}

		func_save_image($file_upload_data, "S", $code.$offerid);
	}

	foreach ($offer_lng as $code=>$val) {
		$val['offerid'] = $offerid;
		$val['code'] = $code;

		func_array2insert('offers_lng', $val, true);
	}

	if (!empty($offer_lng)) {
		db_query("UPDATE $sql_tbl[offers] SET modified_time=UNIX_TIMESTAMP(NOW()) WHERE offerid='$offer[offerid]'");
	}
}

function func_check_update_param($paramid, $update_type, $param_type) {
	global $sql_tbl;

	if ($update_type === true) return true;

	if ($param_type === 'C') {
		$query = "SELECT $sql_tbl[offer_conditions].conditionid FROM $sql_tbl[offer_condition_params], $sql_tbl[offer_conditions] WHERE $sql_tbl[offer_condition_params].paramid='$paramid' AND $sql_tbl[offer_condition_params].conditionid=$sql_tbl[offer_conditions].conditionid AND $sql_tbl[offer_conditions].condition_type='$update_type'";
	}
	else {
		$query = "SELECT $sql_tbl[offer_bonuses].bonusid FROM $sql_tbl[offer_bonus_params], $sql_tbl[offer_bonuses] WHERE $sql_tbl[offer_bonus_params].paramid='$paramid' AND $sql_tbl[offer_bonus_params].bonusid=$sql_tbl[offer_bonuses].bonusid AND $sql_tbl[offer_bonuses].bonus_type='$update_type'";
	}

	$tmp = func_query_first($query);

	return !empty($tmp);
}

function func_update_bonuses_n_conditions($offerid, $mode, $last_item_type) {
	global $sql_tbl;
	global $HTTP_POST_VARS;

	if ($mode == 'conditions') {
		$data = $HTTP_POST_VARS['condition'];
		$short_mode = 'C';
		$next_mode = 'bonuses';
		$prev_mode = 'modify';

		$type_attr = 'condition_type';
		$id_attr = 'conditionid';
		$tbl_items = 'offer_conditions';
		$tbl_params = 'offer_condition_params';
		$tbl_memberships = "condition_memberships";
	}
	else if ($mode == 'bonuses') {
		$data = $HTTP_POST_VARS['bonus'];
		$short_mode = 'B';
		$next_mode = 'promo';
		$prev_mode = 'conditions';

		$type_attr = 'bonus_type';
		$id_attr = 'bonusid';
		$tbl_items = 'offer_bonuses';
		$tbl_params = 'offer_bonus_params';
		$tbl_memberships = "bonus_memberships";
	}
	else {
		# Should not occurs
		return;
	}

	$query_id_by_type = "SELECT $id_attr FROM ".$sql_tbl[$tbl_items]." WHERE offerid='%s' AND $type_attr='%s'";

	$param = $HTTP_POST_VARS['param'];
	$param_del = $HTTP_POST_VARS['param_del'];

	$offer_provider = func_query_first_cell("SELECT provider FROM $sql_tbl[offers] WHERE offerid='$offerid'");

	$update_type = $last_item_type;

	if (!empty($HTTP_POST_VARS['wzNext']) || !empty($HTTP_POST_VARS['wzBack'])) {
		$update_type = true; # update all conditions/bonuses
	}

	$default_data = func_get_default_values($mode);
	if (empty($data) || !is_array($data)) {
		$data = $default_data;
	}
	else {
		foreach ($default_data as $k=>$v) {
			if (empty($data[$k])) $data[$k] = $v;
		}
	}

	#
	# Collect information about new parameters of conditions/bonuses
	#
	$new_param = array();
	foreach($HTTP_POST_VARS as $k=>$v) {
		if (trim($v) == "") continue;

		if (preg_match('!^new_param_(.)_(.)!S', $k, $m)) {
			$ctype = $m[1];
			if ($update_type !== true && $ctype !== $update_type)
				continue;

			$new_param[] = array ('ctype' =>$ctype, 'id'=>$v, 'type'=>strtoupper($m[2]));

			if (empty($data)) $data = array();

			if (empty($data[$ctype])) {
				$data[$ctype] = array ();
				$data[$ctype]['amount_min'] = '0.00';
				$data[$ctype]['amount_max'] = '0.00';
			}
		}
	}

	#
	# Update attributes of condition/bonus
	#
	if (is_array($data) && !empty($data)) {

		foreach($HTTP_POST_VARS as $k=>$v) {
			if (trim($v) == "") continue;

			if (preg_match('!^item_cb_(.)!S', $k, $m)) {
				if (!empty($data[$m[1]]) || $m[1] == 'M') {
					$data[$m[1]]['avail'] = 'Y';
				}
			}
		}

		foreach($data as $type=>$v) {
			if (empty($v['avail'])) $v['avail'] = 'N';

			if ($update_type !== true && $update_type !== $type) {
				$data[$type]['skip'] = true;
				continue;
			}

			$v[$type_attr] = $type;
			$v['offerid'] = $offerid;
			$v['provider'] = addslashes($offer_provider);

			$memberships = array();
			if (isset($v['memberships'])) {
				$memberships = $v['memberships'];
				unset($v['memberships']);
			}

			$v['amount_min'] = func_convert_number($v['amount_min']);
			$v['amount_max'] = func_convert_number($v['amount_max']);

			# validate some fields
			if ($v['amount_min'] < 0)
				$v['amount_min'] = 0;
			if ($v['amount_max'] < 0)
				$v['amount_max'] = 0;

			$id = func_query_first_cell(sprintf($query_id_by_type, $offerid, $type));

			if ($id !== false) {
				func_array2update($tbl_items, $v, $id_attr."='$id'");
			}
			else {
				func_array2insert($tbl_items, $v);
				$id = db_insert_id();
			}

			db_query("DELETE FROM ".$sql_tbl[$tbl_memberships]." WHERE $id_attr = '$id'");
			if (!empty($memberships)) {
				foreach ($memberships as $_id) {
					db_query("INSERT INTO ".$sql_tbl[$tbl_memberships]." VALUES ('$id','$_id')");
				}
			}

			$v[$id_attr] = $id;
			$data[$type] = $v;
		}
	}

	#
	# Delete parameters of conditions/bonuses
	#
	if ($HTTP_POST_VARS['action'] == 'delete' && is_array($param_del) && !empty($param_del)) {
		$tmp_del_list = array_keys($param_del);

		if ($update_type !== true) {
			if (empty($update_type)) {
				$tmp_del_list = array();
			}
			else {
				foreach ($tmp_del_list as $k=>$pid) {
					if (!func_check_update_param($pid, $update_type, $short_mode)) {
						unset($tmp_del_list[$k]);
					}
				}
			}
		}

		if (!empty($tmp_del_list)) {
			db_query("DELETE FROM ".$sql_tbl[$tbl_params]." WHERE paramid IN ('".implode("','",$tmp_del_list)."')");
		}
	}

	#
	# Update condition/bonus parameters
	#
	if (is_array($param) && !empty($param)) {
		foreach($param as $pid=>$v) {
			if ($update_type !== true) {
				if (empty($update_type)) {
					continue;
				}

				if (!func_check_update_param($pid, $update_type, $short_mode))
					continue;
			}

			if (!isset($v['param_arg'])) $v['param_arg'] = '';

			func_array2update($tbl_params, $v, "paramid='$pid'");
		}
	}

	#
	# Add new parameters for condition/bonus
	#
	if (!empty($new_param)) {
		foreach($new_param as $v) {
			if (empty($v['ctype']) || empty($data[$v['ctype']]))
				continue;

			$cid = $data[$v['ctype']][$id_attr];
			$arg = '';
			if ($v['type'] == 'C') $arg = 'R';
			elseif ($short_mode == 'C' && $v['type'] == 'Z') $arg = 'B';

			db_query("INSERT INTO ".$sql_tbl[$tbl_params]." ($id_attr, param_type, param_id, param_arg, param_qnty) VALUES ('$cid','$v[type]','$v[id]','$arg',1)");
		}
	}

	$offer = func_query_first("SELECT * FROM $sql_tbl[offers] WHERE offerid='$offerid'");
	$offer['conditions'] = func_offer_get_conditions($offerid, "");
	$offer['bonuses'] = func_offer_get_bonuses($offerid, "");
	func_check_offer($offer);

	$url = 'offers.php?offerid='.$offerid;
	if (!empty($HTTP_POST_VARS['wzNext']) && $offer[$mode.'_valid']) {
		$url .= '&mode='.$next_mode;
	}
	else
	if (!empty($HTTP_POST_VARS['wzBack']) && $offer[$mode.'_valid']) {
		$url .= '&mode='.$prev_mode;
	}
	else {
		$url .= '&mode='.$mode;
		if (!empty($HTTP_POST_VARS['wzNext']) || !empty($HTTP_POST_VARS['wzBack'])) {
			$url .= '&fill_error=Y';
		}

		if (!empty($last_item_type))
			$url .= '&last_item_type='.$last_item_type;
	}

	func_header_location($url);
}

if ($REQUEST_METHOD=="POST" || ($mode == 'promo' && $action == 'delete_image')) {
	if (in_array($mode, array('conditions','bonuses','promo'))) {
		db_query("UPDATE $sql_tbl[offers] SET modified_time=UNIX_TIMESTAMP(NOW()) WHERE offerid='$offerid'");
	}

	if ($mode == "create" || $mode == "modify") {
		#
		# Create new offer or edit offer details
		#
		if (is_array($offer)) {
			$offer["name"] = @trim(stripslashes($offer["name"]));
			$offer["descr"] = @trim(stripslashes($offer["descr"]));
			$offer["start"] = mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
			$offer["end"] = mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
			if ($offer["avail"] != "Y") $offer["avail"] = "N";
			if ($offer["show_short_promo"] != "N") $offer["show_short_promo"] = "Y";

			if (!empty($offerid) && is_numeric($offerid)) {
				$offer["offerid"] = (int)$offerid;

				if ($action == "update") {
					if (empty($offer["name"])) {
						$offer["name"] = func_get_langvar_by_name('lbl_unnamed_offer_id', array('id'=>$offer["offerid"]));
					}
					db_query("UPDATE $sql_tbl[offers] SET offer_name='".addslashes($offer["name"])."', offer_start='$offer[start]', offer_end='$offer[end]', offer_avail='$offer[avail]', show_short_promo='$offer[show_short_promo]', modified_time=UNIX_TIMESTAMP(NOW()) WHERE offerid='$offer[offerid]'");
				}
				elseif ($action == "delete" && is_array($offer_lng_del) && !empty($offer_lng_del)) {
					foreach($offer_lng_del as $code=>$val) {
						db_query("DELETE FROM $sql_tbl[offers_lng] WHERE offerid='$offer[offerid]' AND code='$code'");
						func_delete_image($code.$offer['offerid'], "S");
					}
					db_query("UPDATE $sql_tbl[offers] SET modified_time=UNIX_TIMESTAMP(NOW()) WHERE offerid='$offer[offerid]'");
				}
			}
			else {
				db_query("INSERT INTO $sql_tbl[offers] (offer_name, offer_start, offer_end, offer_avail, provider, modified_time) VALUES ('".addslashes($offer["name"])."','$offer[start]', '$offer[end]', '$offer[avail]', '$login',UNIX_TIMESTAMP(NOW()))");
				$offer["offerid"] = db_insert_id();
				if (empty($offer["name"])) {
					$offer["name"] = func_get_langvar_by_name('lbl_unnamed_offer_id', array('id'=>$offer["offerid"]));
					db_query("UPDATE $sql_tbl[offers] SET offer_name='".addslashes($offer["name"])."', offer_start='$offer[start]', offer_end='$offer[end]', offer_avail='$offer[avail]' WHERE offerid='$offer[offerid]'");
				}
			}
		}

		$url = 'offers.php?offerid='.$offer['offerid'];

		if (!empty($wzNext)) {
			$url .= '&mode=status';
		}
		else
		if (!empty($wzBack)) {
			$url .= '&mode=promo';
		}
		else
			$url .= '&mode=modify';

		func_header_location($url);
	}
	#
	# Update promo blocks
	#
	elseif ($mode == "promo") {

		if ($action == 'delete_image' && !empty($img_del_code)) {
			func_delete_image($img_del_code, 'S');
			db_query("UPDATE $sql_tbl[offers] SET modified_time=UNIX_TIMESTAMP(NOW()) WHERE offerid='$offerid'");

		} elseif (($image_perms = func_check_image_storage_perms($file_upload_data, "S")) !== true) {
			# Check permissions
			$top_message = array(
				"content" => $image_perms['content'],
				"type" => "E"
			);

		} else {
			func_promo_update();
		}

		$url = 'offers.php?offerid='.$offerid;

		if (!empty($wzNext)) {
			$url .= '&mode=modify';
		}
		else
		if (!empty($wzBack)) {
			$url .= '&mode=bonuses';
		}
		else
			$url .= '&mode=promo&offer_lng_code='.$offer_lng_code;

		func_header_location($url);
	}
	#
	# Update offers statuses
	#
	elseif ($mode == "update") {
		$prov_cond = ($single_mode?"":" AND provider='$login'");
		if (is_array($posted_data))
		foreach($posted_data as $offerid=>$v) {
			$avail = empty($v["avail"])?"":"Y";
			db_query("UPDATE $sql_tbl[offers] SET offer_avail='$avail' WHERE offerid='$offerid'".$prov_cond);
		}
		func_header_location("offers.php");
	}
	#
	# Delete offers
	#
	elseif ($mode == "delete") {
		$prov_cond = ($single_mode?"":" AND provider='$login'");
		if (is_array($to_delete) && !empty($to_delete)) {
			$list = func_get_column("offerid", "SELECT offerid FROM $sql_tbl[offers] WHERE offerid IN ('".join("','",array_keys($to_delete))."')".$prov_cond);

			if (is_array($list)) {
				$offer_cond = "offerid IN ('".join("','",$list)."')";

				$conditions = func_get_column("conditionid", "SELECT conditionid FROM $sql_tbl[offer_conditions] WHERE offerid IN ('".join("','",$list)."')".$prov_cond);
				if (is_array($conditions))
					db_query("DELETE FROM $sql_tbl[offer_condition_params] WHERE conditionid IN ('".join("','",$conditions)."')");

				$bonuses = func_get_column("bonusid", "SELECT bonusid FROM $sql_tbl[offer_bonuses] WHERE offerid IN ('".join("','",$list)."')".$prov_cond);
				if (is_array($bonuses))
					db_query("DELETE FROM $sql_tbl[offer_bonus_params] WHERE bonusid IN ('".join("','",$bonuses)."')");

				db_query("DELETE FROM $sql_tbl[offer_conditions] WHERE ".$offer_cond);
				db_query("DELETE FROM $sql_tbl[offer_bonuses] WHERE ".$offer_cond);
				db_query("DELETE FROM $sql_tbl[offers_lng] WHERE ".$offer_cond);
				db_query("DELETE FROM $sql_tbl[offers] WHERE ".$offer_cond);
				func_delete_images("S", "SUBSTRING(id, 3) IN ('".join("','",$list)."')");

			}
		}
		func_header_location("offers.php");
	}
	#
	# Modify offer conditions/bonuses
	#
	elseif ($mode == "conditions" || $mode == "bonuses") {
		func_update_bonuses_n_conditions($offerid, $mode, $last_item_type);
	}
	elseif ($mode == "status") {
		$url = 'offers.php?offerid='.$offerid;

		if (!empty($wzBack)) {
			$url .= '&mode=modify';
		}
		else
			$url .= '&mode=status';

		func_header_location($url);
	}
}

$offer = "";

if ($mode == 'create' && empty($offerid)) {
	$offer["start"] = mktime(0,0,0);
	$offer["end"] = mktime(23,59,59);
	db_query("INSERT INTO $sql_tbl[offers] (offer_start, offer_end, offer_avail, provider, modified_time) VALUES ('$offer[start]', '$offer[end]', 'N', '$login',UNIX_TIMESTAMP(NOW()))");
	$offerid = db_insert_id();
	$offer["name"] = func_get_langvar_by_name('lbl_unnamed_offer_id', array('id'=>$offerid));
	db_query("UPDATE $sql_tbl[offers] SET offer_name='".addslashes($offer["name"])."' WHERE offerid='$offerid'");
	func_header_location("offers.php?mode=conditions&offerid=".$offerid);
}

if (!empty($offerid)) {
	$offerid = (int)$offerid;
	$prov_cond = ($single_mode?"":" AND provider='$login'");
	$offer = func_query_first("SELECT offerid, offer_name AS name, offer_start, offer_end, offer_avail AS avail, show_short_promo FROM $sql_tbl[offers] WHERE offerid='$offerid'".$prov_cond);
	if (empty($mode)) $mode = 'conditions';
}

if (empty($offer) && !empty($mode) && $mode != "create") {
	$mode = "";
}
else {
	if (!empty($offer)) {
		$offer['bonuses'] = func_offer_get_bonuses($offerid, $login, "");
		$offer['conditions'] = func_offer_get_conditions($offerid, $login, "");
		func_check_offer($offer);
	}

	$smarty->assign("offer", $offer);

	if ($mode == "conditions") {
		include_once $xcart_dir."/include/categories.php";

		$zones = func_query("SELECT * FROM $sql_tbl[zones]".($single_mode?"":" WHERE $sql_tbl[zones].provider='$login'"));
		$smarty->assign("zones", $zones);

		$conditions = func_get_default_values('conditions');

		if (!empty($offer['conditions']) && is_array($offer['conditions']))
		foreach ($offer['conditions'] as $condition) {
			$conditions[$condition['condition_type']] = $condition;
		}

		$tmp = func_get_memberships();
		if (empty($conditions['M']) && !empty($tmp)) {
			$memberships = array();
			if (!empty($tmp)) {
				foreach($tmp as $m) {
					$memberships[$m['membershipid']] = array('name'=>$m['membership'], 'selected'=>false);
				}
			}
			$conditions['M']['memberships'] = $memberships;
		}
		unset($tmp);

		foreach ($conditions as $type=>$data) {
			if (!isset($data['condition_type']))
				$conditions[$type]['condition_type'] = $type;
		}

		$smarty->assign("conditions", $conditions);
	}
	elseif ($mode == "bonuses") {
		include_once $xcart_dir."/include/categories.php";
		$bonuses = func_get_default_values('bonuses');

		if (!empty($offer['bonuses']) && is_array($offer['bonuses']))
		foreach ($offer['bonuses'] as $bonus) {
			$bonuses[$bonus['bonus_type']] = $bonus;
		}

		$tmp = func_get_memberships();
		if (empty($bonuses['M']) && !empty($tmp)) {
			$memberships = array();
			if (!empty($tmp)) {
				foreach($tmp as $m) {
					$memberships[$m['membershipid']] = array('name'=>$m['membership'], 'selected'=>false);
				}
			}
			$bonuses['M']['memberships'] = $memberships;
		}
		unset($tmp);

		foreach ($bonuses as $type=>$data) {
			if (!isset($data['bonus_type']))
				$bonuses[$type]['bonus_type'] = $type;
		}

		foreach ($bonuses as $k=>$v) {
			if (empty($v['bonusid']))
				$bonuses[$k]['bonusid'] = 'NEW_'.$k;
		}

		$smarty->assign("bonuses", $bonuses);
	}
	else {
		if (empty($offer_lng_code))
			$offer_lng_code = $config["default_customer_language"];

		$new_code = '';
		if (is_array($avail_languages)) {
			if (!empty($avail_languages)) {
				list($x) = $avail_languages;
				$new_code = $x['code']; # grab first code
			}

			foreach ($avail_languages as $v) {
				if ($v['code'] == $offer_lng_code) {
					$new_code = $offer_lng_code;
				}
			}
		}
		$offer_lng_code = $new_code;

		$offer_language = "";
		if (!empty($offer_lng_code)) {
			$offer_languages = func_query("SELECT offerid, code, promo_short, IF($sql_tbl[images_S].id IS NULL, '', '1') AS promo_short_img, promo_long FROM $sql_tbl[offers_lng] LEFT JOIN $sql_tbl[images_S] ON SUBSTRING($sql_tbl[images_S].id, 3) = '$offerid' WHERE offerid='$offerid'");

			if (is_array($offer_languages)) {
				foreach ($offer_languages as $ko=>$offer_lang) {
					#$offer_languages[$ko]['language'] = func_get_langvar_by_name('language_'.$offer_lang['code']);
					if ($offer_lang['code'] == $offer_lng_code)
						$offer_language = $offer_lang;
				}
			}
		}

		if (empty($offer_language)) {
			$offer_language['offerid'] = $offerid;
			$offer_language['code'] = $offer_lng_code;
			$offer_language['promo_short'] = '';
			$offer_language['promo_short_img'] = 0;
			$offer_language['promo_long'] = '';
		}

		$smarty->assign("offer_lng_code", $offer_lng_code);
		$smarty->assign("offer_lng", $offer_language);
		$smarty->assign("avail_languages", $avail_languages);
	}

	switch ($mode) {
		case 'modify':
			$location[] = array($offer['name'], 'offers.php?mode=modify&offerid='.$offerid);
			break;
		case 'create':
			$location[] = array(func_get_langvar_by_name('lbl_sp_create_new_offer'), 'offers.php?mode=create');
	}

	#
	# Define data for the navigation within section
	#

	$dialog_tools_data['left'][] = array("link" => 'offers.php', 'title' => func_get_langvar_by_name('lbl_sp_list_of_offers'));
	$dialog_tools_data['left'][] = array("link" => 'offers.php?mode=create', 'title' => func_get_langvar_by_name('lbl_sp_create_new_offer'));

	$cnt_cond = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[offer_conditions] WHERE offerid='$offerid'");
	$cnt_bons = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[offer_bonuses] WHERE offerid='$offerid'");

	$nav_data = "";
	$nav_data[] = array('mode'=>'conditions', 'title'=>func_get_langvar_by_name('lbl_sp_nav_conditions'));
	$nav_data[] = array('title'=>'+');
	$nav_data[] = array('mode'=>'bonuses', 'title'=>func_get_langvar_by_name('lbl_sp_nav_bonuses'));
	$nav_data[] = array('title'=>'+');
	$nav_data[] = array('mode'=>'promo', 'title'=>func_get_langvar_by_name('lbl_sp_nav_promotexts'));
	$nav_data[] = array('title'=>'=');
	$nav_data[] = array('mode'=>'modify', 'title'=>func_get_langvar_by_name('lbl_sp_nav_offer'));

	$smarty->assign("nav_data", $nav_data);

	$offername = func_query_first_cell("SELECT offer_name AS name FROM $sql_tbl[offers] WHERE offerid='$offerid'");
	$smarty->assign("offername", $offername);
	$smarty->assign("offerid", $offerid);

	if (!empty($last_item_type)) {
		$smarty->assign('last_item_type',$last_item_type);
	}
}

if ($mode == "" && empty($offer)) {
	$offers = func_query("SELECT *, offer_avail AS avail FROM $sql_tbl[offers]".($single_mode?"":" WHERE provider='$login'"));

	if (is_array($offers)) {
		foreach ($offers as $k=>$v) {
			$offers[$k]['bonuses'] = func_offer_get_bonuses($v['offerid'], $login, "");
			$offers[$k]['conditions'] = func_offer_get_conditions($v['offerid'], $login, "");
			func_check_offer($offers[$k]);
		}
	}

	$smarty->assign("offers", $offers);
}

$smarty->assign("mode", $mode);

if (!empty($fill_error)) {
	$smarty->assign('fill_error', $fill_error);
}

?>
