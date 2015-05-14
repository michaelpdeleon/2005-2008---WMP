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
# $Id: pages.php,v 1.6 2006/01/30 07:15:50 max Exp $
#
# This script show static page in customer zone

require "./auth.php";
require $xcart_dir."/include/categories.php";

x_load('files');

if ($active_modules["Manufacturers"])
    include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

$pages_dir = $smarty->template_dir."/pages/$store_language/";

if (isset($HTTP_GET_VARS["pageid"])) {
#
# Prepare data for editing
#
    $preview = ($mode=="preview" ? "" : "AND active='Y'");
    $page_data = func_query_first("SELECT * FROM $sql_tbl[pages] WHERE pageid='$pageid' $preview AND level='E'");

	if ($page_data["language"] != $store_language) {
		$page_data = func_query_first("SELECT * FROM $sql_tbl[pages] WHERE filename='$page_data[filename]' $preview AND level='E' AND language='$store_language'");
	}

    if ($page_data) {
        $filename = $pages_dir.$page_data["filename"];
		$page_content = func_file_get($filename, true);
        if ($page_content === false) {
            $page_content = func_get_langvar_by_name("lbl_page_not_found", array(), false, true);
        }
        $smarty->assign("page_data", $page_data);
        $smarty->assign("page_content", $page_content);

		$location[] = array($page_data["title"], "");
    }
    else {
        func_header_location("error_message.php?page_not_found");
    }

    $smarty->assign("main", "pages");
}

# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);
?>
