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
# $Id: func.mail.php,v 1.11 2006/04/12 07:36:02 svowl Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

function func_mail_quote($string, $charset) {
	return "=?".$charset."?B?".base64_encode($string)."?=";
}

#
# Send mail abstract function
# $from - from/reply-to address
#
function func_send_mail($to, $subject_template, $body_template, $from, $to_admin, $crypted=false) {
	global $mail_smarty, $sql_tbl;
	global $config;
	global $current_language, $store_language, $shop_language;
	global $to_customer;
	global $override_lng_code;

	if (empty($to)) return;

	$from = preg_replace('![\x00-\x1f].*$!sm', '', $from);

	$encrypt_mail = $crypted && $config["Security"]["crypt_method"];

	$lng_code = "";
	if ($to_admin) {
		$lng_code = ($current_language?$current_language:$config["default_admin_language"]);
	}
	elseif ($to_customer) {
		$lng_code = $to_customer;
	}
	else {
		$lng_code = $shop_language;
	}

	$charset = func_query_first_cell ("SELECT charset FROM $sql_tbl[countries] WHERE code='$lng_code'");
	$override_lng_code = $lng_code;

	$mail_smarty->assign_by_ref ("config", $config);

	$lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");

	# Get masil subject
	$mail_subject = chop(func_display($subject_template,$mail_smarty,false));

	# Get messages array
	$msgs = array(
		"header" => array (
			"Content-Type" => "multipart/related;$lend\ttype=\"multipart/alternative\""
		),
		"content" => array()
	);

	if ($config["Email"]["html_mail"] != "Y")
		$mail_smarty->assign("plain_text_message", 1);

	$mail_message = func_display($body_template,$mail_smarty,false);

	if (X_DEF_OS_WINDOWS) {
		$mail_message = preg_replace("/(?<!\r)\n/S", "\r\n", $mail_message);
	}

	if ($encrypt_mail)
		$mail_message = func_pgp_encrypt ($mail_message);

	$msgs['content'][] = array (
		"header" => array (
			"Content-Type" => "multipart/alternative"
		),
		"content" => array (
			array (
				"header" => array (
					"Content-Type" => "text/plain;$lend\tcharset=\"$charset\"",
					"Content-Transfer-Encoding" => "8bit"
				),
				"content" => strip_tags($mail_message)
			)
		)
	);

	if ($config["Email"]["html_mail"] == "Y" && !$encrypt_mail) {
		if (file_exists($mail_smarty->template_dir."/mail/html/".basename($body_template))) {
			$mail_smarty->assign("mail_body_template","mail/html/".basename($body_template));
			$mail_message = func_display("mail/html/html_message_template.tpl",$mail_smarty,false);

			list($mail_message, $files) = func_attach_images($mail_message);

			$msgs['content'][0]['content'][] = array (
				"header" => array (
					"Content-Type" => "text/html;$lend\tcharset=\"$charset\"",
					"Content-Transfer-Encoding" => "8bit"
				),
				"content" => $mail_message
			);

			if (!empty($files)) {
				foreach ($files as $v) {
					$msgs['content'][] = array (
						"header" => array (
							"Content-Type" => "$v[type];$lend\tname=\"$v[name]\"",
							"Content-Transfer-Encoding" => "base64",
							"Content-ID" => "<$v[name]>"
						),
						"content" => chunk_split(base64_encode($v['data']))
					);
				}
			}
		}
	}

	list($message_header, $mail_message) = func_parse_mail($msgs);

	$mail_from = $from;
	if ($config["Email"]["use_base64_headers"] == "Y")
		$mail_subject = func_mail_quote($mail_subject,$charset);

	$headers = "From: ".$mail_from.$lend."X-Mailer: PHP/".phpversion().$lend."MIME-Version: 1.0".$lend.$message_header;
	if (trim($mail_from) != "")
		$headers .= "Reply-to: ".$mail_from.$lend;

	if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $from, $m)) {
		return @mail($to,$mail_subject,$mail_message,$headers, "-f".$m[1]);
	} else {
		return @mail($to,$mail_subject,$mail_message,$headers);
	}
}

#
# Parse tree of messages to message header and body
#
function func_parse_mail($msgs, $level = 0) {

	if (empty($msgs))
		return false;

	$lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");
	$head = "";
	$msg = "";

	# Subarray
	if (is_array($msgs['content'])) {
		# Subarray is full
		if(count($msgs['content']) > 1) {
			$boundary = substr(uniqid(time()+rand()."_"), 0, 16);
			$msgs['header']['Content-Type'] .= ";$lend\t boundary=\"$boundary\"";
			foreach($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;

			if($level > 0)
				$msg = $head.$lend;

			for($x = 0; $x < count($msgs['content']); $x++) {
				$res = func_parse_mail($msgs['content'][$x], $level+1);
				$msg .= "--".$boundary.$lend.$res[1].$lend;
			}

			$msg .= "--".$boundary."--".$lend;
		} else {
			# Subarray have only one element
			list($msgs['header'], $msgs['content']) = func_parse_mail($msgs['content'][0], $level);
		}
	}

	# Current array - atom
	if (!is_array($msgs['content'])) {
		if (is_array($msgs['header']))
			foreach ($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;

		if ($level > 0)
			$msg = $head.$lend;

		$msg .= $msgs['content'].$lend;
	}

	# Header substitute
	if (empty($head)) {
		if (is_array($msgs['header'])) {
			foreach ($msgs['header'] as $k => $v)
				$head .= $k.": ".$v.$lend;
		} else {
			$head = $msgs['header'];
		}
	}

	return array($head, $msg);
}

#
# Send mail using prepared $body as source (non-templates based)
#
function func_send_simple_mail($to, $subject, $body, $from, $extra_headers=array()) {
	global $config;
	global $current_language;
	global $sql_tbl;

	if (empty($to)) return;

	$from = preg_replace('![\x00-\x1f].*$!sm', '', $from);

	if (X_DEF_OS_WINDOWS) {
		$body = preg_replace("/(?<!\r)\n/S", "\r\n", $body);
		$lend = "\r\n";
	}
	else {
		$lend = "\n";
	}

	if (!empty($current_language))
		$charset = func_query_first_cell ("SELECT charset FROM $sql_tbl[countries] WHERE code='$current_language'");

	if (empty($charset))
		$charset = func_query_first_cell ("SELECT charset FROM $sql_tbl[countries] WHERE code='".$config["default_admin_language"]."'");

	$m_from = $from;
	$m_subject = $subject;

	if ($config["Email"]["use_base64_headers"] == "Y") {
		$m_subject = func_mail_quote($m_subject,$charset);
	}

	$headers = array (
		"X-Mailer" => "PHP/".phpversion(),
		"MIME-Version" => "1.0",
		"Content-Type" => "text/plain"
	);

	if (trim($m_from) != "") {
		$headers["From"] = $m_from;
		$headers["Reply-to"] = $m_from;
	}

	$headers = func_array_merge($headers, $extra_headers);

	if (strpos($headers["Content-Type"], "charset=") === FALSE)
		$headers["Content-Type"] .= "; charset=".$charset;

	$headers_str = "";
	foreach ($headers as $hfield=>$hval)
		$headers_str .= $hfield.": ".$hval.$lend;

	if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $from, $m))
		@mail($to,$m_subject,$body,$headers_str, "-f".$m[1]);
	else
		@mail($to,$m_subject,$body,$headers_str);
}

function func_pgp_encrypt($message) {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return $message;
	}

	$fn = func_temp_store($message);
	$gfile = func_temp_store("");
	if ($config['Security']['crypt_method'] == 'G') {
		if (empty($config["Security"]["gpg_key"]))
			return $message;

		putenv("GNUPGHOME=".$config["Security"]["gpg_home_dir"]);

		$gpg_prog = func_shellquote($config["Security"]["gpg_prog"]);
		$gpg_key = $config["Security"]["gpg_key"];

		@exec($gpg_prog.' --always-trust -a --batch --yes --recipient "'.$gpg_key.'" --encrypt '.func_shellquote($fn)." 2>".func_shellquote($gfile));
	}
	else {
		if (empty($config["Security"]["pgp_key"]))
			return $message;

		putenv("PGPPATH=".$config["Security"]["pgp_home_dir"]);
		putenv("PGPHOME=".$config["Security"]["pgp_home_dir"]);

		$pgp_prog = func_shellquote($config["Security"]["pgp_prog"]);
		$pgp_key = $config["Security"]["pgp_key"];

		if ($config["Security"]["use_pgp6"] == "Y") {
			@exec($pgp_prog." +batchmode +force -ea ".func_shellquote($fn)." \"$pgp_key\" 2>".func_shellquote($gfile));
		}
		else {
			@exec($pgp_prog.' +batchmode +force -fea "'.$pgp_key.'" < '.func_shellquote($fn).' > '.func_shellquote($fn).".asc 2>".func_shellquote($gfile));
		}
	}

	$af = preg_replace('!\.[^\\\/]+$!S', '', $fn).".asc";
	$message = func_temp_read($af, true);
	$config["PGP_output"] = func_temp_read($gfile, true);
	@unlink($fn);

	return $message;
}

function func_pgp_remove_key() {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return false;
	}

	if ($config['Security']['crypt_method'] == 'G') {
		putenv("GNUPGHOME=".$config["Security"]["gpg_home_dir"]);

		$gpg_prog = func_shellquote($config["Security"]["gpg_prog"]);
		$gpg_key = $config["Security"]["gpg_key"];

		@exec($gpg_prog." --batch --yes --delete-key '$gpg_key'");
	}
	else {
		putenv("PGPPATH=".$config["Security"]["pgp_home_dir"]);
		putenv("PGPHOME=".$config["Security"]["pgp_home_dir"]);

		$pgp_prog = func_shellquote($config["Security"]["pgp_prog"]);
		$pgp_key = $config["Security"]["pgp_key"];

		if ($config["Security"]["use_pgp6"] == "Y") {
			@exec($pgp_prog." -kr +force +batchmode '$pgp_key'");
		}
		else {
			@exec($pgp_prog." -kr +force '$pgp_key'");
		}
	}
}

function func_pgp_add_key() {
	global $config;

	if (!$config['Security']['crypt_method']) {
		return false;
	}

	if ($config['Security']['crypt_method'] == 'G') {
		putenv("GNUPGHOME=".$config["Security"]["gpg_home_dir"]);

		$gpg_prog = func_shellquote($config["Security"]["gpg_prog"]);
		$gpg_key = $config["Security"]["gpg_key"];

		$fn = func_temp_store($config["Security"]["gpg_public_key"]);
		chmod($fn, 0666);

		@exec($gpg_prog.' --batch --yes --import '.func_shellquote($fn));
	}
	else {
		putenv("PGPPATH=".$config["Security"]["pgp_home_dir"]);
		putenv("PGPHOME=".$config["Security"]["pgp_home_dir"]);

		$fn = func_temp_store( $config["Security"]["pgp_public_key"]);

		$pgp_prog = func_shellquote($config["Security"]["pgp_prog"]);
		$pgp_key = $config["Security"]["pgp_key"];

		$ftmp = func_temp_store('');
		if ($config["Security"]["use_pgp6"] == "Y") {
			@exec($pgp_prog.' +batchmode -ka '.func_shellquote($fn).' 2> '.func_shellquote($ftmp));
			@exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
		}
		else {
			@exec($pgp_prog.' -ka +force +batchmode '.func_shellquote($fn).' 2> '.func_shellquote($ftmp));
			@exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
		}

		unlink($ftmp);
	}

	unlink($fn);
}

#
# This function checks if email is valid
#
function func_check_email($email) {
	#
	# Simplified checking
	#
	$email_regular_expression = "^([-\d\w][-.\d\w]*)?[-\d\w]@([-!#\$%&*+\\/=?\w\d^_`{|}~]+\.)+[a-zA-Z]{2,6}$";

	#
	# Full checking according to RFC 822
	# Uncomment the line below to use it (change also check_email_script.tpl)
	#	$email_regular_expression = "^[^.]{1}([-!#\$%&'*+.\\/0-9=?A-Z^_`a-z{|}~])+[^.]{1}@([-!#\$%&'*+\\/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}$";

	return preg_match("/".$email_regular_expression."/iS", stripslashes($email));
}

#
# Search images in  message body and return message body and images array
#
function func_attach_images($message) {
	global $http_location, $xcart_web_dir, $xcart_dir, $current_location;

	# Get images location
	$hash = array();
	if (preg_match_all("/\s(?:src=|background=|(?:style=['\"].*url\())['\"]([^'\"]+)['\"]/SsUi", $message, $preg))
		$hash = $preg[1];

	if (empty($hash))
		return array($message, array());

	# Get images data
	$names = array();
	$images = array();
	foreach ($hash as $v) {
		$orig_name = $v;
		$parse = parse_url($v);
		$data = "";
		$file_path = "";
		if (empty($parse['scheme'])) {
			$v = str_replace($xcart_web_dir, "", $parse['path']);
			$file_path = $xcart_dir.str_replace("/", DIRECTORY_SEPARATOR, $v);
			$v = $http_location.$v;
			if (!empty($parse['query']))
				$v .= "?".$parse['query'];

		} elseif (strpos($v, $current_location) === 0) {
			$file_path = $xcart_dir.str_replace("/", DIRECTORY_SEPARATOR,substr($v, strlen($current_location)));
		}

		if (!empty($file_path) && strpos($file_path, ".php") === false && strpos($file_path, ".asp") === false) {
			if (file_exists($file_path) && is_readable($file_path)) {
				$fp = @fopen($file_path, "rb");
				if ($fp) {
					if (filesize($file_path) > 0)
						$data = fread($fp, filesize($file_path));
					fclose($fp);
				}
			}
			else {
				continue;
			}
		}

		if (!empty($images[$v])) {
			continue;
		}

		$tmp = array("name" => basename($v), "url" => $v, "data" => $data);
		if ($names[$tmp['name']]) {
			$cnt = 1;
			$name = $tmp['name'];
			while ($names[$tmp['name']]) {
				$tmp['name'] = $name.$cnt++;
			}
		}

		$names[$tmp['name']] = true;
		if (empty($tmp['data'])) {
			if ($fp = @fopen($tmp['url'], "rb")) {
				do {
					$tmpdata = fread($fp, 8192);
					if (strlen($tmpdata) == 0) {
						break;
					}
					$tmp['data'] .= $tmpdata;
				} while (true);

				fclose($fp);
			} else {
				continue;
			}
		}

		list($tmp1, $tmp2, $tmp3, $tmp['type']) = func_get_image_size(empty($data) ? $tmp['url'] : $file_path);
		if (empty($tmp['type']))
			continue;

		$message = preg_replace("/(['\"])".str_replace("/", "\/", preg_quote($orig_name))."(['\"])/Ss", "\\1cid:".$tmp['name']."\\2", $message);
		$images[$tmp['url']] = $tmp;
	}

	return array($message, $images);
}

?>
