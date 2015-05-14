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
# $Id: modifier.escape.php,v 1.1.2.2 2006/05/29 07:59:25 svowl Exp $
#
# Templater plugin
# -------------------------------------------------------------
# Type:     modifier
# Name:     escape
# Purpose:  Escape the string according to escapement type
# Comment:  Replaces the original Smarty-modifier because of 4.0.6 incompatibility
# -------------------------------------------------------------
#

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_modifier_escape($string, $esc_type = 'html', $char_set = 'ISO-8859-1') {
    if (zerolen($string))
        return $string;

    switch ($esc_type) {
        case 'html':
            if (phpversion() >= '4.1.0')
                return htmlspecialchars($string, ENT_QUOTES, $char_set);
            else
                return htmlspecialchars($string, ENT_QUOTES);

        case 'htmlall':
            if (phpversion() >= '4.1.0')
                return htmlentities($string, ENT_QUOTES, $char_set);
            else
                return htmlentities($string, ENT_QUOTES);

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F', '/', rawurlencode($string));
            
        case 'quotes':
            return preg_replace("/(?<!\\\\)'/Ss", "\\'", $string);

        case 'hex':
            $s = '%';
        case 'hexentity':
            if (!$s)
                $s = '&#x';
        case 'decentity':
            if (!$s)
                $s = '&#';
            $f = ($esc_type == 'decentity') ? "ord" : "bin2hex";
            $l = strlen($string);
            $return = '';
            for ($x = 0; $x < $l; $x++)
                $return .= $s.$f(substr($string, $x, 1)).';';

            return $return;

        case 'javascript':
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
            
        case 'mail':
            return strtr($string, array('@', '.'), array(' [AT] ', ' [DOT] '));
            
        case 'nonstd':
            $return = '';
            $l = strlen($string);
            for ($i = 0; $i < $l; $i++) {
                $symbol = substr($string, $i, 1);
                $ord = ord($symbol);
                $return .= ($ord >= 126) ? ('&#'.$ord.';') : $symbol;
            }
            return $return;

    }

    return $string;
}
?>
