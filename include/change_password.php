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
# $Id: change_password.php,v 1.12 2006/01/11 06:55:58 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','user');

x_session_register("login");
x_session_register("login_change");

if ($REQUEST_METHOD=="GET") {
	if ($mode == "updated") {
		$smarty->assign("mode", $mode);
	}
	elseif (!isset($login_change["login"]) && empty($login)) {
		$top_message["content"] = func_get_langvar_by_name("txt_chpass_login");
		func_header_location("home.php");
	}
	else {
		if (isset($login_change["login"])) {
			$xlogin = $login_change["login"];
			$xlogin_type = $login_change["login_type"];
		}
		else {
			$xlogin = $login;
			$xlogin_type = $login_type;
		}
	}
	$smarty->assign("username", $xlogin);
}
else if ($REQUEST_METHOD=="POST") {
	if (isset($login_change["login"])) {
		$xlogin = $login_change["login"];
		$xlogin_type = $login_change["login_type"];
	}
	else {
		$xlogin = $login;
		$xlogin_type = $login_type;
		if ($xlogin_type == 'A' && !empty($active_modules['Simple_Mode'])) {
			$xlogin_type = 'P';
		}
	}

	$smarty->assign("username", $xlogin);
	$userinfo = func_userinfo($xlogin,$xlogin_type,true);
	$smarty->assign("old_password", $old_password);
	$smarty->assign("new_password", $new_password);
	$smarty->assign("confirm_password", $confirm_password);

	if ($userinfo["password"] != $old_password) {
		$top_message["content"] = func_get_langvar_by_name("txt_chpass_wrong");
		$top_message["type"] = 'E';
	}
	elseif ($new_password != $confirm_password) {
		$top_message["content"] = func_get_langvar_by_name("txt_chpass_match");
		$top_message["type"] = 'E';
	}
	elseif ($new_password == $userinfo["password"]) {
		$top_message["content"] = func_get_langvar_by_name("txt_chpass_another");
		$top_message["type"] = 'E';
	}
	else {
		$count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[old_passwords] WHERE login='".addslashes($xlogin)."' AND password='".addslashes(md5($new_password))."'");
		if ($count > 0) {
			$top_message["content"] = func_get_langvar_by_name("txt_chpass_another");
			$top_message["type"] = 'E';
		}
		else {
			$count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[old_passwords] WHERE login='".addslashes($xlogin)."' AND password='".addslashes(md5($old_password))."'");
			if ($count<1)
				db_query("INSERT INTO $sql_tbl[old_passwords] (login,password) VALUES ('".addslashes($xlogin)."','".addslashes(md5($old_password))."')");

			db_query("UPDATE $sql_tbl[customers] SET password='".addslashes(text_crypt($new_password))."', change_password='N' WHERE login='".addslashes($xlogin)."'");
			if (isset($login_change["login"])) {
				$login = $login_change["login"];
				$login_type = $login_change["login_type"];
			}

			x_session_unregister("login_change");
			$top_message["content"] = func_get_langvar_by_name("txt_chpass_changed");
			func_header_location("home.php");
		}
	}

	func_header_location("change_password.php");
}

$location[] = array(func_get_langvar_by_name("lbl_chpass"), "");

?>
