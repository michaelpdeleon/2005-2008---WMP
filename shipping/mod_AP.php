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
# $Id: mod_AP.php,v 1.5.2.1 2006/08/15 05:53:51 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

function func_shipper_AP($weight, $userinfo, $debug, $cart) {
	global $config, $sql_tbl;
	global $allowed_shipping_methods, $intershipper_rates;

	if ($config["Company"]["location_country"] != 'AU' || !is_array($allowed_shipping_methods) || empty($allowed_shipping_methods)) {
		return false;
	}

	$stypes = array(
		1001 => "STANDARD",
		1002 => "EXPRESS",
		1003 => "AIR",
		1004 => "ECONOMY",
		1005 => "SEA"
	);

	$ap_host = "drc.edeliver.com.au";
	$ap_url = "/ratecalc.asp";

	$options = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier = 'APOST'");
	$post = "Pickup_Postcode=".$config["Company"]["location_zipcode"] .
		"&Destination_Postcode=".$userinfo['s_zipcode'] .
		"&Country=".$userinfo['s_country'] .
		"&Weight=".func_weight_in_grams($weight) .
		"&Length=".$options['param00'] .
		"&Width=".$options['param01'] .
		"&Height=".$options['param02'] .
		"&Quantity=1";

    if ($debug == "Y") {
    
        # Display debug info (header)
        print "<h1>Australia Post Debug Information</h1>";
		$is_display_debug = false;
	}

	foreach ($allowed_shipping_methods as $value) {
		if ($value["code"] != "APOST" || !isset($stypes[$value['service_code']]))
			continue;

		if (($userinfo['s_country'] != 'AU' && $value['destination'] == "L") || ($userinfo['s_country'] == 'AU' && $value['destination'] == "I")) {
			continue;
		}

		list ($header, $result) = func_http_get_request ($ap_host, $ap_url, $post."&Service_type=".$stypes[$value['service_code']]);
		if (empty($result))
			continue;

		$return = array();
		if (preg_match_all("/^([^=]+)=(.*)$/Sm", $result, $preg)) {
			foreach($preg[1] as $k => $v) {
				$return[$v] = trim($preg[2][$k]);
			}
		}

		if ($return['err_msg'] == "OK") {
			$intershipper_rates[] = array(
				"methodid" => $value["subcode"],
				"rate" => $return['charge'],
				"shipping_time" => $return['days']
			);
		}

	    if ($debug == "Y") {

    	    # Display debug info
            print "<h2>Australia Post Request</h2>";
            print "<pre>".htmlspecialchars($post."&Service_type=".$stypes[$value['service_code']])."</pre>";
            print "<h2>Australia Post Response</h2>";
            print "<pre>".htmlspecialchars($result)."</pre>";	
			$is_display_debug = true;
        }
    }


	if ($debug == "Y" && !$is_display_debug) {
		print "It seems, you have forgotten to fill in an Australia Post account information.";
	}

}

?>
