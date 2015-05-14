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
# $Id: adaptive.php,v 1.4 2006/01/11 06:55:57 mclap Exp $
#
# Save ebrowser enviroment settings
#

define("QUICK_START", true);

require "./top.inc.php";
require "./init.php";

header("Content-type: text/javascript");

x_session_register("is_location");
x_session_register("adaptives");

if(!empty($send_browser) && $adaptives['is_first_start'] == 'Y' && $REQUEST_METHOD == 'GET') {
	$adaptives = array('isJS' => 'Y');

	$arr = explode("|", $send_browser);
	$tmp = array("isDOM","isStrict","isJava");
	for($x = 0; $x < count($tmp); $x++)
		$adaptives[$tmp[$x]] = ($arr[0][$x] == 'Y'?"Y":"");
	$arr = func_array_map("urldecode", $arr);
	$adaptives['browser'] = $arr[1];
	$adaptives['version'] = $arr[2];
	$adaptives['platform'] = preg_replace("/^(\S+).*$/S", "\\1", $arr[3]);
	$adaptives['isCookie'] = $arr[4];
	$adaptives['screen_x'] = $arr[5];
	$adaptives['screen_y'] = $arr[6];

	$adaptives['is_first_start'] = '';

	if ($arr[7] == "C") {
		$count = func_query_first_cell("SELECT count FROM $sql_tbl[stats_adaptive] WHERE platform = '$adaptives[platform]' AND browser = '$adaptives[browser]' AND java = '$adaptives[isJava]' AND js = '$adaptives[isJS]' AND cookie = '$adaptives[isCookie]' AND version = '$adaptives[version]' AND screen_x = '$adaptives[screen_x]' AND screen_y = '$adaptives[screen_y]'")+1;
		db_query("REPLACE INTO $sql_tbl[stats_adaptive] (platform, browser, java, js, count, last_date, cookie, version, screen_x, screen_y) VALUES ('$adaptives[platform]', '$adaptives[browser]', '$adaptives[isJava]', '$adaptives[isJS]', '$count', '".time()."', '$adaptives[isCookie]', '$adaptives[version]', '$adaptives[screen_x]', '$adaptives[screen_y]')");
	}

}

?>
