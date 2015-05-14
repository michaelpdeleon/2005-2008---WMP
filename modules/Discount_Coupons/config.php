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
# $Id: config.php,v 1.6.2.1 2006/07/20 08:25:25 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
#
# Global definitions for Discount Coupons module
#

if (defined("IS_IMPORT")) {
	$modules_import_specification['DISCOUNT_COUPONS'] = array(
		"script"		=> "/modules/Discount_Coupons/import.php",
		"tpl"  			=> array(
			"main/import_option_category_path_sep.tpl"),
		"permissions"	=> "AP",
		"need_provider"	=> true,
		"export_sql"	=> "SELECT coupon FROM $sql_tbl[discount_coupons]",
		"orderby"		=> 25,
		"columns"		=> array(
			"coupon"		=> array(
				"required"	=> true),
			"discount"		=> array(
				"type"		=> "N",
				"default"	=> 0.00),
			"coupon_type"	=> array(
				"required"	=> true),
			"productid"		=> array(
				"type"		=> "N",
				"default"	=> 0),
			"productcode"	=> array(),
			"product"		=> array(),
			"categoryid"	=> array(
				"type"		=> "N",
				"default"	=> 0),
			"category"		=> array(),
			"recursive"		=> array(
				"type"		=> "B"),
			"minimum"		=> array(
				"type"		=> "P",
				"default"	=> 0.00),
			"times"			=> array(
				"required"	=> true,
				"type"		=> "N"),
			"times_used"	=> array(
				"type"		=> "N"),
			"expire"		=> array(
				"required"	=> true,
				"type"		=> "D"),
			"status"		=> array(
				"type"		=> "E",
				"variants"	=> array("A","D","U"))
		)
	);
}

if (defined("TOOLS")) {
	$tbl_keys["discount_coupons.productid"] = array(
		"keys" => array("discount_coupons.productid" => "products.productid"),
		"where" => "discount_coupons.productid != 0",
		"fields" => array("coupon")
	);
	$tbl_keys["discount_coupons.categoryid"] = array(
		"keys" => array("discount_coupons.categoryid" => "categories.categoryid"),
		"where" => "discount_coupons.categoryid != 0",
		"fields" => array("coupon")
	);
	$tbl_keys["discount_coupons.provider"] = array(
		"keys" => array("discount_coupons.provider" => "customers.login"),
		"where" => "customers.usertype IN ('A','P')",
		"fields" => array("coupon")
	);
	$tbl_demo_data[] = 'discount_coupons';
}

?>
