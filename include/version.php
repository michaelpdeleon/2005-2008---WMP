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
# $Id: version.php,v 1.18 2006/01/11 06:55:59 mclap Exp $
# This script is required by X-Cart support team
#

@include_once "../top.inc.php";
if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");

require $xcart_dir."/init.php";

$xcart_db_version = "";
$res = mysql_query("SELECT value FROM $sql_tbl[config] WHERE name='version'");
if (mysql_num_rows($res) < 1) {
	$xcart_db_version = "<= 2.4.1";
}
else {
	for ($i = 0; $i < mysql_num_rows($res);  $i++) {
		list ($version) = mysql_fetch_row($res);
		if ($i != 0) $xcart_db_version .= ", ";

		$xcart_db_version .= $version;
	}
}

mysql_free_result($res);

if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name = 'Simple_Mode'")) {
	$xcart_db_version .= " PRO";
}
else {
	$xcart_db_version .= " GOLD";
}

echo "X-Cart DB Version: $xcart_db_version<br />\n";

$modules = func_query("SELECT module_name FROM $sql_tbl[modules]");
if ($modules) {
	foreach ($modules as $module) {
		if (file_exists($xcart_dir."/modules/".$module['module_name']."/config.php")) {
			include_once $xcart_dir."/modules/".$module['module_name']."/config.php";
		}
	}
}

ksort($addons);
if ($addons) {
	echo "<br />Addons:<br />";
	foreach ($addons as $k => $v) {
		echo str_replace("_", " ", $k);
		if (!empty($active_modules[$k]))
			echo " (enabled)";

		echo ";<br />";
	}
}

$dir = @opendir($xcart_dir."/schemes/templates");
$is_echo_skins = false;
if ($dir) {
	$is_default_skin = false;
	while ($file = readdir($dir)) {
		$f = $xcart_dir."/schemes/templates/".$file;
		if ($file == '.' || $file == '..' || !is_dir($f) || $file == '2-columns' || $file == '2-columns_reversed' || $file == '3-columns_reversed' || $file == 'small_shop' || $file == 'CVS')
			continue;

		if (!$is_echo_skins)
			echo "<br />Skins:<br />";

		echo ucwords(strtolower(str_replace("_", " ", $file)));
		$is_echo_skins = true;
		echo ";<br />";
	}

	closedir($dir);
}

?>
