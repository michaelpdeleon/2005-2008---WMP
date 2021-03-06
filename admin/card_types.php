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
# $Id: card_types.php,v 1.14 2006/01/11 06:55:57 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_edit_cc_types"), "");

function func_local_update_card_types () {
	global $config, $sql_tbl;

	db_query ("UPDATE $sql_tbl[config] SET value='".addslashes(serialize($config["card_types"]))."' WHERE name='card_types'");
}

$test = func_query_first ("SELECT * FROM $sql_tbl[config] WHERE name='card_types'");
if (!$test) {
	db_query ("INSERT INTO $sql_tbl[config] (name, value) VALUES ('card_types', '')");
}


if ($REQUEST_METHOD == "POST") {

	if (isset($new_name))
		$new_name = trim($new_name);
	if (isset($code))
		$code = trim($code);

	if ($mode == "add" && !empty($code) && !empty($new_name)) {
	#
	# Add a new credit card type
	#
		$config["card_types"][] = array ("code"=>stripslashes($code), "type"=>stripslashes($new_name), "cvv2"=>(!empty($new_cvv2) ? "1" : ""));

		func_local_update_card_types();

		$top_message["content"] = func_get_langvar_by_name("msg_adm_card_types_add");
	
	}
	elseif ($mode == "delete" and !empty($posted_data)) {
	#
	# Delete selected credit card types
	#
		if (is_array($posted_data) && is_array($config["card_types"])) {
			$deleted = false;
			$new_levels = array();
			foreach ($config["card_types"] as $key=>$value) {
				foreach ($posted_data as $k=>$v) {
					if ($value["code"] == stripslashes($v["code"]) && $value["type"] == stripslashes($v["old_name"])) {
						if (empty($v["to_delete"]))
							$new_levels[] = $value;
						else
							$deleted = true;
						break;
					}
				}
			}

			if ($deleted) {
				$config["card_types"] = $new_levels;
				func_local_update_card_types();
				$top_message["content"] = func_get_langvar_by_name("msg_adm_card_types_del");
			}
		}

		
	}
	elseif ($mode == "update" && !empty($posted_data)) {
	#
	# Update credit card types list
	#
		if (is_array($posted_data) && is_array($config["card_types"])) {
			$updated = false;
			foreach ($config["card_types"] as $key=>$value) {
				foreach ($posted_data as $k=>$v) {
				
					$v["new_name"] = trim($v["new_name"]);
					if (empty($v["new_name"]))
						continue;
					
					$need_to_update =
						($value["code"] == stripslashes($v["code"]) && 
						$value["type"] == stripslashes($v["old_name"]) && 
						($value["type"] != $v["new_name"] || 
						(empty($value["cvv2"]) + empty($v["new_cvv2"]))));
					
					if ($need_to_update) {
						$config["card_types"][$key]["type"] = stripslashes($v["new_name"]);
						$config["card_types"][$key]["cvv2"] = (!empty($v["new_cvv2"]) ? "1" : "");
						$updated = true;
						break;
					}
				}
			}

			if ($updated) {
				func_local_update_card_types();
				$top_message["content"] = func_get_langvar_by_name("msg_adm_card_types_upd");
			}
		}
	}
	
	func_header_location("card_types.php");

}

$smarty->assign("main","card_types");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>


