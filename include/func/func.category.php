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
# $Id: func.category.php,v 1.12.2.6 2006/08/08 10:46:09 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Delete category recursively and all subcategories and products
#
function func_delete_category($cat) {
	global $sql_tbl;

	$catpair = func_query_first("SELECT categoryid_path, parentid FROM $sql_tbl[categories] WHERE categoryid='$cat'");
	if ($catpair === false) # category is missing
		return 0;

	#
	# Delete products from subcategories
	#
	$categoryid_path = $catpair["categoryid_path"];
	$parent_categoryid = $catpair["parentid"];
	$prods = db_query("SELECT $sql_tbl[products_categories].productid FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE ($sql_tbl[categories].categoryid='$cat' OR $sql_tbl[categories].categoryid_path LIKE '$categoryid_path/%') AND $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND $sql_tbl[products_categories].main='Y'");

	if ($prods) {
		x_load('product');

		while ($prod = db_fetch_array($prods)) {
			func_delete_product($prod["productid"], false);
		}
		db_free_result($prods);
	}

	#
	# Delete subcategories
	#
	$subcats = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE categoryid='$cat' OR categoryid_path LIKE '$categoryid_path/%'");

	if (is_array($subcats) && !empty($subcats)) {
		 x_load('backoffice');

		db_exec("DELETE FROM $sql_tbl[categories] WHERE categoryid IN (?)", array($subcats));
		db_exec("DELETE FROM $sql_tbl[products_categories] WHERE categoryid IN (?)", array($subcats));
		db_exec("DELETE FROM $sql_tbl[categories_subcount] WHERE categoryid IN (?)", array($subcats));
		db_exec("DELETE FROM $sql_tbl[featured_products] WHERE categoryid IN (?)", array($subcats));
		db_exec("DELETE FROM $sql_tbl[categories_lng] WHERE categoryid IN (?)", array($subcats));
		db_exec("DELETE FROM $sql_tbl[category_memberships] WHERE categoryid IN (?)", array($subcats));

		func_delete_image($subcats, 'C');
	}

	$path = explode("/", $categoryid_path);
	array_shift($path);
	if (!empty($path)) {
		func_recalc_subcat_count($path);
	}

	return $parent_categoryid;
}

#
# Recalculate product count in Categories table and Categories counts table
#
function func_recalc_product_count($categoryid = false, $tick = 0) {
	global $sql_tbl, $config, $single_mode;

	# Get mysql resource
	$where = "";
	if ($categoryid !== false) {
		if (empty($categoryid)) {
			return false;
		} elseif (is_array($categoryid)) {
			if (is_array(current($categoryid))) {
				foreach ($categoryid as $k => $v) {
					$categoryid[$k] = $v['categoryid'];
				}
			}
			$where = "WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $categoryid)."')";

		} elseif (!is_array($categoryid) && !is_resource($categoryid)) {
			$where = "WHERE $sql_tbl[categories].categoryid = '$categoryid'";
			
		}

		if (!is_resource($categoryid))
			$categoryid = db_query("SELECT categoryid, categoryid_path FROM $sql_tbl[categories] ".$where);

	} else {
		$categoryid = db_query("SELECT categoryid, categoryid_path FROM $sql_tbl[categories]");

	}

	if (!$categoryid)
		return false;

	# Get membership levels
	$lvl = func_query_column("SELECT membershipid FROM $sql_tbl[memberships] WHERE area = 'C'");
	$lvl[] = 0;

	if ($tick > 0)
		func_display_service_header();

	$finished = false;
	$cnt = 0;
	while ($c = db_fetch_array($categoryid)) {

		# Get category path
		if (isset($c['path'])) {
			$path = $c['path']."/%";
		} else {
			$path = func_query_first_cell("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid = '$c[categoryid]'")."/%";
		}
		$c = $c['categoryid'];

		# Get common counter
		if ($single_mode) {
			$res = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[customers].login=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') GROUP BY $sql_tbl[products].productid");

		} else {
			$res = db_query("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') GROUP BY $sql_tbl[products].productid");
		}

		$product_count = 0;
		if ($res) {
			$product_count = db_num_rows($res);
			db_free_result($res);
		}

		func_array2update("categories", array("product_count" => $product_count), "categoryid = '$c'");

		if (count($lvl) == 1) {

			# If membership list is empty
			$query_data = array(
				"product_count" => $product_count
			);

			if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories_subcount] WHERE categoryid = '$c' AND membershipid = '0'") > 0) {
				func_array2update("categories_subcount", $query_data, "categoryid = '$c' AND membershipid = '0'");

			} else {
				$query_data['categoryid'] = $c;
				$query_data['membershipid'] = 0;
				func_array2insert("categories_subcount", $query_data);
			}

		} else {

			# If membeship list is not empty

			# Get product counter (common products)
			if ($single_mode) {
				$res = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[customers].login=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");

			} else {
				$res = db_query("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");
			}

			$add_product_count = 0;
			if ($res) {
				$add_product_count = db_num_rows($res);
				db_free_result($res);
			}

			# Get product counters (by mebership levels)
			$product_count_member = array();
			if ($add_product_count != $product_count) {
				$product_count_member = array();
				if ($single_mode) {
					$res = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[customers].login=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");

				} else {
					$res = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND ($sql_tbl[categories].categoryid = '$c' OR $sql_tbl[categories].categoryid_path LIKE '$path') GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");
				}

				if ($res) {
					while ($row = db_fetch_array($res)) {
						if (!isset($product_count_member[$row['membershipid']]))
							$product_count_member[$row['membershipid']] = 0;
						$product_count_member[$row['membershipid']]++;
					}
					db_free_result($res);
				}
			}

			foreach ($lvl as $l) {
				$query_data = array(
					"product_count" => $add_product_count
				);
				if (isset($product_count_member[$l]))
					$query_data['product_count'] += $product_count_member[$l];

				if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories_subcount] WHERE categoryid = '$c' AND membershipid = '$l'") > 0) {
					func_array2update("categories_subcount", $query_data, "categoryid = '$c' AND membershipid = '$l'");

				} else {
					$query_data['categoryid'] = $c;
					$query_data['membershipid'] = $l;
					func_array2insert("categories_subcount", $query_data);
				}
			}
		}

		$cnt++;
		if ($tick > 0 && $cnt % $tick == 0) {
			func_flush(". ");
		}
	}

	db_free_result($categoryid);

	return true;
}

#
# Recalculate child categories count in Categories counts table
#
function func_recalc_subcat_count($categoryid = false, $tick = 0) {
	global $sql_tbl, $config;

	$where = "";
	if ($categoryid !== false) {
		if (empty($categoryid)) {
			return false;

		} elseif (!is_array($categoryid)) {
			$where = "WHERE $sql_tbl[categories].categoryid = '$categoryid'";

		} elseif (is_array($categoryid)) {
			if (is_array(current($categoryid))) {
				foreach ($categoryid as $k => $v) {
					$categoryid[$k] = $v['categoryid'];
				}
			}
			$where = "WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $categoryid)."')";

		}
	}

	db_query("DELETE FROM $sql_tbl[categories_subcount] ".str_replace($sql_tbl['categories'].".", "", $where));
	$res = db_query("SELECT $sql_tbl[categories].categoryid, $sql_tbl[categories].categoryid_path, IF($sql_tbl[category_memberships].categoryid IS NULL, '', 'Y') as mexists FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid ".$where." GROUP BY $sql_tbl[categories].categoryid");

	if (!$res)
		return false;

	if ($tick > 0)
		func_display_service_header("lbl_recalc_subcat_count");

	$lvl = func_query_column("SELECT membershipid FROM $sql_tbl[memberships] WHERE area = 'C'");
	$cnt = 0;
	$cat_limit = 100;
	$cat_collector = array();
	while ($c = db_fetch_array($res)) {
		$mexists = $c['mexists'];
		$path = $c['categoryid_path']."/%";
		$c = $c['categoryid'];

		# Category is common
		if (empty($mexists)) {
			$subcat_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] USE INDEX (pa) WHERE $sql_tbl[categories].categoryid_path LIKE '$path' AND $sql_tbl[categories].avail = 'Y'");
			$query_data = array(
				"categoryid" => $c,
				"subcategory_count" => $subcat_count,
				"membershipid" => 0
			);
			func_array2insert("categories_subcount", $query_data);
			if (!empty($lvl)) {
				foreach ($lvl as $v) {
					$query_data['membershipid'] = $v;
					func_array2insert("categories_subcount", $query_data);
				}
			}
		}
		elseif (!empty($lvl)) {
			# Category is limited by memberships
			$subcat_count = func_query("SELECT COUNT(*) as subcategory_count, IFNULL($sql_tbl[category_memberships].membershipid, 0) as membershipid FROM $sql_tbl[categories] USE INDEX (pa) LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid WHERE $sql_tbl[categories].categoryid_path LIKE '$path' AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[category_memberships].membershipid");
			if (!empty($subcat_count)) {
				foreach ($subcat_count as $v) {
					$v['categoryid'] = $c;
					func_array2insert("categories_subcount", $v);
				}
			}
		}

		$cat_collector[] = $c;
		if (count($cat_collector) > $cat_limit) {	
			func_recalc_product_count($cat_collector, $tick);
			$cat_collector = array();
		}

		$cnt++;
		if ($tick > 0 && $cnt % $tick == 0) {
			func_flush(". ");
		}
	}

	db_free_result($res);

	if (!empty($cat_collector))
		func_recalc_product_count($cat_collector, $tick);

	return true;
}

#
# Get a category path consisting of category names on the basis of a category
# path consisting of categoryid's
#
function func_categoryid_path2category_path($categoryid_path) {
	global $sql_tbl;

	$categoryid_path = explode("/", $categoryid_path);
	if (empty($categoryid_path))
		return false;

	$cnt = 1;
	$parentid = 0;
	$where = "";
	foreach ($categoryid_path as $v) {
		$where .= " WHEN categoryid = '$v' AND parentid = '$parentid' THEN ".($cnt++);
		$parentid = $v;
	}

	$data = func_query_column("SELECT category FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("','", $categoryid_path)."') ORDER BY CASE $where ELSE 0 END LIMIT ".count($categoryid_path));
	if (empty($data) || count($data) != count($categoryid_path))
		$data = false;

	return $data;
}

#
# Get parent categories chain
#
function func_get_category_parents($categoryid) {
	global $sql_tbl;

	if (!is_array($categoryid))
		$categoryid = array($categoryid);

	$res = db_query("SELECT categoryid_path FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("','", $categoryid)."')");
	if (!$res)
		return false;

	$cats = array();
	while ($c = db_fetch_row($res)) {
		$cats = array_unique(func_array_merge($cats, explode("/", array_pop($c))));
	}
	db_free_result($res);

	return $cats;
}

?>
