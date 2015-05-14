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
# $Id: search.php,v 1.141.2.13 2006/08/17 07:43:12 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$advanced_options = array("productcode", "productid", "provider", "price_max", "avail_max", "weight_max", "forsale", "flag_free_ship", "flag_ship_freight", "flag_global_disc", "flag_free_tax", "flag_min_amount", "flag_low_avail_limit", "flag_list_price", "flag_vat", "flag_gstpst", "manufacturers");

$sort_fields = array(
	"productcode" 	=> func_get_langvar_by_name("lbl_sku"),
	"title" 		=> func_get_langvar_by_name("lbl_product"),
    "price" 		=> func_get_langvar_by_name("lbl_price"),
	"orderby"		=> func_get_langvar_by_name("lbl_default")
);

if ($config["Appearance"]["display_productcode_in_list"] != "Y" && ($current_area == 'C' || $current_area == 'B'))
	unset($sort_fields["productcode"]);

if($current_area == 'A' || $current_area == 'P') {
    $sort_fields["quantity"] = func_get_langvar_by_name("lbl_in_stock");
}

if (empty($search_data)) {
	$search_data = array();
}

if ($REQUEST_METHOD == "POST" && $mode == 'search') {
	#
	# Update the session $search_data variable from $posted_data
	#
	if (!empty($posted_data)) {
		$need_advanced_options = false;
		foreach ($posted_data as $k=>$v) {
			if (!is_array($v) && !is_numeric($v))
				$posted_data[$k] = stripslashes($v);

			if (in_array($k, $advanced_options) && $v !== "")
				$need_advanced_options = true;
		}

		# Update the search statistics
		if ($posted_data["substring"]) {
			db_query("INSERT INTO $sql_tbl[stats_search] (search, date) VALUES ('".addslashes($posted_data["substring"])."', '".time()."')");
		}

		if (!$need_advanced_options)
			$need_advanced_options = (doubleval($posted_data["price_min"]) != 0 || intval($posted_data["avail_min"]) != 0 || doubleval($posted_data["weight_min"]) != 0);
		if (!$need_advanced_options && $current_area == "C" && !empty($posted_data["categoryid"]))
			$need_advanced_options = true;

		$posted_data["need_advanced_options"] = $need_advanced_options;

		#
		# Data convertation for Feature comparison module
		#
		if(!empty($active_modules['Feature_Comparison'])) {
			include $xcart_dir."/modules/Feature_Comparison/search_define.php";
		}

		if (empty($search_data["products"]["sort_field"])) {
			if ($current_area == 'C' && !empty($config['Appearance']['products_order'])) {
				$posted_data["sort_field"] = $config['Appearance']['products_order'];
				$posted_data["sort_direction"] = 1;

			} else {
				$posted_data["sort_field"] = "title";
				$posted_data["sort_direction"] = 0;
			}

		} else {
			$posted_data["sort_field"] = $search_data["products"]["sort_field"];
			$posted_data["sort_direction"] = $search_data["products"]["sort_direction"];
		}

		$posted_data['is_modify'] = $posted_data['is_modify'];
		$posted_data['is_export'] = $posted_data['is_export'];
		func_unset($posted_data, '_');

		$search_data["products"] = $posted_data;

	}
	func_header_location("search.php?mode=search&page=1");
}


if ($mode == "search") {
	#
	# Perform search and display results
	#
	$data = array();

	$flag_save = false;

	#
	# Initialize service arrays
	#
	$fields = array();
	$fields_count = array();
	$from_tbls = array();
	$inner_joins = array();
	$left_joins = array();
	$where = array();
	$groupbys = array();
	$having = array();
	$orderbys = array();

	#
	# Prepare the search data
	#
	if (!empty($sort) && isset($sort_fields[$sort])) {
		# Store the sorting type in the session
		$search_data["products"]["sort_field"] = $sort;
		$flag_save = true;
	}

	if (isset($sort_direction)) {
		# Store the sorting direction in the session
		$search_data["products"]["sort_direction"] = $sort_direction;
		$flag_save = true;
	}


	if ($current_area == 'C' && !empty($config['Appearance']['products_order']) && empty($search_data["products"]["sort_field"])) {
		$search_data["products"]["sort_field"] = $config['Appearance']['products_order'];
		$search_data["products"]["sort_direction"] = 0;
	}

	if (!empty($page) && $search_data["products"]["page"] != intval($page)) {
		# Store the current page number in the session
		$search_data["products"]["page"] = $page;
		$flag_save = true;
	}

	if (is_array($search_data["products"])) {
		$data = $search_data["products"];
		foreach ($data as $k=>$v)
			if (!is_array($v) && !is_numeric($v))
				$data[$k] = addslashes($v);
	}

	#
	# Translate service data to inner service arrays
	#
	if (!empty($data['_'])) {
		foreach ($data['_'] as $saname => $sadata) {
			if (isset($$saname) && is_array($$saname) && empty($$saname))
				$$saname = $sadata;
		}
	}

	$sort_string = "";
	$membershipid_string = ($user_account['membershipid'] == 0) ? "= 0" : "IN ('$user_account[membershipid]', 0)";

	$fields[] = "$sql_tbl[products].*";
	$from_tbls[] = "pricing";
	$left_joins['quick_flags'] = array(
		"on" => "$sql_tbl[quick_flags].productid = $sql_tbl[products].productid"
	);
	$fields[] = "$sql_tbl[quick_flags].*";

	$inner_joins['quick_prices'] = array(
		"on" => "$sql_tbl[quick_prices].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].membershipid $membershipid_string"
	);
	$where[] = "$sql_tbl[quick_prices].priceid = $sql_tbl[pricing].priceid";
	$fields[] = "$sql_tbl[quick_prices].variantid";
	if ($user_account['membershipid'] == 0) {
		$fields[] = "$sql_tbl[pricing].price";
	} else {
		$fields[] = "MIN($sql_tbl[pricing].price) as price";
	}

	if ($current_area == 'C' && empty($active_modules['Product_Configurator'])) {
		$where[] = "$sql_tbl[products].product_type <> 'C'";
		$where[] = "$sql_tbl[products].forsale <> 'B'";
	}
	if ($current_area == 'C' && defined('SO_CUSTOMER_OFFERS')) {
		# Display all products (including hidden)
		$where[] = "$sql_tbl[products].forsale <> 'N'";
	}


	if (!$single_mode && AREA_TYPE != 'A' && AREA_TYPE != 'P') {
		$inner_joins['ACHECK'] = array(
			"tblname" => 'customers',
			"on" => "$sql_tbl[products].provider = ACHECK.login AND ACHECK.activity='Y'"
		);
	}

	$data["substring"] = trim($data["substring"]);

	$search_by_variants = false;

	if (!empty($data["substring"])) {

		$condition = array();
		$search_string_fields = array();
		if (empty($data["by_title"]) && empty($data["by_shortdescr"]) && empty($data["by_fulldescr"]) && empty($data["extra_fields"]) && empty($data["by_sku"])) {
			$search_data["products"]["by_title"] = $data["by_title"] = "Y";
			$flag_save = true;
		}

		# Search for substring in some fields...

		if (!empty($data["by_title"])) {
			$search_string_fields[] = "product";
		}

		if (!empty($data["by_keywords"])) {
			$search_string_fields[] = "keywords";
		}

		if (!empty($data["by_shortdescr"])) {
			$search_string_fields[] = "descr";
		}

		if (!empty($data["by_fulldescr"])) {
			$search_string_fields[] = "fulldescr";
		}

		if ((!empty($data["by_shortdescr"]) || !empty($data["by_fulldescr"])) && $current_area == 'C' && !in_array("keywords", $search_string_fields)) {
			$search_string_fields[] = "keywords";
		}

		$search_words = array();
		if ($config['General']['allow_search_by_words'] == 'Y' && in_array($data['including'], array("all", "any"))) {
			$tmp = trim($data["substring"]);
			if (preg_match_all('/"([^"]+)"/', $tmp, $match)) {
				$search_words = $match[1];
				$tmp = str_replace($match[0], "", $tmp);
			}
			$tmp = explode(" ", $tmp);
			$tmp = func_array_map("trim", $tmp);
			$search_words = array_merge($search_words, $tmp);
			unset($tmp);

			# Check word length limit
			if ($search_word_length_limit > 0) {
				$search_words = preg_grep("/^..+$/", $search_words);
			}

			# Check stop words
			x_load("product");
			$stopwords = func_get_stopwords();
			if (!empty($stopwords) && is_array($stopwords)) {
				$tmp = preg_grep("/^(".implode("|", $stopwords).")$/i", $search_words);
				if (!empty($tmp) && is_array($tmp)) {
					$search_words = array_diff($search_words, $tmp);
					$search_words = array_values($search_words);
				}
				unset($tmp);
			}

			# Check word count limit
			if ($search_word_limit > 0 && count($search_words) > $search_word_limit) {
				$search_words = array_splice($search_words, $search_word_limit-1);
			}
		}

		foreach ($search_string_fields as $ssf) {
			if ($config['General']['allow_search_by_words'] == 'Y' && !empty($search_words) && in_array($data['including'], array("all", "any"))) {
				if ($data['including'] == 'all') {
					$tmp = array();
					foreach ($search_words as $sw) {
						if ($current_area == 'C' || $current_area == 'B') {
							$tmp[] = "IF($sql_tbl[products_lng].$ssf != '', $sql_tbl[products_lng].$ssf, $sql_tbl[products].$ssf) LIKE '%".$sw."%'";
						} else {
							$tmp[] = "$sql_tbl[products].$ssf LIKE '%".$sw."%'";
						}
					}
					if (!empty($tmp))
						$condition[] = "(".implode(" AND ", $tmp).")";
					unset($tmp);

				} else {
					if ($current_area == 'C' || $current_area == 'B') {
						$condition[] = "IF($sql_tbl[products_lng].$ssf != '', $sql_tbl[products_lng].$ssf, $sql_tbl[products].$ssf) REGEXP '".implode("|", $search_words)."'";
					} else {
						$condition[] = "$sql_tbl[products].$ssf REGEXP '".implode("|", $search_words)."'";
					}
				}

			} elseif ($current_area == 'C' || $current_area == 'B') {
				$condition[] = "IF($sql_tbl[products_lng].$ssf != '', $sql_tbl[products_lng].$ssf, $sql_tbl[products].$ssf) LIKE '%".$data["substring"]."%'";

			} else {
				$condition[] = "$sql_tbl[products].$ssf LIKE '%".$data["substring"]."%'";
			}
		}

		if (!empty($data["by_sku"])) {
			$search_by_variants = true;
			$condition[] = empty($active_modules['Product_Options']) ? "$sql_tbl[products].productcode" : "IFNULL(search_variants.productcode, $sql_tbl[products].productcode)"." LIKE '%".$data["substring"]."%'";
		}

		if (!empty($data["extra_fields"]) && $active_modules['Extra_Fields']) {
			foreach ($data["extra_fields"] as $k => $v)
				$condition[] = "($sql_tbl[extra_field_values].value LIKE '%".$data["substring"]."%' AND $sql_tbl[extra_fields].fieldid = '$k')";

			$left_joins['extra_field_values'] = array(
				"on" => "$sql_tbl[products].productid = $sql_tbl[extra_field_values].productid"
			);
			$left_joins['extra_fields'] = array(
				"on" => "$sql_tbl[extra_field_values].fieldid = $sql_tbl[extra_fields].fieldid AND $sql_tbl[extra_fields].active = 'Y'"
			);
		}

		if (!empty($condition))
			$where[] = "(".implode(" OR ", $condition).")";
		unset($condition);

	} # /if (!empty($data["substring"]))

	#
	# Search by product features
	#
	if (!empty($active_modules['Feature_Comparison'])) {
		include $xcart_dir."/modules/Feature_Comparison/search_define.php";
	}

	#
	# Internation names & descriptions
	#
	if ($current_area == 'C' || $current_area == 'B') {
		$fields[] = "IF($sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product";
		$fields[] = "IF($sql_tbl[products_lng].descr != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) as descr";
		$fields[] = "IF($sql_tbl[products_lng].fulldescr != '', $sql_tbl[products_lng].fulldescr, $sql_tbl[products].fulldescr) as fulldescr";
		if (!empty($data["by_title"]) || !empty($data["by_keywords"]) || !empty($data["by_shortdescr"]) || !empty($data["by_fulldescr"])) {
			$left_joins['products_lng'] = array(
				"on" => "$sql_tbl[products_lng].productid = $sql_tbl[products].productid AND $sql_tbl[products_lng].code = '$shop_language'"
			);
		} else {
			$left_joins['products_lng'] = array(
				"on" => "$sql_tbl[products_lng].productid = $sql_tbl[products].productid AND $sql_tbl[products_lng].code = '$shop_language'",
				"only_select" => true
			);
		}
	}

	if (!empty($data["manufacturers"]) && $active_modules['Manufacturers']) {
		$where[] = "$sql_tbl[products].manufacturerid IN ('".implode("','", $data["manufacturers"])."')";
	}

	if ($current_area == 'C') {
		if ($user_account['membershipid'] == 0) {
			$where[] = "$sql_tbl[category_memberships].membershipid IS NULL AND $sql_tbl[product_memberships].membershipid IS NULL";
		} else {
			$where[] = "($sql_tbl[category_memberships].membershipid IS NULL OR $sql_tbl[category_memberships].membershipid = '$user_account[membershipid]')";
			$where[] = "($sql_tbl[product_memberships].membershipid IS NULL OR $sql_tbl[product_memberships].membershipid = '$user_account[membershipid]')";
		}

		$where[] = "$sql_tbl[categories].avail = 'Y'";
	}

	$inner_joins['products_categories'] = array(
		"on" => "$sql_tbl[products_categories].productid = $sql_tbl[products].productid"
	);
	$inner_joins['categories'] = array(
		"on" => "$sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid"
	);
	$left_joins['category_memberships'] = array(
		"on" => "$sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid",
		"parent" => "categories"
	);
	$left_joins['product_memberships'] = array(
		"on" => "$sql_tbl[product_memberships].productid = $sql_tbl[products].productid"
	);

	if (!empty($data["categoryid"])) {
		# Search by category...

		$data["categoryid"] = intval($data["categoryid"]);

		$category_sign = "";

		if (empty($data["category_main"]) && empty($data["category_extra"])) {
			$category_sign = "NOT";
		}

		if (!empty($data["search_in_subcategories"])) {
			# Search also in all subcategories
			$categoryid_path = addslashes(func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid='".$data["categoryid"]."'"));
			$categoryids = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE categoryid='".$data["categoryid"]."' OR categoryid_path LIKE '$categoryid_path/%'");

			if (is_array($categoryids) && !empty($categoryids)) {
				$where[] = "$sql_tbl[products_categories].categoryid $category_sign IN (".implode(",", $categoryids).")";
			}
		}
		else {
			$where[] = "$category_sign $sql_tbl[products_categories].categoryid='$data[categoryid]'";
		}

		$condition = array();

		if (!empty($data["category_main"]))
			$condition[] = "$sql_tbl[products_categories].main='Y'";

		if (!empty($data["category_extra"]))
			$condition[] = "$sql_tbl[products_categories].main!='Y'";

		if (!empty($condition))
			$where[] = "(".implode(" OR ", $condition).")";
	}
	# /if (!empty($data["categoryid"]))

	if (!empty($data["productcode"])) {
		$search_by_variants = true;
		$productcode_cond_string = empty($active_modules['Product_Options']) ? "$sql_tbl[products].productcode" : "IFNULL(search_variants.productcode, $sql_tbl[products].productcode)";
		$where[] = "$productcode_cond_string LIKE '%".$data["productcode"]."%'";
	}

	if (!empty($data["productid"])) {
		$where[] = "$sql_tbl[products].productid ".(is_array($data["productid"]) ? " IN ('".implode("','", $data["productid"])."')": "= '".$data["productid"]."'");
	}

	if (!empty($data["provider"])) {
		if (is_array($data['provider']))
			$where[] = "$sql_tbl[products].provider IN ('".implode("','", $data['provider'])."')";
		else
			$where[] = "$sql_tbl[products].provider = '".$data["provider"]."'";
	}
	
	if (!empty($data["price_min"])) {
		$where[] = "$sql_tbl[pricing].price >= '".$data["price_min"]."'";
	}

	if (strlen(@$data["price_max"]) > 0) {
		$where[] = "$sql_tbl[pricing].price <= '".$data["price_max"]."'";
	}

	$avail_cond_string = empty($active_modules['Product_Options']) ? "$sql_tbl[products].avail" : "IFNULL(search_variants.avail, $sql_tbl[products].avail)";
	if (!empty($data["avail_min"])) {
		$search_by_variants = true;
		$where[] = "$avail_cond_string >= '".$data["avail_min"]."'";
	}

	if (strlen(@$data["avail_max"]) > 0) {
		$search_by_variants = true;
		$where[] = "$avail_cond_string <= '".$data["avail_max"]."'";
	}

	$weight_cond_string = empty($active_modules['Product_Options']) ? "$sql_tbl[products].weight" : "IFNULL(search_variants.weight, $sql_tbl[products].weight)";
	if (!empty($data["weight_min"])) {
		$search_by_variants = true;
		$where[] = "$weight_cond_string >= '".$data["weight_min"]."'";
	}

	if (strlen(@$data["weight_max"]) > 0) {
		$search_by_variants = true;
		$where[] = "$weight_cond_string <= '".$data["weight_max"]."'";
	}

	if (!empty($data["forsale"]))
		$where[] = "$sql_tbl[products].forsale = '".$data["forsale"]."'";

	if (!empty($data["flag_free_ship"]))
		$where[] = "$sql_tbl[products].free_shipping = '".$data["flag_free_ship"]."'";

	if (!empty($data["flag_ship_freight"]))
		$where[] = "$sql_tbl[products].shipping_freight = '".$data["flag_ship_freight"]."'";

	if (!empty($data["flag_ship_freight"])) {
		if ($data["flag_ship_freight"] == "Y")
			$where[] = "$sql_tbl[products].shipping_freight > 0";
		else
			$where[] = "$sql_tbl[products].shipping_freight = 0";
	}

	if (!empty($data["flag_global_disc"]))
		$where[] = "$sql_tbl[products].discount_avail = '".$data["flag_global_disc"]."'";

	if (!empty($data["flag_free_tax"]))
		$where[] = "$sql_tbl[products].free_tax = '".$data["flag_free_tax"]."'";

	if (!empty($data["flag_min_amount"])) {
		if ($data["flag_min_amount"] == "Y")
			$where[] = "$sql_tbl[products].min_amount != '1'";
		else
			$where[] = "$sql_tbl[products].min_amount = '1'";
	}

	if (!empty($data["flag_low_avail_limit"])) {
		if ($data["flag_low_avail_limit"] == "Y")
			$where[] = "$sql_tbl[products].low_avail_limit != '10'";
		else
			$where[] = "$sql_tbl[products].low_avail_limit = '10'";
	}

	if (!empty($data["flag_list_price"])) {
		if ($data["flag_list_price"] == "Y")
			$where[] = "$sql_tbl[products].list_price != '0'";
		else
			$where[] = "$sql_tbl[products].list_price = '0'";
	}

	if(!empty($active_modules['Product_Options'])) {
		if ($search_by_variants) {
			$left_joins["search_variants"] = array(
				"tblname" => "variants",
				"on" => "search_variants.productid = $sql_tbl[products].productid",
			);
		}
		$left_joins["variants"] = array(
			"on" => "$sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].variantid = $sql_tbl[variants].variantid",
			"parent" => "quick_prices"
		);
		foreach ($variant_properties as $property) {
			$fields[] = "IFNULL($sql_tbl[variants].$property, $sql_tbl[products].$property) as ".$property;
		}
	}

	if (!empty($data["sort_field"])) {
		# Sort the search results...

		$direction = ($data["sort_direction"] ? "DESC" : "ASC");
		switch ($data["sort_field"]) {
			case "productcode":
				$sort_string = "$sql_tbl[products].productcode $direction";
				break;
			case "title":
				$sort_string = "$sql_tbl[products].product $direction";
				break;
			case "orderby":
				$sort_string = "$sql_tbl[products_categories].orderby $direction";
				break;
			case "quantity":
				$sort_string = "$sql_tbl[products].avail $direction";
				break;
			case "price":
				if (!empty($active_modules["Special_Offers"]) && !empty($search_data["products"]["show_special_prices"])) {
					$sort_string = "x_special_price $direction, price $direction";
				}
				else {
					$sort_string = "price $direction";
				}
				break;
			default:
				$sort_string = "$sql_tbl[products].product";
		}
	}
	else {
		$sort_string = "$sql_tbl[products].product";
	}

	if(!empty($data['sort_condition'])) {
		$sort_string = $data['sort_condition'];
	}

	if (($current_area == "C" || $current_area == "B") && $config["General"]["disable_outofstock_products"] == "Y") {
		if (!empty($active_modules['Product_Options'])) {
			$where[] = "(IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > 0 OR $sql_tbl[products].product_type NOT IN ('','N'))";
		} else {
			$where[] = "($sql_tbl[products].avail > 0 OR $sql_tbl[products].product_type NOT IN ('','N'))";
		}
	}

	$groupbys[] = "$sql_tbl[products].productid";
	$orderbys[] = $sort_string;
	$orderbys[] = "$sql_tbl[products].product ASC";

	#
	# Generate search query
	#
	foreach ($inner_joins as $j) {
		if (!empty($j['fields']) && is_array($j['fields']))
			$fields = func_array_merge($fields, $j['fields']);
	}
	foreach ($left_joins as $j) {
		if (!empty($j['fields']) && is_array($j['fields']))
			$fields = func_array_merge($fields, $j['fields']);
	}

	$fields_count[] = "COUNT($sql_tbl[products].productid)";
	$search_query = "SELECT ".implode(", ", $fields)." FROM ";
	$search_query_count = "SELECT ".implode(", ", $fields_count)." FROM ";
	if (!empty($from_tbls)) {
		foreach ($from_tbls as $k => $v) {
			$from_tbls[$k] = $sql_tbl[$v];
		}
		$search_query .= implode(", ", $from_tbls).", ";
		$search_query_count .= implode(", ", $from_tbls).", ";
	}
	$search_query .= $sql_tbl['products'];
	$search_query_count .= $sql_tbl['products'];

	foreach ($left_joins as $ljname => $lj) {
		if (!empty($lj['parent']))
			continue;
		$tmp = " LEFT JOIN ";
		
		if (!empty($lj['tblname'])) {
			$tmp .= $sql_tbl[$lj['tblname']]." as ".$ljname;
		} else {
			$tmp .= $sql_tbl[$ljname];
		}
		$tmp .= " ON ".$lj['on'];
		if (!isset($lj['only_select']))
			$search_query_count .= $tmp;
		$search_query .= $tmp;
	}

	foreach ($inner_joins as $ijname => $ij) {
		$tmp = " INNER JOIN ";
		if (!empty($ij['tblname'])) {
			$tmp .= $sql_tbl[$ij['tblname']]." as ".$ijname;
		} else {
			$tmp .= $sql_tbl[$ijname];
		}
		$tmp .= " ON ".$ij['on'];
		$search_query_count .= $tmp;
		$search_query .= $tmp;
		foreach ($left_joins as $ljname => $lj) {
			if ($lj['parent'] != $ijname)
				continue;
			$tmp = " LEFT JOIN ";
			if (!empty($lj['tblname'])) {
				$tmp .= $sql_tbl[$lj['tblname']]." as ".$ljname;
			} else {
				$tmp .= $sql_tbl[$ljname];
			}
			$tmp .= " ON ".$lj['on'];
			if (!isset($lj['only_select']))
				$search_query_count .= $tmp;
			$search_query .= $tmp;
		}
	}

	$search_query .= " WHERE ".implode(" AND ", $where);
	$search_query_count .= " WHERE ".implode(" AND ", $where);
	if (!empty($groupbys)) {
		$search_query .= " GROUP BY ".implode(", ", $groupbys);
		$search_query_count .= " GROUP BY ".implode(", ", $groupbys);
	}
	if (!empty($having)) {
		$search_query .= " HAVING ".implode(" AND ", $having);
		$search_query_count .= " HAVING ".implode(" AND ", $having);
	}
	if (!empty($orderbys)) {
		$search_query .= " ORDER BY ".implode(", ", $orderbys);
		$search_query_count .= " ORDER BY ".implode(", ", $orderbys);
	}

	#
	# Calculate the number of rows in the search results
	#
	db_query("SET OPTION SQL_BIG_SELECTS=1");
	$_res = db_query($search_query_count);
	$total_items = db_num_rows($_res);
	db_free_result($_res);

	if ($total_items > 0) {
		$page = $search_data["products"]["page"];

		#
		# Prepare the page navigation
		#
		if (!isset($objects_per_page)) {
			if ($current_area == "C" || $current_area == "B")
				$objects_per_page = $config["Appearance"]["products_per_page"];
			else
				$objects_per_page = $config["Appearance"]["products_per_page_admin"];
		}

		$total_nav_pages = ceil($total_items/$objects_per_page)+1;

		include $xcart_dir."/include/navigation.php";

		#
		# Perform the SQL query and getting the search results
		#

		if (!empty($data["is_modify"])) {
			#
			# Get the products and go to modify them
			#
			$res = db_query($search_query);
			if ($res) {
				$geid = false;
				$productid = false;
				x_load("product");
				while ($pid = db_fetch_row($res)) {
					if (empty($productid))
						$productid = $pid[0];
					$geid = func_ge_add($pid[0], $geid);
				}
				func_header_location("product_modify.php?productid=$productid&geid=".$geid);
			}

		}
		elseif ($data["is_export"] == "Y" || $export == 'export_found') {

			x_load("export");
			# Save the SQL query and go to export them
			func_export_range_save("PRODUCTS", $search_query);
			$top_message['content'] = func_get_langvar_by_name("lbl_export_products_add");
			$top_message['type'] = 'I';
			func_header_location("import.php?mode=export");

		}
		else {
			$search_query .= " LIMIT $first_page, $objects_per_page";
			$products = func_query($search_query);
		}

		# Clear service arrays
		unset($fields, $fields_count, $from_tbls, $inner_joins, $left_joins, $where, $groupbys, $having, $orderbys);

		if (!empty($products) && $current_area == "C") {
			x_session_register("cart");

			# Get tax rates cache
			$ids = array();
			foreach ($products as $v) {
				if ($v['is_taxes'] == 'Y')
					$ids[] = $v;
			}

			$_taxes = array();
			if (!empty($ids)) {
				x_load("taxes");
				$_taxes = func_get_product_tax_rates($products, $login);
			}
			unset($ids);

			if (!empty($active_modules['Extra_Fields'])) {

				# Get Extra fields cache
				$ids = array();
				foreach ($products as $k => $v) {
					$ids[] = intval($v['productid']);
				}

				$products_ef = func_query_hash("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid IN (".implode(",", $ids).") AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby", "productid");
				unset($ids);
			}

			if (!empty($active_modules['Product_Options'])) {

				# Get Product options markups cache
				$ids = array();
				foreach ($products as $v) {
					if (!empty($v['is_product_options']))
						$ids[$v['productid']] = doubleval($v['price']);
				}

				$options_markups = array();
				if (!empty($ids))
					$options_markups = func_get_default_options_markup_list($ids);
				unset($ids);
			}

			foreach ($products as $k => $v) {
				if (!empty($active_modules['Feature_Comparison']) && $v['fclassid']) {
					$products_has_fclasses = true;
				}

				$products[$k]['taxed_price'] = $v['taxed_price'] = $v['price'];

				if (!empty($active_modules['Product_Options']) && !empty($v['is_product_options']) && !empty($options_markups[$v['productid']])) {

					# Add product options markup
					$products[$k]['price'] += $options_markups[$v['productid']];
					$products[$k]['taxed_price'] = $products[$k]['price'];
					$v = $products[$k];
				}

				$in_cart = 0;
				if (!empty($cart['products']) && is_array($cart['products'])) {

					# Modify product's quantity based the cart data
					foreach ($cart['products'] as $cv) {
						if ($cv['productid'] == $v['productid'] && $v['variantid'] == $cv['variantid'])
							$in_cart += $cv['amount'];
					}

					$products[$k]['in_cart'] = $in_cart;
					$products[$k]['avail'] -= $in_cart;
					if ($products[$k]['avail'] < 0) {
						$products[$k]['avail'] = 0;
					}
				}

				if (!empty($active_modules['Extra_Fields']) && isset($products_ef[$v['productid']])) {

					# Get extra fields data
					$products[$k]['extra_fields'] = $products_ef[$v['productid']];
				}

				# Get thumbnail URL
				$products[$k]["tmbn_url"] = false;
				if (!is_null($v['image_path_T'])) {
					$products[$k]['is_image_T'] = true;
					if (!empty($v['image_path_T'])) {
						x_load("files");
						$products[$k]["tmbn_url"] = func_get_image_url($v['productid'], "T", $v['image_path_T']);
					}

				} else {
					$products[$k]["tmbn_url"] = func_get_default_image("T");
				}

				unset($products[$k]['image_path_T']);

				# Calculate product taxes
				if (!empty($active_modules["Special_Offers"]) && !empty($search_data["products"]["show_special_prices"])) {
					include $xcart_dir."/modules/Special_Offers/search_results_calculate.php";
				}
				elseif ($v['is_taxes'] == 'Y' && isset($_taxes[$v['productid']])) {
					$products[$k]["taxes"] = func_get_product_taxes($products[$k], $login, false, $_taxes[$v['productid']]);
				}

				if ($products[$k]['descr'] == strip_tags($products[$k]['descr']))
					$products[$k]['descr'] = str_replace("\n", "<br />", $products[$k]['descr']);
				if ($products[$k]['fulldescr'] == strip_tags($products[$k]['fulldescr']))
					$products[$k]['fulldescr'] = str_replace("\n", "<br />", $products[$k]['fulldescr']);
			}

			if (!empty($active_modules["Special_Offers"]) && empty($search_data["products"]["show_special_prices"])) {
				func_offers_check_products($login, $current_area, $products);
			}
		}

		if (isset($products_ef))
			unset($products_ef);

		if (isset($options_markups))
			unset($options_markups);

		if (!$_inner_search) {
			# Assign the Smarty variables
			$smarty->assign("navigation_script","search.php?mode=search");
			$smarty->assign("products", $products);
			$smarty->assign("first_item", $first_page+1);
			$smarty->assign("last_item", min($first_page+$objects_per_page, $total_items));

			if (!empty($active_modules['Feature_Comparison']))
				$smarty->assign("products_has_fclasses", $products_has_fclasses);
		}
	}

	if (!$_inner_search) {
		$smarty->assign("total_items",$total_items);
		$smarty->assign("mode", $mode);

	if ($flag_save)
		x_session_save("search_data");
	}
}

if (!isset($search_data["products"]['substring']) && $current_area == 'C') {
	$search_data["products"]['productcode'] = $config['Search_products']['search_products_sku_d'];
	$search_data["products"]['price_min'] = preg_replace("/-.*$/", "", $config['Search_products']['search_products_price_d']);
	$search_data["products"]['price_max'] = preg_replace("/^.*-/", "", $config['Search_products']['search_products_price_d']);
	$search_data["products"]['weight_min'] = preg_replace("/-.*$/", "", $config['Search_products']['search_products_weight_d']);
	$search_data["products"]['weight_max'] = preg_replace("/^.*-/", "", $config['Search_products']['search_products_weight_d']);
	$search_data["products"]['categoryid'] = $config['Search_products']['search_products_category_d'];
}

if (!empty($active_modules['Feature_Comparison']) && $current_area != 'C' && $current_area != 'P' && !$_inner_search) {
	$fclasses = func_query("SELECT $sql_tbl[feature_classes].*, IFNULL($sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class FROM $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' WHERE $sql_tbl[feature_classes].avail = 'Y' ORDER BY $sql_tbl[feature_classes].orderby");
	if(!empty($fclasses)) {
		$smarty->assign("fclasses", $fclasses);
	}
}

if (!$_inner_search)
	$smarty->assign("search_prefilled", $search_data["products"]);

if ($active_modules['Manufacturers'] && !(!empty($products) && $mode == 'search') && !$_inner_search) {
	if ($current_area == "C") {
		$manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] USE INDEX (avail) LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' WHERE avail = 'Y' ORDER BY orderby, manufacturer");
	}
	else {
		$manufacturers = func_query("SELECT * FROM $sql_tbl[manufacturers] WHERE avail = 'Y' ORDER BY orderby, manufacturer");
	}

	if ($manufacturers) {
		array_unshift($manufacturers, array("manufacturerid" => '0', "manufacturer" => func_get_langvar_by_name("lbl_no_manufacturer")));
		$tmp = explode("\n", $config['Search_products']['search_products_manufacturers_d']);
		foreach ($manufacturers as $k => $v) {
			if (@in_array($v['manufacturerid'], (array)$search_data["products"]['manufacturers']) || (in_array($v['manufacturerid'], $tmp) && $current_area == 'C'))
				$manufacturers[$k]['selected'] = 'Y';
		}

		if ($manufacturers)
			$smarty->assign("manufacturers", $manufacturers);
	}
}

if ($active_modules['Extra_Fields'] && !(!empty($products) && $mode == 'search') && !$_inner_search) {
	$extra_fields = func_query("SELECT $sql_tbl[extra_fields].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE active = 'Y' ORDER BY field");
	if ($extra_fields) {
		$tmp = explode("\n", $config['Search_products']['search_products_extra_fields']);
		foreach ($extra_fields as $k => $v) {
			if (!in_array($v['fieldid'], $tmp) && $current_area == 'C') {
				unset($extra_fields[$k]);
				continue;
			}

			if ($search_data["products"]['extra_fields'][$v['fieldid']])
				$extra_fields[$k]['selected'] = 'Y';
		}

		if ($extra_fields)
			$smarty->assign("extra_fields", $extra_fields);
	}
}

if (!$_inner_search) {
	if ($current_area != 'C')
		include $xcart_dir."/include/categories.php";

	$search_categories = $smarty->get_template_vars("allcategories");
	if ($current_area == "C" && !empty($active_modules["Fancy_Categories"])) {
		if (!function_exists("func_categories_sort_abc")) {
			function func_categories_sort_abc($a, $b) {
				return strcmp($a["category_path"], $b["category_path"]);
			}
		}

		usort($search_categories, "func_categories_sort_abc");
	}

	$smarty->assign("search_categories", $search_categories);
	unset($search_categories);

	$smarty->assign("sort_fields", $sort_fields);
	$smarty->assign("main","search");
}

?>
