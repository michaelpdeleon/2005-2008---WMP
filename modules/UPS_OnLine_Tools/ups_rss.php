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
# $Id: ups_rss.php,v 1.9.2.3 2006/07/25 11:18:34 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (empty($config["UPS_OnLine_Tools"]["UPS_username"]) || empty($config["UPS_OnLine_Tools"]["UPS_password"]) || empty($config["UPS_OnLine_Tools"]["UPS_accesskey"]))
	func_header_location("ups.php");

#
# Check and update UPS configuration
####################################
#
# Fields:
#	param00:	Drop-off/Pickup
#	param01:	Packaging
#	param02:	length
#	param03:	width
#	param04:	height
#	param05:	service options
#	param06:	codvalue
#	param07:	insured value
#	param08:	Rates cost conversion rate
#

$ups_title .= " Rates & Service Selection";

$location[] = array($ups_title, "ups.php?mode=rss");

$smarty->assign("location", $location);
$smarty->assign("ups_reg_step", $ups_reg_step);
$smarty->assign("title", $ups_title);

$smarty->assign("mode", "rss");

if (in_array($config["Company"]["location_country"], array("CA","DO","PR","US")))
	$dim_units = "inches";
else
	$dim_units = "cm";

#
# UPS options update
#
if ($REQUEST_METHOD == "POST") {

	if ($mode == "rss") {
	
		$check = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");
		if (!$check)
			db_query("INSERT INTO $sql_tbl[shipping_options] (carrier) VALUES ('UPS')");

		if (is_array($tmp_var = unserialize($check["param00"])))
			$ups_currency_code = $tmp_var["currency_code"];
		
		$ups_parameters = array(
			"account_type" => $account_type,
//			"customer_classification_code" => $customer_classification_code,
//			"pickup_type" => $pickup_type,
			"packaging_type" => $packaging_type,
			"dim_units" => $dim_units,
			"length" => $length,
			"width" => $width,
			"height" => $height,
			"upsoptions" => implode("|", ($upsoptions?$upsoptions:array())),
			"codvalue" => sprintf("%.2f", $codvalue),
			"cod_currency" => $cod_currency,
			"cod_funds_code" => intval($cod_funds_code),
			"iv_amount" => sprintf("%.2f", $iv_amount),
			"iv_currency" => $iv_currency,
			"delivery_conf" => (in_array($delivery_conf, array(0,1,2,3)) ? $delivery_conf : 0),
			"conversion_rate" => (is_numeric($conversion_rate) ? $conversion_rate : 1),
			"av_status" => $av_status,
			"av_quality" => $av_quality,
			"currency_code" => $ups_currency_code,
			"shipper_number" => $shipper_number,
			"residential" => $residential,
			"oversize" => $oversize
		);
	
		db_query("UPDATE $sql_tbl[shipping_options] SET param00='".addslashes(serialize($ups_parameters))."' WHERE carrier='UPS'");
		
	}

	func_header_location("ups.php?mode=rss");
}

#
# Prepare the configuration page displaying
#

$shipping_options = array ();

$shipping_options = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

$ups_parameters["rss"] = unserialize($shipping_options["param00"]);

if (is_array($ups_parameters["rss"])) {

	$service_options = explode("|",$ups_parameters["rss"]["upsoptions"]);
	if (is_array($service_options))
		foreach ($service_options as $opt)
			if (!empty($opt))
				$ups_parameters["rss"][$opt] = true;

}

$ups_parameters["rss"]["dim_units"] = $dim_units;

$smarty->assign ("shipping_options", $ups_parameters);

?>
