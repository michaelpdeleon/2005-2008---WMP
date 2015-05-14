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
# $Id: config.php,v 1.13 2006/01/11 06:56:20 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

#
# Global definitions for UPS_OnLine_Tools module
#
x_session_register("ups_reg_step", 0);
x_session_register("ups_licensetext");
x_session_register("ups_userinfo");

if (basename($REQUEST_URI) != "ups.php") {
	x_session_unregister("ups_reg_step");
	x_session_unregister("ups_licensetext");
	x_session_unregister("ups_userinfo");
}

include $xcart_dir."/modules/UPS_OnLine_Tools/ups_func.php";

#
# Set up $show_XML to <true> to display all XML-queries (for debug purposes)
#
$show_XML = false;

# Production URL
$UPS_url = "https://www.ups.com:443/ups.app/xml/";

$devlicense="EBA2F47A37670E96";

if ($config["Shipping"]["realtime_shipping"] == "Y" and $config["Shipping"]["use_intershipper"] != "Y") {
	$smarty->assign("insert_trademark", 1);
	$mail_smarty->assign("insert_trademark", 1);
}

@include $xcart_dir."/config.local.UPS_OnLine_Tools.php";

#
# This table provides correct service codes for different origins
# <Code returned from UPS> => array (<origin> => <service_code in xcart_shipping>)
$ups_services = array(
	"01" => array("US" => 5, "CA" => 8, "PR" => 5),
	"02" => array("US" => 1, "CA" => 13, "PR" => 1),
	"03" => array("US" => 4, "PR" => 4),
	"07" => array("US" => 16, "EU" => 8, "CA" => 16, "PR" => 16, "MX" => 8, "OTHER_ORIGINS" => 16),
	"08" => array("US" => 15, "EU" => 13, "CA" => 15, "PR" => 15, "MX" => 13, "OTHER_ORIGINS" => 15),
	"11" => array("US" => 14, "EU" => 14, "CA" => 14),
	"12" => array("US" => 3, "CA" => 3),
	"13" => array("US" => 7, "CA" => 12),
	"14" => array("US" => 6, "CA" => 9, "PR" => 6),
	"54" => array("US" => 17, "EU" => 17, "CA" => 17, "PR" => 17, "MX" => 11, "OTHER_ORIGINS" => 17),
	"59" => array("US" => 2),
	"64" => array("EU" => 10),
	"65" => array("EU" => 12)
);
#
# Note: code "65" is  a mistake in the documentation
#
?>
