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
# $Id: product_variants.php,v 1.24.2.2 2006/06/02 08:29:20 max Exp $
#
# Product variants management
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('backoffice','product');

function func_get_same_variant($variantid, $productid) {
	global $sql_tbl;

	$vid = false;
	$name_where = func_query_hash("SELECT $sql_tbl[classes].class, $sql_tbl[class_options].option_name FROM $sql_tbl[variant_items], $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid = $sql_tbl[variant_items].optionid AND $sql_tbl[variant_items].variantid = '$variantid'", "class", true, true);
	foreach ($name_where as $cn => $opts) {
		$name_where[$cn] = "($sql_tbl[classes].class = '".addslashes($cn)."' AND $sql_tbl[class_options].option_name IN ('".implode("','", func_addslashes($opts))."'))";
	}
	$name_where = " AND ".implode(" OR ", $name_where);

	$cnt = func_query_first_cell("SELECT COUNT($sql_tbl[variant_items].optionid) as cnt FROM $sql_tbl[variants], $sql_tbl[variant_items] WHERE $sql_tbl[variants].variantid = $sql_tbl[variant_items].variantid AND $sql_tbl[variants].productid = '$productid' GROUP BY $sql_tbl[variants].variantid ORDER BY cnt DESC");
	if (!empty($cnt))
		$vid = func_query_first_cell("SELECT $sql_tbl[variant_items].variantid, COUNT($sql_tbl[variant_items].optionid) as cnt FROM $sql_tbl[classes], $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid".$name_where." GROUP BY $sql_tbl[variant_items].variantid HAVING cnt = '$cnt'");

	return $vid;
}

x_session_register("search_variants");

#
# Product variants update
#
$refresh = $rebuild_quick = false;
if ($mode == 'product_variants_modify' && $vs && $tstamp && func_check_image_posted($file_upload_data, "W", $tstamp) && ($image_perms = func_check_image_perms("W")) !== true) {
	$top_message = array(
		"content" => $image_perms['content'],
		"type" => "E"
	);
	$refresh = true;

} elseif ($mode == 'product_variants_modify' && $vs) {

#Start addition by Michael Gunin, X-Cart technician 10.01.06
/*    $j_temp = 0;
     foreach($vs as $k => $v) {
	    if ($j_temp == 0) {
		$t = $v[extra_field2];
        $query_extra1 = "UPDATE $sql_tbl[extra_field_values] SET value='$t' WHERE (productid='$productid')";
        // echo($query_extra1."<br>");
		db_query($query_extra1);
		// echo(mysql_affected_rows()."<br>");
		$j_temp = 1;
		}
} */
#End addition by Michael Gunin, X-Cart technician 10.01.06


	# Update variants data
	foreach($vs as $k => $v) {
		$v['price'] = func_convert_number($v['price']);
		$v['weight'] = func_convert_number($v['weight']);
		$v['avail'] = func_convert_numeric($v['avail']);
		$query_data = array(
			"weight" => $v['weight'],
			"avail" => $v['avail'],
		# Start addition by Michael de Leon 09.20.06
			"extra_field1" => $v['extra_field1']
		# End addition by Michael de Leon 09.20.06
		);
		if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productcode = '$v[productcode]'"))
			$query_data['productcode'] = $v['productcode'];

		func_array2update("variants", $query_data, "variantid = '$k'");

		$priceid = func_query_first_cell("SELECT priceid FROM $sql_tbl[pricing] WHERE variantid = '$k' AND productid = '$productid' AND quantity = 1 AND membershipid = 0");
		$query_price_data = array(
			"price" => $v['price'],
			"variantid" => $k,
			"productid" => $productid,
			"quantity" => 1,
			"membershipid" => 0
		);

		if ($priceid) {
			func_array2update("pricing", $query_price_data, "priceid = '$priceid'");
		} else {
			func_array2insert("pricing", $query_price_data);
		}

		if (!$geid || $fields['variants'][$k] != 'Y')
			continue;

		# Update variants data (Group editing of products functionality)
		func_unset($query_data, "productcode");
		while ($pid = func_ge_each($geid, 1, $productid)) {
			$vid = func_get_same_variant($k, $pid);
			if (empty($vid))
				continue;

			func_array2update("variants", $query_data, "variantid = '$vid'");
			$query_price_data['variantid'] = $vid;
			$query_price_data['productid'] = $pid;
			$priceid = func_query_first_cell("SELECT priceid FROM $sql_tbl[pricing] WHERE variantid = '$vid' AND productid = '$pid' AND quantity = 1 AND membershipid = 0");
			if ($priceid) {
				func_array2update("pricing", $query_price_data, "priceid = '$priceid'");
			} else {
				func_array2insert("pricing", $query_price_data);
			}

			if ($def_variant == $k)
				func_array2update("variants", array("def = IF(variantid = '$vid', 'Y', '')"), "productid = '$pid'");
		}
	}

	if (!empty($def_variant))
		func_array2update("variants", array("def = IF(variantid = '$def_variant', 'Y', '')"), "productid = '$productid'");

	# Update wholesale prices
	if (!empty($vids) && (!empty($wprices) || !empty($new_wprice)) && !empty($active_modules['Wholesale_Trading'])) {
		foreach ($vids as $k => $v) {
			if (!empty($wprices)) {
				foreach ($wprices as $vk => $vw) {
					$vw['price'] = func_convert_number($vw['price']);
					$vw['quantity'] = abs(func_convert_numeric($vw['quantity']));
					if ($vw['quantity'] > 1 || $vw['membershipid'] != 0) {
						$_priceid = func_query_first_cell("SELECT priceid FROM $sql_tbl[pricing] WHERE productid = '$productid' AND quantity = '$vw[quantity]' AND membershipid = '$vw[membershipid]' AND variantid = '$k'");
						if ($_priceid) {
							func_array2update("pricing", array("price" => $vw['price']), "priceid = '$_priceid'");
						} else {
							$vw['productid'] = $productid;
							$vw['variantid'] = $k;
							func_array2insert("pricing", $vw);
						}
					} else {
						unset($wprices[$vk]);
					}
				}
			}

			# Add new wholesale price
			if (!empty($new_wprice)) {
				foreach ($new_wprice['quantity'] as $wpk => $wpv) {
					if ($wpv == 1 && $new_wprice['membershipid'][$wpk] == 0) {
						func_unset($new_wprice['quantity'], $wpk);
						func_unset($new_wprice['membershipid'], $wpk);
						continue;
					}
					$data = array(
						"productid" => $productid,
						"variantid" => $k,
						"quantity" => abs(func_convert_numeric($wpv)),
						"membershipid" => $new_wprice['membershipid'][$wpk],
						"price" => func_convert_number($new_wprice['price'][$wpk])
					);
					func_array2insert("pricing", $data);
				}
			}

			# Group editing of products functionality
			if ($geid && $fields['wp_variant'] == 'Y') {
				while($pid = func_ge_each($geid, 1, $productid)) {
					$vid = func_get_same_variant($k, $pid);
					if (empty($vid))
						continue;

					# Update wholesale prices (Group editing of products functionality)
					if (!empty($wprices)) {
						foreach ($wprices as $vw) {
							$_priceid = func_query_first_cell("SELECT priceid FROM $sql_tbl[pricing] WHERE productid = '$pid' AND quantity = '$vw[quantity]' AND membershipid = '$vw[membershipid]' AND variantid = '$vid'");
							if ($_priceid) {
								func_array2update("pricing", array("price" => $vw['price']), "priceid = '$_priceid'");
							} else {
								$vw['productid'] = $pid;
								$vw['variantid'] = $vid;
								func_array2insert("pricing", $vw);
							}
						}
					}

					# Add new wholesale price (Group editing of products functionality)
					if (!empty($new_wprice)) {
						foreach ($new_wprice['quantity'] as $wpk => $wpv) {
							$data = array(
								"productid" => $pid,
								"variantid" => $vid,
								"quantity" => abs(intval($wpv)),
								"membershipid" => $new_wprice['membershipid'][$wpk],
								"price" => func_convert_number($new_wprice['price'][$wpk])
							);
							func_array2insert("pricing", $data);
						}
					}
				}
			}
		}
	}

	# Update images
	if (!empty($vids) && !empty($tstamp) && isset($file_upload_data['W'])) {
		if (func_check_image_posted($file_upload_data, "W", $tstamp)) {
			$vids = array_keys($vids);
			$vid = array_shift($vids);
			$imageid = func_save_image($file_upload_data, "W", $vid);
			if (!empty($vids)) {
				$res = func_query_first("SELECT * FROM $sql_tbl[images_W] WHERE imageid = '$imageid' LIMIT 1");
				if (!empty($res)) {
					unset($res['imageid']);
					$res = func_addslashes($res);
					foreach ($vids as $v) {
						$res['id'] = $v;
						func_delete_image($v, "W");
						func_array2insert("images_W", $res);
					}
				}
			}

			# Update images (Group editing of products functionality)
			if ($geid && !empty($fields['variants'])) {
				array_unshift($vids, $vid);
				$res = func_query_first("SELECT * FROM $sql_tbl[images_W] WHERE imageid = '$imageid' LIMIT 1");
				unset($res['imageid']);
				if (!empty($res)) {
					$res = func_addslashes($res);
					foreach ($vids as $v) {
						if ($fields['variants'][$v] != 'Y')
							continue;
						while($pid = func_ge_each($geid, 1, $productid)) {
							$res['id'] = func_get_same_variant($v, $pid);
							if (empty($res['id']))
								continue;
							func_delete_image($res['id'], "W");
							func_array2insert("images_W", $res);
						}
					}
				}
			}
		}
	}

	$top_message["content"] = func_get_langvar_by_name("msg_adm_product_variants_upd");
	$top_message["type"] = "I";
	$refresh = $rebuild_quick = true;

#
# Rebuild product variants
#
} elseif ($mode == 'product_variants_rebuild') {
	func_rebuild_variants($productid, true);
	$top_message["content"] = func_get_langvar_by_name("msg_adm_product_variants_rebuilded");
	$top_message["type"] = "I";
	$refresh = $rebuild_quick = true;

#
# Delete Wholesale price
#
} elseif ($mode == "delete_wprice" && (!empty($delete_wprice_quantity) || !empty($delete_wprice_membershipid)) && $section == "variants" && !empty($vids)) {
	$vids = array_keys($vids);

	db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND quantity = '$delete_wprice_quantity' AND membershipid = '$delete_wprice_membershipid' AND variantid IN ('".implode("','", $vids)."')");

	# Delete Wholesale price (Group editing of products functionality)
	if ($geid && $fields['wp_variant'] == 'Y') {
		foreach($vids as $v) {
			while ($pid = func_ge_each($geid, 1, $productid)) {
				$vid = func_get_same_variant($v, $pid);
				if (!empty($vid))
					db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$pid' AND quantity = '$delete_wprice_quantity' AND membershipid = '$delete_wprice_membershipid' AND variantid = '$vid'");
			}
		}
	}
	$refresh = true;

#
# Delete variants image
#
} elseif ($mode == "delete_image" && $section == "variants" && !empty($vids)) {
	foreach ($vids as $k => $v) {
		func_delete_image($k, "W");

		# Delete variants image (Group editing of products functionality)
		if ($geid && $fields['variants'][$k] == 'Y') {
			while ($pid = func_ge_each($geid, 1, $productid)) {
				$vid = func_get_same_variant($k, $pid);
				if (!empty($vid))
					func_delete_image($vid, "W");
			}
		}
	}
	$refresh = $rebuild_quick = true;

#
# Save search conditions
#
} elseif ($mode == "product_variants_search" && $section == "variants") {
	$search_variants[$productid] = empty($search) ? array() : $search;
	$refresh = true;
}

if ($rebuild_quick) {
	if ($geid) {
		while($pid = func_ge_each($geid, 100)) {
			func_build_quick_flags($pid);
			func_build_quick_prices($pid);
		}
	} else {
		func_build_quick_flags($productid);
		func_build_quick_prices($productid);
	}
}

if ($refresh) {
    func_refresh("variants");
}

#
# Assign the Smarty variables
#

# Get the product options list
$product_options = func_get_product_classes($productid);
if(!empty($product_options))
	$smarty->assign("product_options", $product_options);

$variants = func_get_product_variants($productid);
$svariants = $search_variants[$productid];
if ($svariants && !empty($variants)) {
	$tmp = current($variants);
	$cnt = count($tmp['options']);
	unset($tmp);

	foreach ($variants as $k => $v) {
		$local_cnt = 0;
		foreach ($svariants as $cid => $c) {
			foreach ($c as $oid) {
				if (isset($v['options'][$oid]) && $v['options'][$oid]['classid'] == $cid)
					$local_cnt++;
			}
		}

		if ($local_cnt != $cnt) {
			unset($variants[$k]);
		}
	}

} elseif (!is_array($svariants)) {
	$smarty->assign("is_search_all", 'Y');
}

if (!empty($variants)) {
	$smarty->assign("variants", $variants);

	# Check default variant
	foreach ($variants as $vid => $v) {
		if ($v['def'] == 'Y') {
			$vid_def = func_get_default_variantid($productid);
			if ($vid != $vid_def) {
				$smarty->assign("def_variant_failure", true);
			}
			break;
		}
	}

	if (!empty($variantid)) {
		foreach($variants as $k => $v) {
			if ($k == $variantid) {
				$variant = $v;
				break;
			}
		}

		if (!empty($variant))
			$smarty->assign("variant", $variant);
	}
}

$smarty->assign("memberships_keys", func_get_memberships("C", true));
$smarty->assign("search_variants", $svariants);
?>
