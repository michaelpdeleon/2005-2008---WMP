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
# $Id: unallowed_request.php,v 1.6 2006/02/26 12:34:27 mclap Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../index.php"); die("Access denied"); }

if (!defined('ADMIN_UNALLOWED_VAR_FLAG') || @$config["Security"]["unallowed_request_notify"]!="Y")
	return;

x_load('mail');

x_session_register("login");

# save globals
ob_start();
print "[HTTP_GET_VARS] => "; print_r($HTTP_GET_VARS);
print "[HTTP_POST_VARS] => "; print_r($HTTP_POST_VARS);
print "[HTTP_COOKIE_VARS] => "; print_r($HTTP_COOKIE_VARS);
print "[HTTP_SERVER_VARS] => "; print_r($HTTP_SERVER_VARS);
print "[HTTP_ENV_VARS] => "; print_r($HTTP_ENV_VARS);
$text = ob_get_contents(); ob_end_clean();

$err_str  = "Date            : ".date("d-M-Y H:i:s")."\n";
$err_str .= "Site            : $current_location\n";
$err_str .= "Script          : $PHP_SELF\n";
$err_str .= "Remote IP       : $REMOTE_ADDR\n";
$err_str .= "Logged as       : $login\n";
$err_str .= "Query string    : $QUERY_STRING\n";
$err_str .= "Dump of request :\n\n".$text;
func_send_simple_mail(
	$config["Company"]["site_administrator"],
	$config["Company"]["company_name"].": Unallowed request to site notification",
	$err_str, $config["Company"]["site_administrator"]);

?>
