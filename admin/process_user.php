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
# $Id: process_user.php,v 1.51.2.1 2006/07/31 06:38:42 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('category','export','mail','user');

if ($REQUEST_METHOD == "POST") {
	# Export some user(s)
	if ($mode == 'export' && !empty($user)) {
		func_export_range_save("USERS", array_keys($user));
		$top_message['content'] = func_get_langvar_by_name("lbl_export_users_add");
		$top_message['type'] = 'I';
		func_header_location("import.php?mode=export");
	}
	elseif ($mode == "group_operation") {
		$change_statement = array();

		if (!empty($op_change_password)) {
			# Require to change password at next log in
			$change_statement[] = "change_password='Y'";
		}

		if (!empty($op_change_status)) {
			# Enable/suspend accounts
			$change_statement[] = "status='".($op_change_status==='N'?'N':'Y')."'";
		}

		if (!empty($op_change_activity)) {
			# Enable/suspend accounts
			$change_statement[] = "activity='".($op_change_activity==='N'?'N':'Y')."'";
		}

		$operation_ok = false;

		if (!empty($change_statement)) {
			$change_statement = "SET ".implode(', ', $change_statement);

			$recount_providers = array();
			if ($for_users == "A") {
				#
				# For all found users
				#
				if (x_session_is_registered("users_search_condition")) {
					x_session_register("users_search_condition");
					x_session_unregister("users_search_condition");

					db_query("UPDATE $sql_tbl[customers] $change_statement $users_search_condition AND login<>'$login'");
					$operation_ok = true;

					if (!empty($op_change_activity)) {
						# (usertype='P' OR usertype='A')
						$_providers_condition = " AND (usertype='P' ".(!empty($active_modules['Simple_Mode'])?"OR usertype='A'":"").") ";
						$_tmp = func_query("SELECT login FROM $sql_tbl[customers] $users_search_condition $_providers_condition AND login<>'$login'");
						if (!empty($_tmp) && is_array($_tmp)) {
							foreach ($_tmp as $k=>$v) {
								$recount_providers[] = addslashes($v['login']);
							}
						}
					}
				}
			}
			else {
				#
				# For selected users only
				#
				if (is_array($user)) {
					foreach ($user as $k=>$v)
						$to_update[] = "'$k'";

					if (!empty($op_change_activity))
						$recount_providers = array_keys($user);

					$to_update = implode(",", $to_update);
					db_query("UPDATE $sql_tbl[customers] $change_statement WHERE login IN ($to_update)");
					$operation_ok = true;
				}
			}
		}

		if ($operation_ok) {
			$messages = array();
			if (!empty($op_change_password))
				$messages[] = func_get_langvar_by_name('msg_adm_require_to_change_password');

			if (!empty($op_change_status)) {
				$messages[] = func_get_langvar_by_name($op_change_status==='N'?'msg_adm_accounts_login_suspended':'msg_adm_accounts_login_enabled');
			}

			if (!empty($op_change_activity) && !empty($recount_providers)) {
				$messages[] = func_get_langvar_by_name($op_change_activity==='N'?'msg_adm_accounts_activity_disabled':'msg_adm_accounts_activity_enabled');
				$p_categories = db_query("SELECT $sql_tbl[products_categories].categoryid FROM $sql_tbl[products], $sql_tbl[products_categories] WHERE $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products].provider IN ('".implode("','",$recount_providers)."') GROUP BY $sql_tbl[products_categories].categoryid");
				if ($p_categories) {
					$cats = array();
					while ($row = db_fetch_array($p_categories)) {
						$cats[] = $row['categoryid'];

						if (count($cats) >= 100) {
							func_recalc_product_count(func_get_category_parents($cats));
							$cats = array();
						}
					}
					db_free_result($p_categories);
				}
			}

			if (!empty($messages)) {
				$top_message["content"] = implode('<hr width="20%" size="1" noshade="noshade">', $messages);
			}
		}

		func_header_location("users.php?mode=search".(!empty($pagestr)?$pagestr:""));
	}

	if ($mode == "delete") {
		#
		# Request to delete user profile
		#
		x_session_register("users_to_delete");

		if ($confirmed == "Y") {
			#
			# If request is confirmed
			#
			require $xcart_dir."/include/safe_mode.php";

			if (is_array($users_to_delete["user"])) {
				foreach ($users_to_delete["user"] as $user=>$v) {
					#
					# Delete user from database
					#
					$usertype = func_query_first_cell("SELECT usertype FROM $sql_tbl[customers] WHERE login='".addslashes($user)."'");
					if (empty($usertype))
						continue;

					$olduser_info = func_userinfo($user,$usertype);
					$to_customer = $olduser_info["language"];
					func_delete_profile($user, $usertype);

					#
					# Send mail notifications to customer department and signed customer
					$mail_smarty->assign("userinfo",$olduser_info);

					#
					# Send mail to registered user
					#
					$anonymous_user = func_is_anonymous($olduser_info["login"]);

					if (!$anonymous_user && $config['Email_Note']['eml_profile_deleted'] == 'Y')
						func_send_mail($olduser_info["email"], "mail/profile_deleted_subj.tpl", "mail/profile_deleted.tpl", $config["Company"]["users_department"], false);

					#
					# Send mail to customers department
					#
					if (!$anonymous_user && $config['Email_Note']['eml_profile_deleted_admin'] == 'Y')
						func_send_mail($config["Company"]["users_department"], "mail/profile_admin_deleted_subj.tpl", "mail/profile_admin_deleted.tpl", $olduser_info["email"], true);

				}

				#
				# Prepare the message
				#
				$top_message["content"] = func_get_langvar_by_name("msg_adm_users_del");
				$top_message["type"] = "I";
			}
			else {
				#
				# If no selected users display the warning
				#
				$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_users_sel");
				$top_message["type"] = "W";
			}
		}
		else {
			$users_to_delete["user"] = $user;
			$users_to_delete["pagestr"] = $pagestr;
			func_header_location("process_user.php?mode=delete");
		}
	}

	x_session_unregister("users_to_delete");
	func_header_location("users.php?mode=search".(!empty($pagestr)?$pagestr:""));
}

if ($mode == "delete") {
	#
	# Prepare for deleting users profiles
	#
	x_session_register("users_to_delete");

	if (is_array($users_to_delete["user"])) {
		$location[] = array(func_get_langvar_by_name("lbl_users_management"), "users.php");
		$location[] = array(func_get_langvar_by_name("lbl_delete_users"), "");

		foreach ($users_to_delete["user"] as $k=>$v) {
			$condition[] = "login='".addslashes($k)."'";
		}

		$search_condition = implode(" OR ", $condition);

		$users = func_query("SELECT * FROM $sql_tbl[customers] WHERE $search_condition ORDER BY login DESC, lastname DESC, firstname DESC");

		if (is_array($users)) {
			foreach ($users as $k=>$v) {
				list($users[$k]["b_address"], $users[$k]["b_address_2"]) = explode("\n", $users[$k]["b_address"]);
				list($users[$k]["s_address"], $users[$k]["s_address_2"]) = explode("\n", $users[$k]["s_address"]);
				$users[$k]["s_statename"] = func_get_state($v["s_state"], $v["s_country"]);
				$users[$k]["b_statename"] = func_get_state($v["b_state"], $v["b_country"]);
				$users[$k]["s_countryname"] = func_get_country($v["s_country"]);
				$users[$k]["b_countryname"] = func_get_country($v["b_country"]);
				if ($config["General"]["use_counties"] == "Y") {
					$users[$k]["s_countyname"] = func_get_county($v["s_county"]);
					$users[$k]["b_countyname"] = func_get_county($v["b_county"]);
				}
			}
		}

		$smarty->assign("users", $users);
		$smarty->assign("usertypes",$usertypes);
		if (!empty($users_to_delete["pagestr"]))
			$smarty->assign("pagestr", $users_to_delete["pagestr"]);

		$smarty->assign("main","user_delete_confirmation");

		include "./users_tools.php";

		# Assign the current location line
		$smarty->assign("location", $location);

		# Assign the section navigation data
		$smarty->assign("dialog_tools_data", $dialog_tools_data);

		@include $xcart_dir."/modules/gold_display.php";
		func_display("admin/home.tpl",$smarty);
		exit;
	}
	else {
		#
		# If no selected users display the warning
		#
		$top_message["content"] = func_get_langvar_by_name("msg_adm_warn_users_sel");
		$top_message["type"] = "W";
	}
}

func_header_location("users.php?mode=search".(!empty($users_to_delete["pagestr"])?$users_to_delete["pagestr"]:""));

?>
