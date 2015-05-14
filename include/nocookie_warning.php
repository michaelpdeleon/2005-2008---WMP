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
# $Id: nocookie_warning.php,v 1.6.2.2 2006/08/09 13:27:12 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

if (defined('IS_ROBOT'))
	return;

if ((empty($HTTP_COOKIE_VARS) || isset($NO_COOKIE_WARNING))
&& ($REQUEST_METHOD == 'POST' || ($REQUEST_METHOD == 'GET' && (isset($HTTP_GET_VARS['sl']) && !isset($HTTP_GET_VARS['is_https_redirect'])) || isset($NO_COOKIE_WARNING)))
) {
	if (isset($NO_COOKIE_WARNING)) {
		# stage 2: check if cookies was set
		if (empty($HTTP_COOKIE_VARS)) {
			if ($NO_COOKIE_WARNING == 1) {
				# second try
				func_header_location($PHP_SELF."?NO_COOKIE_WARNING=2&ti=$ti");

			} elseif (defined("AREA_TYPE")) {
				# cookies are not enabled yet
				func_header_location('error_message.php?error=disabled_cookies&ti='.$ti);

			} else {
				# cookies are not enabled yet and the user is redirected to the zone from which the initial request was made

				$save_data = func_db_tmpread(stripslashes($ti));
				$prefix = $xcart_catalogs['customer'];

				if ($save_data['__area']) {
					switch($save_data['__area']) {
						case "A":
							$prefix = $xcart_catalogs['admin'];
							break;

						case "P":
							$prefix = $xcart_catalogs['provider'];
							break;

						case "B";
							if (!empty($active_modules['XAffiliate'])) {
								$prefix = $xcart_catalogs['partner'];
								break;
							}

						default:
							$prefix = $xcart_catalogs['customer'];
					}
				}

				func_header_location($prefix.'/error_message.php?error=disabled_cookies&ti='.$ti);
			
			}
		}
		else {
			$save_data = func_db_tmpread(stripslashes($ti), true);
			if (is_array($save_data)) {
				extract($save_data);
				foreach(array("GET","POST","SERVER") as $__avar) {
					foreach (${"HTTP_".$__avar."_VARS"} as $__var => $__res)
						${$__var} = $__res;
				}
			}

			return;
		}

	} else {
		# stage 1: save the data

		$repost = false;
		if ($REQUEST_METHOD == "POST") {

			# Defining a situation, in which a POST request comes from a page
			# located in a different domain, or in which a POST request is made
			# directly from a page stored locally on a user's computer
			if (empty($HTTP_REFERER)) {
				$repost = true;

			} else {
				$old_page = parse_url($HTTP_REFERER);
				$repost = (($old_page['domain'] != $HTTP_SERVER_VARS['HTTP_HOST']) || ($old_page['scheme'] == 'http' && $HTTPS) || ($old_page['scheme'] == 'https' && !$HTTPS));
			}
		}

		if (!$repost && preg_match("/(?:^|\/)([\w\d_]+\.php)\??(.*)/", $REQUEST_URI, $_no_save_match) && $_no_save_match[1]=='login.php') {
			$save_data = false;
			if (!empty($xcart_catalogs[$redirect]))
				$prefix = $xcart_catalogs[$redirect].'/';

		} else {
			$save_data = array (
				'REQUEST_METHOD' => $REQUEST_METHOD,
				'HTTP_POST_VARS' => $HTTP_POST_VARS,
				'HTTP_GET_VARS' => $HTTP_GET_VARS,
				'HTTP_SERVER_VARS' => $HTTP_SERVER_VARS,
				'PHP_SELF' => $PHP_SELF,
				'QUERY_STRING' => $QUERY_STRING,
				'HTTP_REFERER' => $HTTP_REFERER,
				'__area' => defined('AREA_TYPE') ? constant('AREA_TYPE') : (!empty($current_type) ? $current_type : false)
			);
		}

		$id = func_db_tmpwrite($save_data);

		if ($repost)
			func_header_location($PHP_SELF."?NO_COOKIE_WARNING=2&ti=".$id);
		else
			func_header_location($prefix.'error_message.php?error=disabled_cookies&ti='.$id);
	}

}
?>
