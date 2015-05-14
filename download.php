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
# $Id: download.php,v 1.12.2.4 2006/07/12 10:17:29 svowl Exp $
#
# This module adds support for downloading electronicaly distributed goods
#

@set_time_limit(2700);

require	"./auth.php";

if (empty($active_modules["Egoods"]))
	func_header_location("error_message.php?access_denied&id=64");

x_load('files');

if ($HTTP_GET_VARS["action"] != "get") {
	#
	# Prepare the appearing of download page
	#
	include	$xcart_dir."/include/categories.php";

	if ($active_modules["Manufacturers"])
		include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

	if ($id) {
		$productid = func_query_first_cell("SELECT productid FROM $sql_tbl[download_keys] WHERE download_key = '$id'");

		if ($productid) {
			$product_data = func_query_first("SELECT * FROM $sql_tbl[products] WHERE productid='$productid'");
			if($active_modules["Extra_Fields"]) {
				$extra_fields_provider=$product_data["provider"];
				include $xcart_dir."/modules/Extra_Fields/extra_fields.php";
			}

			$distribution = $product_data['distribution'];
			$provider = $product_data['provider'];

			if (!is_url($distribution)) {
				if (!empty($provider) && !$single_mode) {
					$provider_flag = func_query_first_cell("SELECT $sql_tbl[memberships].flag FROM $sql_tbl[customers], $sql_tbl[memberships] WHERE $sql_tbl[customers].login = '$provider' AND $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid");
					if ($provider_flag == 'RP')
						$single_mode = true;
				}

				if (empty($provider) || $single_mode || !empty($active_modules['Simple_Mode']))
					$distribution = $files_dir_name.$distribution;
				else
					$distribution = $files_dir_name."/$provider".$distribution;

				$size = @filesize($distribution);
			}
			else {
				$fp = @fopen($distribution, "rb");
				for ($size=0, $string = @fread($fp, 8192); strlen($string) != 0; $string = @fread($fp, 8192)) {
					$size += strlen($string);
				}
				@fclose($fp);
			}

			$product_data['length'] = number_format($size, 0, '', ' ');

			$smarty->assign("product", $product_data);
			$smarty->assign("url", $xcart_catalogs['customer']."/download.php?".$QUERY_STRING."&action=get");
		}

	}

	$location[] = array(func_get_langvar_by_name("lbl_download"), "");

	$smarty->assign("main", "download");

	# Assign the current location line
	$smarty->assign("location", $location);

	func_display("customer/home.tpl",$smarty);
	exit;
}

if (empty($id)) exit();

$chunk_size = 100*1024;  # 100 Kb

$query = "SELECT * FROM $sql_tbl[download_keys] WHERE download_key = '$id'";
$res = func_query_first($query);

# If there is corresponding key in database and not expired
if ((count($res) > 0) AND ($res['expires'] > time())) {
	# check if there is valid distribution for this product
	$productid = $res['productid'];

	$result = func_query_first("SELECT distribution, product, provider FROM $sql_tbl[products] WHERE productid = '$productid'");

	$distribution = $result['distribution'];
	$provider = $result['provider'];

	if (!is_url($distribution)) {

		if (!empty($provider) && !$single_mode) {
			$provider_flag = func_query_first_cell("SELECT $sql_tbl[memberships].flag FROM $sql_tbl[customers], $sql_tbl[memberships] WHERE $sql_tbl[customers].login = '$provider' AND $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid");
			if ($provider_flag == 'RP')
				$single_mode = true;
		}

		if (empty($provider) || $single_mode || !empty($active_modules['Simple_Mode']))
			$distribution = $files_dir_name.$distribution;
		else
			$distribution = $files_dir_name."/$provider".$distribution;

		$remote_file = false;
		$fd = func_fopen($distribution, "rb");
	}
	else {
		$remote_file = true;
		$fd = fopen($distribution, "rb");
	}

	if ($fd) {

		$fname = basename($distribution);

		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$fname\"");

		if (!$remote_file) {
			$size = filesize($distribution);
			header("Content-length: $size");
		}

		fpassthru($fd);

		fclose ($fd);
	}
	else {
		# If no such distributive
		$smarty->assign("product", $result['product']);

		# Assign the current location line
		$smarty->assign("location", $location);

		func_display("modules/Egoods/no_distributive.tpl",$smarty);
		exit();
	}
}
else {
	db_query("DELETE FROM $sql_tbl[download_keys] WHERE expires <= '".time()."'");

	# Assign the current location line
	$smarty->assign("location", $location);

	func_display("modules/Egoods/wrong_key.tpl",$smarty);
	exit;
}
?>
