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
# $Id: stats.php,v 1.11 2006/01/11 06:56:20 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: error_message.php?permission_denied"); die("Access denied"); }

x_load('user');

$sesses = func_query("SELECT sessid, is_registered, expiry FROM $sql_tbl[users_online] WHERE usertype = 'C'");
$statistics = array();
if ($sesses) {
	foreach($sesses as $s) {
		$data = array();
		if ($use_sessions_type == 3) {
			$data = func_query_first("SELECT data FROM $sql_tbl[sessions_data] WHERE sessid = '$s[sessid]'");
		}
		elseif ($use_sessions_type == 1 || $use_sessions_type == 2) {
			$old_sessid = $XCARTSESSID;
			x_session_id($s['sessid']);
			x_session_start($s['sessid']);
			$vars = array("login", "login_type", "cart", "current_date", "current_url_page", "session_create_date");
			$data = array("data" => array());
			$_s = array();
			foreach ($vars as $v) {
				$_s[$v] == $$v;
				x_session_register($v);
				$data['data'][$v] = $$v;
			}

			x_session_id($old_sessid);
			x_session_start($old_sessid);
			foreach ($vars as $v) {
				x_session_register($v);
				$$v == $_s[$v];
			}
		}

		if (empty($data['data']))
			continue;

		$rec = array("last_date" => $s['expiry']);
		if ($use_sessions_type == 3) {
			$data = unserialize($data['data']);
		}
		elseif ($use_sessions_type == 1 || $use_sessions_type == 2) {
			$data = $data['data'];
		}

		if (!empty($data['login']) && $data['login_type'] != 'C')
			continue;

		if (!empty($data['login']))
			$rec['userinfo'] = func_userinfo($data['login'], 'C');

		if (!empty($data['cart']['products']))
			$rec['products'] = $data['cart']['products'];

		$rec['current_date'] = $data['current_date']+$config["Appearance"]["timezone_offset"];
		$rec['current_url_page'] = $data['current_url_page'];
		if (strstr($data['current_url_page'], $https_location)) {
			$rec['display_url_page'] = str_replace($https_location, "...", $data['current_url_page']);
		}
		else {
			$rec['display_url_page'] = str_replace($http_location, "...", $data['current_url_page']);
		}

		$rec['session_create_date'] = $data['session_create_date']+$config["Appearance"]["timezone_offset"];
		$statistics[] = $rec;
	}
}

?>
