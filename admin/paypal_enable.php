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
# $Id: paypal_enable.php,v 1.6 2006/01/11 06:55:58 mclap Exp $
#

@include_once "./top.inc.php";
@include_once "../top.inc.php";
@include_once "../../top.inc.php";
if (!defined('DIR_ADMIN')) die("ERROR: Can not initiate application! Please check configuration.");

require_once $xcart_dir."/init.php";

x_load('backoffice');

if (!empty($paypal_enable_id) && func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='paypal_enable_id' AND value='$paypal_enable_id'") !== false) {
	# activate paypal processing...
	func_disable_paypal_methods($config['paypal_solution'], true);

	x_session_register("top_message");
	$top_message["content"] = func_get_langvar_by_name("msg_paypal_processing_enabled");
	$top_message["type"] = "I";
	db_query("DELETE FROM $sql_tbl[config] WHERE name='paypal_enable_id' AND value='$paypal_enable_id'");

	x_session_register("login");
	x_session_register("login_type");
	#
	# Check: if admin is already logged in, then redirect him to the settings of paypal
	#
	if (!empty($login) && !empty($login_type)) {
		if ($active_modules["Simple_Mode"] && ($login_type == "P" || $login_type == "A") || $login_type == "A") {
			func_header_location("cc_processing.php?mode=update&cc_processor=ps_paypal.php");
		}
	}
}

func_header_location("home.php");

?>
