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
# X-Cart installation wizard base code
#
# $Id: install.php,v 1.71.2.1 2006/05/02 05:56:51 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

#
# Put access code here
# A person who do not know the auth code can not access the installations script
#
$installation_auth_code = "0196A139";

#
# Changing some configuration parameters
#
if (defined('AREA_TYPE')) return; # for internal use

set_magic_quotes_runtime(0);
error_reporting (E_ALL ^ E_NOTICE);
set_time_limit(300);
umask(0);
#
# While executing sql files re-establish connection with mysql server before every Nth sql command
#
$sql_reconnect_count = 100;

if (empty($HTTP_SERVER_VARS)) {
	$HTTP_GET_VARS = &$_GET;
	$HTTP_POST_VARS = &$_POST;
	$HTTP_SERVER_VARS = &$_SERVER;
	$HTTP_COOKIE_VARS = &$_COOKIE;
}

$__quotes_qpc = get_magic_quotes_gpc();

function mystripslashes($var) {
	if (!is_array($var)) return stripslashes($var);

	foreach($var as $k=>$v) {
		if (is_array($v)) $var[$k] = mystripslashes($v);
		else $var[$k] = stripslashes($v);
	}
	return $var;
}

if ($__quotes_qpc || (!$__quotes_qpc && function_exists("func_addslashes"))) {
	foreach(array("GET","POST","COOKIE","SERVER") as $_k=>$__avar)
		foreach ($GLOBALS["HTTP_".$__avar."_VARS"] as $__var => $__res) $GLOBALS["HTTP_".$__avar."_VARS"][$__var] = mystripslashes($__res);
}

#
# Predefined common variables
#

$templates_repository = "skin1_original";
$schemes_repository = "schemes";
$templates_directory = "skin1";

#
# start: Modules manager
#

$error = false;

# get working parameters

$current = (int)$HTTP_POST_VARS["current"];
$params = $HTTP_POST_VARS["params"];
$orig_params = $params;

require $xcart_dir."/include/install_lng.php";

require_once $xcart_dir."/include/blowfish.php";
$blowfish = new ctBlowfish();

if (isset($params["lngcode"]) && is_array($install_languages[$params["lngcode"]]))
	$install_language_code = $params["lngcode"];
else
	$install_language_code = "US";

$install_language_charset = $install_lng_defs[$install_language_code]["charset"];

if (empty($params['flags']) || !is_array($params)) $params['flags'] = array();

if (isset($params["force_current"])) {
	$_tmp=explode(',',$params["force_current"]);
	unset($params["force_current"]);
	$current = array_shift($_tmp);
	if (!empty($_tmp)) {
		$params['flags'] = array();
		foreach ($_tmp as $k=>$v) {
			$params['flags'][$v] = true;
		}
	}
}

# Disable PayPal when re-installing skins
if (($current == 7 || $current == 8) && !empty($params['flags']['nopaypal']))
	$current = 9;

if (function_exists("func_query")) {
	#
	# Addon installation
	#

	$installation_product = $module_definition["name"];
	if (isset($params["auth_code"]) && $params["auth_code"]==$installation_auth_code) {
		$params["mysqlhost"] = $sql_host;
		$params["mysqluser"] = $sql_user;
		$params["mysqlpass"] = $sql_password;
		$params["mysqlbase"] = $sql_db;

		$module_definition["sql_files"] = array(
			"sql/".$module_definition["prefix"]."_remove.sql",
			"sql/".$module_definition["prefix"].".sql"
		);

		$codes = func_query_column("SELECT DISTINCT(code) FROM $sql_tbl[languages]");
		foreach ($codes as $_code) {
			$_file = "sql/".$module_definition["prefix"]."_lng_".$_code.".sql";
			if (file_exists($xcart_dir."/".$_file))
				$module_definition["sql_files"][] = $_file;
		}

	}

	#
	# Define the installation steps
	#
	$modules = array (
		0 => array(
			"name" => "language",
			"comment" => "mod_language"
		),
		1 => array(
			"name" => "moddefault",
			"comment" => "mod_license",
			"js_next" => true
		),
		2 => array(
			"name" => "modinstall",
			"comment" => "mod_modinstall",
		),
		3 => array(
			"name" => "install_done",
			"comment" => "install_complete",
			"param" => @$module_definition["successmessage"]
		)
	);

	if ($params["install_type"] == 3) {
		$modules[2]["comment"] = "mod_moduninstall";
		$modules[3] = array(
			"name" => "uninstall_done",
			"comment" => "mod_moduninstall_done",
		);
	}
}

# Skip language selecting step for only one language
if ($current == 0 && count($available_install_languages) == 1) {
	list($install_language_code) = $available_install_languages;
	$params["lngcode"] = $install_language_code;
	$current++;
}

if ($current < 0 || $current >= count($modules))
	die("invalid current");

function inst_html_entity_decode($string) {
	static $trans_tbl = false;

	if ($trans_tbl === false) {
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
	}
	return strtr($string, $trans_tbl);
}

function bool_get($param) {
	static $settings = false;

	if (!is_array($settings) && function_exists('ini_get_all')) { # For PHP >= 4.2.0
		$a = ini_get_all();
		foreach ($a as $k=>$v) {
			$value = $v['local_value'];
			$value = inst_html_entity_decode($value);
			$value = preg_replace('!Off!Si',false,$value);
			$value = preg_replace('!On!Si',true,$value);
			$value = str_replace("\x00","",$value);
			$settings[$k] = $value;
		}
	}

	if (!is_array($settings)) {
		ob_start();
		phpinfo(INFO_CONFIGURATION);
		$lines = explode("\n",ob_get_contents());
		ob_end_clean();
		foreach ($lines as $_k=>$line) {
			if (preg_match('!<tr><td class="e">([^<]+)</td><td class="v">([^<]*)</td><td class="v">([^<]*)</td></tr>!Si', $line, $m)) {
				$m[2]=inst_html_entity_decode($m[2]);
				$m[2]=preg_replace('!Off!Si',false,@$m[2]);
				$m[2]=preg_replace('!On!Si',true,@$m[2]);
				$m[2]=str_replace("\x00","",$m[2]);
				$settings[$m[1]] = $m[2];
			}
			else if (preg_match('!<td bgcolor="#ccccff"><b>([^<]+)</b><br[^>]*></td><td align="center">([^<]*)</td><td align="center">([^<]*)</td>!Si', $line, $m)) {
				$m[2]=inst_html_entity_decode($m[2]);
				$m[2]=preg_replace('!Off!Si',false,@$m[2]);
				$m[2]=preg_replace('!On!Si',true,@$m[2]);
				$m[2]=str_replace("\x00","",$m[2]);
				$settings[$m[1]] = $m[2];
			}
			else if (preg_match('!(.+) => ([^ =]*) => (.*)$!S', $line, $m)) {
				$m[2]=preg_replace('!Off!Si',false,@$m[2]);
				$m[2]=preg_replace('!On!Si',true,@$m[2]);
				$m[2]=str_replace("\x00","",$m[2]);
				$settings[$m[1]] = $m[2];
			}
		}
	}

	return isset($settings[$param]) ? $settings[$param] : false;
}

#
# Get directory entries matched regexp (portable glob())
#
function get_dirents_mask($dir, $re) {
	$rval = array();

	$dp = opendir($dir);
	if ($dp !== false) {
		while (($dirent = readdir($dp)) !== false) {
			if (preg_match($re, $dirent, $matches))
				$rval[$dirent] = $matches;
		}

		closedir($dp);
	}

	return $rval;
}

#
# Extract data from file matched regexp
#
function get_file_contents_re($file, $re) {
        ob_start();
        readfile($file);
        $contents = ob_get_contents();
        ob_end_clean();

	$rval = array();
	if (preg_match_all($re, $contents, $rval)) {
		return $rval;
	}

	return false;
}

#
# Make list of countries or languages based on list of files using regexp
#
function get_lang_names_re($dir, $files_re, $current_lng_code, $mode) {
	static $modes = array (
		'country' => '!country_(%s)\',\'(.*)\',\'Countries\'\);!',
		'language' => '!language_(%s)\',\'(.*)\',\'Languages\'\);!'
	);
	static $code_aliases = array (
		'UK' => 'GB'
	);

	global $xcart_dir;

	$files = get_dirents_mask($dir, $files_re);

	$rval = array();
	foreach ($files as $_file=>$matches) {
		# if language is not known, use code instead
		$code = $matches[1];
		if (!empty($code_aliases[$code])) $code = $code_aliases[$code];
		$rval[$matches[1]] = $code;
	}

	$re = sprintf($modes[$mode], implode($rval,'|'));

	$matches = get_file_contents_re($xcart_dir.'/sql/xcart_language_'.$current_lng_code.'.sql', $re);
	if (!empty($matches[1])) {
		# replace language codes with names
		foreach ($matches[1] as $_coden => $_code) {
			if (in_array($_code, $code_aliases)) {
				foreach ($code_aliases as $alias_key=>$alias) {
					if ($alias == $_code) {
						$_code = $alias_key;
						break;
					}
				}
			}
			$rval[$_code] = $matches[2][$_coden];
		}
	}

	asort($rval);

	return $rval;
}

# start html output

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $install_language_charset; ?>" />
<title><?php echo_lng("install_wiz", "product", $installation_product); ?></title>

<style type="text/css">
<!--
BODY,P,DIV,TH,TD,P,INPUT,SELECT {
	COLOR: #550000;
	FONT-FAMILY: Verdana, Arial, Helvetica;
	FONT-SIZE: 12px;
}
A {
	COLOR: #330000;
	TEXT-DECORATION: none;
}
A:hover {
	COLOR: #550000;
	TEXT-DECORATION: underline;
}

BODY {
	MARGIN: 0px;
	PADDING: 0px;
	BACKGROUND-COLOR: #FFFBD3;
}
FORM {
	MARGIN: 0px;
}
TABLE,IMG {
	BORDER: 0px;
}
<?php
if ($current == 0) {
?>
.background {
	BACKGROUND-COLOR: #FFFBD3;
	BACKGROUND-IMAGE: URL('http://www.x-cart.com/img/logo.gif');
}
<?php
}else {
?>
<?php
}
?>
.TableTop {
	BACKGROUND-COLOR: #FFFBD3;
}
.Clr1 {
	BACKGROUND-COLOR: #DDDDDD;
}
.Clr2 {
	BACKGROUND-COLOR: #EEEEEE;
}
.VertMenuBox {
        BACKGROUND-COLOR: #FFD44C;
}
.VertMenuBorder {
        BACKGROUND-COLOR: #8E4B00;
}
.HeadLogo {
	PADDING-LEFT: 27px;
	TEXT-ALIGN: left;
}
.Spc {
	WIDTH: 1px;
	HEIGHT: 1px;
}
LI {
	PADDING-BOTTOM: 16px;
}
-->
</style>
<script type="text/javascript" language="javascript">
<!--
<?php

# show module's according scripts

# 'back' button's script
if (@$modules[$current]["js_back"]) {
	$func = "module_".$modules[$current]["name"]."_js_back";
	$func();
}
else
	default_js_back();

# 'next' button's script
if (@$modules[$current]["js_next"]) {
	$func = "module_".$modules[$current]["name"]."_js_next";
	$func();
}
else
	default_js_next();

?>
-->
</script>
</head>

<body>
<table class="TableTop" width="100%" cellspacing="0" cellpadding="0">

<?php /* common header */ ?>
<tr>
	<td class="HeadLogo"><img src="./skin1_original/images/xlogo.gif" width="244" height="67" alt="" /></td>
	<td valign="middle" align="right">
<font size="+1"><b><?php echo_lng("install_wiz","product",$installation_product); ?></b></font>&nbsp;&nbsp;<br />
<b><?php echo_lng("install_step","num",$current,"comment",lng_get($modules[$current]["comment"])); ?></b>&nbsp;&nbsp;
	</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td colspan="2" class="VertMenuBorder"><img src="./skin1_original/images/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="VertMenuBox" valign="middle" align="right" height="16" width="200">
&nbsp;<font color="#000000" size="1"><b><?php @readfile("VERSION"); ?></b></font>
	</td>
	<td class="VertMenuBox">&nbsp;</td>
</tr>
<tr>
	<td colspan="2" class="VertMenuBorder"><img src="./skin1_original/images/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>
<p />

<?php /* common form */ ?>
<form method="post" name="ifrm" action="<?php echo $HTTP_SERVER_VARS["PHP_SELF"] ?>" onsubmit="javascript: return step_next();">

<table class="TableTop" width="90%" cellspacing="0" cellpadding="0" align="center">

<tr>
	<td>
<?php
# auth_code must present always to prevent non-authorized reinstallation
$_tmp = $orig_params;
if (isset($_tmp['lngcode'])) unset($_tmp['lngcode']);

if (!empty($_tmp) && $_tmp["auth_code"]!=$installation_auth_code) {
	message(lng_get("wrong_auth_code"));
	exit;
}

if (!empty($_tmp) && empty($params['agree']) && $params['install_type'] != 3) {
	message_error(lng_get("mod_license_alert"));

	$current = 1;

	$_tmp = $orig_params;
	$params = array();
	if (!empty($_tmp['lngcode']))
		$params['lngcode'] = $_tmp['lngcode'];
}

# run handler of current step
$func = "module_".$modules[$current]["name"];
$res = $func($params,@$modules[$current]["param"]);
?>
	</td>
</tr>
<?php

# show navigation buttons

$prev = $current;

if (!$res)
	$current += 1;

if ($current < count($modules)) {
?>

<tr>
	<td align="center">
<?php
if (!empty($params)) {
	foreach ($params as $key => $val) {
		if(is_array($val)) {
			foreach($val as $key2 => $val2) {
?><input type="hidden" name="params[<?php echo $key ?>][<?php echo $key2 ?>]" value="<?php echo $val2 ?>" />
<?php
			}
		} else {
?><input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo $val ?>" />
<?php
		}
	}
}
?>
	<input type="hidden" name="current" value="<?php echo $current ?>" />
	<input type="button" value="<?php echo_lng("button_back"); ?>"<?php if ($prev <= 0) { ?> disabled="disabled"<?php } ?> onclick="javascript: return step_back();" />
	<input type="submit" value="<?php echo_lng("button_next"); ?>"<?php if ($error) {?> disabled="disabled"<?php } ?> />
	</td>
</tr>
<?php
}
?>

<?php /* common bottom */ ?>

</table>
</form>
<br />
<hr size="1" noshade="noshade" />
<div align="right">
<font size="1">
  Copyright 2001-2006 <a href="http://www.x-cart.com">X-Cart.com</a><br />
  Copyright 2001-2006 <a href="http://www.creativedevelopment.biz" target="_blank">Creative Development</a>
</font>
</div>
</body>
</html>
<?php

#
# end: Modules manager
#

#
# start: default navigation buttons handlers
#

function default_js_back() {
?>
	function step_back() {
		history.back();
		return true;
	}
<?php
}

function default_js_next() {
?>
	function step_next() {
		return true;
	}
<?php
}

#
# end: default navigation buttons handlers
#

################################################################
#
# Common functions goes here
#
################################################################

function fatal_error($txt) {
?>
<center>
<p>
<b><font color="red"><?php echo_lng("fatal_error", "error", $txt); ?></font></b>
</p>
</center>
<?php
	return false;
}

function warning_error($txt) {
?>
<center>
<p>
 <b><font color="red"><?php echo_lng("warning", "warning", $txt); ?></font></b>
</p>
</center>
<?php
	return false;
}

function message($txt) {
?>
<b><font color="darkgreen"><?php echo $txt ?></font></b>
<?php
}

function message_error($txt) {
?>
<center>
<p>
<b><font color="red"><?php echo $txt ?></font></b>
</p>
</center>
<?php
}

function status($var) {
	return ($var ? "<font color=\"green\">[".lng_get("status_ok")."]</font>" : "<font color=\"red\">[".lng_get("status_failed")."]</font>");
}

function on_off($var) {
	return lng_get($var ? "status_on" : "status_off");
}

function myquery($command) {
	global $params, $sql_reconnect_count;
	static $requests_count = 0;

	if( $sql_reconnect_count > 0 && $requests_count > $sql_reconnect_count ) {
		if( !@mysql_close() ) return false;

		if( !@mysql_connect($params["mysqlhost"], $params["mysqluser"], $params["mysqlpass"]) ) return false;

		if( !@mysql_select_db($params["mysqlbase"]) ) return false;

		$requests_count = 0;
	}
	$requests_count++;
	return mysql_query($command);
}

function runquery($command) {
	myquery($command);
	$myerr = mysql_error();
	if (!empty($myerr))
		echo status(false)." ".$myerr."<br />\n";
	return empty($myerr);
}

function query_upload($filename) {
	global $xcart_dir;

	$fp = fopen($xcart_dir.DIRECTORY_SEPARATOR.$filename, "rb");
	if ($fp === false) {
		echo_lng("upload_cannot_open", "file", $filename, "status", status(false));
		return 0;
	}

	$command = "";
	$counter = 0;

	echo "<br />".lng_get("please_wait")."<br />\n";

	while (!feof($fp)) {
		$c = chop(fgets($fp, 100000));
		$c = ereg_replace("^[ \t]*(#|-- |---*).*", "", $c);

		$command .= $c;

		if (ereg(";$", $command)) {
			$command = ereg_replace(";$", "", $command);

			if (ereg("CREATE TABLE ", $command)) {
				$table_name = ereg_replace(" .*$", "", eregi_replace("^.*CREATE TABLE ", "", $command));
				echo_lng("creating_table", "table", $table_name); flush();

				myquery($command);

				$myerr = mysql_error();
				if (!empty($myerr))
					break;
				else
					echo status(true)."<br />\n";
			} else {
				myquery($command);

				$myerr = mysql_error();
				if (!empty($myerr))
					break;
				else {
					$counter++;

					if (!($counter % 20)) {
						echo "."; flush();
					}
				}
			}

			$command = "";
			flush();
		}
	}

	fclose($fp);

	if (!empty($myerr))
		echo status(false)." ".$myerr."<br />\n";
	else {
		if ($counter > 19) echo "<br />\n";
		echo status(empty($myerr))."<br />\n";
	}

	return empty($myerr);
}

#
# Function to copy directory tree from skin1_original to skin1
#

function copy_files($templates_repository, $parent_dir="") {
	global $templates_directory;
	return copy_files_sub($templates_repository.$parent_dir, $templates_directory.$parent_dir);
}

function copy_files_sub($srcdir, $dstdir) {
	global $xcart_dir;

	$status = true;

	if (!$handle = opendir($xcart_dir.DIRECTORY_SEPARATOR.$srcdir)) {
		echo status(false)."<br />\n";
		return false;
	}

	while ($status && ($file = readdir($handle)) !== false) {
		if ($file == '.' || $file == '..' || !strcasecmp($file,'_private') || !strncasecmp($file, '_vti', 4)) continue;

		if (!strcasecmp($file, 'thumbs.db')) continue;

		if (!file_exists($dstdir))
			$status = $status && create_dirs(array($dstdir));

		if (!$status) break;

		if (is_file($srcdir.DIRECTORY_SEPARATOR.$file)) {
			if (!@copy($srcdir.DIRECTORY_SEPARATOR.$file, $dstdir.DIRECTORY_SEPARATOR.$file)) {
				echo lng_get("copying_file_from_to", "src",$srcdir.DIRECTORY_SEPARATOR.$file,"dst",$dstdir.DIRECTORY_SEPARATOR.$file)." ... ".status(false)."<br />\n";
				$status = false;
			}
			else {
				@chmod($dstdir.DIRECTORY_SEPARATOR.$file, 0666);
			}

			flush();

		} else if (is_dir($srcdir.DIRECTORY_SEPARATOR.$file) && $file != "." && $file != "..") {

			if (!file_exists($dstdir.DIRECTORY_SEPARATOR.$file)) {
				if (!file_exists($dstdir))
					$status = $status && create_dirs(array($dstdir));

				$status = $status && create_dirs(array($dstdir.DIRECTORY_SEPARATOR.$file));
			}

			$status = $status && copy_files_sub($srcdir.DIRECTORY_SEPARATOR.$file, $dstdir.DIRECTORY_SEPARATOR.$file);
		}
	}

	closedir($handle);

	return $status;
}

function check_dir($dir) {
	global $xcart_dir;

	if ($dir == "") return true;

	if (file_exists($dir)) return true;

	if (!check_dir(dirname($dir))) return false;

	echo_lng("creating_directory", "dir", $dir);
	$status = @mkdir($xcart_dir.DIRECTORY_SEPARATOR.$dir, 0777);

	print status($status)."<br />";

	return $status;
}

function copy_files_plain($files) {
	global $templates_directory;
	global $templates_repository;
	global $xcart_dir;

	$status = true;
	foreach($files as $_k=>$file) {
		$status = $status && check_dir(dirname($templates_directory.DIRECTORY_SEPARATOR.$file));

		if (is_dir($templates_repository.DIRECTORY_SEPARATOR.$file)) {
			if (!$handle = opendir($xcart_dir.DIRECTORY_SEPARATOR.$templates_repository.DIRECTORY_SEPARATOR.$file)) {
				echo lng_get("copying_directory", "dir", $templates_repository.DIRECTORY_SEPARATOR.$file, "status", status(false))."<br />\n";
				return false;
			}

			while ($status && ($item = readdir($handle)) !== false) {
				if ($item == '.' || $item == '..' || !strcasecmp($item,'_private') || !strncasecmp($item, '_vti', 4)) continue;

				if (!strcasecmp($item, 'thumbs.db')) continue;

				$status = $status && copy_files_plain(array($file.DIRECTORY_SEPARATOR.$item));
			}

			closedir($handle);
		} else {
			echo lng_get("copying_to_file","dst",$templates_directory.DIRECTORY_SEPARATOR.$file)." - ";
			if (file_exists($templates_directory.DIRECTORY_SEPARATOR.$file)) {
				@unlink($templates_directory.DIRECTORY_SEPARATOR.$file);
			}

			if (!@copy($templates_repository.DIRECTORY_SEPARATOR.$file, $templates_directory.DIRECTORY_SEPARATOR.$file) && basename($file)!='.htaccess')
				$status = false;
			else
				@chmod($templates_directory.DIRECTORY_SEPARATOR.$file, 0666);

			echo status($status);

			echo "<br />\n"; flush();
		}
	}

	return $status;
}

function create_dirs($dirs) {
	global $xcart_dir;
	$status = true;

	foreach ($dirs as $_k=>$val) {
		echo_lng("creating_directory", "dir", $val);

		if (!file_exists($val)) {
			$res = @mkdir($xcart_dir.DIRECTORY_SEPARATOR.$val, 0777);
			$status &= $res;

			echo status($res);
		} else
			echo "<font color=\"blue\">[".lng_get("dir_already_exists")."]</font>";

		echo "<br />\n"; flush();
	}

	return $status;
}

function create_files($files_to_create) {
	global $xcart_dir;

	if (is_array($files_to_create)) {
		foreach($files_to_create as $file=>$content) {
			if ($fd = @fopen($xcart_dir.DIRECTORY_SEPARATOR.$file,"w")) {
				@fwrite($fd, $content);
				@fclose($fd);
				@chmod($xcart_dir.DIRECTORY_SEPARATOR.$file, 0666);
			}
			else
				return warning_error(lng_get("warn_file_create_failed", "file", $file));
		}
	}
	return true;
}

function delete_files($files, $empty_files=false) {
	global $templates_directory;
	global $xcart_dir;

	if (!is_array($files)) $files = array($files);

	$status = true;

	foreach ($files as $_k=>$file) {
		$path = $templates_directory."/".$file;
		$realpath = $xcart_dir."/".$path;
		if (!file_exists($realpath) || basename($file)=='.htaccess')
			continue;

		if (is_array($empty_files) && in_array($file, $empty_files) && @filesize($realpath)==0)
			continue;

		if (is_dir($realpath)) {
			if (!$handle = opendir($realpath)) {
				echo lng_get("removing_directory","dir",$path)." - ".status(false)."<br />\n";
				return false;
			}

			while ($status && ($item = readdir($handle)) !== false) {
				if ($item == '.' || $item == '..') continue;

				$status = $status && delete_files($file."/".$item,$empty_files);
			}

			closedir($handle);
			@rmdir($realpath);
		} else {
			echo lng_get("removing_file","file",$path)." - ";

			$file_status = true;
			if (is_array($empty_files) && in_array($file, $empty_files)) {
				if (@filesize($realpath) > 0) {
					$fp = @fopen($realpath,"w");
					if ($fp === false) $file_status = false;
					else {
						if (fwrite($fp,"{* *}") === false) $file_status = false;
						@fclose($fp);
						@chmod($realpath, 0666);
					}
				}
			}
			elseif (!@unlink($realpath))
				$file_status = false;

			echo status($file_status)."<br />\n"; flush();
			$status = $status && $file_status;
		}
	}

	return $status;
}

function read_skin_descr() {
	global $templates_directory, $xcart_dir;

	$file = $xcart_dir.DIRECTORY_SEPARATOR.$templates_directory.DIRECTORY_SEPARATOR.'.skin_descr';
	if (!file_exists($file)) return false;

	$data = file($file);
	$result = array();
	foreach ($data as $line) {
		$line = trim($line);
		list($key,$value) = explode('=',$line,2);
		$result[$key] = $value;
	}

	return $result;
}

################################################################
#
# Modules goes here
#
################################################################

#
# prepare: Select language
#

function module_language(&$params) {
	global $error, $templates_directory;
	global $installation_auth_code;
	global $installation_product;
	global $available_install_languages, $install_lng_defs;

?>
<center>
<br /><br /><br />
<?php echo_lng("select_language_prompt"); ?>:
<select name="params[lngcode]">
<?php foreach ($available_install_languages as $lngcode) { ?>
	<option value="<?php echo $lngcode; ?>"><?php echo $install_lng_defs[$lngcode]["name"]; ?></option>
<?php } ?>
</select>

<br /><br />

</center>

<br />

<?php
	return false;
}


#
# start: Default module
# Shows Terms & Conditions
#

function module_moddefault(&$params) {
	global $error, $templates_directory;
	global $installation_auth_code;
	global $installation_product;
	global $xcart_dir;
	global $module_definition;
	$func_is_installed = @$module_definition["is_installed"];
?>
<center>
<?php message(lng_get("thank_you", "product", $installation_product)); ?>
<br /><br />

<textarea name="copyright" cols="80" rows="22">
<?php
ob_start();
require "./COPYRIGHT";
$tmp = ob_get_contents();
ob_end_clean();
echo htmlspecialchars($tmp);
?>
</textarea>

<p />
<table>
<?php if (!empty($func_is_installed) && function_exists($func_is_installed) && $func_is_installed()) { ?>
<tr>
	<td><input type="radio" id="install_type_1" name="params[install_type]" value="1" checked="checked" /></td>
	<td align="left"><label for="install_type_1"><b><?php echo_lng("new_install"); ?></b></label></td>
</tr>
<tr>
	<td><input type="radio" id="install_type_3" name="params[install_type]" value="3" /></td>
	<td align="left"><label for="install_type_3"><b><?php echo_lng("uninstall_module"); ?></b></label></td>
</tr>
<?php } else {?>
<tr style="display: none;">
	<td><input type="hidden" name="params[install_type]" value="1" /></td>
</tr>
<?php }?>
<tr>
	<td colspan="2" align="left">
<b><?php echo_lng("auth_code"); ?>: </b><input type="text" name="params[auth_code]" size="20" /><br /><font size="1"><?php echo_lng("auth_code_note"); ?></font>
	</td>
</tr>
</table>

<p />

<input id="agree" type="checkbox" name="params[agree]" /> <label for="agree"><?php echo_lng("i_accept_license"); ?></label>

<br /><br />

</center>

<br />

<?php
	return false;
}

#
# 'next' button handler. checks 'agree' button checked
#

function module_moddefault_js_next() {
?>
	function step_next() {
		if (document.getElementById('agree').checked || (document.getElementById('install_type_3') && document.getElementById('install_type_3').checked))
			return true;

		alert("<?php echo_lng_js("mod_license_alert"); ?>");
		return false;
	}
<?php
}

#
# end: Default module
#

#
# start: modinstall
# Installs the module
#

function module_modinstall($params) {
	global $error;
	global $module_definition;
	global $var_dirs;

	$ck_res = true;

	if (!empty($module_definition["skin_files"])) {
		if (@$params["install_type"] == 3) {
			echo "<b>".lng_get("removing_skin_files")."</b><br /><br />";
			$ck_res = delete_files($module_definition["skin_files"],@$module_definition["skin_files_empty"]);
			echo status($ck_res)."<br /><br />";
			if ($ck_res) {
				echo "<b>".lng_get("deactivating_module")."</b><br />";

				$sqlfiles = $module_definition["sql_files"];
				if (!is_array($sqlfiles))
					$sqlfiles = array($sqlfiles);

				foreach ($sqlfiles as $_k => $f) {
					if (strpos($f,"_remove") === false)
						continue;
					$ck_res = $ck_res && query_upload($f);
					if (!$ck_res)
						break;
				}

				if ($module_definition['onuninstall'] && function_exists($module_definition['onuninstall']))
					$module_definition['onuninstall']();

				echo status($ck_res)."<br /><br />";
			}
		}
		else {
			echo "<b>".lng_get("copying_skin_files")."</b><br /><br />";

			$ck_res = copy_files_plain($module_definition["skin_files"]);
			echo status($ck_res)."<br /><br />";
		}
	}

	if (@$params["install_type"]==1 && $ck_res && !empty($module_definition["sql_files"])) {
		echo "<b>".lng_get("activating_module")."</b><br />";

		$sqlfiles = $module_definition["sql_files"];
		if (!is_array($sqlfiles)) $sqlfiles = array($sqlfiles);
		foreach ($sqlfiles as $_k=>$f) {
			$ck_res = $ck_res && query_upload($f);
			if (!$ck_res) break;
		}
	}

	func_rm_dir_files($var_dirs['cache']);
	func_rm_dir_files($var_dirs['templates_c']);

	$error = !$ck_res;
}

#
# end: modinstall
#

#
# start: Install_done module
#

function module_install_done(&$params, $modparam) {
	global $error, $installation_auth_code, $templates_repository;
	global $xcart_dir;
	global $xcart_package;
	global $module_definition;

	if (!empty($module_definition)) {
		echo "<h3><b><font color=darkgreen>".lng_get("module_installed", "name", $module_definition["name"])."</font></b></h3>";
		echo lng_get("module_final_msg", "name", $module_definition["name"], "script", $module_definition["script"])."<br />";
		echo_lng("distribution_warning", "product", $module_definition["name"]);
	}

	$xcart_package=file_exists((!empty($xcart_dir)?$xcart_dir.DIRECTORY_SEPARATOR:"").$templates_repository.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."home.tpl")?"PRO":"GOLD";
	if (!empty($modparam)) {
		if (function_exists($modparam)) $modparam();
		else echo $modparam;
	}
	return false;
}

#
# end: Install_done module
#

#
# start: uninstall_done module
#

function module_uninstall_done(&$params, $modparam) {
	global $error, $installation_auth_code, $templates_repository;
	global $xcart_dir;
	global $xcart_package;
	global $module_definition;

	if (!empty($module_definition)) {
		echo "<h3><b><font color=\"darkgreen\">".lng_get("module_uninstalled", "name",$module_definition["name"])."</font></b></h3>";
		echo_lng("distribution_warning", "product", $module_definition["name"]);
	}

	return false;
}

#
# end: uninstall_done module
#
?>
