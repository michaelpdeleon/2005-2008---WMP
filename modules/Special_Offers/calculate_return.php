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
# $Id: calculate_return.php,v 1.12 2006/01/11 06:56:18 mclap Exp $
#
# Called from func_calculate()
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('cart');

if (!$single_mode) {
	if (!empty($result['have_offers']))
		$return['have_offers'] = true;

	if (!empty($result['bonuses'])) {
		if (!empty($return['bonuses'])) {
			$return['bonuses']['points'] += $result['bonuses']['points'];

			$return['bonuses']['memberships'] = func_array_merge_assoc($return['bonuses']['memberships'], $result['bonuses']['memberships']);
		}

		if (empty($return['bonuses']['memberships']))
			$return['bonuses']['memberships'] = false;

		if ($return['bonuses']['points'] == 0 && empty($return['bonuses']['memberships']))
			unset($return['bonuses']);
	}

	if (isset($return['extra'])) {
		$return['extra']['special_bonuses'] = false;

		if (!empty($return['bonuses'])) {
			$return['extra']['special_bonuses'] = $return['bonuses'];
		}
	}

	if (empty($return['not_used_free_products'])) {
		$return['not_used_free_products'] =
			$result['not_used_free_products'];
	}
	else {
		$return['not_used_free_products'] =
			func_offer_merge_free_products(
				$return['not_used_free_products'],
				$result['not_used_free_products']);
	}
}

if (empty($return['bonuses']))
	$return['bonuses'] = false;

if (empty($return['not_used_free_products']))
	$return['not_used_free_products'] = false;

#
# Assign cartid to products and correct max_cartid
#
$__key = ($single_mode) ? 0 : $key;

func_offer_correct_cartid($return, $__key, $cart, $single_mode);

?>
