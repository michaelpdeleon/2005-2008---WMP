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
# $Id: login.php,v 1.120.2.6 2006/08/09 13:27:12 max Exp $
#

include "../top.inc.php";

if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");

require $xcart_dir."/init.php";

switch ($redirect) {
	case "admin":
		$redirect_to = DIR_ADMIN;
		$current_type = 'A';
		break;

	case "provider":
		$redirect_to = DIR_PROVIDER;
		$current_type = 'P';
		break;

	case "partner":
		$redirect_to = DIR_PARTNER;
		$current_type = 'B';
		break;

    default:
		$redirect_to = DIR_CUSTOMER;
		$current_type = 'C';
}

require $xcart_dir."/include/nocookie_warning.php";

x_load('crypt','mail');

x_session_register("logout_user");
x_session_register("login");
x_session_register("login_type");
x_session_register("logged");
x_session_register("previous_login_date");

x_session_register("login_attempt");
x_session_register("cart");
x_session_register("intershipper_recalc");

x_session_register("merchant_password");

$merchant_password = "";

$login_error = false;

$redirect_to = $current_location.$redirect_to;

if ($REQUEST_METHOD == "POST") {
	$intershipper_recalc = "Y";
	x_session_register("identifiers",array());
	if ($mode == "login") {

		$username = $HTTP_POST_VARS["username"];
		$password = $HTTP_POST_VARS["password"];


		$user_data = func_query_first("SELECT * FROM $sql_tbl[customers] WHERE BINARY login='$username' AND usertype='$usertype' AND status='Y'");

		$allow_login = true;

		if ($usertype == 'A' || ($usertype == "P" && $active_modules["Simple_Mode"])) {
			$iplist = array_unique(split('[ ,]+', $admin_allowed_ip));
			$iplist = array_flip($iplist);
			unset($iplist[""]);
			$iplist = array_flip($iplist);
			if (count($iplist) > 0)
				$allow_login = in_array($REMOTE_ADDR, $iplist);
		}

		$right_password = text_decrypt($user_data["password"]);
		if (is_null($right_password)) {
			x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt password for the user ".$username, true);
		}

		if (!empty($user_data) && $password == $right_password && !empty($password) && $allow_login) {
			unset($right_password);
			$identifiers[$usertype] = array (
				'login' => $username,
				'login_type' => $usertype
			);

			if (!empty($active_modules['Simple_Mode'])) {
				if ($usertype == 'A') $identifiers['P'] = $identifiers[$usertype];
				if ($usertype == 'P') $identifiers['A'] = $identifiers[$usertype];
			}
#
# Success login
#
			x_session_register("login_change");
			if ($user_data["change_password"] == "Y") {
				$login_change["login"] = $user_data["login"];
				$login_change["login_type"] = $usertype;
				func_header_location($redirect_to."/change_password.php");
			}
			x_session_unregister("login_change");

			$login = $user_data["login"];  //$username;
			$login_type = $usertype;
			$logged = "";
			if ($usertype == "C") {
				if(!empty($active_modules['SnS_connector']))
					func_generate_sns_action("Login");
				x_session_register("login_redirect");
				$login_redirect = 1;
			}

#
# 1) generate $last_login by current timestamp and update database
# 2) insert entry into login history
#
			$tm = time();

			$previous_login_date = func_query_first_cell("SELECT last_login FROM $sql_tbl[customers] WHERE login='$login'");
			if ($previous_login_date == 0)
				$previous_login_date = $tm;

			db_query("UPDATE $sql_tbl[customers] SET last_login='$tm' WHERE login='$login'");
			db_query("REPLACE INTO $sql_tbl[login_history] (login, date_time, usertype, action, status, ip) VALUES ('$username','$tm','$usertype','login','success','$REMOTE_ADDR')");

#
# Set cookie with username if Greet visitor module enabled
#

			if (!empty($active_modules["Greet_Visitor"]) && $login_type == "C")
				include $xcart_dir."/modules/Greet_Visitor/set_cookie.php";

			$logout_user = false;
#
# If shopping cart is not empty then user is redirected to cart.php
# Default password alert
#
			if ($login_type == "A" || $login_type == "P") {
				$to_url = (!empty($active_modules["Simple_Mode"]) || $login_type == "A" ? $xcart_catalogs["admin"] : $xcart_catalogs["provider"])."/home.php";
				$current_area = $login_type;
				include $xcart_dir."/include/get_language.php";
			}

			$default_accounts = func_check_default_passwords($login);

			if (!empty($default_accounts)) {
				$current_area = $login_type;
				$txt_message = strip_tags(func_get_langvar_by_name("txt_your_password_warning_js",false,false,true));
				$txt_continue = strip_tags(func_get_langvar_by_name("lbl_continue",false,false,true));
				$txt_message_js = func_js_escape($txt_message);
				$javascript_message = <<<JS
<script language='JavaScript'>
	alert('$txt_message_js');
	self.location='$to_url';
</script>
$txt_message
<br /><br />
<a href="$to_url">$txt_continue</a>
JS;
			}
			elseif ($usertype == "A" || !empty($active_modules["Simple_Mode"])) {
				$default_accounts = func_check_default_passwords();
				if (!empty($default_accounts)) {
					$txt_message = strip_tags(func_get_langvar_by_name("txt_default_passwords_warning_js", array("accounts"=>implode(", ", $default_accounts)),false,true));
					$txt_continue = strip_tags(func_get_langvar_by_name("lbl_continue",false,false,true));
					$javascript_message = <<<JS
<script language='JavaScript'>
	alert('$txt_message');
	self.location='$to_url';
</script>
$txt_message
<br /><br />
<a href="$to_url">$txt_continue</a>
JS;
				}
			}

			if ($login_type == "C" && $user_data["cart"] && func_is_cart_empty($cart))
				$cart = unserialize($user_data["cart"]);

			if ($login_type == "C" || $login_type == "B") {

				# Redirect to saved URL
				x_session_register("remember_data");
				if ($is_remember == 'Y' && !empty($remember_data)) {
					func_header_location($remember_data['URL'], false);
				}

				if (!func_is_cart_empty($cart)) {

					# Redirect to cart page
					$login_redirect = false;
					if((strpos($HTTP_REFERER, "mode=auth") === false) && (strpos($HTTP_REFERER, "mode=checkout") === false)) {
						func_header_location($redirect_to."/cart.php", false);
					} else {
						func_header_location($redirect_to."/cart.php?mode=checkout", false);
					}

				} elseif (!empty($HTTP_REFERER)) {

					# Redirect to HTTP_REFERER
					if ((strncasecmp($HTTP_REFERER,$http_location,strlen($http_location))==0 || strncasecmp($HTTP_REFERER,$https_location,strlen($https_location))==0) &&
					strpos($HTTP_REFERER,"error_message.php")===false &&
					strpos($HTTP_REFERER,'secure_login.php')===false &&
					strpos($HTTP_REFERER,".php")!==false) {
						func_header_location($redirect_to.strrchr($HTTP_REFERER, "/"), false);
					}
				}
				func_header_location($redirect_to."/home.php", false);
			}

			if (($config["General"]["default_pwd"] == "Y") && !empty($javascript_message) && $admin_safe_mode == false) {
				x_session_save();
				echo $javascript_message;
				exit;
			}

			func_header_location($redirect_to."/home.php");
		
		} else {
#
# Login incorrect
#
			$login_status = "failure";
			unset($right_password);

			if (!$allow_login)
				$login_status = "restricted";

			$disabled = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE login='$username' AND usertype='$usertype' AND status<>'Y' AND status<>'A'");
			if ($disabled) {
				x_session_register('top_message');
				$top_message['type'] = 'I';
				$top_message['content'] = func_get_langvar_by_name('err_account_temporary_disabled');
				$login_status = 'disabled';
			}

			if (!func_query_first("SELECT login FROM $sql_tbl[login_history] WHERE login='$username' AND date_time='".time()."'"))
				db_query("REPLACE INTO $sql_tbl[login_history] (login, date_time, usertype, action, status, ip) VALUES ('$username','".time()."','$usertype','login','$login_status', '$REMOTE_ADDR')");

			if (($redirect == "admin" || (@$active_modules["Simple_Mode"] == "Y" && $redirect == "provider")) && $config['Email_Note']['eml_login_error'] == 'Y') {
#
# Send security alert to website admin
#
				@func_send_mail($config["Company"]["site_administrator"], "mail/login_error_subj.tpl", "mail/login_error.tpl", $config["Company"]["site_administrator"], true);

			}

#
# After 3 failures redirects to Recover password page
#
			$login_attempt++;
			if ($login_attempt >= 3) {
				$login_attempt = "";
				func_header_location($redirect_to."/help.php?section=Password_Recovery");
			}
			else
				func_header_location($redirect_to."/error_message.php?login_incorrect");
		}
	}
}


if ($mode == "logout") {
	x_session_register("identifiers",array());
	x_session_register("payment_cc_fields");
	$payment_cc_fields = array();
	func_unset($identifiers,$current_type);

	if (!empty($active_modules['Simple_Mode'])) {
		if ($current_type == 'A') func_unset($identifiers,'P');
		if ($current_type == 'P') func_unset($identifiers,'A');
	}
#
# Insert entry into login_history
#
	$utype = func_query_first_cell("SELECT usertype FROM $sql_tbl[customers] WHERE login = '$login'");
	if (!empty($active_modules['Simple_Mode']) && $utype == 'A')
		$utype = 'P';
	db_query("REPLACE INTO $sql_tbl[login_history] (login, date_time, usertype, action, status, ip) VALUES ('$login','".time()."','$utype','logout','success','$REMOTE_ADDR')");

	$old_login_type = $current_type;
	$login = "";
	$login_type = "";
	$cart = "";
	$access_status = "";
	$merchant_password = "";
	$logout_user = true;
	if ($current_type == 'A' || $current_type == 'P')
		func_ge_erase();

	x_session_unregister("hide_security_warning");
	x_session_register("login_redirect");
	$login_redirect = 1;
}

if ($old_login_type == 'C') {
	if (!empty($HTTP_REFERER) && (strncasecmp($HTTP_REFERER, $http_location, strlen($http_location)) == 0 || strncasecmp($HTTP_REFERER, $https_location, strlen($https_location)) == 0)) {
		if (strpos($HTTP_REFERER, "mode=order_message") === false &&
			strpos($HTTP_REFERER, "mode=wishlist") === false &&
			strpos($HTTP_REFERER, "bonuses.php") === false &&
			strpos($HTTP_REFERER, "returns.php") === false &&
			strpos($HTTP_REFERER, "orders.php") === false &&
			strpos($HTTP_REFERER, "giftreg_manage.php") === false &&
            strpos($HTTP_REFERER, "order.php") === false &&
			strpos($HTTP_REFERER, "register.php?mode=delete") === false &&
			strpos($HTTP_REFERER, "register.php?mode=update") === false) {
			func_header_location($redirect_to.strrchr($HTTP_REFERER, "/"), false);
		}
	}
}

func_header_location($redirect_to."/home.php", false);

?>
