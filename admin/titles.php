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
# $Id: titles.php,v 1.3 2006/01/11 06:55:58 mclap Exp $
#

define("IS_MULTILANGUAGE", 1);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice');

$location[] = array(func_get_langvar_by_name("lbl_titles_management"), "");

# Add title
if ($mode == "add" && !empty($add['title'])) {
	if (empty($add['orderby']))
		$add['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[titles]")+1;
	func_languages_alt_insert("title_".$id, $v['title'], $shop_language);
	func_array2insert("titles", $add);

# Update title(s)
} elseif ($mode == "update" && !empty($data)) {
	foreach ($data as $id => $v) {
		$v['active'] = $v['active'];
		func_languages_alt_insert("title_".$id, $v['title'], $shop_language);
		if ($shop_language != $config['default_admin_language']) {
			unset($v['title']);
		}
		func_array2update("titles", $v, "titleid = '$id'");
	}

# Delete title(s)
} elseif ($mode == "delete" && !empty($ids)) {
	$string = "titleid IN ('".implode("','", $ids)."')";
	db_query("DELETE FROM $sql_tbl[titles] WHERE ".$string);
	db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name IN ('title_".implode("','title_", $ids)."')");
}

if (!empty($mode)) {
	func_header_location("titles.php");
}

$titles = func_query("SELECT * FROM $sql_tbl[titles] ORDER BY orderby, title");
if (!empty($titles)) {
	foreach ($titles as $k => $v) {
		$name = func_get_languages_alt("title_".$v['titleid']);
		if (!empty($name))
			$titles[$k]['title'] = $name;
	}
	$smarty->assign("titles", $titles);
}

#
# Assign Smarty variables and show template
#
$smarty->assign("main","titles");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
