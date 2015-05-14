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
# $Id: number_conv.php,v 1.6 2006/04/07 06:00:27 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (defined("NUMBER_VARS")) {

	# Get variables list
	$tmp = explode(",", constant("NUMBER_VARS"));
	foreach ($tmp as $v) {
		$v = trim($v);

		if (preg_match("/^([\w\d_]+)(\[[\w\d_]+\])+$/S", $v, $match)) {
			# Variable is cell of array
			eval('$var = isset($'.$v.');');
			if ($var) {
				eval('$'.$v.' = func_convert_number($'.$v.');');
				$pos = strpos($v, "[");
				if ($pos !== false) {
					$v_array = substr($v, $pos);
					$v_orig = substr($v, 0, $pos-1);
					eval('$var = isset($HTTP_POST_VAR['.$v_orig.']'.$v_array.');');
					if ($var) {
						eval('$HTTP_POST_VAR['.$v_orig.']'.$v_array.' = $'.$v.';');
					}
					else {
						eval('$var = isset($HTTP_GET_VAR['.$v_orig.']'.$v_array.');');
						if ($var) {
							eval('$HTTP_GET_VAR['.$v_orig.']'.$v_array.' = $'.$v.';');
						}
					}
				}
			}
		}
		elseif (isset($$v) && is_string($$v)) {
			# Variable is string
			$$v = func_convert_number($$v);
			if (isset($HTTP_POST_VARS[$v])) {
				$HTTP_POST_VARS[$v] = $$v;
			}
			elseif (isset($HTTP_GET_VARS[$v])) {
				$HTTP_GET_VARS[$v] = $$v;
			}
		}
	}
}

$smarty->assign("number_format_dec", $config['Appearance']['number_format']{1});
$smarty->assign("number_format_th", $config['Appearance']['number_format']{2});
$smarty->assign("number_format_point", intval($config['Appearance']['number_format']{0}));

$smarty->assign("zero", func_format_number(0));
?>
