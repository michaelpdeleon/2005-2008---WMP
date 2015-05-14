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
# $Id: cc_processing.php,v 1.56.2.2 2006/06/16 13:00:48 max Exp $
#
# For explanation of cc processing please refer to
# X-Cart developer's documentation
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice','crypt','tests');

if ($mode=="add" && !empty($processor)) {
	require $xcart_dir."/include/safe_mode.php";

	$tmp = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='".$processor."'");
	$cc_processor = $tmp["module_name"];
	if (!zerolen($tmp["c_template"])) {
		$template = $tmp["c_template"];
	} else {
		$type_2_template = array (
			"C" => "customer/main/payment_cc.tpl",
			"D" => "customer/main/payment_dd.tpl",
			"H" => "customer/main/payment_chk.tpl");
		$template = get_value($type_2_template, $tmp["type"], "customer/main/payment_offline.tpl");
	}

	$insert_params = array (
		'payment_method' => $cc_processor,
		'payment_script' => 'payment_cc.php',
		'payment_template' => $template,
		'active' => 'N',
		'orderby' => '999',
		'processor_file' => $processor
	);

	if ($processor == 'ps_paypal.php') {
		// Paypal standard
		$paymentid = func_array2insert('payment_methods', $insert_params);
		db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."' WHERE processor='ps_paypal.php'");

		$tmp = func_query_first("SELECT * from $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
		$cc_processor = $tmp["module_name"];
		// PayPal ExpressCheckout
		$insert_params['payment_method'] = $cc_processor.': '.$tmp['param08'];
		$insert_params['processor_file'] = 'ps_paypal_pro.php';
		$paymentid = func_array2insert('payment_methods', $insert_params);
		db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."' WHERE processor='ps_paypal_pro.php'");

		// PayPal DirectPayment
		$insert_params['payment_template'] = 'customer/main/payment_cc.tpl';
		$insert_params['payment_method'] = $cc_processor.': '.$tmp['param09'];
		func_array2insert('payment_methods', $insert_params);
	}
	else {
		$paymentid = func_array2insert('payment_methods', $insert_params);
		db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."' WHERE processor='".$processor."'");
	}

	func_header_location("payment_methods.php");
}

if ($mode=="delete" && $paymentid) {
	require $xcart_dir."/include/safe_mode.php";

	$tmp = func_query_first("SELECT $sql_tbl[ccprocessors].paymentid, $sql_tbl[ccprocessors].processor FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].paymentid='".$paymentid."' AND ($sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid OR $sql_tbl[ccprocessors].processor = $sql_tbl[payment_methods].processor_file)");

	if (!empty($tmp)) {
		if ($tmp['processor'] == 'ps_paypal.php' || $tmp['processor'] == 'ps_paypal_pro.php') {
			db_query("DELETE from $sql_tbl[payment_methods] WHERE processor_file IN ('ps_paypal.php','ps_paypal_pro.php')");
			db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='0' WHERE processor IN ('ps_paypal.php','ps_paypal_pro.php')");
		} else {
			db_query("DELETE FROM $sql_tbl[payment_methods] WHERE paymentid='".$paymentid."'");
			db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='0' where paymentid='".$paymentid."'");
		}

	}

	func_header_location("payment_methods.php");
}

#
# Setup paramxx in ccprocessors table
#
if ($REQUEST_METHOD=="POST" && empty($mode)) {
	require $xcart_dir."/include/safe_mode.php";

	if (!empty($cc_processor)) {
		if ($cc_processor == 'ps_paypal_pro.php' || $cc_processor == 'ps_paypal.php') {
			$map = array (
				'pro' => 'ps_paypal_pro.php',
				'ipn' => 'ps_paypal.php'
			);

			if (!in_array($paypal_solution, array('ipn','pro','express')))
				$paypal_solution = 'ipn';

			db_query("UPDATE $sql_tbl[config] SET value='$paypal_solution' WHERE name='paypal_solution' AND category=''");

			$enable_paypal = ($paypal_solution != $config['paypal_solution'] && ($paypal_solution == 'ipn' || $config['paypal_solution'] == 'ipn'));

			func_disable_paypal_methods($paypal_solution, $enable_paypal);

			# set params
			foreach ($map as $map_key=>$processor) {
				if (empty($HTTP_POST_VARS['conf_data'][$map_key])) continue;

				foreach ($HTTP_POST_VARS['conf_data'][$map_key] as $k=>$v) {
					db_query("UPDATE $sql_tbl[ccprocessors] SET $k='$v' WHERE processor='".$processor."'");
				}
			}
		} else {
			foreach($HTTP_POST_VARS as $key=>$value) {
				if ($key == $XCART_SESSION_NAME) continue;

				if (($cc_processor=='cc_authorizenet.php' || $cc_processor=='ch_authorizenet.php') && ($key == 'param01' || $key == 'param02')) {
					$value = addslashes(text_crypt($value));
				}

				db_query("update $sql_tbl[ccprocessors] set $key='$value' where processor='".$cc_processor."'");
			}
		}
	}

	func_header_location("payment_methods.php");
}

#
# $cc_processing_module
#
if ($mode=="update") {
	require $xcart_dir."/include/safe_mode.php";

	if (!empty($cc_processor) && $subscribe!="yes") {
		$cc_processing_module = func_query_first("SELECT $sql_tbl[ccprocessors].*, $sql_tbl[payment_methods].protocol FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor = '$cc_processor' AND $sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid");
		if ($cc_processor == 'cc_authorizenet.php' || $cc_processor == 'ch_authorizenet.php') {
			$cc_processing_module['param01'] = text_decrypt(trim($cc_processing_module["param01"]));
			if (is_null($cc_processing_module['param01'])) {
				x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt the field 'param01' for AuthorizeNet: AIM payment module", true);
			}
			$cc_processing_module['param02'] = text_decrypt(trim($cc_processing_module["param02"]));
			if (is_null($cc_processing_module['param02'])) {
				x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt the field 'param02' for AuthorizeNet: AIM payment module", true);
			}
		}
		elseif ($cc_processor == 'ps_paypal.php' || $cc_processor == 'ps_paypal_pro.php') {
			$cc_processing_module["template"] = 'ps_paypal_group.tpl';
			if ($cc_processor == 'ps_paypal.php') {
				$pkey = 'ipn';
				$akey = 'pro';
				$asearch = 'ps_paypal_pro.php';
			}
			else {
				$pkey = 'pro';
				$akey = 'ipn';
				$asearch = 'ps_paypal.php';
			}
			$conf_data[$pkey] = $cc_processing_module;
			$conf_data[$akey] = func_query_first("SELECT $sql_tbl[ccprocessors].* FROM $sql_tbl[ccprocessors] WHERE $sql_tbl[ccprocessors].processor = '$asearch'");
			$smarty->assign('conf_data', $conf_data);
		}
	} elseif ($subscribe == "yes") {
		db_query("UPDATE $sql_tbl[config] SET value='$cc_processor' WHERE name='active_subscriptions_processor'");
		$config["active_subscriptions_processor"] = $cc_processor;
		if (!zerolen($cc_processor)) {
			$cc_processing_module = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$cc_processor'");
			if ($cc_processor == 'cc_authorizenet.php' || $cc_processor == 'ch_authorizenet.php') {
				$cc_processing_module['param01'] = text_decrypt(trim($cc_processing_module["param01"]));
				$cc_processing_module['param02'] = text_decrypt(trim($cc_processing_module["param02"]));
			}
		}
	}
}

if (empty($cc_processing_module))
	func_header_location("payment_methods.php");

$cc_processing_module = func_array_merge($cc_processing_module, test_ccprocessor($cc_processing_module));

$location[] = array(func_get_langvar_by_name("lbl_payment_gateways"), "cc_processing.php");
if ($cc_processing_module)
	$location[] = array($cc_processing_module["module_name"], "");

# Assign the current location line
$smarty->assign("location", $location);

$smarty->assign("timezone_offset",floor(date("Z")/3600));
$smarty->assign("main","cc_processing");
$smarty->assign("module_data",$cc_processing_module);
$smarty->assign("processing_module","payments/".$cc_processing_module["template"]);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
