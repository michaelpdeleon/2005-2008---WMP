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
# $Id: image_selection.php,v 1.41.2.1 2006/06/23 07:14:17 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','files');

x_session_register("file_upload_data");

$service_fields = array("file_path", "source", "image_x", "image_y", "image_size", "image_type", "dir_upload", "id", "type", "date", "filename");

if (!isset($config['available_images'][$type]) || empty($type)) {
	func_close_window();
}

$config_data = $config['setup_images'][$type];
$userfiles_dir = func_get_files_location().DIRECTORY_SEPARATOR;

#
# POST method
#
if ($REQUEST_METHOD == "POST") {

	$data = array();
	$data["is_copied"] = false; # file is not a copy and should not deleted

	switch($source) {
	case "S": # server path (user's files)
		$newpath = trim($newpath);
		if (!zerolen($newpath)) {
			$data["file_path"] = $userfiles_dir.$newpath;
		}
		break;
	case "U": # URL
		$fileurl = trim($fileurl);
		if (!zerolen($fileurl)) {
			if (strpos($fileurl, "/") === 0) {
				$fileurl = $http_location.$fileurl;
			} elseif (!is_url($fileurl)) {
				$fileurl = "http://".$fileurl;
			}

			$data["file_path"] = $fileurl;
		}
		break;
	case "L": # uploaded file
		if (zerolen($userfile)) break;

		if (func_is_image_userfile($userfile, $userfile_size, $userfile_type)) {
			$data["is_copied"] = true; # can be deleted
			$data["filename"] = basename($HTTP_POST_FILES['userfile']['name']);
			$userfile = func_move_uploaded_file("userfile");
			$data["file_path"] = $userfile;
		}
	}

	if (isset($data["file_path"]) && !func_is_allowed_file($data["file_path"])) {
		# cannot accept this file
		if ($data["is_copied"])
			unlink($data["file_path"]);

		unset($data["file_path"]);
	}

	if (!isset($data["file_path"]) || zerolen($data["file_path"])) {
		# No file is selected
		echo "<script>window.close();</script>";
		exit;
	}

	list(
		$data["file_size"],
		$data["image_x"],
		$data["image_y"],
		$data["image_type"]) = func_get_image_size($data["file_path"]);

	if ($data["file_size"] == 0) {
		# Ignore non readable or zero-sized
		if ($data["is_copied"])
			unlink($data["file_path"]);

		$data["file_path"] = "";
		$data["is_copied"] = false;
	}

	if (!isset($data["filename"])) {
		$data["filename"] = basename($data['file_path']);
	}

	$data["source"] = $source;
	$data["id"] = $id;
	$data["type"] = $type;
	$data["date"] = time();

	$file_upload_data[$type] = $data;

	x_session_save();

	$image_data = array(
		"image_x" => $data['image_x'],
		"image_y" => $data['image_y'],
		"image_type" => $data['image_type'],
		"image_size" => $data['file_size']
	);
	$smarty->assign("image", $image_data);
	$alt = func_display("main/image_property.tpl", $smarty, false);
	echo "<script type=\"text/javascript\">
<!--
if (window.opener.document.getElementById('".$imgid."')) {
	window.opener.document.getElementById('".$imgid."').src = '".$xcart_web_dir."/image.php?type=".$type."&id=".$id."&tmp=".time()."';
	window.opener.document.getElementById('".$imgid."').alt = \"".str_replace(array("\n","\r",'"'), array("\\n","",'\"'), $alt)."\";

} else if (window.opener.document.getElementById('".$imgid."_0')) {
	var cnt = 0;
	while (window.opener.document.getElementById('".$imgid."_'+cnt)) {
		window.opener.document.getElementById('".$imgid."_'+cnt).src = '".$xcart_web_dir."/image.php?type=".$type."&id=".$id."&tmp=".time()."';
		cnt++;
	}
}

if (window.opener.document.getElementById('".$imgid."_text')) {
	window.opener.document.getElementById('".$imgid."_text').style.display = '';
	var cnt;
	for (cnt = 1; true; cnt++) {
		if (!window.opener.document.getElementById('".$imgid."_text'+cnt))
			break;
		window.opener.document.getElementById('".$imgid."_text'+cnt).style.display = '';
	}
}

if (window.opener.document.getElementById('skip_image_".$type."')) {
	window.opener.document.getElementById('skip_image_".$type."').value = '';
} else if (window.opener.document.getElementById('skip_image_".$type."_".$id."')) {
	window.opener.document.getElementById('skip_image_".$type."_".$id."').value = '';
}

if (window.opener.document.getElementById('".$imgid."_reset'))
	window.opener.document.getElementById('".$imgid."_reset').style.display = '';

if (window.opener.document.getElementById('".$imgid."_onunload'))
	window.opener.document.getElementById('".$imgid."_onunload').value = 'Y';

window.close();
-->
</script>";
	exit;
}

$_table = $sql_tbl["images_".$type];
$_field = $config['available_images'][$type] == 'U' ? "id" : "imageid";

$smarty->assign("type", $type);
$smarty->assign("imgid", $imgid);
$smarty->assign("id", $id);
$smarty->assign("parent_window", $parent_window);
$smarty->assign("config_data", $config_data);

$smarty->assign("upload_max_filesize", ($config['setup_images'][$type]['location'] == 'DB') ? func_get_max_upload_size() : ini_get("upload_max_filesize"));

func_display("main/popup_image_selection.tpl",$smarty);

?>
