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
# $Id: shipping_tools.php,v 1.9 2006/01/11 06:55:58 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$is_realtime = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code != ''") > 0);
#
# Define data for the navigation within section
#
$dialog_tools_data = array();

$dialog_tools_data["left"][] = array("link" => "shipping.php", "title" => func_get_langvar_by_name("lbl_shipping_methods"));

if ($is_realtime && $config["Shipping"]["realtime_shipping"] == "Y" and (empty($active_modules["UPS_OnLine_Tools"]) or $config["Shipping"]["use_intershipper"] == "Y"))
	$dialog_tools_data["left"][] = array("link" => "shipping_options.php", "title" => func_get_langvar_by_name("lbl_shipping_options"));


$dialog_tools_data["right"][] = array("link" => "configuration.php?option=Shipping", "title" => func_get_langvar_by_name("lbl_general_settings"));

if ($is_realtime) {
	if ($config["Shipping"]["realtime_shipping"] == "Y" and !empty($active_modules["UPS_OnLine_Tools"]) and $config["Shipping"]["use_intershipper"] != "Y")
		$dialog_tools_data["right"][] = array("link" => "ups.php", "title" => func_get_langvar_by_name("lbl_ups_online_tools_configure"));

	$dialog_tools_data["right"][] = array("link" => "test_realtime_shipping.php", "title" => func_get_langvar_by_name("lbl_test_realtime_calculation"), "target" => "testrt");
}

?>
