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
# $Id: popup_image.php,v 1.10.2.1 2006/05/19 07:09:54 max Exp $
#

require "./top.inc.php";
require "./init.php";

x_load('files');

#
# Check input data
#
if(!isset($config['setup_images'][$type])) {
	func_close_window();
}

# Get image(s)
$images = func_query("SELECT ".(($config['available_images'][$type] == "U") ? "id" : "imageid")." as id, image_path, image_x, image_y, image_size, alt FROM ".$sql_tbl['images_'.$type]." WHERE id = '$id' AND avail = 'Y' ORDER BY orderby");

if (empty($images)) {
	func_close_window();
}

$objects_per_page = 1;
$total_nav_pages = count($images)+1;
include $xcart_dir."/include/navigation.php";

if ($config["setup_images"][$type]["location"] == "FS") {
	foreach ($images as $k => $v) {
		if (!empty($v['image_path'])) {
			$images[$k]["url"] = func_get_image_url($id, $type, $v["image_path"]);
		}
	}
}

if (!empty($title))
	$smarty->assign("title", $title);

if (($type != "D" || $config['Detailed_Product_Images']['det_image_popup_js_based'] == 'Y') && count($images) > 1)
	$smarty->assign("js_selector", true);

$smarty->assign("href", "popup_image.php?type=$type&amp;id=$id&amp;title=".urlencode($title));
$smarty->assign("navigation_script", "popup_image.php?type=$type&amp;id=$id&amp;title=".urlencode($title));

$smarty->assign("images_count", count($images));
$smarty->assign("images", $images);
$smarty->assign("id", $id);
$smarty->assign("type", $type);

$smarty->assign("page", $page);

func_display("main/popup_image.tpl",$smarty);
?>
