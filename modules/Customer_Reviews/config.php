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
# Global definitions for Customer Reviews module
#

if (defined("IS_IMPORT")) {
	$modules_import_specification['CUSTOMER_REVIEWS'] = array(
		"script"		=> "/modules/Customer_Reviews/import.php",
		"permissions"	=> "AP",
		"need_provider"	=> true,
		"parent"		=> "PRODUCTS",
		"export_sql" 	=> "SELECT productid FROM $sql_tbl[product_reviews]",
		"table"         => "product_reviews",
		"key_field"     => "productid",
		"columns"		=> array(
			"productid"		=> array(
				"type"		=> "N",
				"is_key"	=> true,
				"default"	=> 0),
			"productcode"	=> array(
				"is_key"	=> true),
			"product"		=> array(
				"is_key"    => true),
			"email"			=> array(
				"required"  => true,
				"array"	=> true),
			"message"		=> array(
				"required"  => true,
				"array"		=> true)
		)
	);
}

if (defined("TOOLS")) {
	$tbl_keys["product_votes.productid"] = array(
		"keys" => array("product_votes.productid" => "products.productid"),
		"fields" => array("vote_id")
	);
	$tbl_keys["product_reviews.productid"] = array(
		"keys" => array("product_reviews.productid" => "products.productid"),
		"fields" => array("review_id")
	);
	$tbl_demo_data[] = 'product_reviews';
	$tbl_demo_data[] = 'product_votes';
}
?>
