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
# $Id: webmaster.php,v 1.29 2006/01/11 06:55:59 mclap Exp $
#
# "Webmaster" mode initialization script 
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($HTTP_POST_VARS["editor_mode"] || $HTTP_GET_VARS["editor_mode"] || $HTTP_COOKIE_VARS["editor_mode"]) {
    func_header_location($xcart_catalogs['customer']."/error_message.php?access_denied&id=38");
}

x_session_register('editor_mode');

$smarty->webmaster_mode = $editor_mode=='editor';
$smarty->assign("webmaster_mode", $editor_mode);

if (strpos($HTTP_USER_AGENT,'Opera')!==false)
	$user_agent="opera";
elseif (strpos($HTTP_USER_AGENT,'MSIE')!==false)
	$user_agent="ie";
else
	$user_agent="ns";

$smarty->assign("user_agent", $user_agent);

#
# Used from get_languages to convert "lng" smarty variable.
# Replaces each variable "value" with "<span ...>value</span>"
# except some listed in "if" statement (see below). Add variables
# which could appear in javascript code into this "if".
#
function func_webmaster_convert_labels (&$lang) {
	global $user_agent;
	global $smarty;
	if (is_array($lang)) {
		$lang_copy = array();
		foreach ($lang as $name=>$val) {
			$lang_copy[$name] = addcslashes($val, "\0..\37\\");
			$lang_copy[$name] = htmlspecialchars($lang_copy[$name],ENT_QUOTES);
			$lang[$name] = func_webmaster_label($user_agent,$name,$val);
		}
		$smarty->assign("webmaster_lng", $lang_copy);
	}
}

function func_webmaster_label($user_agent,$label,$value) {
	// check for exceptions
	if ($label!='txt_gc_enter_mail_address' && $label!='txt_site_title' && !eregi('txt_subtitle.*', $label) && $label!='txt_cc_number_invalid' && $label!='txt_email_invalid' && $label!='txt_recipient_invalid' && $label!='txt_amount_invalid') {

		if ($user_agent=='ie' || $user_agent=='opera') {
			$value = "<span class='Lbl' id='$label' onmouseover=\"lmo('$label')\" onmouseout=\"lmu('$label')\" onclick=\"lmc('$label')\">$value</span>";
		} else if ($user_agent=='ns'){
			$value = "<span class='Lbl' id='$label' onmouseover=\"lmo('$label', event)\" onmouseout=\"lmu('$label', event)\" onclick=\"lmc('$label', event)\">$value</span>";
		}
	}

	return $value;
}

?>
