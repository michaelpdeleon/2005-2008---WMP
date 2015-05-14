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
# $Id: func.image.php,v 1.7.2.4 2006/07/12 04:13:07 svowl Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Construct path to directory of images of type $type
#
function func_image_dir($type) {
	global $xcart_dir;

	$dir = $xcart_dir."/images/".$type;
	if (!is_dir($dir) && file_exists($dir))
		unlink($dir);

	if (!file_exists($dir))
		func_mkdir($dir);

	return $dir;
}

#
# Get image file extension using mime type of image
#
function func_get_image_ext($mime_type) {
	static $corrected = array (
		"application/x-shockwave-flash" => "swf"
	);

	if (!empty($corrected[$mime_type]))
		return $corrected[$mime_type];

	if (!zerolen($mime_type)) {
		list($type, $subtype) = explode('/', $mime_type, 2);
		if (!strcmp($type, "image") && !zerolen($subtype))
			return $subtype;
	}

	return "img"; # unknown generic file extension
}

#
# Check uniqueness of image filename
#
function func_image_filename_is_unique($file, $type, $imageid=false) {
	global $config, $sql_tbl, $xcart_dir;

	if (empty($config['available_images'][$type]) || empty($config['setup_images'][$type])) {
		# ERROR: unknown or not aavailable image type
		return false;
	}

	$_table = $sql_tbl['images_'.$type];
	$_where = "filename='".addslashes($file)."'";
	if (!empty($imageid)) {
		# ignore ourself
		$_where .= " AND imageid<>'".addslashes($imageid)."'";
	}

	if (func_query_first_cell("SELECT COUNT(*) FROM ".$_table." WHERE ".$_where) > 0)
		return false;

	return !@file_exists(func_image_dir($type)."/".$file);
}

#
# Generate unique filename for image in directory defined for $type
# and corresponding database table
#
function func_image_gen_unique_filename($file_name, $type, $mime_type="image/jpg", $id=false, $imageid=false) {
	static $max_added_idx = 99999;
	static $last_max_idx = array();

	if (zerolen($file_name)) {
		# File name is empty
		$file_name = strtolower($type);
		if (!zerolen((string)$id))
			$file_name .= "-".$id."-".$imageid;

		$file_ext = func_get_image_ext($mime_type);

	} elseif (preg_match("/^(.+)\.([^\.]+)$/S", $file_name, $match)) {
		# Detect file extension
		$file_name = $match[1];
		$file_ext = $match[2];
	}

	$is_unique = func_image_filename_is_unique($file_name.".".$file_ext, $type, $imageid);

	if ($is_unique)
		return $file_name.".".$file_ext;

	# Generate unique name
	$idx = isset($last_max_idx[$type][$file_name]) ? $last_max_idx[$type][$file_name] : 1;
	$name_tmp = $file_name;
	$dest_dir = func_image_dir($type);
	do {
		$file_name = sprintf("%s-%02d", $name_tmp, $idx++);

		$is_unique = func_image_filename_is_unique($file_name.".".$file_ext, $type, $imageid);
	} while (!$is_unique && $idx < $max_added_idx);

	if (!$is_unique) {
		# ERROR: cannot generate unique name
		return false;
	}

	if ($idx > 2) {

		# Save last suffix
		if (!isset($last_max_idx[$type]))
			$last_max_idx[$type] = array();
		$last_max_idx[$type][$name_tmp] = $idx-1;
	}

	return $file_name.".".$file_ext;
}

#
# Move images of $type to the new location (generic function)
#
function func_move_images($type, $config_data) {
	global $sql_tbl, $config, $images_step, $str_out, $xcart_dir;

	if (zerolen($type, $config_data['location'])) {
		return false;
	}

	$image_table = $sql_tbl['images_'.$type];
	$count = func_query_first_cell("SELECT COUNT(*) FROM ".$image_table);
	if (!$count)
		return true; # success

	#
	# Transfer images by $images_step per pass
	#
	$move_functions = array (
		"FS" => "func_move_images_to_fs",
		"DB" => "func_move_images_to_db"
	);

	$move_func = $move_functions[$config_data['location']];

	$error = false;
	# $rec_no used for displaying dots
	for ($rec_no=0, $pos=0; $pos < $count && !$error; $pos+=$images_step) {
		$sd = db_query("SELECT * FROM ".$image_table." LIMIT $pos,$images_step");

		$error = $error || ($sd === false);
		if (!$sd || !function_exists($move_func))
			continue;
		
		$error = $error || !$move_func($sd, $type, $rec_no, $config_data);

		db_free_result($sd);
	}

	return !$error;
}

#
# Move images of $type to the filesystem
# Please use func_move_images() instead.
#
function func_move_images_to_fs($db_image_set, $type, &$rec_no, $config_data) {
	global $sql_tbl, $str_out, $xcart_dir;

	$dest_dir = func_image_dir($type);

	# Storing of image_path field for images stored in filesystem
	# is necessary for compatibility with data caching
	$update_query = "UPDATE ".$sql_tbl['images_'.$type]." SET image_path=?, filename=?, image='', md5=?, date=?, image_size=?, image_x=?, image_y=?, image_type=? WHERE imageid=?";

	$error = false;
	while ($v = db_fetch_array($db_image_set)) {
		if (zerolen($v["image"]) && (!is_url($v['image_path']) || $config_data['save_url'] != 'Y')) {
			# 1. URL images are NOT moving (if 'save_url' option is disabled)
			# 2. for empty "image" assume what image in filesystem already
			continue;
		}

		if (!empty($v['image_path']))
			$v['filename'] = basename($v['image_path']);

		$str_out .= "image #".$v['imageid']." (owner: ".$v['id'].")";

		$moved = false;
		$reason = '';
		if (is_url($v['image_path']) && $config_data['save_url'] == 'Y')
			$v['file_path'] = $v['image_path'];

		$file = func_store_image_fs($v, $type);
		if ($file === false) {
			$reason = 'cannot create file for the image';

		} else {
			$new_data = func_get_image_size($file);
			$image_path = func_relative_path($file);
			$str_out .= " (file: ".$image_path.") - ";

			$file_name = basename($file);
			$md5 = func_md5_file($file);

			if (empty($v['date']))
				$v['date'] = time();

			$update_params = array(
				$image_path,
				$file_name,
				$md5,
				$v['date']
			);
			$update_params = func_array_merge($update_params, $new_data);
			$update_params[] = $v['imageid'];

			$moved = db_exec($update_query, $update_params);

			$error = $error || !$moved;

			if (!$moved) {
				$reason = "cannot update database";
				unlink(func_realpath($file));
			}
		}

		$str_out .= ($moved ? "OK" : "Failed ($reason)")."\n";

		func_echo_dot($rec_no, 1, 100);
	}

	return !$error;
}

#
# Move images of $type to the database.
# Please use func_move_images() instead.
#
function func_move_images_to_db($db_image_set, $type, &$rec_no, $config_data) {
	global $config, $sql_tbl, $str_out;

	$update_query = "UPDATE ".$sql_tbl['images_'.$type]." SET image_path='', image=?, md5=?, date=?, image_size=?, image_x=?, image_y=?, image_type=? WHERE imageid=?";

	$src_dir = func_image_dir($type).DIRECTORY_SEPARATOR;

	$error = false;

	while (!$error && ($v = db_fetch_array($db_image_set))) {
		if (!zerolen($v['image']) || (is_url($v['image_path']) && $config_data['save_url'] != 'Y')) {
			# image in database already ?
			continue;
		}

		if (!empty($v['image_path']) && is_url($v['image_path'])) {
			$file = $fn = $v['image_path'];

		} elseif (!empty($v['image_path'])) {
			$file = $v['image_path'];
			$fn = func_relative_path($file);

		} else {
			$file = $src_dir.$v['filename'];
			$fn = func_relative_path($file);
		}

		$str_out .= $fn." (ID: ".$v['id'].") - ";

		$moved = false;
		$reason = '';

		$image = func_file_get($file, true);
		if ($image === false) {
			$reason = 'cannot open';
		}
		elseif (zerolen($image)) {
			$reason = 'empty image';
		}
		else {
			if (empty($v['date']))
				$v['date'] = time();

			$new_data = func_get_image_size($image, true);
			$update_params = array(
				$image,
				md5($image),
				$v['date']
			);
			$update_params = func_array_merge($update_params, $new_data);
			$update_params[] = $v['imageid'];

			$moved = db_exec($update_query, $update_params);

			$error = $error || !$moved;
			if (!$moved) {
				$reason = "cannot update database";
			}
		}

		if ($moved && !is_url($file)) {
			# finish transfer of image
			@unlink(func_realpath($file));
		}

		$str_out .= ($moved ? "OK" : "Failed ($reason)")."\n";

		func_echo_dot($rec_no, 1, 100);
	}

	return !$error;
}

#
# Check image permissions
#
function func_check_image_storage_perms($file_upload_data, $type = 'T', $get_message = true) {
	global $config, $xcart_dir;

	if (!func_check_image_posted($file_upload_data, $type))
		return true;

	return func_check_image_perms($type, $get_message);
}

#
# Check image type permissions
#
function func_check_image_perms($type, $get_message = true) {
	global $config, $xcart_dir;

	if (!isset($config['setup_images'][$type]) || $config['setup_images'][$type]['location'] == 'DB')
		return true;

	$path = func_image_dir($type);
	$arr = explode("/", substr($path, strlen($xcart_dir)+1));
	$suffix = $xcart_dir;

	foreach ($arr as $p) {
		$suffix .= DIRECTORY_SEPARATOR.$p;

		$return = array();
		if (!is_writable($suffix))
			$return[] = 'w';

		if (!is_readable($suffix))
			$return[] = 'r';

		if (count($return) > 0) {
			$return['path'] = $suffix;
			if ($get_message) {
				if (in_array("r", $return) && in_array("w", $return)) {
					$return['label'] = "msg_err_image_cannot_saved_both_perms";

				} elseif (in_array("r", $return)) {
					$return['label'] = "msg_err_image_cannot_saved_read_perms";

				} else {
					$return['label'] = "msg_err_image_cannot_saved_write_perms";
				}
				$return['content'] = func_get_langvar_by_name($return['label'], array("path" =>  $return['path']));
			}

			return $return;
		}
	}
	
	return true;
}

#
# Checking that posted image is exist
#
function func_check_image_posted($file_upload_data, $type = 'T') {
	global $config;

	$return = false;
	$config_data = $config['setup_images'][$type];

	$image_posted = $file_upload_data[$type];

	if (!func_allow_file($image_posted["file_path"], true))
		return false;

	if ($image_posted["source"] == "U") {
		if ($fd = func_fopen($image_posted["file_path"], "rb", true)) {
			fclose($fd);
			$return = true;
		}
	} else {
		$return = file_exists($image_posted["file_path"]);
	}

	if ($return) {
		$return = ($image_posted["file_size"] <= $config_data["size_limit"] || $config_data["size_limit"]=="0");
	}

	return $return;
}

#
# Prepare posted image for saving
#
function func_prepare_image($file_upload_data, $type = 'T', $id = 0) {
	global $config, $xcart_dir, $sql_tbl;

	if (empty($file_upload_data[$type]['file_path']) || empty($config['setup_images'][$type]) || !in_array($file_upload_data[$type]['source'], array("U","S","L"))) {
		# ERROR: incorrect value
		return false;
	}

	$image_data = $file_upload_data[$type];

	$config_data = $config['setup_images'][$type];

	$file_path = $image_data["file_path"];
	if (!is_url($file_path))
		$file_path = func_realpath($file_path);

	$image = func_file_get($file_path, true);
	if ($image === false)
		return false;

	$prepared = array(
		"image_size" => strlen($image),
		"md5" => md5($image),
		"filename" => $image_data['filename'],
		"image_type" => $image_data['image_type'],
		"image_x" => $image_data['image_x'],
		"image_y" => $image_data['image_y'],
	);

	if ($config_data["location"] == "FS") {
		$prepared['image_path'] = "";

		if (!is_url($file_path) || $config_data['save_url'] == 'Y') {

			$dest_file = func_image_dir($type);
			if (!zerolen($prepared['filename'])) {
				$dest_file .= "/".$prepared['filename'];
			}

			$prepared['image_path'] = func_store_image_fs($image_data, $type);

			if (zerolen($prepared['image_path']))
				return false;

			$prepared['filename'] = basename($prepared['image_path']);

			$path = func_relative_path($prepared['image_path'], $xcart_dir);
			if ($path !== false) {
				$prepared['image_path'] = $path;
			}

		} else {
			$prepared['image_path'] = $file_path;

		}
	}
	else {

		if (is_url($file_path) && $config_data['save_url'] != 'Y') {
			$prepared['image_path'] = $file_path;
		} else {
			$prepared['image'] = $image;
		}
		unset($image);
		if ($image_data["source"] == "L") {
			@unlink(func_realpath($file_path));
		}
	}

	return $prepared;
}

#
# Save uploaded/changed image
#
function func_save_image(&$file_upload_data, $type, $id, $added_data = array(), $_imageid = NULL) {
	global $sql_tbl, $config, $skip_image;

	$image_data = func_prepare_image($file_upload_data, $type, $id);
	if (empty($image_data) || empty($id))
		return false;

	if ($skip_image[$type] == 'Y') {
		if (!empty($file_upload_data[$type]['is_copied'])) {
			# Should delete image file
			@unlink($file_upload_data[$type][$file_path]);
		}
		unset($file_upload_data[$type]);
		return false;
	}

	$image_data['id'] = $id;
	$image_data['date'] = time();
	if (!empty($added_data)) {
		$image_data = func_array_merge($image_data, $added_data);
	}

	$image_data = func_addslashes($image_data);
	unset($file_upload_data[$type]);

	$_table = $sql_tbl['images_'.$type];

	if ($config['available_images'][$type] == 'U') {
		if (!empty($_imageid)) {
			$_old_id = func_query_first_cell("SELECT id FROM ".$_table." WHERE imageid = '$_imageid'");
			if (empty($_old_id) || $_old_id == $id)
				$image_data['imageid'] = $_imageid;
		}

		if (empty($image_data['imageid']))
			$image_data['imageid'] = func_query_first_cell("SELECT imageid FROM ".$_table." WHERE id = '$id'");

		db_query("DELETE FROM ".$_table." WHERE id = '$id'");
	}

	return func_array2insert('images_'.$type, $image_data);
}

#
# Store image in FS
# Return: path to the file or FALSE
#
function func_store_image_fs($image_data, $type) {
	$dest_dir = func_image_dir($type);

	if (isset($image_data['file_path'])) {
		# this is uploaded image
		# add some missing fields

		$image_data['id'] = false;
		$image_data['imageid'] = false;
		$image_data['image'] = func_file_get($image_data['file_path'],true);
	}

	# unique file location
	$file_name = func_image_gen_unique_filename(
		$image_data['filename'], $type, $image_data['image_type'],
		$image_data['id'], $image_data['imageid']);

	if ($file_name === false) {
		# ERROR: cannot continue
		return false;
	}

	$file = $dest_dir."/".$file_name;

	$fd = func_fopen($file, "wb", true);
	if ($fd === false) {
		# ERROR: cannot continue
		return false;
	}

	fwrite($fd, $image_data["image"]);
	fclose($fd);
	@chmod($file, 0666);

	if (!empty($image_data['is_copied'])) {
		# should present only in structure of uploaded image
		unlink(func_realpath($image_data['file_path']));
	}

	return $file;
}

function func_echo_dot(&$rec_no, $threshold_dot, $threshold_newline) {
	$rec_no ++;
	if ($threshold_dot==1 || ($rec_no % $threshold_dot) == 0) {
		echo ".";
		flush();
	}
	
	if ($threshold_newline==1 || ($rec_no % $threshold_newline) == 0) {
		echo "<br />\n";
		flush();
	}
}

#
# Get image properties
#
function func_image_properties($type, $id) {
	global $config, $sql_tbl;

	if (empty($config['available_images'][$type]) || empty($config['setup_images'][$type]))
		return false;

	return func_query_first("SELECT image_x, image_y, image_type, image_size FROM ".$sql_tbl['images_'.$type]." WHERE id = '$id'");
}

?>
