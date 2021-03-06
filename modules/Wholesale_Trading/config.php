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
# $Id: config.php,v 1.5.2.3 2006/07/26 07:52:51 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
#
# Global definitions for Wholesale Trading module
#

if (defined("IS_IMPORT")) {
	$modules_import_specification['WHOLESALE_PRICES'] = array(
		"script"		=> "/modules/Wholesale_Trading/import.php",
		"permissions"	=> "AP",
		"need_provider"	=> true,
		"parent"		=> "PRODUCTS",
		"export_sql"	=> "SELECT productid FROM $sql_tbl[pricing] WHERE (quantity > 1 OR membershipid != 0) GROUP BY productid",
		"table"         => "pricing",
		"key_field"     => "productid",
		"orderby"		=> 100,
		"columns"		=> array(
			"productid"		=> array(
				"type"		=> "N",
				"is_key"	=> true,
				"default"	=> 0),
			"productcode"	=> array(
				"is_key"	=> true),
			"product"		=> array(
				"is_key"    => true),
			"variantcode"	=> array(
				"is_key"	=> true),
			"quantity"		=> array(
				"array"		=> true,
				"required"	=> true,
				"type"		=> "N"),
			"membership"	=> array(
				"array"		=> true),
			"membershipid"	=> array(
				"array"     => true,
				"type"		=> "N",
				"default"	=> 0),
			"price"			=> array(
				"array"     => true,
				"required"	=> true,
				"type"		=> "P")
		)

	);
}
?>
