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
# $Id: auth.php,v 1.45 2006/01/11 06:56:25 mclap Exp $
#

define('AREA_TYPE', 'P');

@include_once "./top.inc.php";
@include_once "../top.inc.php";
@include_once "../../top.inc.php";
if (!defined('DIR_CUSTOMER')) die("ERROR: Can not initiate application! Please check configuration.");

require_once $xcart_dir."/init.php";

x_session_register("login");
x_session_register("login_type");

x_session_register("logged");

x_session_register("export_ranges");

$smarty->assign("js_enabled", "Y");

x_session_register("top_message");
if (!empty($top_message)) {
	$smarty->assign("top_message", $top_message);
	if($config['Adaptives']['is_first_start'] != 'Y')
		$top_message = "";
	x_session_save("top_message");
}

$current_area="P";

include $xcart_dir."/include/get_language.php";

$_loc = (!empty($active_modules["Simple_Mode"])) ? $xcart_catalogs["admin"]."/" : "";

$location = array();
$location[] = array(func_get_langvar_by_name("lbl_main_page"), $_loc."home.php");

@include $xcart_dir."/modules/gold_auth.php";
include $xcart_dir."/include/check_useraccount.php";

#
# Single Mode always active for root account
#
if($user_account["flag"]=="RP") $single_mode=true;

x_session_save();

$smarty->assign("redirect","provider");

if (!empty($active_modules["News_Management"]))
	include $xcart_dir."/modules/News_Management/news_last.php";
?>
