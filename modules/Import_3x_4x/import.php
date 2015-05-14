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
# $Id: import.php,v 1.1.2.8 2006/07/19 08:17:50 max Exp $
#

x_load('backoffice','files','image','category');

@set_time_limit(1800);

$location[] = array(func_get_langvar_by_name("lbl_import_products"), "");

x_session_register("import_3x_4x_saved");

$excluded_columns = array(
	"provider",
	"image_x",
	"image_y",
	"add_date",
	"rating",
	"sales_stats",
	"views_stats",
	"del_stats",
	"manufacturerid"
);

#
# Stop import when errors or warnings limit is reached
#
$import_warnings_max = 25;
$import_errors_max = 25;

$step_row = 500;

$max_line_size = 65536 * 3;
$extra_data_fields = array(
	'category',
	'categoryid1',
	'categoryid2',
	'categoryid3',
	'orderby',
	'price',
	'thumbnail',
	'applied_taxes',
	'vat',
	'apply_gst',
	'apply_pst'
);
if(!empty($active_modules['Manufacturers'])) {
	$extra_data_fields[] = 'manufacturer';
}
if(!empty($active_modules['Product_Options'])) {
	$extra_data_fields[] = 'product_options';
}
if(!empty($active_modules['Extra_Fields'])) {
	$extra_data_fields[] = 'extra_fields';
	$extra_data_fields[] = 'param00';
	$extra_data_fields[] = 'param01';
	$extra_data_fields[] = 'param02';
	$extra_data_fields[] = 'param03';
	$extra_data_fields[] = 'param04';
	$extra_data_fields[] = 'param05';
	$extra_data_fields[] = 'param06';
	$extra_data_fields[] = 'param07';
	$extra_data_fields[] = 'param08';
	$extra_data_fields[] = 'param09';
}
if(!empty($active_modules['Feature_Comparison'])) {
	$extra_data_fields[] = 'feature_type';
	$extra_data_fields[] = 'feature_values';
}

$tmp_provider_condition = $single_mode ? "" : " AND provider='$login'";

#
# Obtain columns from table products
#
$columns = func_query_column("SHOW COLUMNS FROM $sql_tbl[products]");
$imported_columns = array_diff($columns, $excluded_columns);

if (empty($import_layout)) {
	$import_layout = $config['import_layout'];

	if (empty($import_layout)) {
		$fields_layout = func_array_merge($imported_columns, $extra_data_fields);
		$idx = 0;
		foreach($fields_layout as $val) {
			if (!in_array($val, $excluded_columns))
				$import_layout[] = $val."=".$idx++;
		}
		$import_layout = implode(",", $import_layout);
		unset($fields_layout);

		func_array2insert("config", array("name" => "import_layout", "value" => addslashes($import_layout)), true);
	}
}

if ($mode == "layout") {

	# Save layout
	$vals = array();

	$index = 0;
	if (is_array($import_columns)) {
		foreach ($import_columns as $val) {
			if (!empty($val))
				$vals[] = $val."=".$index++;
		}
	}

	$import_layout = implode (",", $vals);
	func_array2insert("config", array("name" => "import_layout", "value" => addslashes($import_layout)), true);

	func_header_location("import_3x_4x.php?import_layout=".urlencode($import_layout));
}

$layout = array();

if (!empty($import_layout)) {
	$lay = explode(",", $import_layout);
	foreach ($lay as $v) {
		list($value, $key) = explode("=", $v);
		$layout[$key] = $value;
	}
	unset($lay);
}

#
# Get content of XML tag
#
function func_get_xml_tag($data, $tag) {
    $tag = preg_quote($tag, "/");
    if (preg_match("/<$tag>(.*)<\/$tag>/USis", $data, $preg))
        return $preg[1];
    return false;
}   

#
# Remove XML tag
#    
function func_remove_xml_tag($data, $tag) {
	$tag = preg_quote($tag, "/");
	return preg_replace("/<$tag>.*<\/$tag>/USis", "", $data, 1);
}

#
# Check XML tag
#
function func_check_xml_tag($data, $tag, $inner_tag = true, $flag = false) {
	if (stristr($data, "<".$tag.">") === false && stristr($data, "</".$tag.">") === false) {
		return !$flag;

	} elseif (stristr($data, "<".$tag.">") === false || stristr($data, "</".$tag.">") === false) {
		return false;
	}

	$res = func_get_xml_tag($data, $tag);
	if ($res !== false) {
		if ($inner_tag === true) {
			return (strip_tags($res) == $res);

		} elseif (stristr($res, "<".$tag.">") !== false) {
			return false;
		}
	}

	return $res;
}
#
# This function parses product options line and insert it into the
# 'product_options' table.
# Options line example:
/*
<Class>
	<Name></Name> 
	<Text></Text>
	<Type></Type>
	<Avail></Avail>
	<OrderBy></OrderBy>
	<Options>
		<Option>
			<ID></ID>
			<Name></Name>
			<PriceModifier></PriceModifier>
			<ModifierType></ModifierType>
		</Option>
		...
	</Options>
 </Class>
...
<Exception>
	<ID></ID>
	...
</Exception>
...
<JScode></JScode>
<Variant>
	<SKU></SKU>
	<Avail></Avail>
	<Weight></Weight>
	<Price></Price>
	<Items>
		<ID></ID>
		...
	</Items>
</Variant>
...
*/
function func_create_product_options($productid, $string) {
    global $sql_tbl;

	$hash = array();
	while (preg_match("/<Class>(.+)<\/Class>/USis", $string, $preg)) {
		$class = array();

		$class['class'] = func_get_xml_tag($preg[1], "Name");
		if ($class['class'] === false) {
			$string = func_remove_xml_tag($string, "Class");
			continue;
		}

		$class['classtext'] = func_get_xml_tag($preg[1], "Text");
		if ($class['classtext'] === false)
			$class['classtext'] = $class['class'];

		$class['avail'] = func_get_xml_tag($preg[1], "Avail");
		$class['is_modifier'] = func_get_xml_tag($preg[1], "Type");
		$class['orderby'] = func_get_xml_tag($preg[1], "OrderBy");
		$class['productid'] = $productid;

		$options_text = func_get_xml_tag($preg[1], "Options");
		$options = array();

		if ($options_text) {
			while (preg_match("/<Option>(.+)<\/Option>/USis", $options_text, $preg_o)) {
				$option = array();

				$option['option_name'] = func_get_xml_tag($preg_o[1], "Name");

				$option['optionid'] = func_get_xml_tag($preg_o[1], "ID");
				if ($option['optionid'] === false || $option['option_name'] === false) {
					$options_text = func_remove_xml_tag($options_text, "Option");
					continue;
				}

				$option['avail'] = func_get_xml_tag($preg_o[1], "Avail");
				$option['orderby'] = func_get_xml_tag($preg_o[1], "OrderBy");
				$option['price_modifier'] = func_get_xml_tag($preg_o[1], "PriceModifier");
				$option['modifier_type'] = func_get_xml_tag($preg_o[1], "ModifierType");

				foreach ($option as $k => $v) {
					if ($v === false)
						unset($option[$k]);
				}
				$option = func_array_map("addslashes",$option);
				$options[$option['optionid']] = $option;
				$options_text = func_remove_xml_tag($options_text, "Option");
			}
		}

		foreach($class as $k => $v) {
			if ($v === false)
				unset($class[$k]);
		}
				
		$class = func_array_map("addslashes", $class);
		$classid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$productid' AND class = '$class[class]'");
		if ($classid) {
			func_array2update("classes", $class, "classid = '$classid'");

		} else {
			$classid = func_array2insert("classes", $class);
		}

		if ($classid && $options) {
			foreach($options as $k => $v) {
				$v['classid'] = $classid;
				$v['optionid'] = func_query_first_cell("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$classid' AND option_name = '$v[option_name]'");
				if ($v['optionid'] > 0) {
					func_array2update("class_options", $v, "optionid = '$v[optionid]'");

				} else {
					unset($v['optionid']);
					$v['optionid'] = func_array2insert("class_options", $v);
				}
				$hash[$k] = $v['optionid'];
			}
		}

		$string = func_remove_xml_tag($string, "Class");
	}

	while(preg_match("/<Exception>(.+)<\/Exception>/USs", $string, $preg)) { 
		if (preg_match_all("/<ID>\s*(\d+)\s*<\/ID>/USs", $preg[1], $ppreg)) {
			$count = func_query_first_cell("SELECT COUNT(*) as count FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $ppreg[1])."') GROUP BY exceptionid ORDER BY count desc");

			if ($count != count($ppreg[1])) {
				$exceptionid = func_query_first_cell("SELECT MAX(exceptionid) FROM $sql_tbl[product_options_ex]")+1;
				$flag = true;
				foreach($ppreg[1] as $v)
					if (!isset($hash[$v])) {
						$flag = false;
						break;
					}

				if (!$flag)
					continue;

				foreach($ppreg[1] as $v) {
					func_array2insert("product_options_ex", array("exceptionid" => $exceptionid, "optionid" => $hash[$v]));
				}
			}
		}
		$string = func_remove_xml_tag($string, "Exception");
	}

	$last_sku = 1;
	while(preg_match("/<Variant>(.+)<\/Variant>/USs", $string, $preg)) {
    	$variant = array();
	    $variant['productcode'] = func_get_xml_tag($preg[1], "SKU");
    	$variant['avail'] = func_get_xml_tag($preg[1], "Avail");
	    $variant['weight'] = func_get_xml_tag($preg[1], "Weight");
		if ($variant['productcode'] === false || $variant['avail'] === false || $variant['weight'] === false) {
			$product = func_query_first("SELECT * FROM $sql_tbl[products] WHERE productid = '$productid'");
			if ($variant['productcode'] === false)
				$variant['productcode'] = $product['productcode'];
			if ($variant['avail'] === false)
				$variant['avail'] = $product['avail'];
			if ($variant['weight'] === false)
				$variant['weight'] = $product['weight'];
			unset($product);
		}
		$sku = $variant['productcode'];
		while(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid != '$productid' AND productcode = '".addslashes($sku)."'") > 0) {
			$sku = $variant['productcode'].$last_sku++;
		}
		$variant['productcode'] = $sku;

		$variant = func_array_map("addslashes", $variant);
   		$price = func_get_xml_tag($preg[1], "Price");
		if ($price === false)
			$price = func_query_first_cell("SELECT MIN(price) FROM $sql_tbl[pricing] WHERE productid = '$productid' AND quantity = 1 AND membership = ''");

	    $option_text = func_get_xml_tag($preg[1], "Items");
		if ($option_text && preg_match_all("/<ID>\s*(\d+)\s*<\/ID>/USs", $option_text, $ppreg)) {
			$options = $ppreg[1];
		}

		$variant['productid'] = $productid;
		
		if ($options) {
			$flag = true;
			foreach($options as $v) {
				if (!@isset($hash[$v])) {
					$flag = false;
					break;
				}
			}

			if ($flag) {
				$variantid = func_query_first_cell("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$productid' AND productcode = '$variant[productcode]'");
				if ($variantid) {
					func_array2update("variants", $variant, "variantid = '$variantid'");
					db_query("DELETE FROM $sql_tbl[variant_items] WHERE variantid = '$variantid'");

				} else {
					$variantid = func_array2insert("variants", $variant);
				}

				foreach($options as $v) {
					func_array2insert("variant_items", array("optionid" => $hash[$v], "variantid" => $variantid), true);
				}

				if ($price) {
					db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND variantid = '$variantid' AND quantity = 1");
					db_query("INSERT INTO $sql_tbl[pricing] (productid,variantid,quantity,price) VALUES ('$productid','$variantid','1','$price')");
				}
			}
		}
		$string = func_remove_xml_tag($string, "Variant");
	} 

	if (preg_match("/<JScode>(.+)<\/JScode>/USs", $string, $preg))
		db_query("REPLACE INTO $sql_tbl[product_options_js] (productid, javascript_code) VALUES ('$productid','".addslashes($preg[1])."')");
	
	return true;
}

#
# For 3.x branch import data
# This function parses product options line and insert it into the
# 'product_options' table.
# Options line example:
# optclass=>Color&&&opttext=>Choose color&&&options=>Red###Blue&&&orderby=>1```optclass=>Size&&&opttext=>Choose size&&&options=>XX###XXL&&&orderby=>2
#
# <```>, <&&&>, <###> - is delimiters
function func_create_product_options_3x($productid, $options_string) {
	global $sql_tbl;

	# Get separated option groups
	$product_option_groups = explode("```", $options_string);
	if (empty($product_option_groups))
		return false;

	# Parse option group
	$product_options = array();
	foreach ($product_option_groups as $product_options_tmp) {
		$product_options_tmp = explode("&&&", $product_options_tmp);

		if (empty($product_options_tmp))
			continue;

		$product_option = array();
		foreach ($product_options_tmp as $product_option_tmp) {
			# Get the pairs <field name> => <value>
			list($key, $value) = explode("=>", $product_option_tmp);
			$product_option[$key] = $value;
		}

		$product_option["options"] = explode("###", $product_option["options"]);
		$product_options[] = $product_option;

	}

    if (empty($product_options))
		return false;

	# Insert product options for current product into the database
	foreach ($product_options as $product_option) {
		if (empty($product_option['optclass']))
			continue;

		$query_data = array(
			"productid" => $productid,
			"class" => addslashes($product_option['optclass']),
			"classtext" => addslashes(empty($product_option['opttext']) ? $product_option['optclass'] : $product_option['opttext']),
			"orderby" => intval($product_option['orderby']),
			"is_modifier" => empty($product_option['options']) ? "T" : "Y"
		);

		$classid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$productid' AND class = '$query_data[class]'");
		if (empty($classid)) {
			$classid = func_array2insert("classes", $query_data);

		} else {
			func_array2update("classes", $query_data, "classid = '$classid'");
		}

		if (empty($classid) || $query_data['is_modifier'] == 'T')
			continue;

		foreach ($product_option['options'] as $i => $option) {
			if (!preg_match("/([^=\n]+)[ \t]*=?[ \t]*([\d-+.]*)([$%]?)/Ss", $option, $match))
				continue;

			$query_data = array(
				"classid" => $classid,
				"option_name" => addslashes($match[1]),
				"orderby" => $i,
				"price_modifier" => doubleval($match[2]),
				"modifier_type" => ($match[3] == '%') ? "%" : "$"
			);

			$optionid = func_query_first_cell("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$classid' AND option_name = '$query_data[option_name]'");
			if (empty($optionid)) {
				$optionid = func_array2insert("class_options", $query_data);

			} else {
				func_array2update("class_options", $query_data, "optionid = '$optionid'");
			}
		}

	}

    return true;
}

#
# Product options check function
#
function func_test_product_options($string) {
    global $sql_tbl;

	$hash = array();

	# Check classes
	while (true) {
		$c = func_check_xml_tag($string , "Class", false, false);
		if ($c === false) {
			return false;

		} elseif ($c === true) {
			break;
		}

		$class['class'] = func_check_xml_tag($c, "Name", true, true);
		if ($class['class'] === false)
			return $c;

		if (func_check_xml_tag($c, "Avail") === false || func_check_xml_tag($c, "Text") === false || func_check_xml_tag($c, "OrderBy") === false)
			return $c;

		$class['is_modifier'] = func_check_xml_tag($c, "Type");
		if ($class['is_modifier'] === false)
			return $c;

		# Check class options
		$options_text = func_check_xml_tag($c, "Options", false, ($class['is_modifier'] !== 'T'));
		if ($options_text === false)
			return $c;

		if ($options_text) {
			$option = array();
			while (true) {
				$o = func_check_xml_tag($options_text, "Option", false);
				if ($o === false)
					return $options_text;

				if ($o === true)
					break;

				if (func_check_xml_tag($o, "Name", true, true) === false)
					return $o;
				$id = func_get_xml_tag($o, "ID", true, true);
				if (
					$id === false || 
					func_check_xml_tag($o, "Avail") === false || 
					func_check_xml_tag($o, "OrderBy") === false || 
					func_check_xml_tag($o, "PriceModifier") === false || 
					func_check_xml_tag($o, "ModifierType") === false
				) {
					return $o;
				}

				$options_text = func_remove_xml_tag($options_text, "Option");
				$hash[$id] = true;
			}
		}

		$string = func_remove_xml_tag($string, "Class");
	}

	# Check exceptions
	$last_cnt = false;
	while (true) {
		$e = func_check_xml_tag($string, "Exception", false);

		if ($e === false)
			return false;

		if ($e === true)
			break;

		# Check exception IDs
		if (!preg_match_all("/<ID>\s*(\d+)\s*<\/ID>/USs", $e, $ppreg))
			return $e;

		if ($last_cnt !== false && $last_cnt != count($ppreg[1]))
			return $e;

		$last_cnt = count($ppreg[1]);
		foreach($ppreg[1] as $v) {
			if (!isset($hash[$v]))
				return $e;
		}

		$string = func_remove_xml_tag($string, "Exception");
	}

	# Check variants
	$last_cnt = false;
	while(true) {
		$v = func_check_xml_tag($string, "Variant", false);

		if ($v === false)
			return false;

		if ($v === true)
			break;

		if (func_check_xml_tag($v, "SKU") === false || func_check_xml_tag($v, "Avail") === false || func_check_xml_tag($v, "Weight") === false)
			return $v;

		# Check variant items
	    $option_text = func_check_xml_tag($v, "Items", false, true);
		if ($option_text === false)
			return $v;

		if (!$option_text)
			return $v;

		if (preg_match_all("/<ID>\s*(\d+)\s*<\/ID>/USs", $option_text, $ppreg)) {
			if ($last_cnt !== false && $last_cnt != count($ppreg[1]))
				return $v;

			$last_cnt = count($ppreg[1]);
			foreach($ppreg[1] as $v) {
				if (!isset($hash[$v]))
					return $v;
			}
		}

		$string = func_remove_xml_tag($string, "Variant");
	} 

	# Check JS code
	if (func_check_xml_tag($string , "JScode", false) === false)
		return false;

	return true;
}

#
# Define type imported product options: XML or raw format
#
function func_product_options_type($data) {
	return (func_check_xml_tag($data, "Class", false, true) === false) ? "RAW" : "XML";
}

#
# Get category id (based category path)
#
function func_get_categoryid ($c, $path_sep="///") {
	global $sql_tbl, $config;

	$cats = explode($path_sep, $c);

	if (empty($cats))
		return false;
	
	$parentid = 0; 
	$id = false;
	foreach($cats as $c) {
		$id = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE parentid='$parentid' AND category = '".addslashes($c)."'");
		if ($id === false) 
			break;
		$parentid = $id;
	}

	return $id;
}

#
# Create category chain (based on category path)
#
function func_create_category ($c, $path_sep="///") {
	global $sql_tbl, $config;

	$id = func_get_categoryid($c, $path_sep);
	if ($id !== false)
		return $id;

	if ($config["Import_3x_4x"]["allow_auto_create_categories"] != "Y")
		return false;

	$cats = explode($path_sep, $c);
	if (empty($cats)) 
		return false;
	
	$path = ''; 
	$parentid = 0;
	foreach($cats as $c) {
		$c = addslashes($c);
		$id = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE category = '$c' AND parentid = '$parentid'");
		if (empty($id)) {
			$query_data = array(
				"parentid" => $parentid,
				"category" => $c,
				"categoryid_path" => $path
			);
			$id = func_array2insert("categories", $query_data);
			$path .= ($path ? "/" : "").$id;
			func_array2update("categories", array("categoryid_path" => $path), "categoryid = '$id'");

		} else {
			$path .= ($path ? "/" : "").$id;
		}

		$parentid = $id;
	}

	return $id;
}

#
# Process error message
#
function func_import_error($type, $field, $label, $data) {
	global $import_stats, $import_line, $import_file;
	global $import_warnings_max, $import_errors_max;

	$line = array(
		"line" => $import_line,
		"field" => $field,
		"label" => $label,
		"data" => $data
	);

	$stop = false;
	if ($type == "W") {
		$import_stats["warnings"][] = $line;
		if ($import_warnings_max >=0 && count($import_stats["warnings"]) >= $import_warnings_max) {
			$stop = true;
		}

	} else {
		$import_stats["errors"][] = $line;
		if ($import_errors_max >=0 && count($import_stats["errors"]) >= $import_errors_max) {
			$stop = true;
		}
	}

	if ($stop) {
		if ($import_file["uploaded"])
			@unlink($import_file["location"]);

		$import_file = "";
		$import_stats["stop"] = true;
		func_header_location("import_3x_4x.php?mode=results");
	}
}

#
# Prepare csv lines for test/import
#
function func_import_fill($import_columns, $csvcolumns, &$import_data, &$extra_data, $quote=true) {
	static $multi_columns = array(
		"category",
		"price"
	);
	global $extra_data_fields;
	
	foreach ($import_columns as $pos=>$index) {
		if (in_array($index, $extra_data_fields)) {
			if (in_array($index, $multi_columns)) {
				if (!isset($extra_data[$index]))
					$extra_data[$index] = array();
				if (trim($csvcolumns[$pos]) != "")
					$extra_data[$index][] = trim($csvcolumns[$pos]);

			} else {
				$extra_data[$index] = $csvcolumns[$pos];
			}

		} elseif (!empty($index)) {
			$import_data[$index] = ($quote) ? addslashes($csvcolumns[$pos]) : $csvcolumns[$pos];
		}
	}
}

#
# Check membership name
#
function func_check_membership($name) {
	global $sql_tbl;

	return func_query_first_cell("SELECT membershipid FROM $sql_tbl[memberships] WHERE area = 'C' AND membership = '".addslashes($name)."'");
}

#
# Parse price string
#
function func_parse_price($value) {
	$tmp = explode(':', $value);
	$result = false;

	switch(count($tmp)) {
		case 1:
			$result["price"] = $tmp[0];
			break;

		case 2:
			$result["price"] = $tmp[1];
			$result["qty"] = $tmp[0];
			break;

		default:
			$result["price"] = $tmp[2];
			$result["qty"] = $tmp[1];
			$result["membershipid"] = func_check_membership($tmp[0]);
	}

	return $result;
}

#
# CSV import facility, default delimiter '\t'
#
$quote_symbol="\"";

if ($REQUEST_METHOD=="POST" && $mode=="") {
	require $xcart_dir."/include/safe_mode.php";

	$images_directory = stripslashes($images_directory);
	if (substr ($images_directory, -1, 1) != '/')
		$images_directory .= '/';

	#
	# Import pass 1: test
	#
	x_session_register("import_file");
	x_session_register("import_stats");

	$import_file = array();
	$import_stats = array (
		"total_products" => 0,
		"categories" => 0,
		"products" => 0,
		"products_updated" => 0,
		"products_deleted" => 0,
		"thumbnails" => 0,
		"warnings" => array(),
		"errors" => array(),
		"pass" => "test",
		"delete_products" => ($delete_products=='yes'),
		"categories_hash" => array()
	);

	if ($localfile) {

		# Import from local file
		$localfile = stripslashes($localfile);
		if (func_allow_file($localfile,true)) {
			$import_file["location"] = $localfile;
			$import_file["uploaded"] = false;
		}

	} else {

		# Import from uploaded file
		$userfile = func_move_uploaded_file("userfile");
		if ($userfile !== false) {
			$import_file["location"] = $userfile;
			$import_file["uploaded"] = true;
		}
	}

	# Check import file
	if (!empty($import_file) && isset($import_file["location"])) {
		$fp = @func_fopen($import_file["location"], "r", true);
		if (!@filesize($import_file["location"]) || $fp === false) {
			if ($fp !== false) {
				fclose($fp);
				$fp = false;
			}
			
			if ($import_file["uploaded"])
				@unlink($import_file["location"]);

			$import_file = "";
		}
	}

	if (empty($import_file)) {
		x_session_unregister("import_file");
		$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
		$top_message["type"] = "E";
		func_header_location("import_3x_4x.php");
	}

	$import_3x_4x_saved['delimiter'] = $import_file["delimiter"] = $delimiter;
	$import_3x_4x_saved['default_categoryid'] = $import_file["default_categoryid"] = $categoryid;
	$import_3x_4x_saved['images_directory'] = $import_file["images_directory"] = $images_directory;
	$import_3x_4x_saved['category_sep'] = $import_file["category_sep"] = ($category_sep != "") ? $category_sep : "///";

	$import_file["skip_header"] = false;
	$import_line = 0;

	$import_3x_4x_saved['read_csv_header'] = ($HTTP_POST_VARS["read_csv_header"] == "Y");

	if ($HTTP_POST_VARS["read_csv_header"] == "Y") {
		$first_row = fgetcsv ($fp, $max_line_size, $import_file["delimiter"]);
		$import_line ++;
		$import_file["skip_header"] = true;

		if (is_array($first_row)) {
			$first_row = func_array_map('strtolower', $first_row);
			$first_row = func_array_map('trim', $first_row);
		}

		$fields_layout = func_array_merge($imported_columns, $extra_data_fields);
		$fields_to_compare = array_diff($fields_layout, $excluded_columns);

		if (!is_array($first_row) || count(array_diff($first_row, $fields_to_compare)) > 0) {

			# Error: cvs header error (wrong list of fields)
			$top_message["content"] = func_get_langvar_by_name("msg_err_csv_header");
			$top_message["type"] = "E";

			if ($import_file["uploaded"])
				@unlink($import_file["location"]);
			x_session_unregister("import_file");
			
			func_header_location("import_3x_4x.php");
		}

		$import_columns = $first_row;
	}

	$import_file["columns"] = $import_columns;

	#
	# Check import data
	#
	if ($import_stats["delete_products"]) {
		$import_stats["products_deleted"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE 1".$provider_condition);
	}
	
	while ($columns = fgetcsv ($fp, $max_line_size, $import_file["delimiter"])) {
		$product_update = false;
		$import_stats["total_products"]++;
		$import_line++;

		# combine keys & values
		$import_data = array();
		$extra_data = array();
		func_import_fill($import_file["columns"], $columns, $import_data, $extra_data, false);

		# check import data
		$productid = false;
		foreach ($import_data as $key=>$value) {
			$f_numeric = is_numeric($value);
			$f_float = $f_numeric;
			$f_int = preg_match('/^[+-]?\d+$/S', trim($value));
			$line_ok = true;
			switch ($key) {
				case 'productid':
					if ($value >= pow(2,31)-1)
						$f_int = false;
					if (!$f_int && !empty($value)) {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;

					} elseif (!$import_stats["delete_products"] ) {
						$tmp = func_query_first("SELECT productid, provider FROM $sql_tbl[products] WHERE productid='$value'");
						if (count($tmp) > 1) {
							if ($single_mode || $tmp["provider"] == $login) {
								$productid = $tmp["productid"];

							} else {
								func_import_error("E",$key,"missing",$value);
								$line_ok = false;
							}
						}
					}
					break;

				case 'productcode':
					if (!$import_stats["delete_products"] && !isset($import_data["productid"])) {
						$tmp = func_query_first("SELECT productid, provider FROM $sql_tbl[products] WHERE productcode='".addslashes($value)."'");
						if (count($tmp) > 1) {
							if ($single_mode || $tmp["provider"] == $login) {
								$productid = $tmp["productid"];
							}
							else {
								func_import_error("E",$key,"missing",$value);
								$line_ok = false;
							}
						}
					}
					break;
					
				case 'vat':
					if (!$f_int) {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;

					} elseif ($value) {
						$tmp = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[vat_rates] WHERE rateid = '$value'".$tmp_provider_condition);
						if ($tmp < 1) {
							func_import_error("E",$key,"missing",$value);
							$line_ok = false;
						}
					}
					break;

				case 'weight':
				case 'list_price':
				case 'shipping_freight':
					if (!$f_float && strlen($value) > 0) {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;
					}
					break;

				case 'orderby':
					if (!$f_int && strlen($value) > 0) {
						func_import_error("E", $key, "format", $value);
						$line_ok = false;
					}
					break;

				case 'categoryid':
				case 'categoryid1':
				case 'categoryid2':
				case 'categoryid3':
					if (!$f_int) {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;

					} elseif ($value) {
						$tmp = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid = '$value'");
						if ($tmp < 1) {
							func_import_error("E",$key,"missing",$value);
							$line_ok = false;
						}
					}
					break;

				case 'free_shipping':
				case 'discount_avail':
				case 'free_tax':
				case 'apply_gst':
				case 'apply_pst':
					$value = trim($value);
					if ($value == "") {
						unset($import_data[$key]);

					} elseif ($value != "Y" && $value != "N") {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;
					}
					break;

				case 'product_type':
					$value = trim($value);
					if (strlen($value) > 1) {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;
					}
					break;

				case 'forsale':
					$value = trim($value);
					if ($value == "") {
						unset($import_data[$key]);

					} elseif($value != "Y" && $value != "N" && $value != "B" && $value != "H") {
						func_import_error("E",$key,"format",$value);
						$line_ok = false;
					}
					break;

			}
		}

		# check thumbnail
		if ($line_ok && isset($extra_data["thumbnail"]) && !empty($extra_data["thumbnail"])) {
			$extra_data["thumbnail"] = trim($extra_data["thumbnail"]);
			if (dirname($extra_data["thumbnail"]) == ".") {
				$path = $import_file["images_directory"].basename($extra_data["thumbnail"]);
			} else {
				$path = $extra_data["thumbnail"];
			}

			if (($image_perms = func_check_image_perms("T")) !== true) {
				func_import_error("E", "thumbnail", $image_perms['content'], $extra_data["thumbnail"]);

			} elseif (func_allow_file($path,true) === false) {
				func_import_error("E","thumbnail","wrong",$extra_data["thumbnail"]);

			} else {
				$tmp = @func_fopen($path,"rb",true);
				if ($tmp === false) {
					func_import_error("E","thumbnail","fileopen",$extra_data["thumbnail"]);

				} else {
					fclose($tmp);
					$import_stats["thumbnails"]++;
				}
			}
		}

		# check category
		if ($line_ok && isset($extra_data["category"])) {
			foreach ($extra_data["category"] as $c) {
				$cid = func_get_categoryid($c, $import_file["category_sep"]);
				if ($cid === false) {
					if ($config["Import_3x_4x"]["allow_auto_create_categories"] != "Y") {
						func_import_error(empty($import_file["default_categoryid"]) ? "E" : "W","category","missing",$c);

					} else {
						$hid = md5($c);
						if (!isset($import_stats["categories_hash"][$hid])) {
							$import_stats["categories"]++;
							$import_stats["categories_hash"][$hid] = true;
						}
					}
				}
			}
		}

		if ($line_ok && empty($import_file["default_categoryid"]) && (!isset($extra_data["category"]) || empty($extra_data["category"]))) {
			func_import_error("E", "category", func_get_langvar_by_name("msg_err_import_empty_category"), '');
		}

		# check price
		if ($line_ok && isset($extra_data["price"])) {
			$is_general_price = false;
			foreach ($extra_data["price"] as $p) {
				$val = func_parse_price($p);

				if (isset($val["membership"]) !== false && !func_check_membership($val["membership"])) {
					func_import_error("E","price.membership","missing",$val["membership"]);
				}

				if (isset($val["qty"]) !== false && !is_numeric($val["qty"])) {
					func_import_error("E","price.quantity","format",$qty);
				}

				if (!is_numeric($val["price"])) {
					func_import_error("E","price.price","format",$val["price"]);
				}

				if (empty($val["membership"]) && (!isset($val['qty']) || $val["qty"] == 1))
					$is_general_price = true;
			}
			if (!$is_general_price && ($import_stats["delete_products"] || empty($productid))) {
				func_import_error("E","price",func_get_langvar_by_name("err_empty_base_price", array(), false, true),'');
			}
		}

		# Check product options
		if ($line_ok && !empty($active_modules['Product_Options']) && isset($extra_data["product_options"]) && !empty($extra_data["product_options"])) {
			if (func_product_options_type($extra_data['product_options']) == 'XML') {
				$f = func_test_product_options($extra_data['product_options']);
				if ($f !== true) {
					func_import_error("E", "product_options", "format", (($f !== false) ? $f : $value));
				}
			}
		}
		#
		# Count products
		#
		if ($line_ok) {
			if ($productid !== false) {
				if (!isset($import_stats["prod_update_hash"][$productid])) {
					$import_stats["prod_update_hash"][$productid] = 1;
					$import_stats["products_updated"]++;
				}

			} else {
				$import_stats["products"] ++;
			}
		}

	}
	fclose($fp);

	#
	# Remove file if any errors occured
	#
	if (count($import_stats["errors"]) > 0) {
		if ($import_file["uploaded"]) @unlink($import_file["location"]);

		$import_file = "";
	}

	func_header_location("import_3x_4x.php?mode=results");
}

x_session_register("import_file");
if ((($REQUEST_METHOD=="POST" && $mode=="import") || ($REQUEST_METHOD=="GET" && $mode == "continue" && $import_file['last_position'] > 0)) && $import_file!="") {
	require $xcart_dir."/include/safe_mode.php";

	#
	# Import pass 2: import
	#
	$fp = @func_fopen($import_file["location"], "r", true);
	if ($fp === false) {
		x_session_unregister("import_file");
		x_session_unregister("import_stats");
		$top_message["content"] = func_get_langvar_by_name("msg_err_file_wrong");
		$top_message["type"] = "E";
		func_header_location("import_3x_4x.php");
	}
	x_session_register("import_stats");

	func_display_service_header("txt_import_could_take_several_minutes");
	func_flush("<hr />\n");

	if ($import_stats["delete_products"] && !$import_file['last_position']) {
		x_load("product");
		func_flush(func_get_langvar_by_name("lbl_deleting_all_old_products", array(), false, true)."<br />\n");
		$products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1".$tmp_provider_condition);
		if ($products_to_delete) {
			$cnt = 0;
			while ($value = db_fetch_array($products_to_delete)) {
				func_delete_product ($value["productid"],false);
				$cnt++;
				if ($cnt % 10 == 0)
					echo ".";
				if ($cnt % 1000 == 0)
					echo "<br />\n";
				func_flush();
			}
			db_free_result($p_result);
			$import_stats["products_deleted"] = $cnt;
		}
		echo "<hr />";
	}

	func_flush(func_get_langvar_by_name("lbl_importing_new_products", array(), false, true)."<br />\n");

	$import_line = 0;
	$entries = 0;
	$last_sku_id = false;
	$max_sku = func_query_first_cell("SELECT MAX(productcode) FROM $sql_tbl[products] WHERE provider = '$login'");
	while ($columns = fgetcsv ($fp, $max_line_size, $import_file["delimiter"])) {
		$import_line ++;
		if ($import_file['last_position'] >= $import_line || ($import_file["skip_header"] && $import_line == 1))
			continue;
		
		# combine keys & values
		$import_data = array(); $extra_data = array();
		func_import_fill($import_file["columns"], $columns, $import_data, $extra_data);

		# insert section
		$import_data["provider"] = $login;
		$import_data["add_date"] = time();
		$productid = false;
		if (strlen((string)$import_data["productid"]) > 0) {
			$tmp = func_query_first("SELECT productid,provider FROM $sql_tbl[products] WHERE productid='$import_data[productid]'");
			if (count($tmp) > 1 && ($single_mode || $tmp["provider"] == $login)) {
				$productid = $tmp["productid"];
			}

		} elseif(strlen((string)$import_data["productcode"]) > 0) {
			$tmp = func_query_first("SELECT productid,provider FROM $sql_tbl[products] WHERE productcode='$import_data[productcode]'");
			if (count($tmp) > 1 && ($single_mode || $tmp["provider"] == $login)) {
				$productid = $tmp["productid"];
			}
		}

		if ($productid === false || isset($import_data["productcode"])) {
			if (isset($import_data["productcode"])) {
				$import_data["productcode"] = trim($import_data["productcode"]);
			}

			# if SKU is not set or new product has an already existing SKU then generate a unique SKU
			$sku_cnt = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode='$import_data[productcode]'".($productid ? " AND productid<>'$productid'" : ""));

			if (strlen((string)$import_data["productcode"]) == 0 || $sku_cnt > 0) {
				if (!isset($import_data['productcode'])) {
					$import_data['productcode'] = $max_sku;
				}

				$plus = ($last_sku_id === false) ? 1 : $last_sku_id;
				while (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode='".($import_data['productcode'].$plus)."'") > 0) {
					$plus++;
				}

				$import_data['productcode'] .= $plus;
				$last_sku_id = $plus;
			}
		}

		$is_update = ($productid !== false);

		$product_categories = array();
		if (!empty($extra_data["category"])) {
			foreach ($extra_data["category"] as $cat) {
				$catid = func_create_category($cat,$import_file["category_sep"]);
				if ($catid !== false)
					$product_categories[] = $catid;
			}
		}
		foreach(array("", "1", "2", "3") as $cidx) {
			if (empty($extra_data['categoryid'.$cidx]))
				continue;

			$cid = $extra_data['categoryid'.$cidx];
			if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid = '$cid'") == 0)
				continue;

			$product_categories[] = $cid;
		}

		if (!$is_update && empty($product_categories))
			$product_categories[] = $import_file["default_categoryid"];

		if(!empty($extra_data['manufacturer'])) {
			$import_data['manufacturerid'] = func_query_first_cell("SELECT manufacturerid FROM $sql_tbl[manufacturers] WHERE manufacturer='".addslashes($extra_data['manufacturer'])."'".$tmp_provider_condition);
			if(empty($import_data['manufacturerid'])) {
				db_query("INSERT INTO $sql_tbl[manufacturers] (provider, manufacturer) VALUES ('$login', '".addslashes($extra_data["manufacturer"])."')");
				$import_data['manufacturerid'] = db_insert_id();
			}
		}

		$is_insert = false;
		if ($is_update) {
			unset($import_data["productid"]);
			func_array2update("products", $import_data, "productid='$productid'");

		} else {

			# Create new product
			$productid = func_array2insert("products", $import_data);
			$is_insert = true;
		}

		if (!$productid) {

			# Cannot create product (database problem ?)
			fclose($fp);
			if ($import_file["uploaded"])
				@unlink($import_file["location"]);

			$top_message["content"] = func_get_langvar_by_name("err_import_msg", array(), false, true);
			$top_message["type"] = "E";
			func_html_location("import_3x_4x.php", 0); 
		}

		$entries++;

		# set category
		if (!empty($product_categories) && is_array($product_categories)) {
			$tmp = func_query("SELECT categoryid, orderby FROM $sql_tbl[products_categories] WHERE productid='$productid'");
			$orderbys = array();
			if (!empty($tmp)) {
				foreach ($tmp as $v) {
					$orderbys[$v['categoryid']] = $v['orderby'];
				}
			}
			db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid='$productid'");

			$is_main = 'Y';
			$product_categories = array_unique($product_categories);
			foreach ($product_categories as $catid) {
				$query_data = array(
					"productid" => $productid,
					"categoryid" => $catid,
					"main" => $is_main,
					"orderby" => isset($orderbys[$catid]) ? $orderbys[$catid] : 0
				);
				if (isset($extra_data['orderby']) && $is_main == 'Y')
					$query_data['orderby'] = $extra_data['orderby'];

				db_query("INSERT INTO $sql_tbl[products_categories] (productid,categoryid,main,orderby) VALUES ('$productid','$catid','$is_main','$orderby')");
				$is_main = 'N';
			}
		}

		# set price
		if (empty($extra_data["price"]) && $is_insert)
			$extra_data["price"] = array(0);

		if (!empty($extra_data["price"])) {
			$is_default_price = false;
			foreach ($extra_data["price"] as $p) {
				$val = func_parse_price($p);

				if ($val['price'] == 0 && $is_default_price)
					continue;		

				if (!isset($val["membershipid"]) && !isset($val["qty"])) {
					$is_default_price = true;
				}
				if (!isset($val["membershipid"])) 
					$val["membershipid"] = "";
				if (!isset($val["qty"])) 
					$val["qty"] = 1;

				$val = func_array_map("addslashes",$val);
				db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND quantity = '$val[qty]' AND membershipid = '$val[membershipid]'");
				db_query("INSERT INTO $sql_tbl[pricing] (productid, quantity, price, membershipid) VALUES ('$productid','$val[qty]','$val[price]','$val[membershipid]')");
			}
		}

		# set product options
		if (!empty($active_modules['Product_Options']) && !empty($extra_data["product_options"])) {

			# Insert data
			if (func_product_options_type($extra_data["product_options"]) == "XML") {
				# 4.x
				func_create_product_options($productid, $extra_data["product_options"]);

			} else {
				# 3.x
				func_create_product_options_3x($productid, $extra_data["product_options"]);
			}
		}

		# set applied taxes
		if (!empty($extra_data["applied_taxes"])) {
			$taxes = explode("&&&", $extra_data["applied_taxes"]);
			if(is_array($taxes) && !empty($taxes)) {
				$taxids = func_query_column("SELECT taxid FROM $sql_tbl[taxes] WHERE tax_name IN ('".implode("','", $taxes)."')");
				foreach($taxids as $taxid) {
					func_array2insert("product_taxes", array("productid" => $productid, "taxid" => $taxid), true);
				}
			}
		}

		# set thumbnail
		if (isset($extra_data["thumbnail"]) && !empty($extra_data["thumbnail"])) {
			$extra_data["thumbnail"] = trim($extra_data["thumbnail"]);
			if (dirname($extra_data["thumbnail"]) == ".") {
				$path = $import_file["images_directory"].basename($extra_data["thumbnail"]);
			} else {
				$path = $extra_data["thumbnail"];
			}

			$source = 'S';
			if ((strncasecmp($path, "http://", 7) === 0) || (strncasecmp($path, "ftp://", 6) === 0) || (strncasecmp($path, "https://", 8) === 0)) {
				$source = 'U';
			}
			$tmp_data = array(
				"source" => $source,
				"type" => "T",
				"date" => time(),
				"file_path" => $path,
				"filename" => basename($path)
			);
			list($tmp_data["file_size"], $tmp_data["image_x"], $tmp_data["image_y"], $tmp_data["image_type"]) = func_get_image_size($tmp_data["file_path"]);

			$temp_file_upload_data = array('T' => $tmp_data);
			if ($tmp_data["file_size"] > 0 && func_check_image_posted($temp_file_upload_data, "T")) {
				func_save_image($temp_file_upload_data, 'T', $productid);
			}
		}

		# set extra fields
		if (!empty($extra_data["extra_fields"])) {

			# 4.x
			$tmp = explode("###", $extra_data["extra_fields"]);
			$cnt = 0;
			foreach ($tmp as $v) {
				if (++$cnt > $config['Extra_Fields']['extra_fields_limit'])
					continue;

				if (!preg_match("/^field=(.+)&&&value=(.*)$/S", trim($v), $res))
					continue;

				array_shift($res);
				$res = func_array_map("addslashes", $res);
				$fieldid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE field='$res[0]' ".$tmp_provider_condition);
				if (empty($fieldid)) {
					$service_name_prefix = "SERVICE_NAME";
					$service_name = func_query_first_cell("SELECT MAX(SUBSTRING(service_name, ".(strlen($service_name_prefix)+1).")) FROM $sql_tbl[extra_fields] WHERE service_name LIKE '$service_name_prefix%'")+1;
					if (strlen($service_name) < 2)
    					$service_name = "0".$service_name;

					$query_data = array(
						"provider" => addslashes($login),
						"field" => $res[0],
						"service_name" => $service_name_prefix.$service_name
					);
					$fieldid = func_array2insert("extra_fields", $query_data);
				}

				if (!empty($fieldid)) {
					func_array2insert("extra_field_values", array("fieldid" => $fieldid, "productid" => $productid, "value" => $res[1]), true);
				}
			}

		}
		if (!empty($active_modules['Extra_Fields'])) {

			# 3.x
			for ($i = 0; $i < 10 && !$is_3x_extra_fields; $i++) {
				$key = 'param0'.$i;
				if (isset($extra_data[$key]) && !empty($extra_data[$key])) {
					$fieldid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] ORDER BY orderby LIMIT $i, 1");
					if (empty($fieldid))
						continue;

					$query_data = array(
						"productid" => $productid,
						"fieldid" => $fieldid,
						"value" => addslashes($extra_data[$key])
					);
					func_array2insert("extra_field_values", $query_data, true);
				}
			}
		}

		# set features type and feature values
		if(!empty($extra_data["feature_type"])) {
			$fclassid = func_query_first_cell("SELECT fclassid FROM $sql_tbl[feature_classes] WHERE class = '".addslashes($extra_data['feature_type'])."'");
			if (!empty($fclassid)) {
				db_query("REPLACE INTO $sql_tbl[product_features] (productid,fclassid) VALUES ('$productid','$fclassid')");

				# set feature values
				if (!empty($extra_data["feature_values"])) {
					$tmp = explode("###", $extra_data["feature_values"]);
					foreach($tmp as $v) {
						if (!preg_match("/^(.+)&&&(.+)$/USs", $v, $preg))
							continue;

						$option = func_query_first("SELECT foptionid, option_type, variants FROM $sql_tbl[feature_options] WHERE option_name = '".addslashes($preg[1])."'");
						if (empty($option))
							continue;

						$fvalue = '';
						if (!empty($preg[2])) {
							if ($option['option_type'] == 'S' || $option['option_type'] == 'M') {
								$option['variants'] = unserialize($option['variants']);
								if ($option['option_type'] == 'S') {
									foreach($option['variants'] as $vk => $vv) {
										if ($vv[$config['default_admin_language']] == $preg[2]) {
											$fvalue = $vk;
											break;
										}
									}

								} else {
									foreach($option['variants'] as $vk => $vv) {
										if (strpos($preg[2], "|".$vv[$config['default_admin_language']]."|") !== false) {
											$fvalue .= "|$vk|";
										}
									}
								}

							} elseif($option['option_type'] == 'D' && strpos($preg[2], "/") !== false) {
								list($m, $d, $y) = explode("/", $preg[2]);
								$fvalue = mktime(0, 0, 0, $m, $d, $y);

							} else {
								$fvalue = $preg[2];
							}
						}

						db_query("REPLACE INTO $sql_tbl[product_foptions] (foptionid, productid, value) VALUES ('$option[foptionid]','$productid','".addslashes($fvalue)."')");
					}
				}
			}
		}

		if ($entries %   10 == 0)
			echo ".";
		if ($entries % 1000 == 0)
			echo "<br />\n";
		func_flush();

		if ($import_line % $step_row == 0 && $import_line > $import_file['last_position']) {
			$import_file['last_position'] = $import_line;
			func_header_location("import_3x_4x.php?mode=continue");
		}
	}
	fclose($fp);

	#
	# Update 'product_count' field in the table 'categories' after success
	#

	func_flush("<br />\n<hr />\n<br />\n");

	func_build_quick_flags(false, 100);
	func_flush("<br />\n");

    func_build_quick_prices(false, 100);
    func_flush("<br />\n"); 

    if (!empty($active_modules['Fancy_Categories']) && function_exists("func_fc_build_categories")) {
		func_fc_remove_cache(10);
		func_flush("<br />\n");
		if (func_fc_check_rebuild()) {
			func_fc_build_categories(false, 10);
			func_flush("<br />\n");
		}
	}

	func_recalc_subcat_count(false, 10);	
	
	if ($import_file["uploaded"])
		@unlink($import_file["location"]);
	x_session_unregister("import_file");
			
	$top_message["content"] = func_get_langvar_by_name("msg_import_products_success", array("items" => $import_stats["products"]));
	$import_stats["pass"] = "final";

	func_html_location("import_3x_4x.php?mode=results", 0);
}

if ($REQUEST_METHOD == "GET" && $mode == "results") {
	require $xcart_dir."/include/safe_mode.php";

	x_session_register("import_file");
	x_session_register("import_stats");
	$smarty->assign("import_stats", $import_stats);
	$smarty->assign("import_pass", $import_stats["pass"]);
}


require $xcart_dir."/include/categories.php";

if ($config['setup_images']['T']["location"] == "FS") 
	$smarty->assign("default_imagepath", $config["Images"]["thumbnails_path"]); 

if (!is_array($imported_columns))
	$imported_columns = array();

$imported_columns = func_array_merge($imported_columns, $extra_data_fields);

$number_of_columns = is_array($imported_columns) ? count($imported_columns) : 0;

$smarty->assign("columns", $imported_columns);
$smarty->assign("columns2", $imported_columns);
$smarty->assign("number_of_columns", $number_of_columns);
$smarty->assign("rows", ceil($number_of_columns/3));
$smarty->assign("xcart_dir", $xcart_dir);
$smarty->assign("main", "import_3x_4x");
$smarty->assign("my_files_location", func_get_files_location());
$smarty->assign("import_3x_4x_saved", $import_3x_4x_saved);
$smarty->assign ("layout", $layout);

$smarty->assign("upload_max_filesize", ini_get("upload_max_filesize"));

# Assign the current location line
$smarty->assign("location", $location);

?>
