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
# $Id: referer.php,v 1.6 2006/02/14 13:26:36 mclap Exp $
#
# This module tracks referer headers 
#

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

	$referer = substr(@$HTTP_REFERER,0,255);
	#Don't count referers that came from the same site
	if (!(strstr($referer, "http://$xcart_http_host/") || strstr($referer, "https://$xcart_https_host/") || $referer == "") && !isset($HTTP_COOKIE_VARS["RefererCookie"])){
        $curr_time = time();
		$referer_result = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[referers] WHERE referer='".addslashes($referer)."'");
		if ($referer_result)
			db_query("UPDATE $sql_tbl[referers] SET visits = (visits+1), last_visited='$curr_time' WHERE referer='".addslashes($referer)."'");
		else
			db_query("REPLACE INTO $sql_tbl[referers] (referer, visits, last_visited) VALUES('".addslashes($referer)."', '1', '$curr_time')");
    }

# If user have no cookie with referer to place from where he came set it
# It will be used later when he decides to register
x_session_register("referer_session");
if (!isset($HTTP_COOKIE_VARS["RefererCookie"]) || empty($referer_session)) {
	if(empty($referer_session)) {
		$referer_session = (isset($HTTP_COOKIE_VARS["RefererCookie"])?$HTTP_COOKIE_VARS["RefererCookie"]:$referer);
	}
	$referer = $referer_session;
	$_tmp = parse_url($current_location);
	@setcookie("RefererCookie", $referer, time()+3600*24*180, "/", $_tmp["host"]);
}
?>
