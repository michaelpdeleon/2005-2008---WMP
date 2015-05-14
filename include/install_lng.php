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
# $Id: install_lng.php,v 1.16 2006/01/17 08:22:30 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$available_install_languages = array();
$install_lng_defs = array ();

#
# Declaration of $install_languages begins in appropriate language files (e.g. install_lng_XX.php)
#
$install_languages = array ();

$_lng_files = get_dirents_mask($xcart_dir.'/include',
	'!^install_lng_..\.php$!S');

foreach ($_lng_files as $dirent=>$dirent_matches) {
	require $xcart_dir.'/include/'.$dirent;
}

$available_install_languages = array_keys($install_languages);

function lng_get() {
	global $install_languages;
	global $install_language_code;

	$args = func_get_args();

	if (count($args) == 0) return "";
	$index = array_shift($args);

	if (isset($install_languages[$install_language_code][$index]))
		$result = $install_languages[$install_language_code][$index];
	else
	if (isset($install_languages["US"][$index]))
		$result = $install_languages["US"][$index];
	else
		return "";

	$replace_to = array();
	for ($i=0, $cnt=count($args); $i < $cnt; $i+=2) {
		$replace_to[$args[$i]] = $args[$i+1];
	}

	if (!empty($replace_to))
		foreach ($replace_to as $k=>$v)
			$result = str_replace("{{".$k."}}", $v, $result);

	return $result;
}

function echo_lng() {
	$args = func_get_args();
	echo call_user_func_array("lng_get", $args);
}

function echo_lng_quote() {
	$args = func_get_args();
	echo htmlspecialchars(call_user_func_array("lng_get", $args));
}

function echo_lng_js() {
	$args = func_get_args();
	$data = addslashes(call_user_func_array("lng_get", $args));
	$data = str_replace("\r","\\r", $data);
	$data = str_replace("\n","\\n", $data);
	echo $data;
}


?>
