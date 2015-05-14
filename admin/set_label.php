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
# $Id: set_label.php,v 1.19.2.2 2006/07/31 06:24:54 svowl Exp $
#

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);
$trusted_post_variables = array("val");

require "./auth.php";
require $xcart_dir."/include/security.php";

unset($editor_mode);
x_session_register("editor_mode");

if ($REQUEST_METHOD == 'POST' && $editor_mode == 'editor' && !$admin_safe_mode) {
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE name='$name' AND code='$lang'") > 0) {
		func_array2update("languages", array("value" => $val), "code='$lang' AND name='$name'");
	} else {
		$data = func_query_first("SELECT topic, name FROM $sql_tbl[languages] WHERE name='$name' LIMIT 1");
		if (!empty($data)) {
			$data['code'] = $lang;
			$data['value'] = $val;
			func_array2insert("languages", $data, true);
		}
	}
}

?>
<html>
<body onload="javascript: wndClose();">
<script type="text/javascript">
<!--
function wndClose() {
	if (window.opener) {
		if (window.screenLeft && window.screenTop) {
			window.opener.defaultLabelWindowX = window.screenLeft;
			window.opener.defaultLabelWindowY = window.screenTop;
		} else {
			window.opener.defaultLabelWindowX = window.screenX;
			window.opener.defaultLabelWindowY = window.screenY;
		}
	}
	window.close();
}
-->
</script>
</body>
</html>
