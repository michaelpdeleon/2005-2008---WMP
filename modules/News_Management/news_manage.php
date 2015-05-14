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
# $Id: news_manage.php,v 1.3 2006/01/11 06:56:15 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('mail');

if (empty($mode)) {
	if ($REQUEST_METHOD == "GET")
		$mode = "archive";
	else
		$mode = "view";
}

if ($REQUEST_METHOD == "POST") {
	$email = trim($newsemail);
	if (!func_check_email($email)) {
		func_header_location("error_message.php?subscribe_bad_email");
	}

	$c = func_query_first_cell("
SELECT COUNT(DISTINCT($sql_tbl[newslists].listid))
FROM $sql_tbl[newslists], $sql_tbl[newslist_subscription]
WHERE
$sql_tbl[newslists].listid=$sql_tbl[newslist_subscription].listid AND
$sql_tbl[newslists].lngcode='$subscribe_lng' AND
$sql_tbl[newslist_subscription].email='$email'");
	if ($c > 0) {
		func_header_location("error_message.php?subscribe_exist_email");
	}

	if ($mode != "subscribe") {
		$lists = func_query("SELECT * FROM $sql_tbl[newslists] WHERE avail='Y' AND subscribe='Y' AND lngcode='$subscribe_lng'");
		if (!is_array($lists) || empty($lists)) {
			$top_message["type"] = "I";
			$top_message["content"] = "Where are no newslists for subscription";
			func_header_location("home.php");
		}
	}

	if (count($lists) == 1 || $mode == "subscribe") {
		foreach ($lists as $list) {
			db_query("INSERT INTO $sql_tbl[newslist_subscription] (listid, email, since_date) VALUES ('$list[listid]', '$email', '".time()."')");
		}

		$saved_lng = $current_language;

		#
		# Send mail notification to customer
		#
		$mail_smarty->assign("email",$email);
		if($config['News_Management']['eml_newsletter_subscribe'] == 'Y') {
			$current_language = $subscribe_lng;
			func_send_mail($email, "mail/newsletter_subscribe_subj.tpl", "mail/newsletter_subscribe.tpl", $config["News_Management"]["newsletter_email"], false);
		}
		#
		# Send mail notification to admin
		#
		if($config['News_Management']['eml_newsletter_subscribe_admin'] == 'Y') {
			$current_language = '';
			func_send_mail($config["News_Management"]["newsletter_email"], "mail/newsletter_admin_subj.tpl", "mail/newsletter_admin.tpl", $email, true);
		}

		$current_language = $saved_lng;

		func_header_location("home.php?mode=subscribed&email=".urlencode(stripslashes($email)));
	}
}

if ($REQUEST_METHOD=="POST" && $mode == "view") {
	$location[] = array(func_get_langvar_by_name("lbl_news_subscribe_to_newslists"), "");
	$smarty->assign("main", "news_lists");
	$smarty->assign("lists", $lists);
	$smarty->assign("newsemail", $email);
}
else {
	#
	# Show the news from archive
	#
	$location[] = array(func_get_langvar_by_name("lbl_news_archive"), "");

	$smarty->assign("main", "news_archive");

	$smarty->assign("news_messages", func_news_get($shop_language));
}

$smarty->assign("location", $location);

?>
