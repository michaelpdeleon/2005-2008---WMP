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
# $Id: product_clone.php,v 1.39.2.3 2006/07/31 06:38:44 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('category');

#
# This is the array of tables that should be affected by clone procedure
#
$tables_array = array(
	array("table"=>"images_T","key_field"=>"id"),
	array("table"=>"images_P","key_field"=>"id"),
	array("table"=>"images_D","key_field"=>"id"),
	array("table"=>"delivery","key_field"=>"productid"),
	array("table"=>"product_links","key_field"=>"productid1"),
	array("table"=>"products_lng","key_field"=>"productid"),
	array("table"=>"subscriptions","key_field"=>"productid"),
	array("table"=>"products_categories","key_field"=>"productid"),
	array("table"=>"product_taxes","key_field"=>"productid"),
	array("table"=>"product_memberships","key_field"=>"productid")
);

if ($active_modules["Product_Configurator"]) {
	$tables_array[] = array("table"=>"pconf_products_classes","key_field"=>"productid");
	$tables_array[] = array("table"=>"pconf_wizards","key_field"=>"productid");
	$tables_array[] = array("table"=>"languages_alt","key_field"=>"name");
}

if ($active_modules["Product_Options"]) {
	$tables_array[] = array("table"=>"product_options_js","key_field"=>"productid");
}

if ($active_modules["Extra_Fields"]) {
	$tables_array[] = array("table"=>"extra_field_values","key_field"=>"productid");
}

if ($active_modules["Feature_Comparison"]) {
	$tables_array[] = array("table"=>"product_features","key_field"=>"productid");
	$tables_array[] = array("table"=>"product_foptions","key_field"=>"productid");
}

#
# Make copying data for specified product in another table
#
function func_copy_tables($table, $key_field, $productid, $new_productid) {
	global $sql_tbl;
	global $xcart_dir;
	global $language_var_names;

	if (empty($table))
		return false;

	$table_name = $sql_tbl[$table];

	$error_string = "";

	$res = db_query("SHOW COLUMNS FROM $table_name");
	while ($row = db_fetch_array($res)) {
		$name  = $row['Field'];
		$flags = $row['Extra'];
		$fields[$name] = $flags;
	}

	db_free_result($res);

	$result = func_query("SELECT * FROM $table_name WHERE $key_field='$productid'");

	if (!$result)
		return false;

	foreach ($result as $key=>$row) {
		if (!$row) continue;

		$str = "INSERT INTO $table_name (";
		foreach ($row as $k=>$v) {
			if (is_numeric($k)) continue;

			if ($k==$key_field || !eregi("auto_increment",$fields[$k]))
				$str .= "$k,";
		}

		$str = ereg_replace(",$", ") VALUES (", $str);
		foreach ($row as $k=>$v) {
			if (is_numeric($k)) continue;

			if ($k==$key_field || !eregi("auto_increment",$fields[$k])) {
				if ($k == $key_field) {
					if (is_numeric($new_productid))
						$str .= "$new_productid,";
					else
						$str .= "'".addslashes($new_productid)."',";
				}
				else {
					$str .= "'".addslashes($v)."',";
				}
			}
		}

		$str = preg_replace("/,$/Ss", ")", $str);
		db_query($str);

		if (db_affected_rows() < 0) {
			$error_string .= "$str<br />";
		}
		else {
			#
			# Create additional records in the linked tables
			#
			if ($table == "pconf_products_classes") {
				$new_classid = db_insert_id();
				$old_classid = $row["classid"];
				func_copy_tables("pconf_class_specifications", "classid", $old_classid, $new_classid);
				func_copy_tables("pconf_class_requirements", "classid", $old_classid, $new_classid);
			}

			if ($table == "pconf_wizards") {
				$new_stepid = db_insert_id();
				$old_stepid = $row["stepid"];

				$old_stepname = $language_var_names["step_name"].$old_stepid;
				$old_stepdescr = $language_var_names["step_descr"].$old_stepid;
				$new_stepname = $language_var_names["step_name"].$new_stepid;
				$new_stepdescr = $language_var_names["step_descr"].$new_stepid;

				db_query("UPDATE $sql_tbl[pconf_wizards] SET step_name='$new_stepname', step_descr='$new_stepdescr' WHERE stepid='$new_stepid'");
				func_copy_tables("languages_alt", "name", $old_stepname, $new_stepname);
				func_copy_tables("languages_alt", "name", $old_stepdescr, $new_stepdescr);
				func_copy_tables("pconf_slots", "stepid", $old_stepid, $new_stepid);
			}

			if ($table == "pconf_slots") {
				$new_slotid = db_insert_id();
				$old_slotid = $row["slotid"];

				$old_slotname = $language_var_names["slot_name"].$old_slotid;
				$old_slotdescr = $language_var_names["slot_descr"].$old_slotid;
				$new_slotname = $language_var_names["slot_name"].$new_slotid;
				$new_slotdescr = $language_var_names["slot_descr"].$new_slotid;

				db_query("UPDATE $sql_tbl[pconf_slots] SET slot_name='$new_slotname', slot_descr='$new_slotdescr' WHERE slotid='$new_slotid'");
				func_copy_tables("languages_alt", "name", $old_slotname, $new_slotname);
				func_copy_tables("languages_alt", "name", $old_slotdescr, $new_slotdescr);
				func_copy_tables("pconf_slot_rules", "slotid", $old_slotid, $new_slotid);
				func_copy_tables("pconf_slot_markups", "slotid", $old_slotid, $new_slotid);
			}
		}
	}

	return $error_string;
}


#
# Get product info
#
if ($productid!="") {
	$product_info = func_query_first("SELECT * FROM $sql_tbl[products] WHERE productid='$productid'");
}

if ($product_info["provider"]==$login || $single_mode || $current_area == "A") {

	$c_login = ($current_area == "A" ? $product_info["provider"] : $login);
	#
	# Get unique productcode (SKU) value
	#
	$max_productcode = func_query_first_cell("SELECT MAX(CAST(SUBSTRING(productcode, 4) AS UNSIGNED)) FROM $sql_tbl[products] WHERE productcode REGEXP '^SKU[0-9]+$'");
	$max_productcode = empty($max_productcode) ? 1000 : intval($max_productcode);

	while (1) {
		$productcode = "SKU".rand($max_productcode, $max_productcode+99);

		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode='$productcode'") == 0)
			break;
	}

	#
	# Create a new product
	#
	$query = "INSERT INTO $sql_tbl[products] (provider, add_date, productcode) VALUES ('$c_login', '".time()."', '$productcode')";
	db_query($query);
	$new_productid = db_insert_id();

	if (!empty($active_modules["Magnifier"])) {
		include $xcart_dir."/modules/Magnifier/clone.php";
	}

	if (db_affected_rows() < 0)
		$error_string = "$query<br />";

	if ($new_productid) {
		#
		# Update just created product by values from existing product
		#
		$query = "UPDATE $sql_tbl[products] SET ";
		foreach ($product_info as $k=>$v) {
			if (!is_numeric($k) && $k!="productid" && $k!="productcode" && $k!="provider" && $k!="add_date" && $k!="views_stats" && $k!="del_stats" && $k!="sales_stats" ) {
				if ($k=="product") $v="$v (CLON)";

				$query .= "`$k`='".addslashes($v)."', ";
			}
		}

		$query = ereg_replace(", $", " WHERE productid='$new_productid'", $query);

		db_query($query);

		if (db_affected_rows() < 0)
			$error_string = "$query<br />";
		else {
			#
			# Update products counter for categories in which product is placed
			#
			$product_categories = func_query_column("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid = '$productid'");
			func_recalc_product_count(func_get_category_parents($product_categories));

			#
			# Copy product options
			#
			if ($active_modules['Product_Options']) {
				$hash = array();
				$classes = func_query("SELECT * FROM $sql_tbl[classes] WHERE productid = '$productid'");
				if (!empty($classes)) {
					foreach ($classes as $v) {
						$options = func_query("SELECT * FROM $sql_tbl[class_options] WHERE classid = '$v[classid]'");
						$old_classid = $v['classid'];
						unset($v['classid']);
						$v['productid'] = $new_productid;
						$v = func_addslashes($v);
						$classid = func_array2insert('classes', $v);
						if ($options) {
							foreach ($options as $o) {
								$old_optionid = $o['optionid'];
								unset($o['optionid']);
								$o['classid'] = $classid;
								$o = func_addslashes($o);
								$optionid = func_array2insert('class_options', $o);
								$hash[$old_optionid] = $optionid;
								func_copy_tables("product_options_lng", "optionid", $old_optionid, $optionid);
							}
						}

						func_copy_tables("class_lng", "classid", $old_classid, $classid);
					}
				}

				# Clone product option exceptions
				if (!empty($hash)) {
					$hash_ex = array();
					$exceptions = func_query("SELECT * FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", array_keys($hash))."')");
					if (!empty($exceptions)) {
						foreach ($exceptions as $v) {
							if (empty($hash[$v['optionid']]))
								continue;

							$v['optionid'] = $hash[$v['optionid']];
							if (empty($hash_ex[$v['exceptionid']]))
								$hash_ex[$v['exceptionid']] = func_query_first_cell("SELECT MAX(exceptionid) FROM $sql_tbl[product_options_ex]")+1;
							$v['exceptionid'] = $hash_ex[$v['exceptionid']];
							func_array2insert('product_options_ex', $v);
						}
					}

					unset($hash_ex);
				}

				# Clone product option variants
				$variants = db_query("SELECT * FROM $sql_tbl[variants] WHERE productid = '$productid' ORDER BY variantid");
				if ($variants) {
					while ($v = db_fetch_array($variants)) {
						$old_variantid = $v['variantid'];
						$v['productid'] = $new_productid;
						unset($v['variantid']);
						$cnt = 0;
						while (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productcode = '".addslashes($v['productcode'].$cnt)."'") > 0) {
							$cnt++;
						}

						$v['productcode'] .= $cnt;
						$v = func_addslashes($v);
						$variantid = func_array2insert('variants', $v);

						# Add Variant items
						$items = func_query("SELECT optionid FROM $sql_tbl[variant_items] WHERE variantid = '$old_variantid'");
						if (!empty($items)) {
							foreach($items as $i) {
								if (isset($hash[$i['optionid']])) {
									db_query("INSERT INTO $sql_tbl[variant_items] (variantid, optionid) VALUES ('$variantid', '".$hash[$i['optionid']]."')");
								}
							}
						}

						# Add Variant prices
						$prices = func_query("SELECT * FROM $sql_tbl[pricing] WHERE variantid = '$old_variantid' AND productid = '$productid'");
						if ($prices) {
							foreach($prices as $p) {
								unset($p['priceid']);
								$p['variantid'] = $variantid;
								$p['productid'] = $new_productid;
								func_array2insert("pricing", $p);
							}
						}

						# Add Variant thumbnails & variant images
						$error_string .= func_copy_tables("images_W", "id", $old_variantid, $variantid);
					}
					db_free_result($variants);
				}
			}

			#
			# Copy records that are linked with this product in the other tables
			#
			foreach ($tables_array as $k=>$v) {
				$error_string .= func_copy_tables($v["table"], $v["key_field"], $productid, $new_productid);
			}

			# Clone prices
			$prices = func_query("SELECT * FROM $sql_tbl[pricing] WHERE productid = '$productid' AND variantid = 0");
			if (!empty($prices)) {
				foreach ($prices as $v) {
					unset($v['priceid']);
					$v['productid'] = $new_productid;
					func_array2insert("pricing", $v);
				}
			}

		}

		# Rebuild product's cache tables
		func_build_quick_flags($new_productid);
		func_build_quick_prices($new_productid);

		if (!empty($active_modules['Fancy_Categories'])) {
			$cats = func_fc_check_rebuild($productid, "P");
			if (!empty($cats))
				func_fc_build_categories($cats, 10);
		}

		if (empty($error_string))
			func_header_location("product_modify.php?productid=$new_productid");
	}

}

#
# Display error message if operation failed
#
echo "<b>ERROR: Product #$new_productid has not been created!</b><br />$error_string";
exit();

?>
