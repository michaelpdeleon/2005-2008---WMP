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
# $Id: news_send.php,v 1.26.2.1 2006/06/06 14:27:23 svowl Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

require $xcart_dir."/include/safe_mode.php";

x_load('files','mail');

#
# Send email to the specified news
#
function func_spam($message, $recipients, $send_language, $listid) {

	global $config, $smarty;
	global $current_language, $sql_tbl;
	global $xcart_dir;

	$saved_language = $current_language;

	$current_language = $send_language;

	$email_spec = "###EMAIL###";

	$smarty->assign("email", $email_spec);
	$smarty->assign("listid", $listid);

	$signature_template = "mail/newsletter_signature.tpl";
	$sign_delim = "\n\n";

	if ($message["allow_html"]=="Y") $config["Email"]["html_mail"] = "Y";

	if ($config["Email"]["html_mail"] == "Y") {
		$message["headers"] = array("Content-Type"=>"text/html");
		$sign_delim = "<br /><br />";
		if (file_exists($smarty->template_dir."/mail/html/".basename($signature_template)))
			$signature_template = "mail/html/".basename($signature_template);
	}
	else {
		$message["headers"] = array("Content-Type"=>"text/plain");
	}

	$signature = func_display($signature_template,$smarty,false);

	if ($config["Email"]["use_PHP_mailer"] == 'Y') {
		#
		# Use PHP mailer for sending newsletter
		#

		$extra = array();
		if ($config["Email"]["html_mail"] == "Y") {
			$extra = array("Content-Type" => "text/html");
		}
		foreach($recipients as $recipient) {
			func_send_simple_mail($recipient, $message["subject"], $message["body"].$sign_delim.preg_replace("/$email_spec/S", $recipient, $signature), $config["News_Management"]["newsletter_email"], $extra);
		}

	} else {
		#
		# Use external shell script
		#
		$mail_file = func_temp_store(implode("\n", $recipients));
		$subject_file = func_temp_store($message["subject"]);
		$body_file = func_temp_store($message["body"].$sign_delim.$signature);

		if (!empty($current_language)) {
			$charset = func_query_first_cell("SELECT charset FROM $sql_tbl[countries] WHERE code='$current_language'");
		}
		else {
			$charset = func_query_first_cell("SELECT charset FROM $sql_tbl[countries] WHERE code='".$config["default_admin_language"]."'");
		}

		$additional_headers = array();
		$additional_headers[] = "MIME-Version: 1.0";

		if ($config["Email"]["html_mail"] == "Y")
			$additional_headers[] = "Content-Type: text/html; charset=".$charset;
		else
			$additional_headers[] = "Content-Type: text/plain; charset=".$charset;

		$addheader = join("\\n", $additional_headers);
		putenv("REPLYTO=".$config["News_Management"]["newsletter_email"]);
		@exec(func_shellquote($xcart_dir.DIR_ADMIN."/newsletter.sh",
			$mail_file, $subject_file, $body_file,
			$config["News_Management"]["newsletter_email"], $addheader)." &");
	}

	$current_language = $saved_language;
}

$do_not_update_status = false;

$recipients = array();

$limit = "";

if (is_array($message)) {
	$message = func_array_map('stripslashes',$message);

	foreach (array("email1", "email2", "email3") as $f) {
		if (!empty($message[$f]))
			$recipients[] = $message[$f];
	}
	$do_not_update_status = true;

	$list_lng = func_query_first_cell("SELECT lngcode FROM $sql_tbl[newslists] WHERE listid='$targetlist'");
}
else {
	$list = func_query_first("SELECT * FROM $sql_tbl[newslists] WHERE listid='$targetlist'");
	if (empty($list)) return;

	$list_lng = $list['lngcode'];

	if ($config['News_Management']['news_emails_per_pass'] > 0) {
		x_session_register('news_send_data');

		if ($action == 'send') {
			$subscribers_count = func_query_first_cell("SELECT count(*) FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist'");
			if (!$subscribers_count) return;

			$news_send_data[$messageid] = array (
				'count' => $subscribers_count,
				'lastpos' => 0
			);

			echo func_get_langvar_by_name("lbl_news_sending_messages", array ("count" => $news_send_data[$messageid]['count']), false, true);
		}
		else {
			echo func_get_langvar_by_name("lbl_news_continue_sending_messages", array ("last" => $news_send_data[$messageid]['lastpos'], "count" => $news_send_data[$messageid]['count']), false, true);
		}
		func_flush();

		$limit = sprintf(" LIMIT %d,%d", $news_send_data[$messageid]['lastpos'], $config['News_Management']['news_emails_per_pass']);
	}

	$tmp = func_query("SELECT email FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' ORDER BY email".$limit);
	if (is_array($tmp))
	foreach ($tmp as $v) {
		if (!empty($v["email"]))
			$recipients[] = $v["email"];
	}
	$message = func_query_first("SELECT * FROM $sql_tbl[newsletter] WHERE newsid='$messageid'");
}

if (count($recipients)>0) {

	func_spam($message, $recipients, $list_lng, $targetlist);

	if (!$do_not_update_status)
		db_query("UPDATE $sql_tbl[newsletter] SET status='S' WHERE newsid='$message[newsid]'");
}

if (!empty($limit) && count($recipients) > 0) {
	$news_send_data[$messageid]['lastpos'] += count($recipients);

	if ($news_send_data[$messageid]['lastpos'] >= $news_send_data[$messageid]['count']) {
		func_unset($news_send_data, $messageid);
		return;
	}

	func_html_location("news.php?mode=messages&targetlist=$targetlist&messageid=$messageid&action=send_continue", $config['News_Management']['news_sleep_interval']);
}

?>
