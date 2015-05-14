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
# $Id: file_edit.php,v 1.47.2.1 2006/07/31 06:24:54 svowl Exp $
#
# This script allows administrator to browse thought templates tree
# and edit files (these files must be writable for httpd daemon).
#

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);
$trusted_post_variables = array("filebody");

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files');

x_session_register("editor_mode");

if (empty($login) && $editor_mode!='editor') {
    func_header_location("error_message.php?access_denied&id=4");
}

$location[] = array(func_get_langvar_by_name("lbl_edit_templates"), "file_edit.php");

#
# Set-up root directory for templates editing or files in providers directory
#
$root_dir = $smarty->template_dir;

if ($mode=="preview" && $filename) {
#
# Preview template in _blank browser window
#
	if (!func_allowed_path($xcart_dir, $root_dir."/".$filename)) {
		func_header_location("error_message.php?access_denied&id=5");
	}

    include $xcart_dir."/include/categories.php";
    $smarty->assign("template",".$filename");
    $smarty->assign("use_default_css", $use_default_css==1);
    func_display("admin/preview.tpl",$smarty);
    exit;
}

$what_to_edit = "templates";
$action_script = "file_edit.php";

$smarty->assign("what_to_edit", $what_to_edit);
$smarty->assign("action_script", $action_script);

include $xcart_dir."/include/file_operations.php";

if(empty($dir)) {
#
# Obtain languages list for compiling facility
#

	$smarty->assign("languages", $avail_languages);
}

if (eregi("\.(css|conf|js)$", $file))
	$smarty->assign("nopreview","1");

$smarty->assign("opener", $opener);

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
