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
# $Id: customer_bonuses.php,v 1.8 2006/01/11 06:56:18 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('user','order');

$bonus = false;

$bonus = func_get_customer_bonus($login);
if (empty($bonus['memberships'])) unset($bonus['memberships']);

if (!isset($mode)) $mode = "";

if ($mode == 'points') {
	$location[] = array(func_get_langvar_by_name("lbl_sp_convert_points"), "bonuses.php?mode=points");

	$userinfo = func_userinfo($login, 'C');
	$smarty->assign('userinfo', $userinfo);

	if ($REQUEST_METHOD == "GET") {
		if (empty($giftcert['amount']))
			$giftcert['amount'] = $bonus['points'];

		if (empty($giftcert['recipient_email']))
			$giftcert['recipient_email'] = $userinfo['email'];
	}

	if ($REQUEST_METHOD == "POST") {
		$fill_error = (empty($purchaser) || empty($recipient));
		$amount_error = (($amount < $config["Special_Offers"]["offers_bp_min"]) || ($amount > $bonus['points']));
		$fill_error = ($fill_error || empty($recipient_email));
		$giftcert = array (
			"purchaser" => stripslashes($purchaser),
			"recipient" => stripslashes($recipient),
			"message" => stripslashes($message),
			"amount" => $amount,
			"recipient_email" => $recipient_email
		);

		if (!$fill_error && !$amount_error) {
			# create new gict certificate
			$giftcert['gcid'] = substr(strtoupper(md5(uniqid(rand()))), 0, 16);
			$bp_amount = $giftcert['amount'];
			$amount = price_format($bp_amount * $config["Special_Offers"]["offers_bp_rate"]);
			$giftcert['amount'] = $amount;
			$giftcert['debit'] = $amount;
			$giftcert['send_via'] = 'E';
			$giftcert['add_date'] = time();
			$giftcert['status'] = 'A';
			func_array2insert('giftcerts', $giftcert);

			# update bonuses
			$bonus['points'] -= $bp_amount;
			func_update_customer_bonus($login, $bonus);

			# notify customer
			$top_message['content'] = func_get_langvar_by_name('lbl_sp_points_converted2gc');
			$top_message['type'] = 'I';
			func_send_gc($userinfo["email"], $giftcert, $login);
			func_header_location('bonuses.php');
		}
		else {
			$smarty->assign("giftcert",$giftcert);
			$smarty->assign("fill_error",$fill_error);
			$smarty->assign("amount_error",$amount_error);
		}
	}

	$smarty->assign('giftcert', $giftcert);
}
elseif ($mode == 'membership') {
	if (!empty($change_to) && !empty($bonus['memberships']) && isset($bonus['memberships'][$change_to])) {
		unset($bonus['memberships'][$change_to]);
		func_update_customer_bonus($login, $bonus);
		db_query("UPDATE $sql_tbl[customers] SET membershipid = '$change_to' WHERE login='$login'");
		$top_message['content'] = func_get_langvar_by_name('lbl_sp_membership_changed');
		$top_message['type'] = 'I';
	}
	else {
		$top_message['content'] = func_get_langvar_by_name('lbl_sp_incorrect_membership');
		$top_message['type'] = 'E';
	}

	func_header_location('bonuses.php');
}

$smarty->assign('mode', $mode);
$smarty->assign('bonus', $bonus);

?>
