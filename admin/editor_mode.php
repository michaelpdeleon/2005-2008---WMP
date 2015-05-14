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
# $Id: editor_mode.php,v 1.18.2.1 2006/07/31 06:24:54 svowl Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_session_register("editor_mode");

if (empty($login) && $editor_mode!='editor') {
    func_header_location("error_message.php?access_denied&id=2");
}
		
if ($HTTP_POST_VARS["editor_mode"] || $HTTP_GET_VARS["editor_mode"] || $HTTP_COOKIE_VARS["editor_mode"]) {
    func_header_location("error_message.php?access_denied&id=3");
}

$location[] = array(func_get_langvar_by_name("lbl_webmaster_mode"), "");

if ($REQUEST_METHOD == "POST") {

	if ($mode == "start_mode") 
		$editor_mode = "editor";
	elseif ($mode == "quit_mode")
		$editor_mode = "";
	
	x_session_save("editor_mode");
	
	func_header_location("editor_mode.php");

}

$smarty->assign("editor_mode_enabled", ($editor_mode?"editor_mode":""));
$smarty->assign("site_edit", $http_location);
$smarty->assign("main", "editor_mode");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
