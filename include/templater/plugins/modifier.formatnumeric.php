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
# $Id: modifier.formatnumeric.php,v 1.2 2006/01/11 06:56:01 mclap Exp $
#
# Templater plugin
# -------------------------------------------------------------
# Type:     modifier
# Name:     formatnumeric
# Purpose:  format numeric with configurable thousands and decimal separators
# -------------------------------------------------------------
#

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_modifier_formatnumeric($price, $thousand_delim = NULL, $decimal_delim = NULL, $precision = NULL) {
	global $config;

	if (strlen(@$price) == 0)
		return $price;

	$format = $config['Appearance']['number_format'];

	if (empty($format)) $format = "2.,";

	if (is_null($thousand_delim) || $thousand_delim === false)
		$thousand_delim = substr($format,2,1);

	if (is_null($decimal_delim) || $decimal_delim === false)
		$decimal_delim = substr($format,1,1);

	if (is_null($precision) || $precision === false) {
		$price = (string)$price;
		$zero_pos = strpos($price, ".");
		$precision = ($zero_pos === false) ? 0 : (strlen($price)-$zero_pos-1);
	}

	return number_format((double)$price+0.00000000001, $precision, $decimal_delim, $thousand_delim);
}

?>
