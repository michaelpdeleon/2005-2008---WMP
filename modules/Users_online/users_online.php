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
# $Id: users_online.php,v 1.5 2006/02/10 14:27:31 svowl Exp $
#
# Users online
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if($current_area == 'C' || empty($login))
	$where_condition = " AND usertype = 'C'";
elseif($current_area == 'P' && !$active_modules['Simple_Mode'])
	$where_condition = " AND usertype IN ('C', 'P')";
elseif($current_area == 'B')
	$where_condition = " AND usertype IN ('C', 'B')";
	
$users_online = func_query("SELECT usertype, COUNT(*) as count, is_registered FROM $sql_tbl[users_online] WHERE IF(usertype = 'C', 'Y', is_registered) = 'Y' ".$where_condition." GROUP BY usertype, IF(usertype = 'C', is_registered, '')");
if($active_modules['Simple_Mode'] && ($current_area == 'P' || $current_area == 'A') && $users_online) {
	$count = 0;
	foreach($users_online as $k => $v) {
		if($v['usertype'] == 'P' || $v['usertype'] == 'A') {
			$count += $v['count'];
			unset($users_online[$k]);
		}
	}
	$users_online[] = array("usertype" => "A", "count" => $count);
}

$smarty->assign("users_online", $users_online);
?>
