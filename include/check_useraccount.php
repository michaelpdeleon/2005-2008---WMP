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
# $Id: check_useraccount.php,v 1.61 2006/03/29 09:21:21 max Exp $
#
# This script authenticates user (session variables "login" and "login_type")
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }
 
x_session_register("login");
x_session_register("login_type");
x_session_register("identifiers", array());

if (!is_array($identifiers)) $identifiers = array();

if (!empty($HTTP_GET_VARS['operate_as_user']) && (!empty($identifiers['A']) || !empty($identifiers['P']) && !empty($active_modules['Simple_Mode']))) {
	# operate as user
	$tmp_type = func_query_first_cell("SELECT usertype FROM $sql_tbl[customers] WHERE login='$operate_as_user'");
	if (!empty($tmp_type) && !($tmp_type == "A" || $tmp_type == "P" && !empty($active_modules['Simple_Mode']))) {
		$identifiers[$tmp_type] = array (
			'login' => $operate_as_user,
			'login_type' => $tmp_type
		);
	}
}

if (defined('AREA_TYPE')) {
	if (empty($identifiers[AREA_TYPE]) && !empty($active_modules['Simple_Mode']) && !empty($login)) {
		# grant additional rights when Simple_Mode is turned ON to other logged-in users
		if (strpos('AP', $login_type) !== FALSE && strpos('AP', AREA_TYPE) !== FALSE){
			# provider became admin
			if (!isset($identifiers['A'])) $identifiers['A'] = $identifiers['P'];

			# admin get access to the provider area
			if (!isset($identifiers['P'])) $identifiers['P'] = $identifiers['A'];
		}
	}

	if (!empty($identifiers[AREA_TYPE])) {
		$login = $identifiers[AREA_TYPE]['login'];
		$login_type = (empty($active_modules['Simple_Mode']) ? $identifiers[AREA_TYPE]['login_type'] : AREA_TYPE);
	} else {
		$login = '';
		$login_type = '';
	}
}

if (!empty($login)) {
	$__tmp = func_query_first_cell("SELECT status FROM $sql_tbl[customers] WHERE login='$login'");
	if ($__tmp != 'Y' && $__tmp != 'A') {
		func_unset($identifiers,$login_type);

		if (!empty($active_modules['Simple_Mode'])) {
			if ($login_type == 'A') func_unset($identifiers,'P');
			if ($login_type == 'P') func_unset($identifiers,'A');
		}

		$login = $login_type = '';
	}
}

x_session_register("logged");
if($current_area == 'A' || ($current_area == 'P' && $active_modules["Simple_Mode"])) {
    x_session_register("merchant_password");
}

$is_merchant_password = '';


if (($current_area == 'A' || $current_area == 'P') && !defined("IS_IMAGE_SELECTION")) {
#
# $file_upload_data service array initialization
#
	x_session_register("file_upload_data");
	if (!empty($file_upload_data)) {
		foreach ($file_upload_data as $k => $v) {
			if (!isset($config['available_images'][$k])) {
				unset($file_upload_data[$k]);
			} elseif (isset($v['file_path'])) {
				if ($v['is_redirect']) {
					unset($file_upload_data[$k]);
				} else {
					$file_upload_data[$k]['is_redirect'] = true;
				}
			} elseif(!empty($v) && is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if (!isset($v2['file_path']))
						continue;
					if ($v2['is_redirect']) {
						unset($file_upload_data[$k][$k2]);
					} else {
						$file_upload_data[$k][$k2]['is_redirect'] = true;
					}
				}
			}
		}
	}

}

if ($login_type!=$current_area && !empty($login)) {
	$logged=$login;
	$login="";
}
elseif ($login_type==$current_area && !empty($logged)) {
        $login=$logged;
        $logged="";
}

if ($login) {
	$user_account = func_query_first("SELECT $sql_tbl[customers].login, $sql_tbl[customers].usertype, $sql_tbl[customers].membershipid, $sql_tbl[customers].title, $sql_tbl[customers].firstname, $sql_tbl[customers].lastname, $sql_tbl[customers].s_country, $sql_tbl[memberships].membership, $sql_tbl[memberships].flag FROM $sql_tbl[customers] LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid WHERE login='$login' AND status <> 'Q'");
	if (empty($user_account)) {
		$login="";
		$login_type="";
	} elseif($current_area == 'A' || ($current_area == 'P' && $active_modules["Simple_Mode"])) {
		if(($config['mpassword'] != md5($merchant_password) && $merchant_password) || (!$merchant_password)) {
			$merchant_password = '';
		} else {
			$is_merchant_password = 'Y';
		}
	}

	if (!empty($login) && $current_area == 'C' && func_is_anonymous($login)) {
		$smarty->assign("anonymous_login", ($anonymous_login = 1));
	}
}

if ($active_modules['Users_online'] && !defined('IS_ROBOT')) {
	include $xcart_dir."/modules/Users_online/registered_user.php";
	include $xcart_dir."/modules/Users_online/users_online.php";
}
	
#
# Remember visitor for a long time period
#
if ($remember_user && $remember_user_days > 0) {
	x_session_register("remember_login", false);
	x_session_register("remember_data");

	$remember_key = $XCART_SESSION_NAME.$current_area."_remember";

	if (!empty($login)) {

		# Check remember data
		if (!empty($remember_data) && $remember_data['cnt']-- <= 0) {
			$remember_data = false;
		}

		# Set login as cookie's remember key
		if (empty($HTTP_COOKIE_VARS[$remember_key])) {
			setcookie($remember_key, $login, time()+86400*$remember_user_days, "/", "$xcart_http_host", 0);
			setcookie($remember_key, $login, time()+86400*$remember_user_days, "/", "$xcart_https_host", 0);
		}

	} elseif (zerolen($remember_login) && !empty($HTTP_COOKIE_VARS[$remember_key])) {

		# Check remember key
		$remember_login = $HTTP_COOKIE_VARS[$remember_key];
		if (empty($remember_login) || !is_string($remember_login) || !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE login = '$remember_login' AND usertype = '$current_area'")) {
			$remember_login = false;

		} else {
			$smarty->assign("remember_login", $remember_login);
		}
	}
}

x_session_save();

$smarty->assign("is_merchant_password", $is_merchant_password);
$smarty->assign("login",$login);
$smarty->assign("usertype",$current_area);
$mail_smarty->assign("usertype",$current_area);

?>
