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
# $Id: cc_smartpag_74.php,v 1.9 2006/01/11 06:56:23 mclap Exp $
#

require("../top.inc.php");

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

require $xcart_dir.DIR_ADMIN."/auth.php";

$module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_smartpag.php'");

?>
<html>
<body>
<table>
<form action="https://www.smartpag.com.br/reenvioPost.asp" method=POST>
<input type=hidden name=5DED746B8F924F2E value="<?php echo $module_params["param01"]; ?>">
<tr><td>Input OrderID with prefix: </td><td><input type=text name=91D4C3128BF7DA7F value="<?php echo $module_params["param02"].$ord; ?>"> </td></tr>
<tr><td>URLPOSTLOJA: </td><td><input type=text name=URLPOSTLOJA value=""></td></tr>
<tr><td>TIPO: </td><td><select name=TIPO><option value=2>Post Off-line<option value=3>Payment Post</select></td></tr>
<tr><td>NUM_PARCELA: </td><td><input type=text name=NUM_PARCELA value="0"> [In the case of TIPO being 2 th parameter NUM_PARCELA should be sent with the value 0.]</td></tr>
<tr><td colspan=2><input type=submit value="Set URLPOSTLOJA"></td></tr>
</td></tr>
</form>
</table>
</body>
</html>

<?php
exit;

?>
