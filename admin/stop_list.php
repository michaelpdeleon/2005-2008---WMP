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
# $Id: stop_list.php,v 1.8 2006/01/11 06:55:58 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";
 
if(!$active_modules['Stop_List'])
    func_header_location ("error_message.php?access_denied&id=28");

$location[] = array(func_get_langvar_by_name("lbl_stop_list"), "");

$dialog_tools_data["left"][] = array("link" => "stop_list.php", "title" => func_get_langvar_by_name("lbl_stop_list"));
$dialog_tools_data["left"][] = array("link" => "stop_list.php?mode=add", "title" => func_get_langvar_by_name("lbl_add_ip_address"));

#
# Add/Modify IP
#
if ($mode == 'add' && $octet && @count($octet) == 4) {
	$flag_int = true;
	foreach ($octet as $k => $v) {
		if ($v != '*')
			$octet[$k] = (int)$v;
		if ($octet[$k] > 255 || $octet[$k] < 0)
			$flag_int = false;
	}
	if ($octet[0] == 0 || !$flag_int) {
		$top_message["content"] = func_get_langvar_by_name("txt_stop_list_warning");
		$top_message["type"] = 'E';
		func_header_location("stop_list.php?mode=add");
	}
	$ip = implode(".", $octet);
	if (empty($ipid)) {
		func_add_ip_to_slist($ip, "M", $ip_type);
	} else {
		foreach ($octet as $k => $v) {
			if ($v == "*")
				$octet[$k] = -1;
		}
		$data_query = array(
			"octet1" => $octet[0],
			"octet2" => $octet[1],
			"octet3" => $octet[2],
			"octet4" => $octet[3],
			"ip" => $ip,
			"ip_type" => $ip_type
		);
		func_array2update("stop_list", $data_query, "ipid = '$ipid'");
	}

#
# Delete IP
#
} elseif ($mode == 'delete' && $to_delete && is_array($to_delete)) {
	db_query("DELETE FROM $sql_tbl[stop_list] WHERE ip IN ('".implode("','", array_keys($to_delete))."')");
	$top_message["content"] = func_get_langvar_by_name("msg_adm_ip_address_del");
}

if (!empty($mode) && $REQUEST_METHOD == "POST") {
	func_header_location("stop_list.php");
}

$stop_list = func_query("SELECT * FROM $sql_tbl[stop_list] ORDER BY ip");
if (!empty($stop_list)) {
	foreach ($stop_list as $k => $v) {
		if ($v['reason'] == 'M') {
			$stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_added_by_admin");
		} elseif (in_array($v['reason'], array('T','P','S','F','A'))) {
			$stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_slist_reason_".strtolower($v['reason']));
		} else {
			$stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_unknown");
		}
	}
	$smarty->assign("stop_list", $stop_list);
}

if ($mode == "add" && !empty($ipid)) {
	$ip = func_query_first("SELECT * FROM $sql_tbl[stop_list] WHERE ipid = '$ipid'");
	if (!empty($ip)) {
		$location[count($location)-1][1] = "stop_list.php";
		$location[] = array($ip['ip'], "");
	}
} else {
	$ip = array("octet1" => 0,"octet2" => 0,"octet3" => 0,"octet4" => 0);
}

$smarty->assign("main", "stop_list");
$smarty->assign("mode", $mode);
$smarty->assign("ip", $ip);

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
