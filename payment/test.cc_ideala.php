<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2005 Ruslan R. Fazliev <rrf@rrf.ru>                      |
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
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2005           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: test.cc_ideala.php,v 1.1.2.2 2006/07/31 06:36:26 max Exp $
#
# iDEAL: Postbank payment gateway
#
# Check payment module requirements
#
if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$good = function_exists("openssl_x509_read") &&
		function_exists("openssl_x509_export") &&
		function_exists("openssl_get_privatekey") &&
		function_exists("openssl_sign") &&
		function_exists("openssl_free_key");
?>
