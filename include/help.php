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
# $Id: help.php,v 1.54.2.4 2006/08/10 12:11:21 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','mail','user');

$location[] = array(func_get_langvar_by_name("lbl_help_zone"), "help.php");

if (!empty($login))
	$userinfo = func_userinfo($login,$login_type);

if (empty($section)) $section = "";
if (empty($action)) $action = "";

if ($action == "contactus" || $section == 'contactus') {

	$additional_fields = func_get_add_contact_fields($current_area);

	$default_fields = unserialize($config["Contact_Us"]["contact_us_fields"]);
	if (!$default_fields) {
		$default_fields = array();
		foreach ($default_contact_us_fields as $k => $v) {
			$default_fields[$k]["title"] = func_get_default_field($k);
			$default_fields[$k]['avail'] = (is_array($v['avail'])?$v['avail'][$current_area]:$v['avail']);
			$default_fields[$k]['required'] = (is_array($v['required'])?$v['required'][$current_area]:$v['required']);
		}
	}
	else {
		$tmp = array();
		foreach ($default_fields as $k => $v) {
			$tmp[$v['field']] = array (
				"avail" => (strpos($v['avail'],$current_area)!==FALSE ? "Y" : ""),
				"required" => (strpos($v['required'],$current_area)!==FALSE ? "Y" : ""),
				"title" => func_get_default_field($v['field']));
		}

		$default_fields = $tmp;
		unset($tmp);
	}

	$is_areas = array(
		"I" => (
			!empty($default_fields['title']['avail']) || 
			!empty($default_fields['firstname']['avail']) ||
			!empty($default_fields['lastname']['avail']) ||
			!empty($default_fields['company']['avail'])
		),
		"A" => (
			!empty($default_fields['b_address']['avail']) ||
			!empty($default_fields['b_address_2']['avail']) ||
			!empty($default_fields['b_city']['avail']) ||
			!empty($default_fields['b_county']['avail']) ||
			!empty($default_fields['b_state']['avail']) ||
			!empty($default_fields['b_country']['avail']) ||
			!empty($default_fields['b_zipcode']['avail']) ||
			!empty($default_fields['phone']['avail']) ||
			!empty($default_fields['fax']['avail']) ||
			!empty($default_fields['avail']['avail']) ||
			!empty($default_fields['url']['avail'])
		),
	);

	include $xcart_dir."/include/states.php";
	include $xcart_dir."/include/countries.php";
	if ($config["General"]["use_counties"] == "Y") {
		include $xcart_dir."/include/counties.php";
	}
}

if ($REQUEST_METHOD=="POST" && $action=="contactus") {
	#
	# Send mail to support
	#
	$HTTP_POST_VARS["body"] = stripslashes($HTTP_POST_VARS["body"]);

	foreach ($HTTP_POST_VARS as $key=>$val) {
		if ($key != 'additional_values')
			$contact[$key]=$val;
	}

	$contact['titleid'] = func_detect_title($contact['title']);

	$fillerror = false;
	foreach ($default_fields as $k => $v) {
		if ($k == "b_county" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['b_county']))) {
			if ($config["General"]["use_counties"] != "Y")
				continue;
			if (!func_check_county($contact[$k], stripslashes($contact["b_state"]), $contact['b_country']))
				$fillerror = true;
		} elseif ($k == "b_state" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['b_state']))) {
			$has_states = (func_query_first_cell("SELECT display_states FROM $sql_tbl[countries] WHERE code = '".$contact['b_country']."'") == 'Y');
			if (is_array($states) && $has_states && !func_check_state($states, stripslashes($contact["b_state"]), $contact['b_country']))
				$fillerror = true;
		} elseif ($k == "email" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['email']))) {
			if (!func_check_email($contact['email']))
				$fillerror = true;
		} elseif (empty($contact[$k]) && $v['required'] == 'Y' &&  $v['avail'] == 'Y') {
			$fillerror = true;
		}
	}

	if (!$fillerror && is_array($additional_fields)) {
		foreach($additional_fields as $k => $v) {
			$additional_fields[$k]['value'] = stripslashes($HTTP_POST_VARS['additional_values'][$v['fieldid']]);
			if (empty($HTTP_POST_VARS['additional_values'][$v['fieldid']]) && $v['required'] == 'Y' &&  $v['avail'] == 'Y')
				$fillerror = true;
		}
	}

	if (!$fillerror) {
		$fillerror = (empty($subject) || empty($body));
	}

	if (!$fillerror) {
		$contact["b_statename"]= func_get_state($contact["b_state"], $contact["b_country"]);
		$contact["b_countryname"]= func_get_country($contact["b_country"]);
		if ($config["General"]["use_counties"] == "Y")
			$contact["b_countyname"]= func_get_county($contact["b_county"]);

		$contact = func_array_map("stripslashes", $contact);
		if (!empty($active_modules['SnS_connector']) && $current_area == 'C')
			func_generate_sns_action("FillContactForm");

		$mail_smarty->assign("contact", $contact);
		$mail_smarty->assign("default_fields", $default_fields);
		$mail_smarty->assign("is_areas", $is_areas);
		$mail_smarty->assign("additional_fields", $additional_fields);

		func_send_mail($config["Company"]["support_department"], "mail/help_contactus_subj.tpl", "mail/help_contactus.tpl", $contact["email"], true);

		func_header_location("help.php?section=contactus");
	}
	else {
		func_unset($HTTP_POST_VARS,'additional_values');
		$userinfo = $HTTP_POST_VARS;
		$userinfo["login"] = $userinfo["uname"];
	}
}

#
# Recover password
#
if ($REQUEST_METHOD=="POST" && $action=="recover_password") {
	$accounts = func_query("SELECT login, password, usertype FROM $sql_tbl[customers] WHERE email='$email' AND status='Y'");

	#
	# Decrypt passwords
	#
	if (empty($accounts))
		func_header_location("help.php?section=Password_Recovery_error&email=".urlencode($email));

	foreach ($accounts as $key => $account) {
		$accounts[$key]["password"] = text_decrypt($account["password"]);
		if (is_null($accounts[$key]["password"]) || $accounts[$key]["password"] === false) {
			$accounts[$key]["password"] = func_get_langvar_by_name("err_data_corrupted");
			if (is_null($accounts[$key]["password"])) {
				x_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt password for the user ".$account['login'], true);
			}
		}
	}

	$mail_smarty->assign("accounts",$accounts);
	func_send_mail($email, "mail/password_recover_subj.tpl", "mail/password_recover.tpl", $config["Company"]["support_department"], false);

	func_header_location("help.php?section=Password_Recovery_message&email=".urlencode($email));

}

if ($section == 'cvv2') {
	$popup_title = 'What is CVV2?';
}

if ($popup_title)
	$smarty->assign("popup_title", $popup_title);

if (!empty($active_modules['SnS_connector']) && $current_area == 'C' && $section != 'contactus') {
	if($section == 'business' || $section == 'conditions') {
		func_generate_sns_action("ViewLegalInfo");
	}
	else {
		func_generate_sns_action("ViewHelp");
	}
}

$smarty->assign("userinfo",@$userinfo);
$smarty->assign("fillerror",@$fillerror);

$smarty->assign("default_fields", $default_fields);
$smarty->assign("additional_fields", $additional_fields);
$smarty->assign("fillerror", $fillerror);
$smarty->assign("titles", func_get_titles());

$smarty->assign("main","help");
$smarty->assign("help_section",$section);

?>
