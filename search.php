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
# $Id: search.php,v 1.14.2.1 2006/10/30 08:10:32 max Exp $
#

define("NUMBER_VARS", "posted_data[price_min],posted_data[price_max],posted_data[avail_min],posted_data[avail_max],posted_data[weight_min],posted_data[weight_max],price_min,price_max,avail_min,avail_max,weight_min,weight_max");
require "./auth.php";
define("GET_ALL_CATEGORIES", true);
require $xcart_dir."/include/categories.php";

if($active_modules["Manufacturers"])
    include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

$tmp=strstr($QUERY_STRING, "$XCART_SESSION_NAME=");
if (!empty($tmp))  
	$QUERY_STRING=ereg_replace("$XCART_SESSION_NAME=([0-9a-zA-Z]*)", "", $QUERY_STRING);

if (!empty($active_modules['SnS_connector']) && $REQUEST_METHOD == 'POST') {
	if ($simple_search == 'Y') {
		func_generate_sns_action("SiteSearch");
	}
	else {
		func_generate_sns_action("AdvancedSearch");
	}
}

x_session_register("search_data");

# The list of the fields allowed for searching
$allowable_search_fields = array (
	"substring",
	"by_title",
	"by_shortdescr",
	"by_fulldescr",
	"extra_fields",
	"by_keywords",
	"categoryid",
	"category_main",
	"category_extra",
	"search_in_subcategories",
	"price_max",
	"price_min",
	"price_max",
	"avail_min",
	"avail_max",
	"weight_min",
	"weight_max",
	"manufacturers");

if ($REQUEST_METHOD == 'GET' && $mode == "search") {
	# Check the variables passed from GET-request
	$get_vars = array();
	foreach ($HTTP_GET_VARS as $k=>$v) {
		if (in_array($k, $allowable_search_fields))
			$get_vars[$k] = $v;
	}

	# Prepare the search data
	if (!empty($get_vars)) {
		$search_data["products"] = $get_vars;
	}
}

$search_data["products"]["forsale"] = "Y";
include $xcart_dir."/include/search.php";

if (!empty($search_data["products"]) && !empty($products)) {

	if (!empty($active_modules["Subscriptions"])) {
		# Get the subscription plans
		include $xcart_dir."/modules/Subscriptions/subscription.php";
		$smarty->assign("products", $products);
	}

	# Generate the URL of the search result page for accesing it via GET-request
	$search_url_args = array();
	foreach ($search_data["products"] as $k=>$v) {
		if (in_array($k, $allowable_search_fields) && !empty($v)) {
			if (is_array($v)) {
				foreach ($v as $k1=>$v1)
					$search_url_args[] = $k."[".$k1."]=".urlencode($v1);
			}
			else {
				$search_url_args[] = "$k=".urlencode($v);
			}
		}
	}

	if ($search_url_args && $page > 1)
		$search_url_args[] = "page=$page";

	$search_url = "search.php?mode=search".(!empty($search_url_args) ? "&".implode("&", $search_url_args) : "");
	$smarty->assign("search_url", $search_url);
}

unset($search_data["products"]["forsale"]);

if(empty($search_data["products"]))
	$search_data["products"] = '';
$smarty->assign("search_prefilled", $search_data["products"]);
$search_data["products"]["forsale"] = "Y";

if(empty($products))
	define("GET_ALL_CATEGORIES", 1);

if (!empty($QUERY_STRING)) {
	$location[] = array(func_get_langvar_by_name("lbl_search_results"), "");
	$smarty->assign("main","search");
}
else {
	$location[] = array(func_get_langvar_by_name("lbl_advanced_search"), "");
	$smarty->assign("main","advanced_search");
}

if ($user_account){
$customer_info = func_userinfo($user_account['login'],$user_account['usertype']);
}
$price = array();
if ($products){
foreach ($products as $key => $val)
    $price[$key]=$val['taxed_price'];
    if (!empty($active_modules["Special_Offers"])) {
	    include $xcart_dir."/modules/Special_Offers/product_offers.php";
	    include $xcart_dir."/modules/Special_Offers/calculate_prepare.php";
	    include $xcart_dir."/modules/Special_Offers/calculate.php";
	}
foreach ($products as $key => $val){
  if ($products[$key]['taxed_price']!=$price[$key])
      $products[$key]['discount_price']= $products[$key]['price'];
    $products[$key]['taxed_price']=$price[$key];
	if ($products[$key]['discount_price'] == "0")
       $products[$key]['discount_price'] = "0.00";
	}
}
$smarty->assign('products',$products);
# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);
?>
