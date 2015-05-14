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
# $Id: func.https_ssleay.php,v 1.2 2006/01/11 06:56:00 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

# INPUT:

# $method		[string: POST|GET]

# $url			[string]
#	user:password@www.yoursite.com:443/path/to/script.asp

# $data			[array]
#	$data[] = "parametr=value";

# $join			[string]
#	$join = "\&";

# $cookie		[array]
#	$cookie = "parametr=value";

# $conttype		[string]
#	$conttype = "text/xml";

# $referer		[string]
#	$referer = "http://www.yoursite.com";

# $cert			[string]
#	$cert = "../certs/demo-cert.pem";

# $kcert		[string]
#	$keyc = "../certs/demo-keycert.pem";

# $rhead		[string]
#	$rhead = "...";

# $rbody		[string]
#	$rbody = "...";

function func_https_request_ssleay($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="")
{
	global $config;
	global $xcart_dir;

	if ($method != "POST" && $method != "GET")
			return array("0","X-Cart HTTPS: Invalid method");

	if (!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
			return array("0","X-Cart HTTPS: Invalid URL");

	$perl_exe = func_find_executable("perl",$config["General"]["perl_binary"]);
	if ($perl_exec === false)
			return array("0","X-Cart HTTPS: perl is not found");

	$includes  = " -I".func_shellquote($xcart_dir.'/payment');
	$includes .= " -I".func_shellquote($xcart_dir.'/payment/Net');
	$execline = func_shellquote($perl_exe).' '.$includes.' '.func_shellquote($xcart_dir."/payment/netssleay.pl");

	$ui = parse_url($url);
	if (empty($ui['port'])) $ui['port'] = 443;

	$request = func_https_prepare_request($method, $ui,$data,$join,$cookie,$conttype,$referer,$headers);
	$tmpfile = func_temp_store($request);
	if (empty($tmpfile))
		return array(0, "X-Cart HTTPS: cannot create temporaly file");

	$ignorefile = func_temp_store("");
	$execline .= " $ui[host] $ui[port] ".func_shellquote($cert).' '.func_shellquote($kcert).' < '.func_shellquote($tmpfile).' 2>'.func_shellquote($ignorefile);

	$fp = popen($execline, "r");
	if (!$fp)
		return array(0, "X-Cart HTTPS: Net::SSLeay execution failed");

	$res = func_https_receive_result($fp);
	pclose($fp);
	@unlink($tmpfile);
	@unlink($ignorefile);

	return $res;
}

?>
