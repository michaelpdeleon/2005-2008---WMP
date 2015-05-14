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
# $Id: config.php,v 1.6.2.1 2006/06/15 07:01:24 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
#
# Global definitions for Detailed product images module
#

$config['available_images']['D'] = "M";

if (defined("IS_IMPORT")) {
	$modules_import_specification['DETAILED_IMAGES'] = array(
		"script"		=> "/modules/Detailed_Product_Images/import.php",
		"permissions"	=> "AP",
		"need_provider"	=> true,
		"parent"		=> "PRODUCTS",
		"export_sql"	=> "SELECT id as productid FROM $sql_tbl[images_D] GROUP BY id",
		"table"         => "images_D",
		"key_field"     => "id",
		"parent_key_field" => "id",
		"columns"		=> array(
			"productid"		=> array(
				"is_key"	=> true,
				"type"		=> "N",
				"default"	=> 0),
			"productcode"	=> array(
				"is_key"	=> true),
			"product"		=> array(
				"is_key"	=> true),
			"image"			=> array(
				"array"		=> true,
				"type"		=> "I",
				"itype"		=> "D",
				"require"	=> true),
			"alt"			=> array(
				"array"		=> true),
			"orderby"		=> array(
				"type"		=> "N",
				"array"		=> true)
        )
    );
}

if (defined("TOOLS")) {
	$tbl_keys["images_D.id"] = array(
		"keys" => array("images_D.id" => "products.productid"),
		"fields" => array("imageid")
	);
	$tbl_demo_data[] = 'images_D';
}

?>
