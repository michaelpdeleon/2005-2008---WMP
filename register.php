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
# $Id: register.php,v 1.7 2006/01/11 06:55:57 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/categories.php";

if ($active_modules["Manufacturers"])
    include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

x_session_register("cart");
x_session_register("current_carrier");

$newbie = "Y";

if (!empty($action) and $action == "cart") {
	$smarty->assign("action", $action);
	$smarty->assign("paymentid", $paymentid);
}

#
# Where to forward <form action
#
$smarty->assign("register_script_name",(($config["Security"]["use_https_login"]=="Y")?$xcart_catalogs_secure['customer']."/":"")."register.php");

if (empty($mode) and !empty($login))
	$mode = "update";

require $xcart_dir."/include/register.php";

if ($REQUEST_METHOD == "POST" && $action == "cart") {

	if (empty($reg_error))
		func_header_location("cart.php?mode=checkout&paymentid=$paymentid");
}

$smarty->assign("login",$login);

$smarty->assign("newbie",$newbie);

if (!empty($active_modules["Fast_Lane_Checkout"]))
    include $xcart_dir."/modules/Fast_Lane_Checkout/cart.php";

# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);
?>
