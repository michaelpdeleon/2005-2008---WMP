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
# $Id: https_detect.php,v 1.4 2006/04/10 12:33:53 max Exp $
#
# Called from prepare.php
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$HTTPS_RELAY = false;
$HTTPS = (stristr($HTTP_SERVER_VARS["HTTPS"], "on") || ($HTTP_SERVER_VARS["HTTPS"] == 1) || ($HTTP_SERVER_VARS["SERVER_PORT"] == 443));

# ========================================================= #
# Please place your custom detection code below these lines #
# ========================================================= #

#
# If you wish to set X-Cart to work through an HTTPS proxy, define the proxy
# IP address here and set the variable $HTTPS to 'true'. X-Cart will match all
# the IP addresses it will receive with incoming requests against the IP
# address specified here and thus will be able to define whether a request is
# coming from HTTPS proxy or not. 
# If the web path used for work via HTTPS proxy differs from the path used for
# work via HTTP (for example, HTTP xcart web root: '/xcart/'; HTTPS xcart web
# root: '/~example/xcart/'), you also need to set the variable $HTTPS_RELAY to
# 'true'.
# Please find an example of processing such a situation below (In the example,
# the HTTPS proxy IP address is 192.160.1.1):
#
# if ($HTTP_SERVER_VARS['REMOTE_ADDR'] == '192.160.1.1') {
# 	$HTTPS_RELAY = true;
#	$HTTPS = true;
# }
#

?>
