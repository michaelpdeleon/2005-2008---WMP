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
# $Id: user_modify.php,v 1.38.2.1 2006/04/19 13:29:18 max Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('mail','user');

define('USER_MODIFY', 1);

$location[] = array(func_get_langvar_by_name("lbl_users_management"), "users.php");

$_usertype = (($usertype == "P" && !empty($active_modules["Simple_Mode"])) ? "A" : $usertype);

$_loc_type = array (
	'A' => 'lbl_modify_admin_profile',
	'P' => 'lbl_modify_provider_profile',
	'C' => 'lbl_modify_customer_profile'
);

if (!empty($active_modules['XAffiliate'])) {
	$_loc_type['B'] = 'lbl_modify_partner_profile';
}

if (isset($_loc_type[$_usertype])) {
	$location[] = array(func_get_langvar_by_name($_loc_type[$_usertype]), "");

} elseif (!empty($_usertype)) {
	$top_message = array(
		"content" => func_get_langvar_by_name("txt_wrong_usertype_modify"),
		"type" => "E"
	);

	func_header_location("users.php");
}

include "./users_tools.php";

$smarty->assign("usertype_name", $usertypes[$usertype]);

#
# Update profile only
#
$mode = "update";

if ($REQUEST_METHOD=="POST")
	require $xcart_dir."/include/safe_mode.php";

if ($REQUEST_METHOD=="POST" && $HTTP_GET_VARS["usertype"] == 'B' && ($HTTP_POST_VARS['mode'] == 'approved' || $HTTP_POST_VARS['mode'] == 'declined')) {
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE usertype = '$HTTP_GET_VARS[usertype]' AND login = '$HTTP_GET_VARS[user]' AND status = 'Q'")) {
		$userinfo = func_userinfo($HTTP_GET_VARS["user"], $HTTP_GET_VARS["usertype"]);
		$mail_smarty->assign("userinfo", $userinfo);

		if ($HTTP_POST_VARS['mode'] == 'approved') {
			if ($config['XAffiliate']['eml_partner_approved'] == 'Y') {
				func_send_mail($userinfo["email"],
					"mail/partner_approved_subj.tpl",
					"mail/partner_approved.tpl",
					$config["Company"]["users_department"], false);
			}

			db_query("UPDATE $sql_tbl[customers] SET status = 'Y' WHERE usertype = '$HTTP_GET_VARS[usertype]' AND login = '$HTTP_GET_VARS[user]' AND status = 'Q'");
		}
		else {
            $mail_smarty->assign("reason", $reason);
			if ($config['XAffiliate']['eml_partner_declined'] == 'Y') {
				func_send_mail($userinfo["email"],
					"mail/partner_declined_subj.tpl",
					"mail/partner_declined.tpl",
					$config["Company"]["users_department"], false);
			}

			db_query("UPDATE $sql_tbl[customers] SET status = 'D' WHERE usertype = '$HTTP_GET_VARS[usertype]' AND login = '$HTTP_GET_VARS[user]' AND status = 'Q'");
		}
	}

	func_header_location("user_modify.php?user=".$HTTP_GET_VARS["user"]."&usertype=".$HTTP_GET_VARS["usertype"]);
}

$login_ = $login;
$login_type_ = $login_type;

$login = $HTTP_GET_VARS["user"];
$login_type = $HTTP_GET_VARS["usertype"];

#
# Where to forward <form action
#
$smarty->assign("register_script_name",(($config["Security"]["use_https_login"]=="Y")?$xcart_catalogs_secure['admin']."/":"")."user_modify.php");

require $xcart_dir."/include/register.php";

$tpldir = ($login_type=="A"?"admin":($login_type=="P"?"provider":($login_type=="C"?"customer":"partner")));

if ($active_modules["Simple_Mode"] && ($usertype=="A" || $usertype=="P"))
    $tpldir = "admin";

# Display the 'Activity' input box for admin, provider or partner
if (in_array($usertype, array("A", "P", "B")))
	$smarty->assign("display_activity_box", "Y");

$smarty->assign("main", "user_profile");
$smarty->assign("tpldir", $tpldir);

$login = $login_;
$login_type = $login_type_;

x_session_save();

if (!empty($page))
	$smarty->assign("navigation_page", $page);

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
