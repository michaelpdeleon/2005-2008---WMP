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
# $Id: register.php,v 1.211.2.12 2006/08/12 06:46:10 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart','category','crypt','mail','user');

x_session_register ("intershipper_recalc");
x_session_unregister("secure_oid");

require $xcart_dir."/include/countries.php";
require $xcart_dir."/include/states.php";
if ($config["General"]["use_counties"] == "Y")
	include $xcart_dir."/include/counties.php";

x_session_register("profile_modified_data");
x_session_register("profile_modified_add_field");

$user = (string)$user;
if (!isset($card_expire) && $card_expire_Month) {
	$card_expire = $card_expire_Month.substr($card_expire_Year, 2);
}

if ($newbie == "Y") {
	#
	# Register/Modify own profile
	#
	$location[] = array(func_get_langvar_by_name("lbl_profile_details"), "");
}

if (empty($mode))
	$mode = "";

if ($current_area == 'A' || ($current_area == 'P' && !empty($active_modules['Simple_Mode'])) && ($login_type != $current_area)) {
	if ($login_type == 'C') {
		$fields_area = array("C", "H");
	} else {
		$fields_area = $login_type;
	}

} elseif ($action == 'cart') {
	$fields_area = "H";

} else {
	$fields_area = $current_area;
}

if ($fields_area == 'P' && $current_area == 'A' && !empty($active_modules['Simple_Mode'])) {
	$fields_area = 'A';
}

$additional_fields = func_get_additional_fields($fields_area, $login);
$default_fields = func_get_default_fields($fields_area);

$is_areas = array(
	"P" => (($default_fields['title']['avail'] == 'Y' || $default_fields['firstname']['avail'] == 'Y' || $default_fields['lastname']['avail'] == 'Y' || $default_fields['company']['avail'] == 'Y' || $default_fields['ssn']['avail'] == 'Y')?"Y":""),
	"B" => (($default_fields['b_title']['avail'] == 'Y' || $default_fields['b_firstname']['avail'] == 'Y' || $default_fields['b_lastname']['avail'] == 'Y' || $default_fields['b_address']['avail'] == 'Y' || $default_fields['b_city']['avail'] == 'Y' || $default_fields['b_state']['avail'] == 'Y' || $default_fields['b_country']['avail'] == 'Y' || $default_fields['b_zipcode']['avail'] == 'Y')?"Y":""),
    "S" => (($default_fields['s_title']['avail'] == 'Y' || $default_fields['s_firstname']['avail'] == 'Y' || $default_fields['s_lastname']['avail'] == 'Y' || $default_fields['s_address']['avail'] == 'Y' || $default_fields['s_city']['avail'] == 'Y' || $default_fields['s_state']['avail'] == 'Y' || $default_fields['s_country']['avail'] == 'Y' || $default_fields['s_zipcode']['avail'] == 'Y')?"Y":""),
    "C" => (($default_fields['phone']['avail'] == 'Y' || $default_fields['email']['avail'] == 'Y' || $default_fields['fax']['avail'] == 'Y' || $default_fields['url']['avail'] == 'Y')?"Y":""),
	"A" => ''
);

if ($additional_fields) {
	foreach ($is_areas as $k => $v) {
		if ($v) continue;

		foreach ($additional_fields as $v2) {
			if ($v2['section'] == $k && $v2['avail'] == 'Y') {
				$is_areas[$k] = 'Y';
				break;
			}
		}
	}
}

if ($REQUEST_METHOD == "POST" && isset($HTTP_POST_VARS['usertype'])) {
	#
	# Process the POST request and create/update profile
	#

	if (isset($cart_operation))
		return;

	#
	# Check if user have permissions to update/create profile
	#
	$allowed_registration = ($usertype == "C" || ($usertype == "B" && $config["XAffiliate"]["partner_register"] == "Y") || ($current_area == "P" && $active_modules["Simple_Mode"]) || $current_area == "A");

	$allowed_update = (($usertype == $current_area && !empty($login) && !empty($uname) && $login == $uname) || ($current_area == "P" && $active_modules["Simple_Mode"]) || $current_area == "A");

	if (($mode!="update" && !$allowed_registration) || ($mode=="update" && !$allowed_update)) {
		func_header_location("error_message.php?access_denied&id=36");
	}

	#
	# Anonymous registration (X-Cart generates username by itself)
	#
	$anonymous_user=false;

	$passed_uname = $uname;

	if ($anonymous && empty($uname) && $config["General"]["disable_anonymous_checkout"] != "Y") {
		$uname = func_generate_anonymous_username();

		#
		# All anonymous accounts must be customers
		#
		$usertype = "C";
		$passwd1 = $anonymous_password;
		$passwd2 = $anonymous_password;

		$anonymous_user=true;
	}
	
	if (!$anonymous) {
		$anonymous_user = func_is_anonymous($uname);
	}

	#
	# User registration info passed to register.php via POST method
	#
	$existing_user = func_query_first("select password, email from $sql_tbl[customers] where login='$uname'");
	if (empty($existing_user))
		$existing_user = func_query_first("SELECT login FROM $sql_tbl[orders] WHERE login='$uname'");

	if ($mode == "update") {
		$uerror = false;
	}
	else {
		$uerror = !(empty($uname)) && !empty($existing_user);
		$uerror = $uerror || func_is_anonymous($passed_uname);
	}

	#
	# Check for errors
	#
	$uname_tmp=stripslashes($uname);
	if (strcmp($uname_tmp, $uname) !=0)
		$error="Username ".$uname_tmp." is invalid! Please correct";
	else
		$error='';

	$smarty->assign("error",$error);

	# Inherited first name and last name from Personalinformation to Billing / Shipping address
	foreach (array("firstname", "lastname") as $fn) {
		if ($default_fields[$fn]['avail'] == 'Y') {
			foreach (array('b_', 's_') as $fn_prefix) {
				if ($default_fields[$fn_prefix.$fn]['avail'] != 'Y')
					$HTTP_POST_VARS[$fn_prefix.$fn] = ${$fn_prefix.$fn} = $$fn;
			}
		}
	}

	if ($ship2diff != 'Y') {
		foreach (array('title','firstname','lastname','city','state','country','zipcode','address','address_2','county') as $v) {
			if ($default_fields['b_'.$v]['avail'] == 'Y') {
				$HTTP_POST_VARS['s_'.$v] = ${'s_'.$v} = ${'b_'.$v};
			}
		}
	}

	$trim_fields = array ('uname', 'firstname', 'lastname', 'b_address', 'b_city', 'states', 'b_state', 'b_country', 'b_zipcode', 'phone', 'email', 'b_title', 'b_firstname', 'b_lastname', 's_title', 's_firstname', 's_lastname', 'tax_number');
	foreach ($trim_fields as $k=>$v) {
		if (isset($$v)) $$v = trim($$v);
	}

	$fillerror = (empty($uname) || !empty($error) || empty($passwd1) || empty($passwd2) || ($passwd1 != $passwd2));
	if (!$fillerror) {
		if ($default_fields['b_country']['avail'] != 'Y') {
			$b_country = $config['General']['default_country'];
			if ($default_fields['s_country']['avail'] != 'Y')
				$s_country = $config['General']['default_country'];
		}

		if ($default_fields['b_state']['avail'] != 'Y') {
			$b_state = $config['General']['default_state'];
			if ($default_fields['s_state']['avail'] != 'Y')
				$s_state = $config['General']['default_state'];
		}

		$s_address = trim($s_address);
		$s_address_2 = trim($s_address_2);
		$s_city = trim($s_city);
		$s_zipcode = trim($s_zipcode);

		foreach (array('city','state','country','zipcode') as $v) {
			if ($default_fields['b_'.$v]['avail'] != 'Y') {
				$HTTP_POST_VARS['b_'.$v] = ${'b_'.$v} = $config['General']['default_'.$v];
			}

			if ($default_fields['s_'.$v]['avail'] != 'Y' && empty(${'s_'.$v})) {
				$HTTP_POST_VARS['s_'.$v] = ${'s_'.$v} = $config['General']['default_'.$v];
			}
		}

		$s_display_states = (func_query_first_cell("SELECT display_states FROM $sql_tbl[countries] WHERE code = '$s_country'") == 'Y');
		$b_display_states = (func_query_first_cell("SELECT display_states FROM $sql_tbl[countries] WHERE code = '$b_country'") == 'Y');
		foreach ($default_fields as $k => $v) {
			if ($v['required'] == 'Y' && empty($$k) && $v['avail'] == 'Y') {
				if ($k != 'b_state' && $k != 's_state' && $k != 'b_county' && $k != 's_county') {
					$fillerror = true;
					break;
				}
				elseif (($k == 's_state' || ($k == 's_county' && $config["General"]["use_counties"] == "Y")) && $s_display_states) {
					$fillerror = true;
					break;
				}
				elseif (($k == 'b_state' || ($k == 'b_county' && $config["General"]["use_counties"] == "Y")) && $b_display_states) {
					$fillerror = true;
					break;
				}
			}
		}
	}

	if (!$fillerror && $additional_fields) {
		foreach ($additional_fields as $v) {
			if ($v['required'] == 'Y' && empty($additional_values[$v['fieldid']]) && $v['avail'] == 'Y') {
				$fillerror = true;
				break;
			}
		}
	}

	$emailerror = false;
	if(($default_fields['email']['required'] == 'Y' || !empty($email))&& $default_fields['email']['avail'] == 'Y') {
		$emailerror = !func_check_email($email);
		if ($default_fields['email']['required'] == 'Y' && $emailerror)
			$fillerror = $emailerror;

		if ($emailerror)
			$error = "email";
	}

	if($default_fields['b_state']['avail'] == 'Y' && $default_fields['b_country']['avail'] == 'Y') {
		if (is_array($states) && !func_check_state($states, stripslashes($b_state), $b_country) && $b_display_states) {
			$error = "b_statecode";
			$smarty->assign("error",$error);
		}
		elseif ($default_fields['b_county']['avail'] == 'Y' && $config["General"]["use_counties"] == "Y") {
			if (!func_check_county($b_county, stripslashes($b_state), $b_country)) {
				$error = "b_county";
				$smarty->assign("error",$error);
			}
		}
	}

	if (!(@$uerror || @$eerror || @$fillerror || @$error)) {
		#
		# Fields filled without errors. User registered successfully
		#
		$crypted = addslashes(text_crypt($passwd1));

		if ($default_fields['s_state']['avail'] == 'Y' && $default_fields['s_country']['avail'] == 'Y') {
			if (is_array($states) && !func_check_state($states, stripslashes($s_state), $s_country) && $s_display_states) {
				$error = "s_statecode";
				$smarty->assign("error",$error);
			}
			elseif ($default_fields['s_county']['avail'] == 'Y') {
				if ($config["General"]["use_counties"] == "Y" && !func_check_county($s_county, stripslashes($s_state), $s_country)) {
					$error = "s_county";
					$smarty->assign("error",$error);
				}
			}
		}

		if (!@$error && $current_area == "C" && $active_modules["UPS_OnLine_Tools"]) {
			#
			# Shipping Address Validation by UPS OnLine Tools module
			#
			include $xcart_dir."/modules/UPS_OnLine_Tools/ups_av.php";
		}
	}

	if (!(@$uerror || @$eerror || @$fillerror || @$error)) {
		#
		# Add new member to newsletter list
		#
		$cur_subs = array();
		if (!empty($existing_user)) {
			$tmp = func_query("SELECT DISTINCT($sql_tbl[newslist_subscription].listid) FROM $sql_tbl[newslist_subscription], $sql_tbl[newslists] WHERE $sql_tbl[newslist_subscription].email='".addslashes($existing_user["email"])."' AND $sql_tbl[newslist_subscription].listid=$sql_tbl[newslists].listid AND $sql_tbl[newslists].lngcode='$shop_language'");
			if (is_array($tmp)) {
				foreach ($tmp as $v)
					$cur_subs[] = $v["listid"];
			}
		}

		$ext_subs = array();
		$tmp = func_query("SELECT DISTINCT($sql_tbl[newslist_subscription].listid) FROM $sql_tbl[newslist_subscription], $sql_tbl[newslists] WHERE $sql_tbl[newslist_subscription].email='$email' AND $sql_tbl[newslist_subscription].listid=$sql_tbl[newslists].listid AND $sql_tbl[newslists].lngcode='$shop_language'");
		if (is_array($tmp)) {
			foreach ($tmp as $v)
				$ext_subs[] = $v["listid"];
		}

		$subs_keys = array();
		if (is_array($subscription)) $subs_keys = array_keys($subscription);

		$delid = array_diff($cur_subs,$subs_keys);
		$insid = array_diff($subs_keys,$cur_subs,$ext_subs);
		$updid = array_intersect($cur_subs, $subs_keys);
		$updid = array_diff($updid, $ext_subs);

		if (count($delid)>0)
			db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE email='$existing_user[email]' AND listid IN ('".implode("','",$delid)."')");

		if (count($updid)>0 && $existing_user["email"] != $email)
			db_query("UPDATE $sql_tbl[newslist_subscription] SET email='$email' WHERE email='$existing_user[email]' AND listid IN ('".implode("','",$updid)."')");

		foreach ($insid as $id) {
			db_query("INSERT INTO $sql_tbl[newslist_subscription] (listid, email, since_date) VALUES ('$id','$email', '".time()."')");
		}

		# URL normalization
		if (!empty($url)) {
			if(strpos($url, "http") !== 0) {
				$url = "http://".$url;
			}
		}

		#
		# Update/Insert user info
		#
		$profile_values = array();
		$profile_values['password'] = $crypted;
		$profile_values['password_hint'] = $password_hint;
		$profile_values['password_hint_answer'] = $password_hint_answer;
		$profile_values['title'] = $title;
		$profile_values['firstname'] = $firstname;
		$profile_values['lastname'] = $lastname;
		$profile_values['company'] = $company;
		$profile_values['b_title'] = $b_title;
		$profile_values['b_firstname'] = $b_firstname;
		$profile_values['b_lastname'] = $b_lastname;
		$profile_values['b_address'] = $b_address."\n".$b_address_2;
		$profile_values['b_city'] = $b_city;
		$profile_values['b_county'] = @$b_county;
		$profile_values['b_state'] = $b_state;
		$profile_values['b_country'] = $b_country;
		$profile_values['b_zipcode'] = $b_zipcode;
		$profile_values['s_title'] = $s_title;
		$profile_values['s_firstname'] = $s_firstname;
		$profile_values['s_lastname'] = $s_lastname;
		$profile_values['s_address'] = $s_address."\n".$s_address_2;
		$profile_values['s_city'] = $s_city;
		$profile_values['s_county'] = @$s_county;
		$profile_values['s_state'] = $s_state;
		$profile_values['s_country'] = $s_country;
		$profile_values['s_zipcode'] = $s_zipcode;
		$profile_values['phone'] = $phone;
		$profile_values['email'] = $email;
		$profile_values['fax'] = $fax;
		$profile_values['url'] = $url;
		$profile_values['card_name'] = $card_name;
		$profile_values['card_type'] = $card_type;
		$profile_values['card_number'] = addslashes(text_crypt(@$card_number));
		$profile_values['card_expire'] = $card_expire;
		$profile_values['card_cvv2'] = $card_cvv2;
		$profile_values['pending_membershipid'] = $pending_membershipid;
		$profile_values['ssn'] = $ssn;
		$profile_values['parent'] = $parent;
		$profile_values['pending_plan_id'] = $pending_plan_id;
		
		if ($current_area == "C") {
			if ($config["Taxes"]["allow_user_modify_tax_number"] == "Y" || empty($existing_user) || func_query_first_cell("SELECT tax_exempt FROM $sql_tbl[customers] WHERE login='$login' LIMIT 1") != "Y") {
				# Existing customer cannot edit 'tax_number' if
				# 'tax_exempt' == 'Y' and
				# 'allow_user_modify_tax_number' option == 'N'
				$profile_values['tax_number'] = @$tax_number;
			}
		}
		elseif ($current_area == 'A' || (!empty($active_modules['Simple_Mode']) && $current_area == "P")) {
			# Administrator can edit 'tax_number' and 'tax_exempt'
			$profile_values['tax_number'] = $tax_number;
			$profile_values['tax_exempt'] = (@$tax_exempt == 'Y' ? 'Y' : 'N');
		}

		$activity_changed = false;

		if ((defined('USER_MODIFY') || defined('USER_ADD')) && @$login_ != @$login ) {
			# Currently logged admin cannot change self status or activity flag
			$profile_values['change_password'] = empty($change_password) ? 'N' : 'Y';
			$profile_values['status'] = empty($status) ? 'N' : $status;
			$profile_values['activity'] = empty($activity) ? 'N' : $activity;

			$old_activity = func_query_first_cell("SELECT activity FROM $sql_tbl[customers] WHERE login='$uname' AND usertype='$login_type'");
			$activity_changed = ($profile_values['activity'] != $old_activity);
		}

		if ($mode=="update") {
			$intershipper_recalc = "Y";
			func_array2update('customers', $profile_values, "login='$login' AND usertype='$login_type'");

			db_query("DELETE FROM $sql_tbl[register_field_values] WHERE login = '$login'");
			if ($additional_values) {
				foreach ($additional_values as $k => $v)
					db_query("INSERT INTO $sql_tbl[register_field_values] (fieldid, login, value) VALUES ('$k', '$login', '$v')");
			}

			if ($login_type == 'B') {
				if (!$plan_id)
					$plan_id = $config['XAffiliate']['default_affiliate_plan'];

				if ($plan_id)
					db_query("REPLACE INTO $sql_tbl[partner_commissions] VALUES ('$uname', '$plan_id')");
			}

			#
			# Update membership
			#
			if ($current_area=="A" || ($active_modules["Simple_Mode"] && $current_area=="P"))
				db_query("UPDATE $sql_tbl[customers] SET membershipid = '$membershipid' WHERE login='$login' AND usertype='$login_type'");

			$registered="Y";

			#
			# Send mail notifications to customer department and signed customer
			#
			if (!$anonymous_user) {

				$newuser_info = func_userinfo($login, $login_type, true);
				$mail_smarty->assign("userinfo",$newuser_info);

				#
				# Send mail to registered user
				#
				$to_customer = $newuser_info["language"];

				if($config['Email_Note']['eml_profile_modified_customer'] == 'Y')
					func_send_mail($newuser_info["email"], "mail/profile_modified_subj.tpl", "mail/profile_modified.tpl", $config["Company"]["users_department"], false);
				#
				# Send mail to customers department
				#
				if($config['Email_Note']['eml_profile_modified_admin'] == 'Y')
					func_send_mail($config["Company"]["users_department"], "mail/profile_admin_modified_subj.tpl", "mail/profile_admin_modified.tpl", $newuser_info["email"], true);
			}
		}
		else {
			#
			# Add new person to customers table
			#
			$intershipper_recalc = "Y";

			$profile_values['login'] = $uname;
			$profile_values['usertype'] = $usertype;

			if (!defined('USER_MODIFY') && !defined('USER_ADD')) {
				$profile_values['change_password'] = 'N';
				$profile_values['status'] = 'Y';
				$profile_values['activity'] = 'Y';
			}

			if (!isset($profile_values['cart']))
				$profile_values['cart'] = '';

			func_array2insert('customers', $profile_values);

			db_exec("REPLACE INTO $sql_tbl[login_history] (login, date_time, usertype, action, status, ip) VALUES (?,?,?,?,?,?)",
				array ($uname,time(),$usertype,'login','success',$REMOTE_ADDR));

			$new_user_flag = true;

			db_query("DELETE FROM $sql_tbl[register_field_values] WHERE login = '$uname'");
			if ($additional_values) {
				foreach ($additional_values as $k => $v) {
					db_query("INSERT INTO $sql_tbl[register_field_values] (fieldid, login, value) VALUES ('$k', '$uname', '$v')");
				}
			}

			if ($usertype == 'B') {
				if ($config['XAffiliate']['partner_register_moderated'] == 'Y' && !defined('USER_MODIFY') && !defined('USER_ADD')) {
					db_query("UPDATE $sql_tbl[customers] SET status = 'Q' WHERE login = '$uname'");
				}

				if (!$plan_id)
					$plan_id = $config['XAffiliate']['default_affiliate_plan'];

				if ($plan_id)
					db_query("INSERT INTO $sql_tbl[partner_commissions] VALUES ('$uname', '$plan_id')");
			}

			#
			# Set prefered language for new customer
			#
			$_user_lngcode = "US";
			if (defined('USER_ADD')) {
				if ($usertype == "C")
					$_user_lngcode = $config["default_customer_language"];
				else
					$_user_lngcode = $config["default_admin_language"];
			}
			elseif ($store_language) {
				$_user_lngcode = $store_language;
			}

			db_query ("UPDATE $sql_tbl[customers] SET language='$_user_lngcode' WHERE login='$uname'");

			#
			# Update membership
			#
			if ($current_area=="A" || ($active_modules["Simple_Mode"] && $current_area=="P"))
				db_query("UPDATE $sql_tbl[customers] SET membershipid = '$membershipid' WHERE login='$uname'");

			#
			# Set A-status
			#
			if ($anonymous_user) {
				db_query("UPDATE $sql_tbl[customers] SET status='A' WHERE login='$uname' AND usertype='$usertype'");
			}

			$registered="Y";

			#
			# Send mail notifications to customer department and signed customer
			#
			$newuser_info = func_userinfo($uname, $usertype, true);
			$mail_smarty->assign("userinfo",$newuser_info);

			#
			# Send mail to registered user (do not send to anonymous)
			#
			if (!$anonymous_user && $email) {
				if ($usertype=="B") {
					if (@$config['XAffiliate']['eml_signin_partner_notif'] == 'Y')
						func_send_mail($email, "mail/signin_notification_subj.tpl", "mail/signin_partner_notif.tpl", $config["Company"]["users_department"], false);
				}
				else {
					if ($config['Email_Note']['eml_signin_notif'] == 'Y')
						func_send_mail($email, "mail/signin_notification_subj.tpl", "mail/signin_notification.tpl", $config["Company"]["users_department"], false);
				}
			}

			#
			# Send mail to customers department
			#
			if (!$anonymous_user && $config['Email_Note']['eml_signin_notif_admin'] == 'Y') {
				func_send_mail($config["Company"]["users_department"], "mail/signin_admin_notif_subj.tpl", "mail/signin_admin_notification.tpl", $email, true);

			}

			#
			# Auto-log in
			#
			if ($usertype=="C" || ($usertype=="B" && $login=="")) {
				$_curtime = time();
				db_query("UPDATE $sql_tbl[customers] SET last_login='$_curtime', first_login='$_curtime' WHERE login = '$uname'");
				$auto_login = true;
				$login = $uname;
				$login_type = $usertype;
				$logged = "";
				x_session_register("identifiers",array());
				$identifiers[$usertype] = array (
					'login' => $login,
					'login_type' => $login_type,
				);

			}

		}

		if (!empty($active_modules['SnS_connector']) && $usertype == 'C' && defined("AREA_TYPE") && constant("AREA_TYPE") == 'C') {
			func_generate_sns_action("Register");
			if ($auto_login)
				func_generate_sns_action("Login");
		}

		if (!empty($active_modules['Special_Offers']) && $usertype=='C' && (defined('USER_MODIFY') || defined('USER_ADD'))) {
			include $xcart_dir."/modules/Special_Offers/register_customer.php";
		}

	}
	else {
		#
		# Fields filled with errors
		#

		if (!empty($fillerror)) $reg_error="F";
		if (!empty($eerror)) $reg_error="E";
		if (!empty($uerror)) $reg_error="U";
		if (!empty($error)) $reg_error="I";
	}

	if ($anonymous_user) {
		$uname="";
		$passwd1="";
		$passwd2="";
	}

	#
	# Fill $userinfo array if error occured
	#
	$userinfo = $HTTP_POST_VARS;
	if ($HTTP_POST_VARS['additional_values'] && $additional_fields) {
		foreach ($additional_fields as $k => $v) {
			$additional_fields[$k]['value'] =
				$HTTP_POST_VARS['additional_values'][$v['fieldid']];
		}
	}

	$profile_modified_data[$user] = func_array_map("stripslashes", $userinfo);
	$profile_modified_add_field[$user] = $additional_fields;

	if (!empty($av_recheck)) {
		$top_message["type"] = "E";
		$top_message["content"] = func_get_langvar_by_name("txt_ups_av_reenter");
	}
	elseif ($reg_error || $error) {
		if($reg_error == 'U') {
			$top_message["content"] = func_get_langvar_by_name("txt_user_already_exists");
		}
		elseif($reg_error == 'E') {
			$top_message["content"] = func_get_langvar_by_name("txt_email_already_exists");
		}
		else {
			$top_message["content"] = func_get_langvar_by_name("msg_err_profile_upd");
		}

		$top_message["type"] = "E";
		$top_message["reg_error"] = $reg_error;
		$top_message["error"] = $error;
		$top_message["emailerror"] = $emailerror;
	}
	else {
		if (@$new_user_flag && $anonymous_user) {
			# Anonymois profile is created
			$top_message["content"] = func_get_langvar_by_name("msg_anonymous_profile_add");
		}
		elseif (@$new_user_flag) {
			# Profile is created
			$top_message["content"] = func_get_langvar_by_name("msg_profile_add");
		}
		else {
			if ($anonymous_user) {
				# Anonymous profile is updated
				$top_message["content"] = func_get_langvar_by_name("msg_anonymous_profile_upd");
			}
			else {
				# Profile is updated
				$top_message["content"] = func_get_langvar_by_name("msg_profile_upd");
			}
		}

		$profile_modified_data = "";
		$profile_modified_add_field = "";
	}

	$script = $PHP_SELF."?".$QUERY_STRING;

	if (defined('USER_MODIFY') || defined('USER_ADD')) {
		$login = $login_;
		$login_type = $login_type_;

		if (defined('USER_ADD') && !$reg_error && !$error) {
			$script = "user_modify.php?$QUERY_STRING&user=".urlencode($uname);
			if(($mode != "update") && ($usertype == "P") && ( !($uerror || $eerror || $fillerror) )) {
				@mkdir($files_dir_name.DIRECTORY_SEPARATOR.$uname, 0775);
			}
		}

		if ($usertype == 'P' && $activity_changed) {
			$p_categories = db_query("SELECT $sql_tbl[products_categories].categoryid FROM $sql_tbl[products], $sql_tbl[products_categories] WHERE $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products].provider='$uname' GROUP BY $sql_tbl[products_categories].categoryid");
			if ($p_categories) {
				$cats = array();
				while ($row = db_fetch_array($p_categories)) {
					$cats[] = $row['categoryid'];

					if (count($cats) >= 100) {
						func_recalc_product_count(func_get_category_parents($cats));
						$cats = array();
					}
				}
				db_free_result($p_categories);
			}
		}
	}
	elseif ($action == "cart") {
		if (!empty($reg_error) || !empty($av_error) || !empty($av_recheck)) {
			if (empty($login)) {
				# Anonymous checkout
				$script = "cart.php?mode=checkout";
			}
			elseif (!empty($paymentid)) {
				$script = "register.php?mode=update&action=cart&paymentid=".intval($paymentid);
			}
			else {
				$script = "register.php?mode=update&action=cart";
			}
		}
		elseif (empty($paymentid)) {
			$script = "cart.php?mode=checkout&registered=";
		}
		else {
			$script = "cart.php?mode=checkout&paymentid=".intval($paymentid);
		}
	}
	elseif ($current_area == "C" && !empty($cart)) {
		include_once $xcart_dir."/shipping/shipping.php";

		$shippings = func_get_shipping_methods_list($cart, $cart["products"], $userinfo);
		if (is_array($shippings)) {
			$found = false;
			$shippingid = $cart["shippingid"];
			for ($i = 0; $i < count($shippings); $i++) {
				if ($shippingid == $shippings[$i]["shippingid"]) {
					$found = true;
					break;
				}
			}

			if (!$found) {
				$shippingid = $shippings[0]["shippingid"];
			}
		}
		else {
			$shippingid = 0;
		}

		$cart["shippingid"] = $shippingid;
		$products = func_products_in_cart($cart, (!empty($userinfo["membership"])?$userinfo["membership"]:""));
		$cart = func_array_merge($cart, func_calculate($cart, $products, $login, "C", (!empty($paymentid) ? intval($paymentid) : 0)));

		# And again, because shippingid is not saved after func_calculate
		$cart["shippingid"] = $shippingid;
	}
	elseif ($current_area == 'B' && $login_type == 'B' && func_query_first_cell("SELECT status FROM $sql_tbl[customers] WHERE login = '$login' AND usertype = '$login_type'") == 'Q') {
		$script = $xcart_catalogs['partner']."/home.php?mode=profile_created";
	}

	if (empty($av_error)) {
		func_header_location($script);
	}
}
else {
	#
	# REQUEST_METHOD = GET
	#
	if ($mode=="update" && !empty($login)) {
		$userinfo = func_userinfo($login, $login_type, true);
	}
	elseif ($mode=="delete" && @$confirmed=="Y" && !empty($login)) {
		require $xcart_dir."/include/safe_mode.php";

		$olduser_info = func_userinfo($login, $login_type, true);

		$to_customer = $olduser_info["language"];

		func_delete_profile($login,$login_type);
		$login="";
		$login_type="";
		$smarty->clear_assign("login");
		#
		# Send mail notifications to customer department and signed customer
		#
		$mail_smarty->assign("userinfo",$olduser_info);

		#
		# Send mail to registered user
		#
		$anonymous_user = func_is_anonymous($olduser_info["login"]);

		if (!$anonymous_user && $config['Email_Note']['eml_profile_deleted'] == 'Y') {
			func_send_mail($olduser_info["email"], "mail/profile_deleted_subj.tpl", "mail/profile_deleted.tpl", $config["Company"]["users_department"], false);
		}

		#
		# Send mail to customers department
		#
		if (!$anonymous_user && $config['Email_Note']['eml_profile_deleted_admin'] == 'Y') {
			func_send_mail($config["Company"]["users_department"], "mail/profile_admin_deleted_subj.tpl", "mail/profile_admin_deleted.tpl", $olduser_info["email"], true);
		}
	}
}

if (!empty($active_modules['Special_Offers']) && $usertype=='C' && (defined('USER_MODIFY') || defined('USER_ADD'))) {
	include $xcart_dir."/modules/Special_Offers/register_customer.php";
}

if ($current_area == "C" && $active_modules["UPS_OnLine_Tools"]) {
	#
	# Get the UPS OnLine Tools module settings
	#
	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

	$ups_parameters = unserialize($params["param00"]);
	if (!is_array($ups_parameters))
		$ups_parameters["av_status"] = "N";

	$smarty->assign("av_enabled", $ups_parameters["av_status"]);
}

if (!empty($uerror) || !empty($eerror) || !empty($fillerror) || !empty($error)) {
	$userinfo["firstname"]=stripslashes($firstname);
	$userinfo["lastname"]=stripslashes($lastname);
	$userinfo["company"]=stripslashes($company);
	$userinfo["ssn"]=stripslashes(@$ssn);
	$userinfo["tax_number"]=stripslashes(@$tax_number);
	$userinfo["b_title"]=stripslashes($b_title);
	$userinfo["b_firstname"]=stripslashes($b_firstname);
	$userinfo["b_lastname"]=stripslashes($b_lastname);
	$userinfo["b_address"]=stripslashes($b_address);
	$userinfo["b_address_2"]=stripslashes($b_address_2);
	$userinfo["b_city"]=stripslashes($b_city);
	$userinfo["b_county"]=stripslashes(@$b_county);
	$userinfo["b_state"]=stripslashes($b_state);
	$userinfo["b_zipcode"]=stripslashes($b_zipcode);
	$userinfo["s_title"]=stripslashes($s_title);
	$userinfo["s_firstname"]=stripslashes($s_firstname);
	$userinfo["s_lastname"]=stripslashes($s_lastname);
	$userinfo["s_address"]=stripslashes($s_address);
	$userinfo["s_address_2"]=stripslashes($s_address_2);
	$userinfo["s_city"]=stripslashes($s_city);
	$userinfo["s_county"]=stripslashes(@$s_county);
	$userinfo["s_state"]=stripslashes($s_state);
	$userinfo["s_zipcode"]=stripslashes($s_zipcode);
	$userinfo["phone"]=stripslashes($phone);
	$userinfo["fax"]=stripslashes($fax);
	$userinfo["email"]=stripslashes($email);
	$userinfo["uname"]=stripslashes($uname);
	$userinfo["passwd1"]=stripslashes($passwd1);
	$userinfo["passwd2"]=stripslashes($passwd2);
	$userinfo["password_hint"]=stripslashes(@$password_hint);
	$userinfo["password_hint_answer"]=stripslashes(@$password_hint_answer);
	
	if ($current_area == 'A' || (!empty($active_modules['Simple_Mode']) && $current_area == "P")) {
		$userinfo["tax_exempt"] = (@$tax_exempt == "Y" ? "Y" : "N");
	}

	$profile_modified_data[$user] = $userinfo;
}

if ($REQUEST_METHOD == "GET") {
	if (!empty($profile_modified_data) && !empty($profile_modified_data[$user]))
		$userinfo = $profile_modified_data[$user];

	if (!empty($profile_modified_add_field) && !empty($profile_modified_add_field[$user])) {
		$additional_fields = $profile_modified_add_field[$user];
	}
}

$ship2diff = false;
if (!empty($userinfo)) {
	foreach ($userinfo as $key=>$value) {
		if (is_string($value)) {
			$userinfo[$key] = htmlspecialchars($value);
		}
	}

	foreach (array('title','firstname','lastname','city','state','country','zipcode','address','address_2','county') as $v) {
		if ($userinfo['s_'.$v] != $userinfo['b_'.$v]) {
			$ship2diff = true;
			break;
		}
	}

	$smarty->assign("userinfo",$userinfo);
	if ($REQUEST_METHOD == "GET" && !empty($active_modules["News_Management"])) {
		$tmp = func_query("SELECT listid FROM $sql_tbl[newslist_subscription] WHERE email='$userinfo[email]'");
		$subscription = "";
		if (is_array($tmp)) {
			foreach ($tmp as $v) {
				$subscription[$v["listid"]] = true;
			}
		}
	}

} else {

	$share_fields = array("s_title", "s_lastname", "s_firstname", "s_address", "s_address_2", "s_city", "s_state", "s_county", "s_country", "s_zipcode");
	foreach ($default_fields as $k => $v) {
		if (in_array($k, $share_fields) && $v['avail'] && $v['required']) {
			$k = substr($k, 2);
			if (!$default_fields['b_'.$k]['avail'] || !$default_fields['b_'.$k]['required']) {
				$ship2diff = true;
				break;
			}
		}
	}

}

$smarty->assign("ship2diff", $ship2diff);
$smarty->assign("subscription", $subscription);

$newslists = func_query("SELECT * FROM $sql_tbl[newslists] WHERE avail='Y' AND subscribe='Y' AND lngcode='$shop_language'");
$smarty->assign("newslists", $newslists);

if (!empty($registered))
	$smarty->assign("registered", $registered);

if (!empty($reg_error)) {
	$smarty->assign("reg_error",$reg_error);
}

if (!empty($emailerror))
	$smarty->assign("emailerror",$emailerror);

if ($mode=="delete") {
	$location[count($location)-1] = array(func_get_langvar_by_name("lbl_delete_profile"), "");
	$smarty->assign("main","profile_delete");
}
elseif($mode=="notdelete") {
	$top_message["content"] = func_get_langvar_by_name("txt_profile_not_deleted");
	func_header_location("home.php");
}
else {
	$smarty->assign("main","register");
}

if (!empty($active_modules['XAffiliate']) && (($mode == 'update' && $login_type == 'B' ) || $current_area == 'B')) {
    $plans = func_query("SELECT * FROM $sql_tbl[partner_plans] WHERE status = 'A' ORDER BY plan_title");
    $smarty->assign("plans", $plans);
}

if ($REQUEST_METHOD == "GET")
	$profile_modified_data = "";

if ($HTTP_GET_VARS['parent']) {
	$smarty->assign("parent", $parent);
}

# Hide section 'Username & Password' when anonymous customer updates his profile
if ($current_area == "C" && $userinfo["status"] == "A" && !empty($login)) {
	$smarty->assign("hide_account_section", "Y");
}

$smarty->assign("default_fields", $default_fields);
$smarty->assign("additional_fields", $additional_fields);
$smarty->assign("is_areas", $is_areas);
$m_usertype = (empty($HTTP_GET_VARS['usertype']) ? $current_area : $HTTP_GET_VARS['usertype']);
$membership_levels = func_get_memberships($m_usertype);
$smarty->assign("membership_levels", $membership_levels);
$smarty->assign("titles", func_get_titles());

x_session_save();
?>
