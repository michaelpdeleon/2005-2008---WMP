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
# $Id: users_tools.php,v 1.7 2006/01/11 06:55:58 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }


#
# Define data for the navigation within section
#
$dialog_tools_data = array();

$dialog_tools_data["left"][] = array("link" => "users.php", "title" => func_get_langvar_by_name("lbl_search_users"));

if (empty($active_modules["Simple_Mode"])) {
	$dialog_tools_data["left"][] = array("link" => "user_add.php?usertype=A", "title" => func_get_langvar_by_name("lbl_create_admin_profile"));
	
	$dialog_tools_data["left"][] = array("link" => "user_add.php?usertype=P", "title" => func_get_langvar_by_name("lbl_create_provider_profile"));
}
else {
	$dialog_tools_data["left"][] = array("link" => "user_add.php?usertype=P", "title" => func_get_langvar_by_name("lbl_create_admin_profile"));
}

$dialog_tools_data["left"][] = array("link" => "user_add.php?usertype=C", "title" => func_get_langvar_by_name("lbl_create_customer_profile"));

if (!empty($active_modules["XAffiliate"]))
	$dialog_tools_data["left"][] = array("link" => "user_add.php?usertype=B", "title" => func_get_langvar_by_name("lbl_create_partner_profile"));


$dialog_tools_data["right"][] = array("link" => "orders.php", "title" => func_get_langvar_by_name("lbl_orders"));

$dialog_tools_data["right"][] = array("link" => "memberships.php", "title" => func_get_langvar_by_name("lbl_membership_levels"));

$dialog_tools_data["right"][] = array("link" => "configuration.php?option=User_Profiles", "title" => func_get_langvar_by_name("option_title_User_Profiles"));

$is_admin_usertype = ($usertype == "A" || $usertype == "P" && !empty($active_modules['Simple_Mode']));
if (!$is_admin_usertype) {
	$target_area = false;
	if ($usertype == "C")
		$target_area = $xcart_catalogs['customer'];
	elseif ($usertype == "B")
		$target_area = $xcart_catalogs['partner'];
	elseif ($usertype == "P")
		$target_area = $xcart_catalogs['provider'];

	if ($target_area !== false) {
		$dialog_tools_data["right"][] = array("link" => $target_area."/home.php?operate_as_user=".$user, "title" => func_get_langvar_by_name("lbl_operate_as_user"));
	}
}

?>
