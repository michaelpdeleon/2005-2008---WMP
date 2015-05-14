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
# $Id: navigation.php,v 1.17.2.1 2006/04/27 12:43:32 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (empty($page)) $page=1;

if ($page >= $total_nav_pages)
	$page = $total_nav_pages-1;

$first_page = $objects_per_page*($page-1);

#
# $total_super_pages - how much groups of pages exists
#
$total_super_pages = (($total_nav_pages-1) / ($config["Appearance"]["max_nav_pages"] ? $config["Appearance"]["max_nav_pages"] : 1));

#
# $current_super_page - current group of pages
#
$current_super_page = ceil($page / $config["Appearance"]["max_nav_pages"]);

#
# $start_page - start page number in the list of navigation pages
#
$start_page = $config["Appearance"]["max_nav_pages"] * ($current_super_page - 1);

#
# $total_pages - the maximum number of pages to display in the navigation bar
# plus $start_page
$total_pages = ($total_nav_pages>$config["Appearance"]["max_nav_pages"] ? $config["Appearance"]["max_nav_pages"]+1 : $total_nav_pages) + $start_page;

#
# Cut off redundant pages from the tail of navigation bar
#
if ($total_pages > $total_nav_pages)
	$total_pages = $total_nav_pages;
	
if ($page > 1 and $page >= $total_pages) {
	$page = $total_pages - 1;
	$first_page = $objects_per_page*($page-1);
}

if ($first_page < 0)
	$first_page = 0;

$smarty->assign("navigation_page", $page);
$smarty->assign("total_pages", $total_pages);
$smarty->assign("total_super_pages", $total_super_pages);
$smarty->assign("current_super_page", $current_super_page);
$smarty->assign("start_page", $start_page + 1);
?>
