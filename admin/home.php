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
# $Id: home.php,v 1.33 2006/01/11 06:55:57 mclap Exp $
#

require "./auth.php";

if (!empty($login) && $user_account["flag"] != "FS") {

	include "./quick_menu.php";
	
	#
	# Define data for the navigation within section
	#
	$dialog_tools_data = array();

	$dialog_tools_data["left"][] = array("link" => "home.php?promo#menu", "title" => func_get_langvar_by_name("lbl_quick_menu"));
	
	if (!isset($promo)) {
		$dialog_tools_data["left"][] = array("link" => "#orders", "title" => func_get_langvar_by_name("lbl_last_orders_statistics"));
		$dialog_tools_data["left"][] = array("link" => "#topsellers", "title" => func_get_langvar_by_name("lbl_top_sellers"));

		$dialog_tools_data["right"][] = array("link" => "home.php?promo", "title" => func_get_langvar_by_name("lbl_quick_start"));
		$dialog_tools_data["right"][] = array("link" => "home.php?promo&display=news", "title" => func_get_langvar_by_name("lbl_new_features_in_xcart"), "style" => "hl");
	}
	else {
		$dialog_tools_data["left"][] = array("link" => "home.php?promo#qs", "title" => func_get_langvar_by_name("lbl_quick_start_text"));
		$dialog_tools_data["left"][] = array("link" => "home.php?promo&display=news", "title" => func_get_langvar_by_name("lbl_new_features_in_xcart"), "style" => "hl");

		$dialog_tools_data["right"][] = array("link" => "home.php", "title" => func_get_langvar_by_name("lbl_top_info"));
	}

	# Assign the section navigation data
	$smarty->assign("dialog_tools_data", $dialog_tools_data);


	if (isset($promo)) {
		if ($display == "news") {
			$location[] = array(func_get_langvar_by_name("lbl_new_features_in_xcart"), "");
			$smarty->assign("display", "news");
		}
		else
			$location[] = array(func_get_langvar_by_name("lbl_quick_start"), "");
		$smarty->assign("main", "promo");
	}
	else {
		include "./main.php";
		$smarty->assign("main","top_info");
	}
}
else
	$smarty->assign("main", "home");


# Assign the current location line
if (!empty($login))
	$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl", $smarty);
?>
