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
# $Id: ups_shipping_methods.php,v 1.15 2006/01/11 06:56:20 mclap Exp $
#

#
# Preparing data to filter UPS shipping methods in admin/shipping.php
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }


if (in_array($config["Company"]["location_country"], array("US","CA","PR","MX")))
#
# Origin is US, Canada, Puerto Rico or Mexico
#
	$origin_code = $config["Company"]["location_country"];

elseif (in_array($config["Company"]["location_country"], array("AT","BE","DK","FI","FR","DE","GR","IE","IT","LU","NL","PT","ES","SE","GB")))
#
# Origin is European Union
#
	$origin_code = "EU";

else
#
# Origin is other countries
#
	$origin_code = "OTHER_ORIGINS";
		
foreach ($ups_services as $service) {
	if (!empty($service[$origin_code])) {
		$valid_ups_services[] = $service[$origin_code];
	}
}
if (is_array($valid_ups_services)) {
	if ($origin_code == "US")
		$valid_ups_services[] = "100";  # UPS Standard to Canada
	$ups_services_condition = " AND service_code IN (".implode(",",$valid_ups_services).")";
}

#
# This condition is used in admin/shipping.php
#
if (!$ups_only)
	$condition = " AND (is_new = 'Y' OR code<>'UPS' OR (code='UPS' AND service_code!=''".@$ups_services_condition."))";
else
	$condition = " AND (code='UPS' AND service_code!=''".@$ups_services_condition.")";

#
# This condition is used in provider/shipping_rates.php
#
$markup_condition .= $condition;

$carriers_tmp = array();

if (is_array($carriers)) {
	foreach ($carriers as $k=>$v) {
		if ($v["code"] == "UPS") {
			$v["total_methods"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code='UPS' AND service_code!=''".@$ups_services_condition);
			$v["total_enabled"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y' AND code='UPS' AND service_code!=''".@$ups_services_condition);
			$carriers[$k] = $v;

		}
	}
}

?>
