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
# $Id: payment_methods.php,v 1.33 2006/01/11 06:55:58 mclap Exp $
#
# For explanation of Payment Methods please refer to
# X-Cart developer's documentation
#

define("IS_MULTILANGUAGE", 1);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice','tests');

$location[] = array(func_get_langvar_by_name("lbl_payment_methods"), "");

if ($REQUEST_METHOD == "POST") {
	require $xcart_dir."/include/safe_mode.php";

	if (is_array($posted_data)) {
		foreach ($posted_data as $k=>$v) {
			$v["active"] = (!empty($v["active"]) ? "Y" : "N");
			$v["is_cod"] = (!empty($v["is_cod"]) ? "Y" : "N");
			$v["af_check"] = (!empty($v["af_check"]) ? "Y" : "N");
			$v["surcharge"] = func_convert_number($v["surcharge"]);
			if ($v["surcharge_type"] != "%")
				$v["surcharge_type"] = "$";
			func_languages_alt_insert("payment_method_".$k, $v['payment_method'], $shop_language);
			func_languages_alt_insert("payment_details_".$k, $v['payment_details'], $shop_language);
			if ($shop_language != $config["default_admin_language"]) {
				unset($v["payment_method"], $v["payment_details"]);
			}
			func_membership_update("pmethod", $k, $v["membershipids"], "paymentid");
			unset($v["membershipids"]);
			func_array2update("payment_methods", $v, "paymentid = '$k'");
			if ($paypal_directid !== false && $paypal_directid == $k) {
				$tmp_update = array (
					"param05" => $active
				);
				func_array2update("ccprocessors", $tmp_update, "processor='ps_paypal_pro.php'");
			}
		}
		func_data_cache_get("payments_https", array(), true);
		func_disable_paypal_methods($config["paypal_solution"]);

		$top_message["content"] = func_get_langvar_by_name("msg_adm_payment_methods_upd");

	}
	else {
		$top_message["content"] = func_get_langvar_by_name("msg_adm_err_payment_methods_upd");
		$top_message["type"] = "E";
	}

	func_header_location("payment_methods.php");
}

#
# Obtain payment methods
#
$payment_methods = func_query("SELECT pm.*,cc.module_name,cc.processor,cc.type FROM $sql_tbl[payment_methods] AS pm LEFT JOIN $sql_tbl[ccprocessors] AS cc ON (pm.paymentid=cc.paymentid OR pm.paymentid<>cc.paymentid AND pm.processor_file=cc.processor) ORDER BY pm.orderby, pm.paymentid");
$payment_methods = test_payment_methods($payment_methods);

#
# Hide not usable PayPal methods
#
if (is_array($payment_methods)) {
	$_payment_methods = array();
	foreach ($payment_methods as $pm) {
		$skip = false;
		if ($pm["processor_file"] == "ps_paypal.php" || $pm["processor_file"] == "ps_paypal_pro.php") {
			# $config["paypal_solution"] = [ ipn | pro | express ]
			switch ($config["paypal_solution"]) {
				case "ipn":
					if ($pm["processor_file"] == "ps_paypal_pro.php")
						$skip = true;
					break;
				case "pro":
					if ($pm["processor_file"] == "ps_paypal.php")
						$skip = true;
					if (preg_match("/(payment_cc\.tpl)$/", $pm["payment_template"]))
						$pm["disable_checkbox"] = "Y";
					break;
				case "express":
					if ($pm["processor_file"] == "ps_paypal.php" || preg_match("/(payment_cc\.tpl)$/", $pm["payment_template"]))
						$skip = true;
			}
		}
		if ($skip)
			continue;
		$_payment_methods[] = $pm;
	}
	$payment_methods = $_payment_methods;
}

if (!empty($payment_methods)) {
	foreach ($payment_methods as $k => $v) {
		$tmp = func_get_languages_alt("payment_method_".$v['paymentid']);
		if (!empty($tmp))
			$payment_methods[$k]['payment_method'] = $tmp;
		$tmp = func_get_languages_alt("payment_details_".$v['paymentid']);
		if (!empty($tmp))
			$payment_methods[$k]['payment_details'] = $tmp;
		$tmp = func_query("SELECT membershipid FROM $sql_tbl[pmethod_memberships] WHERE paymentid = '$v[paymentid]'");
		if (!empty($tmp)) {
			$payment_methods[$k]['membershipids'] = array();
			foreach ($tmp as $v) {
				$payment_methods[$k]['membershipids'][$v['membershipid']] = 'Y';
			}
		}
	}
}

if ($config["active_subscriptions_processor"])
	$active_sb = test_ccprocessor(func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$config[active_subscriptions_processor]'"));
else
	$active_sb = array("status"=>1);
$smarty->assign("active_sb",$active_sb);

$cc_module_files = func_query("select * from $sql_tbl[ccprocessors] where paymentid=0 and processor<>'ps_paypal_pro.php' order by type,module_name");
$sb_module_files = func_query("select * from $sql_tbl[ccprocessors] where type='C' and background='Y' order by module_name");

$smarty->assign("cc_modules",$cc_module_files);
$smarty->assign("sb_modules",$sb_module_files);

$smarty->assign("memberships",func_get_memberships());

$smarty->assign("payment_methods",$payment_methods);
$smarty->assign("main","payment_methods");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);

?>
