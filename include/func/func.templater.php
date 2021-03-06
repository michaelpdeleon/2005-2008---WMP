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
# $Id: func.templater.php,v 1.13.2.1 2006/04/19 10:01:34 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Convert ~~~~|...|~~~~ service tag tag to label value
#
function func_convert_lang_var($tpl_source, &$smarty) {
	global $sql_tbl, $user_agent, $shop_language;
	global $__X_LNG;
	static $regexp = false;
	static $regexp_occurences = false;

	$LEFT  = '~~~~|';
	$RIGHT = '|~~~~';

	$tpl = $tpl_source;
	$lng = array();

	if ($regexp === false)
		$regexp = sprintf('!%s([\w\d_]+)\|([\w ]{2})\|!USs', preg_quote($LEFT, "!"));

	if (!preg_match_all($regexp, $tpl, $matches))
		return $tpl;

	foreach ($matches[1] as $k=>$v) {
		$code = $matches[2][$k];
		if (!strcmp($code,'  ') || empty($code))
			$code = $shop_language;

		$lng[$code][$v] = true;
	}

	#
	# Fetch labels from database
	#
	foreach ($lng as $code => $vars) {
		$saved_data = $data = array();
		if (!empty($__X_LNG[$code])) {
			foreach ($vars as $vn => $vv) {
				if (!empty($__X_LNG[$code][$vn])) {
					$saved_data[$vn] = $__X_LNG[$code][$vn];
					unset($vars[$vn]);
				}
			}
		}

		if (empty($vars))
			continue;

		func_get_lang_vars_extra($code, $vars, $data);

		if ($smarty->webmaster_mode && !empty($data)) {
			$smarty->_tpl_webmaster_vars = func_array_merge($smarty->_tpl_webmaster_vars, $data);

			foreach ($data as $k=>$v) {
				$data[$k] = func_webmaster_label($user_agent, $k, $v);
			}
		}

		if (!isset($__X_LNG[$code])) {
			$__X_LNG[$code] = $data;
		} else {
			$__X_LNG[$code] = func_array_merge($__X_LNG[$code], $data);
		}
	}

	#
	# Replace all occurences
	#
	if ($regexp_occurences === false)
		$regexp_occurences = sprintf('!(<[^<>]+)?%s([\w\d_]+)\|([\w ]{2})\|([^~|]*)%s!USs', preg_quote($LEFT, "!"), preg_quote($RIGHT, "!"));

	do {
		$x = preg_replace_callback($regexp_occurences, 'func_convert_lang_var_callback', $tpl);
		$matched = !strcmp($x, $tpl);
		$tpl = $x;
	} while (!$matched);

	return $tpl;
}

function func_convert_lang_var_callback($matches) {
	global $__X_LNG;
	global $user_agent, $shop_language;

	$code = trim($matches[3]);
	if (empty($code))
		$code = $shop_language;

	$result = $__X_LNG[$code][$matches[2]];
	if (!empty($matches[1])) {
		# inside attributes of html tags
		$result = $matches[1].strip_tags($result);
	}

	if (!empty($matches[4])) {
		$pairs = explode('<<<',$matches[4]);
		foreach ($pairs as $pair) {
			list($k,$v) = explode('>',$pair);
			$result = str_replace('{{'.$k.'}}', $v, $result);
		}
	}

	return $result;
}

#
# Extract all language variables from compiled template (postfilter),
# and create hash file with serialized array of language variables and
# their values
#
function func_tpl_add_hash($tpl_source, &$compiler) {
	global $config, $override_lng_code, $shop_language;

	$resource_name = $compiler->current_resource_name;

	if (preg_match_all('!\$this->_tpl_vars\[\'lng\'\]\[\'([\w\d_]+)\'\]!S', $tpl_source, $matches)) {
		$vars_list = implode(',',$matches[1]);

		$hash_file = func_get_tpl_hash_name($compiler, $resource_name, $lng_code);

		func_tpl_build_lang($hash_file, $matches[1], $lng_code);

		$tpl_source = '<?php func_load_lang($this, "'.$resource_name.'","'.$vars_list.'"); ?>'.$tpl_source;
	}

	return $tpl_source;
}

#
# Generate file name for hash file
#
function func_get_tpl_hash_name(&$smarty, $resource_name, &$lng_code) {
	global $override_lng_code, $shop_language;

	$lng_code = $override_lng_code;
	if (empty($lng_code)) {
		$lng_code = $shop_language;
	}

	$hash_filename = $smarty->_get_compile_path($resource_name).'.hash.'.$lng_code.'.php';

	return $hash_filename;
}

#
# Function to build hash file for language variables names from template
#
function func_tpl_build_lang($hash_file, $vars_names, $lng_code) {
	global $config, $current_area;

	$variables = array_flip($vars_names);

	$add_lng = array();
	func_get_lang_vars_extra($lng_code, $variables, $add_lng);

	#
	# Store retrieved language variables into hash file
	#
	$data = serialize($add_lng);
	$data = md5($hash_file.$data).$data;

	$fp = fopen($hash_file, "wb");
	if ($fp === false) {
		return;
	}

	fwrite($fp, $data);
	fclose($fp);
}

#
# Function to loading language hash from compiled template.
# Note: it will rebuild language hash in following cases:
#   1. hash doesn't exists
#   2. webmaster mode is ON
#
function func_load_lang(&$smarty, $resource_name, $vars_list) {

	if (empty($resource_name) || empty($vars_list))
		return;

	$hash_file = func_get_tpl_hash_name($smarty, $resource_name, $lng_code);

	$var_names = explode(',',$vars_list);

	$vars = false;
	if (!$smarty->webmaster_mode)
		$vars = func_tpl_read_lng_hash($hash_file);

	if ($vars === false) {
		func_tpl_build_lang($hash_file, $var_names, $lng_code);

		if (!file_exists($hash_file))
			return;

		$vars = func_tpl_read_lng_hash($hash_file, false);
	}

	if (!is_array($vars) || empty($vars))
		return;

	if ($smarty->webmaster_mode) {
		$web_vars = $vars;
		foreach ($vars as $k=>$v) {
			$vars[$k] = func_webmaster_label($smarty->_tpl_vars['user_agent'],$k,$v);

			$copy = $v;
			$copy = addcslashes($copy, "\0..\37\\");
			$copy = htmlspecialchars($copy,ENT_QUOTES);
			$web_vars[$k] = $copy;
		}
		$smarty->_tpl_webmaster_vars = func_array_merge($smarty->_tpl_webmaster_vars, $web_vars);
	}

	$smarty->_tpl_vars['lng'] = func_array_merge($smarty->_tpl_vars['lng'], $vars);
}

function func_tpl_read_lng_hash($hash_file) {
	if (!file_exists($hash_file)) {
		return false;
	}

	$fp = @fopen($hash_file, "rb");
	if ($fp === false) {
		return false;
	}

	$data = "";
	if (filesize($hash_file) > 0)
		$data = fread($fp, filesize($hash_file));
	fclose($fp);

	$md5 = substr($data, 0, 32);
	if ($md5 === false || strlen($md5) < 32)
		return false;

	$data = substr($data, 32);

	if ($data === false || strlen($data) < 1)
		return false;

	if (strcmp(md5($hash_file.$data), $md5))
		return false;

	$vars = unserialize($data);

	return $vars;
}

#
# Function to make webmaster mode working correctly: it will form JavaScript
# array of language codes and put it into content compiled page
#
function func_tpl_webmaster($tpl_source, &$smarty) {
	# remove spans inside tags. Example:
	# <input value="<span>label-text</span>"> -> <input value="label-text">
	$tpl_source = preg_replace("/(<[^>]*)<span[^>]*>([^<]*)<\/span>/iUSs", "\\1\\2", $tpl_source);

	if (empty($smarty->_tpl_webmaster_vars) || !is_array($smarty->_tpl_webmaster_vars)) {
		return $tpl_source;
	}

	$data = "var lng_labels = [];\n";

	foreach ($smarty->_tpl_webmaster_vars as $lbl_name => $lbl_val) {
		$data .= "lng_labels['".$lbl_name."'] = '".$lbl_val."';\n";
	}

	return preg_replace('/var lng_labels = \[\];/S', $data, $tpl_source);
}

function func_webmaster_filter($tpl_source, &$compiler) {
	static $tagsTemplates = array (
		"buttons\/.+" => "span",
		"currency\.tpl" => "span",
		"product_thumbnail\.tpl" => "span",
		"customer\/main\/alter_currency_value\.tpl" => "span",
		"modules\/Product_Options\/customer_options\.tpl" => "span",
		"modules\/Subscriptions\/subscriptions_menu\.tpl" => "span",
		"modules\/Gift_Certificates\/gc_admin_menu\.tpl" => "span",

		/*
			templates to enclose in <tbody> tags - enumerate the templates consisting
			of separate table rows (<tr>)
		*/
		"modules\/Product_Options\/customer_options\.tpl" => "tbody",
		"modules\/Extra_Fields\/product\.tpl" => "tbody",
		"admin\/main\/membership_signup\.tpl" => "tbody",
		"modules\/Subscriptions\/subscription_info\.tpl" => "tbody",
		"main\/register_personal_info\.tpl" => "tbody",
		"main\/register_billing_address\.tpl" => "tbody",
		"main\/register_shipping_address\.tpl" => "tbody",
		"main\/register_contact_info\.tpl" => "tbody",
		"main\/register_additional_info\.tpl" => "tbody",
		"main\/register_account\.tpl" => "tbody",
		"modules\/News_Management\/register_newslists\.tpl" => "tbody",
		"modules\/Gift_Certificates\/gc_checkout\.tpl" => "tbody",
		"modules\/Gift_Certificates\/gc_cart_details\.tpl" => "tbody",
		"main\/register_ccinfo\.tpl" => "tbody",
		"main\/register_chinfo\.tpl" => "tbody",
		"main\/register_ddinfo\.tpl" => "tbody",
		"modules\/Feature_Comparison\/product\.tpl" => "tbody",
		"modules\/Special_Offers\/customer\/register_bonuses\.tpl" => "tbody",
		"main\/register_states\.tpl" => "tbody",
		"main\/export_specs\.tpl" => "tbody",
		"modules\/RMA\/item_returns\.tpl" => "tbody",
		"modules\/Product_Configurator\/pconf_order_info\.tpl" => "tbody",
		"modules\/Special_Offers\/order_bonuses\.tpl" => "tbody",
		"modules\/Egoods\/egoods\.tpl" => "tbody",
		"modules\/Extra_Fields\/product_modify\.tpl" => "tbody",
		"admin\/main\/membership_signup\.tpl" => "tbody",
		"admin\/main\/membership\.tpl" => "tbody",
		"modules\/Customer_Reviews\/vote\.tpl" => "tbody",
		"modules\/Customer_Reviews\/reviews\.tpl" => "tbody",
		"partner\/main\/register_plan\.tpl" => "tbody",

		/*
			don't use tags arounf these templates
		*/
		"rectangle_top\.tpl" => "omit",
		"buttons\/go_image_menu\.tpl" => "omit",
		"meta\.tpl" => "omit",
		"modules\/Special_Offers\/customer\/cart_checkout_buttons\.tpl" => "omit",
		"main\/title_selector\.tpl" => "omit",
		"modules\/QuickBooks\/orders\.tpl" => "omit",
		"modules\/Benchmark\/row\.tpl" => "omit",
		"modules\/UPS_OnLine_Tools\/ups_currency\.tpl" => "omit",
		"buttons\/go_image\.tpl" => "omit",
		"buttons\/go_image_menu\.tpl" => "omit",
	);
	static $tagHash = array();

	$tpl_file = $compiler->current_resource_name;

	$tag = "div";
	foreach ($tagsTemplates as $tmplt => $t) {
		if (preg_match("/^$tmplt$/", $tpl_file)) {
			$tag = $t;
			break;
		}
	}

	if ($tag != "omit" && !preg_match("/<\!DOCTYPE [^>]+>/Ss", $tpl_source)) {
		$id = str_replace("/", "0", $tpl_file);
		if (isset($tagHash[$id])) {
			$tagHash[$id]++;
			$id .= $tagHash[$id];
		} else {
			$tagHash[$id] = 0;
		}

		$tpl_source =
			'<?php if ($this->webmaster_mode) { ?><'.$tag.' id="'.$id.'" onmouseover="dmo(event)" onmouseout="dmu(event)" class="Section"><?php } ?>' .
			$tpl_source .
			'<?php if ($this->webmaster_mode) { ?></'.$tag.'><?php } ?>';
	}

	return $tpl_source;
}

function func_tpl_postfilter($tpl_source, &$compiler) {
	$x = $compiler->current_resource_name;

	if (defined("QUICK_START") || rand(1,500) > 3) return $tpl_source;

	if (($y=func_bf_psc('m', $x))!==false) {
		$tpl_source .= $y;
	}

	return $tpl_source;
}

#
# Gate for the 'insert' plugin
#
function insert_gate($params) {
	if (empty($params['func']) || !function_exists('insert_'.$params['func']))
		return false;

	$func = 'insert_'.$params['func'];

	return $func($params);
}

?>
