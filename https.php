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
# $Id: https.php,v 1.21.2.1 2006/04/20 08:19:43 max Exp $
#
# HTTP-HTTPS redirection mechanism code
#

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

x_load('files');

$https_messages = array(array("mode=order_message","orderids="), "error_message.php");
$https_scripts = array();

if (empty($REQUEST_URI) || substr($REQUEST_URI, -1) == '/')
	$HTTP_SERVER_VARS['REQUEST_URI'] = $REQUEST_URI = $PHP_SELF.($QUERY_STRING ? ("?".$QUERY_STRING) : "");

#
# create payment scripts entries in $https_scripts
#
$payment_data = func_data_cache_get("payments_https");

if ($payment_data && is_array($payment_data)) {
	foreach ($payment_data as $payment_method_data)
		$https_scripts[]=array("paymentid=".$payment_method_data["paymentid"],"mode=checkout");
}

$https_scripts[] = "secure_login.php";
if ($config["Security"]["use_https_login"] == "Y") {
	$https_scripts[] = "register.php";
	$https_scripts[] = array("cart.php", "mode=checkout");
	$https_scripts[] = array("cart.php", "mode=auth");
}

function is_https_link($link, $https_scripts) {
	if (empty($https_scripts))
		return false;

	$link = preg_replace('!^/+!S','', $link);

	foreach ($https_scripts as $https_script) {
		if (!is_array($https_script))
			$https_script = array($https_script);

		$tmp = true;
		foreach ($https_script as $v) {
			$p = strpos($link, $v);
			if ($p === false) {
				$tmp = false;
				break;
			}

			if ($v[strlen($v)-1] === '=') continue;

			if ($p + strlen($v) < strlen($link)) {
				$last = $link[$p+strlen($v)];
				if ($last === '?' && $p == 0) continue;

				if ($last !== '&') {
					$tmp = false;
					break;
				}
			}
		}

		if ($tmp) return true;
	}

	return false;
}

$_location = parse_url($current_location.DIR_CUSTOMER);
$_location["path"] = func_normalize_path($_location["path"],'/');
$current_script = substr(func_normalize_path($REQUEST_URI,'/'), strlen($_location["path"]));

#
# Generate additional PHPSESSID var
#
$additional_query = ($QUERY_STRING?"&":"?").(strstr($QUERY_STRING,$XCART_SESSION_NAME) ? "" : $XCART_SESSION_NAME."=".$XCARTSESSID);
if (!preg_match("/(?:^|&)sl=/", $additional_query) && $xcart_http_host != $xcart_https_host)
	$additional_query .= "&sl=".$store_language."&is_https_redirect=Y";

if ($REQUEST_METHOD=="GET" && empty($HTTP_GET_VARS["keep_https"])) {
	$tmp_location = "";
	if (!$HTTPS && is_https_link($current_script, $https_scripts)) {
		$tmp_location = $https_location.DIR_CUSTOMER.$current_script.$additional_query;
	}
	elseif (!$HTTPS && is_https_link($current_script, $https_messages) && !strncasecmp($HTTP_REFERER, $https_location, strlen($https_location))) {
		$tmp_location = $https_location.DIR_CUSTOMER.$current_script.$additional_query;
	}
	elseif ($config["Security"]["dont_leave_https"] != "Y" && $HTTPS && !is_https_link($current_script, $https_scripts) && !is_https_link($current_script, $https_messages)) {
		x_session_register("login_redirect");
		$do_redirect = empty($login_redirect);
		x_session_unregister("login_redirect");
		if ($do_redirect) {
			$tmp_location = $http_location.DIR_CUSTOMER.$current_script.$additional_query;
		}
	}

	if (!empty($tmp_location)) {
		if ($smarty->webmaster_mode) {
			echo '<html><body>
<script type="text/javascript" language="JavaScript 1.2">
<!--
_smarty_console = window.open("","console","width=360,height=500,resizable,scrollbars=yes");
if (_smarty_console)
	_smarty_console.close();
-->
</script>
</body></html>';
		}

		func_header_location($tmp_location);
	}
}

?>
