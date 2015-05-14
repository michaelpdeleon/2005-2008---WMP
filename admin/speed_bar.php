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
# $Id: speed_bar.php,v 1.16.2.2 2006/07/05 07:24:10 svowl Exp $
#

define("IS_MULTILANGUAGE", 1);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('backoffice');

if (isset($config["speed_bar"]))
	$speed_bar = unserialize($config["speed_bar"]);

$location[] = array(func_get_langvar_by_name("lbl_speed_bar_management"), "");

if (empty($speed_bar)) {
	$speed_bar = array();
}
else {
	foreach($speed_bar as $k => $v) {
		$speed_bar[$k] = func_array_map("stripslashes", $v);
	}
}

if ($REQUEST_METHOD == "POST") {
	require $xcart_dir."/include/safe_mode.php";

	if ($mode == "delete" && !empty($id)) {
		#
		# Delete link from Speed Bar
		#
		foreach ($speed_bar as $k=>$v) {
			if ($v["id"] == $id) {
				unset($speed_bar[$k]);
			}
		}
	}
	elseif ($mode == "update") {
		#
		# Update Speed Bar
		#
		if (is_array($posted_data) && !empty($posted_data)) {
			foreach ($posted_data as $k=>$v) {
				$v["orderby"] = abs(intval($v["orderby"]));
				$v["active"] = ($v["active"] == "Y" ? "Y" : "N");
				$v["link"] = (empty($v["link"]) ? "#" : $v["link"]);
				func_languages_alt_insert("speed_bar_".$v['id'], $v['title'], $shop_language);
				if ($shop_language != $config['default_admin_language']) {
					foreach ($speed_bar as $v2) {
						if ($v2['id'] == $v['id']) {
							$v['title'] = $v2['title'];
							break;
						}
					}
				}

				$posted_data[$k] = $v;
			}

			$speed_bar = $posted_data;
		}

		if (!empty($new_title)) {
			#
			# Generate unique id for new link
			#
			$found = true;
			$idx = 1;
			while ($found) {
				$found = false;
				foreach ($speed_bar as $k=>$v) {
					if ($v["id"] == $idx++) {
						$found = true;
						break;
					}
				}
			}

			func_languages_alt_insert("speed_bar_".$idx, $new_title, $shop_language);
			$speed_bar[] = array(
				"id"      => $idx,
				"orderby" => abs(intval($new_orderby)),
				"title"   => stripslashes($new_title),
				"link"    => (empty($new_link) ? "#" : $new_link),
				"active"  => ($new_active == "Y" ? "Y" : "N"));
		}
	}
	
	if (is_array($speed_bar)) {
		function mysortfunc($a,$b) {
			return ($a["orderby"] >= $b["orderby"]);
		}

		usort ($speed_bar, "mysortfunc");
	}

	db_query("REPLACE INTO $sql_tbl[config] (name,value) VALUES ('speed_bar','".addslashes(serialize($speed_bar))."')");

	func_header_location("speed_bar.php");
}

foreach ($speed_bar as $k => $v) {
	$tmp = func_get_languages_alt("speed_bar_".$v['id']);
	if (!empty($tmp))
		$speed_bar[$k]['title'] = $tmp;
}

$smarty->assign("speed_bar", $speed_bar);

$smarty->assign("main","speed_bar");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
