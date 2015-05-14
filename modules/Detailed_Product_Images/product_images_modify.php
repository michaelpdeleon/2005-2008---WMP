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
# $Id: product_images_modify.php,v 1.30.2.1 2006/06/02 08:29:19 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('backoffice','product');

# Upload additional product image
if ($mode == "product_images") {

	$image_perms = func_check_image_storage_perms($file_upload_data, "D");
	if ($image_perms !== true) {
		$top_message["content"] = $image_perms['content']; 
		$top_message["type"] = "E";
		func_refresh("images");
	}

	$image_posted = func_check_image_posted($file_upload_data, "D");

	if ($image_posted) {
		$image_id = func_save_image($file_upload_data, "D", $productid, array("alt" => $alt));
		if ($geid && $fields['new_d_image'] == 'Y') {
			$data = func_query_first("SELECT * FROM $sql_tbl[images_D] WHERE id = '$productid' AND imageid = '$image_id'");
			unset($data['imageid']);
			$data = func_array_map("addslashes", $data);
			while($pid = func_ge_each($geid, 1, $productid)) {
				$id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$data[md5]'");
				if (!empty($id))
					func_delete_image($id, "D", true);
				$data['id'] = $pid;
				func_array2insert("images_D", $data);
			}
		}
		$top_message["content"] = func_get_langvar_by_name("msg_adm_product_images_add");
		$top_message["type"] = "I";

	}
	func_refresh("images");

# Update product image
} elseif ($mode == "update_availability" && !empty($image)) {

	foreach ($image as $key => $value) {
		func_array2update("images_D", $value, "imageid = '$key'");
		if($geid && $fields['d_image'][$key] == 'Y') {
			$data = func_query_first("SELECT * FROM $sql_tbl[images_D] WHERE imageid = '$key'");
			unset($data['imageid']);
			$data = func_array_map("addslashes", $data);
			while($pid = func_ge_each($geid, 1, $productid)) {
				$id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$data[md5]'");
				if (!empty($id))
					func_delete_image($id, "D", true);
				$data['id'] = $pid;
				func_array2insert("images_D", $data);
			}
		}
	}
	$top_message["content"] = func_get_langvar_by_name("msg_adm_product_images_upd");
	$top_message["type"] = "I";
	func_refresh("images");

# Delete product image
} elseif ($mode == "product_images_delete") {
	if (!empty($iids)) {
		foreach($iids as $imageid => $tmp) {
			$md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_D] WHERE imageid = '$imageid'");
			func_delete_image($imageid, "D", true);
			if ($geid && $fields['d_image'][$imageid] == 'Y') {
				while($pid = func_ge_each($geid, 1, $productid)) {
					$id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$md5'");
					if (!empty($id))
						func_delete_image($id, "D", true);
				}
			}
		}

		$top_message["content"] = func_get_langvar_by_name("msg_adm_product_images_del");
		$top_message["type"] = "I";
	}
	func_refresh("images");
}

?>
