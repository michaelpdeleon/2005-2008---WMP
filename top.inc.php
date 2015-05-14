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
# $Id: top.inc.php,v 1.17.2.3 2006/07/26 10:59:39 max Exp $
#


if (!defined('XCART_START')) {

define('XCART_START',1);

define('XCART_START_TIME',microtime());
define('XCART_START_MEM', function_exists("memory_get_usage") ? memory_get_usage() : 0);

#
# Switching on the internal performance measurement mechanism
#
define('BENCH', false);

#
# Do not display the performance report
#
define('BENCH_SIMPLE', true);

#
# Show tracing
#
define('BENCH_BACKTRACE', false);

#
# Write to log file only
#
define('BENCH_LOG_ONLY', false);

#
# Write summary counters to log file
#
define('BENCH_LOG_SUMMARY', false);

#
# Code execution time, threshold value (for logging)
#
define('BENCH_LOG_TIME_LIMIT', 0.05);

#
# A comma-separated list of measurable performance characteristics that you wish to be logged
#
define('BENCH_LOG_TYPE_LIMIT', "");

#
# Report type to be displayed:
# T - only total values
# F - full report
# A - advanced report
define('BENCH_DISPLAY_TYPE', "T");

#
# Code execution time, threshold value
#
define('BENCH_TIME_LIMIT', 0.05);

#
# Amount of memory being used, threshold value
#
define('BENCH_MEM_LIMIT', 0.1);

#
# Remove results of automatic variables registration then register_globals=on
#

foreach (get_defined_vars() as $__key=>$__val) {
	if (defined('USE_TRUSTED_POST_VARIABLES') && $__key == "trusted_post_variables") continue;

	if (defined('XCART_INSTALL') && $__key == "module_definition") continue;

	if (!in_array($__key, array("GLOBALS","HTTP_GET_VARS","HTTP_POST_VARS","HTTP_SERVER_VARS","HTTP_ENV_VARS","HTTP_COOKIE_VARS","HTTP_POST_FILES","__key","__val","_GET","_POST","_SERVER","_COOKIE","HTTP_RAW_POST_DATA"))) {
		unset($$__key);
	}
}

unset($__key, $__val);

$bench_counts = $bench_profilier = array();
$__smarty_size = $bench_max_session = $bench_max_memory = 0;

#
# Directories structure definitions
#

#
# Real path to the directory where X-Cart is installed
# If you have problems with __FILE__ constant definition on your server
# you can specify path directly. For example:
# $xcart_dir = '/home/user/public_html/xcart';
#
$xcart_dir = realpath(dirname(__FILE__));

# Directories location definition
# Examples:
# Customer's scripts are placed into the X-Cart subdirectory:
# 	define ('DIR_CUSTOMER', '/<name_of_directory>');
# 	define ('DIR_CUSTOMER', '/customer');
# 	define ('DIR_ADMIN', '/admin');
# 	define ('DIR_ADMIN', '/service_area/administration');
#
# (!) Customer's scripts are placed into the root X-Cart directory:
# 	define ('DIR_CUSTOMER', '');
#
define ('DIR_CUSTOMER', '');
define ('DIR_ADMIN', '/admin');
define ('DIR_PROVIDER', '/provider');
define ('DIR_PARTNER', '/partner');

#
# Note: DIR_PARTNER is valid only for installed X-Affiliate module
#
}
?>
