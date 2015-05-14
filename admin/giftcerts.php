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
# $Id: giftcerts.php,v 1.39 2006/03/03 07:01:35 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('order');

if (empty($mode)) $mode = "";

$location[] = array(func_get_langvar_by_name("lbl_gift_certificates"), "giftcerts.php");

if ($REQUEST_METHOD=="POST") {
	if ($mode == "add_gc" || $mode == "modify_gc" || $mode == "preview") {

		$fill_error = (empty($purchaser) || empty($recipient));

		$giftcert = array(
			'purchaser' => stripslashes($purchaser),
			'recipient' => stripslashes($recipient),
			'message' => stripslashes($message),
			'amount' => $amount,
			'debit' => $amount,
			'send_via' => $send_via,
			'tpl_file' => stripslashes($gc_template)
		);

		if ($send_via == "E") {
			#
			# Send via Email
			#
			$fill_error = ($fill_error || empty($recipient_email));

			$giftcert['recipient_email'] = $recipient_email;
		}
		else {
			#
			# Send via Postal Mail
			#
			$has_states = (func_query_first_cell("SELECT display_states FROM $sql_tbl[countries] WHERE code = '".$recipient_country."'") == 'Y');
			$fill_error = ($fill_error || empty($recipient_firstname) || empty($recipient_lastname) || empty($recipient_address) || empty($recipient_city) || empty($recipient_zipcode) || (empty($recipient_state) && $has_states) || empty($recipient_country) || (empty($recipient_county) && $has_states && $config["General"]["use_counties"] == "Y"));

			$giftcert['recipient_firstname'] = stripslashes($recipient_firstname);
			$giftcert['recipient_lastname'] = stripslashes($recipient_lastname);
			$giftcert['recipient_address'] = stripslashes($recipient_address);
			$giftcert['recipient_city'] = stripslashes($recipient_city);
			$giftcert['recipient_zipcode'] = $recipient_zipcode;
			$giftcert['recipient_county'] = $recipient_county;
			$giftcert['recipient_state'] = $recipient_state;
			$giftcert['recipient_country'] = $recipient_country;
			$giftcert['recipient_phone'] = $recipient_phone;
		}

		#
		# If gcindex is empty - add
		# overwise - update
		#
		if (!$fill_error) {

			if ($mode != 'preview') {
				$db_gc = $giftcert;
				foreach ($db_gc as $k=>$v) {
					$db_gc[$k] = addslashes($v);
				}
			}

			if ($mode == "add_gc") {

				$db_gc['gcid'] = $gcid = strtoupper(md5(uniqid(rand())));
				$db_gc['status'] = 'P';
				$db_gc['add_date'] = time();

				func_array2insert('giftcerts', $db_gc);

				$top_message["content"] = func_get_langvar_by_name("msg_adm_gc_add");
			}
			elseif ($mode == "preview") {
				if ($config["General"]["use_counties"] == "Y")
					$giftcert["recipient_countyname"] = func_get_county($recipient_county);
				$giftcert["recipient_statename"] = func_get_state($recipient_state, $recipient_country);
				$giftcert["recipient_countryname"] = func_get_country($recipient_country);
				$giftcert['gcid'] = $gcid;
				$smarty->assign("giftcerts", array($giftcert));

				header("Content-Type: text/html");
				header("Content-Disposition: inline; filename=giftcertificates.html");

				$_tmp_smarty_debug = $smarty->debugging;
				$smarty->debugging = false;

				func_display("modules/Gift_Certificates/gc_admin_print.tpl",$smarty);
				$smarty->debugging = $_tmp_smarty_debug;
				exit;
			}
			elseif ($gcid) {
				func_array2update('giftcerts', $db_gc, "gcid='$gcid'");
				$top_message["content"] = func_get_langvar_by_name("msg_adm_gc_upd");

			}

			func_header_location("giftcerts.php");
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("err_filling_form");
			$top_message["type"] = "E";
		}
	}
	elseif ($mode != 'print') {
		global $to_customer;
		$to_customer = $config['default_admin_language'];

		while (list ($key,$val)=each($HTTP_POST_VARS)) {
			if (strstr($key,"-")) {
				list ($field,$gcid) = split("-",$key);
				if ($field == "status") {
					$res = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");
					if ($val=="A" && $val!=$res["status"] && $res["send_via"]=="E") {
						func_send_gc($config["Company"]["orders_department"], $res);
					}
				}

				db_query("UPDATE $sql_tbl[giftcerts] SET $field='$val' WHERE gcid='$gcid'");
			}
		}

		$top_message["content"] = func_get_langvar_by_name("msg_adm_gcs_upd");
		func_header_location("giftcerts.php");
	}
}

if ($mode=="delete") {
	#
	# Delete gift certificate
	#
	db_query("DELETE FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");
	$top_message["content"] = func_get_langvar_by_name("msg_adm_gcs_del");
	func_header_location("giftcerts.php");
}

if ($mode == "add_gc" || $mode == "modify_gc") {
	include $xcart_dir."/include/countries.php";
	include $xcart_dir."/include/states.php";
	if ($config["General"]["use_counties"] == "Y")
		include $xcart_dir."/include/counties.php";

	$giftcert = func_query_first("SELECT * FROM $sql_tbl[giftcerts] where gcid='".@$gcid."'");
	if ($giftcert["send_via"] != "E") {
		if ($config["General"]["use_counties"] == "Y")
			$giftcert["recipient_countyname"] = func_get_county($giftcert["recipient_county"]);
		$giftcert["recipient_statename"] = func_get_state($giftcert["recipient_state"], $giftcert["recipient_country"]);
		$giftcert["recipient_countryname"] = func_get_country($giftcert["recipient_country"]);
	}

	$smarty->assign("giftcert",$giftcert);
	$gc_readonly = ($mode == "modify_gc" && $giftcert["status"]!="P"?"Y":"");

	if (!$gc_readonly) {
		$smarty->assign('gc_templates', func_gc_get_templates($smarty->template_dir));
	}
}
elseif ($mode == 'print') {
	$giftcerts = false;

	if (!empty($gcids) && is_array($gcids)) {
		$tpl_cond = (!empty($tpl_file) ? " AND tpl_file='$tpl_file'" : '');
		$giftcerts = func_query("SELECT *, add_date+'".$config["Appearance"]["timezone_offset"]."' as add_date FROM $sql_tbl[giftcerts] WHERE send_via<>'E' AND gcid IN ('".implode("','", array_keys($gcids))."') ".$tpl_cond);
	}

	if (empty($giftcerts) || !is_array($giftcerts)) {
		$top_message['type'] = 'W';
		$top_message['content'] = func_get_langvar_by_name("msg_adm_warn_gc_sel");
		func_header_location('giftcerts.php');
	}

	foreach ($giftcerts as $k=>$v) {
		if ($config["General"]["use_counties"] == "Y")
			$giftcerts[$k]["recipient_countyname"] = func_get_county($v["recipient_county"]);
		$giftcerts[$k]["recipient_statename"] = func_get_state($v["recipient_state"], $v["recipient_country"]);
		$giftcerts[$k]["recipient_countryname"] = func_get_country($v["recipient_country"]);
	}

	$smarty->assign("giftcerts",$giftcerts);

	header("Content-Type: text/html");
	header("Content-Disposition: inline; filename=giftcertificates.html");

	$_tmp_smarty_debug = $smarty->debugging;
	$smarty->debugging = false;

	if (!empty($tpl_file)) {
		$css_file = preg_replace('!\.tpl$!', '.css', $tpl_file);
		if ($css_file != $tpl_file) {
			$smarty->assign('css_file', $css_file);;
		}
	}

	func_display("modules/Gift_Certificates/gc_admin_print.tpl",$smarty);
	$smarty->debugging = $_tmp_smarty_debug;

	exit;
}
else {
	$expired_condition=($config["Gift_Certificates"]["gc_show_expired"]=="Y"?"":" and status!='E'");

	$giftcerts=func_query("SELECT *, add_date+'".$config["Appearance"]["timezone_offset"]."' as add_date FROM $sql_tbl[giftcerts] where 1 $expired_condition");

	if (is_array($giftcerts)) {
		foreach ($giftcerts as $k=>$v) {
			if (empty($v["orderid"]) && !empty($active_modules['RMA'])) {
				$return = func_query_first("SELECT * FROM $sql_tbl[returns] WHERE credit = '$v[gcid]'");
				if (!empty($return)) $giftcerts[$k]['return'] = $return;
			}

			if ($v["orderid"] == 0) continue;

			$giftcerts[$k] = func_array_merge($v, func_query_first("SELECT $sql_tbl[customers].login, $sql_tbl[customers].usertype FROM $sql_tbl[customers], $sql_tbl[orders] WHERE $sql_tbl[customers].login=$sql_tbl[orders].login AND $sql_tbl[orders].orderid='$v[orderid]'"));
		}

		$smarty->assign("giftcerts",$giftcerts);
	}
}

$smarty->assign("main","giftcerts");
$smarty->assign("gc_readonly", @$gc_readonly);

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
