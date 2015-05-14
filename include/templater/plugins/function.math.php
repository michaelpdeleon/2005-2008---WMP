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
# $Id: function.math.php,v 1.5 2006/01/11 06:56:01 mclap Exp $
#
# Templater plugin
# -------------------------------------------------------------
# Type:     function
# Name:     math
# Purpose:  allow mathematical equations
# -------------------------------------------------------------
#

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_math($params, &$smarty) {
	static $reserved_params = array ('assign', 'equation', 'format');
	static $allowed_funcs = array (
		'ceil','floor','round',
		'int','float','base_convert',
		'abs','max','min','pi','rand','lcg_value',
		'cos','sin','tan','acos','asin','atan',
		'log','log10','exp','pow','sqrt');

	$error_prefix = 'math ``<b>'.htmlspecialchars($params['equation']).'</b>\'\' in ``'.$smarty->current_resource_name.'\'\': ';

	if (!isset($params['equation'])) {
		$smarty->trigger_error($error_prefix.'missing equation');
		return;
	}

	$equation = $params['equation'];
	$result = null;
	if (empty($equation)) {
		$result = $equation;
	}
	else {
		if (substr_count($equation,"(") != substr_count($equation,")")) {
			$smarty->trigger_error($error_prefix.'unbalanced parenthesis');
			return;
		}

		# match all vars in equation, make sure all are passed
		preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z][a-zA-Z0-9_]+)!S",$equation, $match);
    
		foreach($match[1] as $curr_var) {
			if ($curr_var && !in_array($curr_var, array_keys($params)) && !in_array($curr_var, $allowed_funcs)) {
				$smarty->trigger_error($error_prefix."function call $curr_var is not allowed");
				return;
			}
		}

		$keys_empty = array();
		$keys_not_numeric = array();
		$error = false;

		# substitute parameters in equation
		foreach($params as $key => $val) {
			if (in_array($key, $reserved_params)) continue;

			if (strlen($val)==0) {
				$keys_empty[] = $key;
				$error = true;
				continue;
			}
			if (!is_numeric($val)) {
				$keys_not_numeric[] = $key;
				$error = true;
				continue;
			}

			if (!$error) {
				$equation = preg_replace("!\b$key\b!S",$val, $equation);
			}
		}

		if ($error) {
			$err_arr = array();
			$err_def = array (
				'parameter%s ``<b>%s</b>\'\' %s empty' => $keys_empty,
				'parameter%s ``<b>%s</b>\'\' %s not numeric' => $keys_not_numeric
			);
			foreach ($err_def as $fmt => $keys_arr) {
				$cnt = count($keys_arr);
				if ($cnt < 1) continue;
				$err_arr[] = sprintf( $fmt,
					($cnt>1?'s':''),
					implode('</b>\'\', ``<b>', $keys_arr),
					($cnt>1?'are':'is')
				);
			}

			$smarty->trigger_error($error_prefix.implode('; ', $err_arr));
			return;
		}

		@eval("\$result = ".$equation.";");
	}

	if (!empty($params['format']))
		$result = sprintf($params['format'], $result);

	if (!empty($params['assign'])) {
		$smarty->assign($params['assign'], $result);
		return '';
	}

	return $result;
}

?>
