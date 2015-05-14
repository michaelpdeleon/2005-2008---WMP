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
# $Id: import_users.php,v 1.13.2.2 2006/06/15 07:01:23 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','mail','user');

/******************************************************************************
Used cache format:
Memberships:
	data_type: 	M
	key:		<Membership name>
	value:		<Membership ID>
Users:
	data_type: 	U
	key:		<Login>
	value:		<Login | RESERVED>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == "define") {

	$import_specification['USERS'] = array(
		"script"		=> "/include/import_users.php",
		"tpls"			=> array(
			"main/import_option_password_crypt.tpl"),
		"permissions"	=> "A",
		"is_range"		=> $xcart_web_dir.DIR_ADMIN."/users.php?is_range",
		"export_sql"	=> "SELECT login FROM $sql_tbl[customers]",
		"table"			=> "customers",
		"key_field"		=> "login",
		"columns"		=> array(
			"login"					=> array(
				"required"	=> true),
			"usertype"				=> array(
				"required"	=> true,
				"type"		=> "E",
				"variants"	=> array("C","A","P","B")),
			"password"				=> array(
				"required"	=> true),
			"password_hint"			=> array(),
			"password_hint_answer"	=> array(),
			"b_title"				=> array(),
			"b_firstname"			=> array(),
			"b_lastname"			=> array(),
			"b_address"				=> array(),
			"b_address_2"			=> array(),
			"b_city"				=> array(),
			"b_county"				=> array(),
			"b_state"				=> array(),
			"b_country"				=> array(),
			"b_zipcode"				=> array(),
			"title"					=> array(),
			"firstname"				=> array(),
			"lastname"				=> array(),
			"company"				=> array(),
			"s_title"				=> array(),
			"s_firstname"			=> array(),
			"s_lastname"			=> array(),
			"s_address"				=> array(),
			"s_address"				=> array(),
			"s_city"				=> array(),
			"s_county"				=> array(),
			"s_state"				=> array(),
			"s_country"				=> array(),
			"s_zipcode"				=> array(),
			"email"					=> array(),
			"phone"					=> array(),
			"fax"					=> array(),
			"url"					=> array(),
			"status"				=> array(
				"type"		=> "E",
				"variants"	=> array("N","Y","Q","D","A")),
			"referer"				=> array(),
			"ssn"					=> array(),
			"language"				=> array(
				"type"		=> "C"),
			"cart"					=> array(),
			"change_password"		=> array(
				"type"		=> "B"),
			"parent"				=> array(),
			"pending_plan_id"		=> array(
				"type"		=> "N"),
			"activity"				=> array(
				"type"		=> "B"),
			"membership"			=> array(),
			"pending_membership"	=> array(),
			"tax_number"			=> array(),
			"tax_exempt"			=> array(
				"type"		=> "B"),
			"last_login"			=> array(
				"type"		=> "D"),
			"first_login"			=> array(
				"type"		=> "D")
		)
	);
}
elseif ($import_step == "process_row") {
	#
	# PROCESS ROW from import file
	#

	if ($login == $values['login'])
		return false;

	# Check login
	$tmp = func_import_get_cache("U", $values['login']);
	if (is_null($tmp)) {
		func_import_save_cache("U", $values['login']);
		if ($values['usertype'] == "P")
			func_import_save_cache("P", $values['login']);
	}

	# Check parent
	if (!empty($values['parent'])) {
		$_parent = func_import_get_cache("U", $values['parent']);
		if (is_null($_parent)) {
			$_parent = func_query_first_cell("SELECT login FROM $sql_tbl[customers] WHERE login = '".addslashes($values['parent'])."'");
			if (empty($_parent)) {
				$_parent = NULL;
			}
			else {
				func_import_save_cache("U", $values['parent'], $_parent);
			}
		}

		if (is_null($_parent) || ($action == "do" && empty($_parent))) {
			func_import_module_error("msg_err_import_log_message_29", array("login" => $values['parent']));
			return false;
		}
	}

	# Check membership
	$values["membershipid"] = false;
	if (!empty($values["membership"])) {
		$_membershipid = func_import_get_cache("M", $values["membership"]);
		if (empty($_membershipid)) {
			$_membershipid = func_detect_membership($values["membership"], $values['usertype']);
			if ($_membershipid == 0) {
				# Membership is specified but does not exist
				func_import_module_error("msg_err_import_log_message_5", array("membership"=>$values["membership"]));
				return false;
			}
			else {
				func_import_get_cache("M", $values["membership"], $_membershipid);
			}
		}

		if (!empty($_membershipid))
			$values["membershipid"] = $_membershipid;
	}

	# Check pending membership
	$values["pending_membershipid"] = false;
	if (!empty($values["pending_membership"])) {
		$_membershipid = func_import_get_cache("M", $values["pending_membership"]);
		if (empty($_membershipid)) {
			$_membershipid = func_detect_membership($values["pending_membership"], $values['usertype']);
			if ($_membershipid == 0) {
				# Membership is specified but does not exist
				func_import_module_error("msg_err_import_log_message_5", array("membership"=>$values["pending_membership"]));
				return false;
			}
			else {
				func_import_get_cache("M", $values["pending_membership"], $_membershipid);
			}
		}

		if (!empty($_membershipid))
			$values["pending_membershipid"] = $_membershipid;
	}

	# Check email
	if (!empty($values['email'])) {
		if (!func_check_email($values['email'])) {
			func_import_module_error("msg_err_import_log_message_28", array("email"=>$values["email"]));
			return false;
		}
	}

	# Check title
	foreach (array('title','s_title','b_title') as $k) {
		if (empty($values[$k])) continue;

		if (func_detect_title($values[$k]) === false) {
			func_import_module_error("msg_err_import_log_message_30",
				array("title" => $values[$k]));

			return false;
		}
	}

	$data_row[] = $values;
}
elseif ($import_step == "finalize") {
	#
	# FINALIZE rows processing: update database
	#

	# Drop old data
	if ($import_file["drop"][strtolower($section)] == "Y") {

		$users = db_query("SELECT login, usertype FROM $sql_tbl[customers] WHERE login != '$login'");
		if (!empty($users)) {
			while ($user = db_fetch_array($users)) {
				func_delete_profile($user['login'], $user['usertype'], false);
			}
		}

		$import_file["drop"][strtolower($section)] = "";
	}

	# Import users
	foreach ($data_row as $row) {

		if ($row['login'] == $login)
			continue;

		func_unset($row, "membership", "pending_membership");
		if ($import_file['crypt_password'] != 'Y') {
			$row['password'] = text_crypt($row['password']);
		}

		if (isset($row['b_address_2'])) {
			$row['b_address'] = trim($row['b_address']."\n".$row['b_address_2']);
			func_unset($row, "b_address_2");
		}
		if (isset($row['s_address_2'])) {
			$row['s_address'] = trim($row['s_address']."\n".$row['s_address_2']);
			func_unset($row, "s_address_2");
		}

		$data = func_addslashes($row);

		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE login = '$data[login]'") != 0) {
			# Update user
			unset($data['login']);
			func_array2update("customers", $data, "login = '".addslashes($row['login'])."'");
			$result[strtolower($section)]["updated"]++;

		}
		else {
			# Add user
			func_array2insert("customers", $data);
			$result[strtolower($section)]["added"]++;
		}

		func_import_save_cache("U", $data['login'], $data['login']);
		if ($data['usertype'] == "P")
			func_import_save_cache("P", $data['login'], $data['login']);

		echo ". ";
		func_flush();
	}
}
elseif ($import_step == "export") {
	# Export data

	while ($id = func_export_get_row($data)) {
		if (empty($id))
			continue;

		# Get data
		$row = func_query_first("SELECT $sql_tbl[customers].*, m.membership FROM $sql_tbl[customers] LEFT JOIN $sql_tbl[memberships] as m ON m.membershipid = $sql_tbl[customers].membershipid WHERE $sql_tbl[customers].login = '".addslashes($id)."'");
		if (empty($row))
			continue;

		$row['pending_membership'] = func_query_first_cell("SELECT membership FROM $sql_tbl[memberships] WHERE membershipid = '$row[pending_membershipid]'");

		func_unset($row, "membershipid", "pending_membershipid");

		list($row['b_address'], $row['b_address_2']) = explode("\n", $row['b_address'], 2);
		list($row['s_address'], $row['s_address_2']) = explode("\n", $row['s_address'], 2);

		# Write row
		if (!func_export_write_row($row))
			break;
	}
}

?>
