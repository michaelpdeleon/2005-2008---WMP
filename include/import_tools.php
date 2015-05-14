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
# $Id: import_tools.php,v 1.5.2.1 2006/04/19 13:50:02 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (!defined('IMPORT_DIALOG_TOOLS')) {
#
# Define data for the navigation within section
#
	define('IMPORT_DIALOG_TOOLS', 1);

	$dialog_tools_data = array(
		"left" => array(
			array("link" => "import.php", "title" => func_get_langvar_by_name("lbl_import_data")),
			array("link" => "import.php?mode=export", "title" => func_get_langvar_by_name("lbl_export_data"))
	));

	if (!empty($active_modules['Import_3x_4x']) && (AREA_TYPE == 'P' || (AREA_TYPE == 'A' && !empty($active_modules['Simple_Mode'])))) {
		$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_PROVIDER."/import_3x_4x.php", "title" => func_get_langvar_by_name("lbl_3x_4x_import"));
	}

	if(!empty($active_modules["Froogle"]))
		$dialog_tools_data['left'][] = array("link" => "froogle.php", "title" => func_get_langvar_by_name("lbl_froogle_export"));

	if (AREA_TYPE == 'A')
		$dialog_tools_data["right"][] = array("link" => "db_backup.php", "title" => func_get_langvar_by_name("lbl_db_backup_restore"));


	# Assign the section navigation data
	$smarty->assign("dialog_tools_data", $dialog_tools_data);

}

?>
