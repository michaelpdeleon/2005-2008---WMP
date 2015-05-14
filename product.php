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
# $Id: product.php,v 1.21.2.4 2006/12/07 08:28:02 svowl Exp $
#

define('OFFERS_DONT_SHOW_NEW',1);
require "./auth.php";

x_load('product');

#
# Put all product info into $product array
#

$product_info = func_select_product($productid, @$user_account['membershipid']);
if (intval($cat) == 0) {
	$cat = $product_info["categoryid"];
}

$main = "product";
$smarty->assign("main",$main);

if (!empty($product_info["productid"])) {
	$product_info["meta_descr"] = strip_tags($product_info["descr"]);
	$product_info["meta_keywords"] = strip_tags($product_info["product"])." ".preg_replace("/[^a-zA-Z0-9]/", " ", strip_tags($product_info["descr"]));
}

include $xcart_dir.DIR_CUSTOMER."/send_to_friend.php";

if (!empty($send_to_friend_info)) {
	$smarty->assign("send_to_friend_info", $send_to_friend_info);
	if (!empty($active_modules['Image_Verification'])) {
		$smarty->assign("antibot_err", $send_to_friend_info['antibot_err']);
	}
	x_session_unregister("send_to_friend_info");
}

if (!empty($active_modules["Detailed_Product_Images"]))
	include $xcart_dir."/modules/Detailed_Product_Images/product_images.php";

if (!empty($active_modules["Magnifier"]))
	include $xcart_dir."/modules/Magnifier/product_magnifier.php";

if (!empty($active_modules["Product_Options"]))
	include $xcart_dir."/modules/Product_Options/customer_options.php";

if (!empty($active_modules["Upselling_Products"]))
	include $xcart_dir."/modules/Upselling_Products/related_products.php";

if (!empty($active_modules["Advanced_Statistics"]) && !defined("IS_ROBOT"))
    include $xcart_dir."/modules/Advanced_Statistics/prod_viewed.php";

if ($active_modules["Manufacturers"])
	include $xcart_dir."/modules/Manufacturers/customer_manufacturers.php";

if ($product_info["product_type"] != "C") {
	#
	# If this product is not configurable
	#
	if ($config["General"]["disable_outofstock_products"] == "Y" && empty($product_info['distribution'])) {
		$is_avail = true;
		if ($product_info['avail'] <= 0 && empty($variants)) {
			$is_avail = false;
		}
		elseif(!empty($variants)) {
			$is_avail = false;
			foreach($variants as $v) {
				if ($v['avail'] > 0) {
					$is_avail = true;
					break;
				}
			}
		}

		if(!empty($cart['products']) && !$is_avail) {
			foreach($cart['products'] as $v) {
				if($product_info['productid'] == $v['productid']) {
					$is_avail = true;
					break;
				}
			}
		}

		if(!$is_avail) {
			func_header_location("error_message.php?access_denied&id=44");
		}
	}

	if(!empty($active_modules["Extra_Fields"])) {
		$extra_fields_provider=$product_info["provider"];
		include $xcart_dir."/modules/Extra_Fields/extra_fields.php";
	}

	if(!empty($active_modules["Subscriptions"])) {
		$_products = $products;
		$products = array($product_info);
		include_once $xcart_dir."/modules/Subscriptions/subscription.php";
		$products = $_products;
	}

	if(!empty($active_modules["Feature_Comparison"]))
		include $xcart_dir."/modules/Feature_Comparison/product.php";

	if (!empty($active_modules["Wholesale_Trading"]) && empty($product_info['variantid']))
		include $xcart_dir."/modules/Wholesale_Trading/product.php";

	if (!empty($active_modules['Product_Configurator']) && !empty($HTTP_GET_VARS['pconf']))
		include $xcart_dir."/modules/Product_Configurator/slot_product.php";
		
}

if (!empty($active_modules["Recommended_Products"]))
	include "./recommends.php";

if (!empty($active_modules["SnS_connector"]))
	include $xcart_dir."/modules/SnS_connector/product.php";

include "./vote.php";

require $xcart_dir."/include/categories.php";

if (!empty($current_category) and is_array($current_category["category_location"])) {
	foreach ($current_category["category_location"] as $k=>$v)
		$location[] = $v;
}

if (!empty($product_info)) $location[] = array($product_info["product"],"");

if (!empty($active_modules["Special_Offers"])) {
	include $xcart_dir."/modules/Special_Offers/product_offers.php";
}

if ($variants){
foreach  ($variants as  $key => $val){
    $products[$key]=$product_info; 
	$products[$key]['price']=$val['price'];
	$products[$key]['taxed_price']=$val['taxed_price'];
if (!empty($active_modules["Special_Offers"])) {
        include $xcart_dir."/modules/Special_Offers/calculate_prepare.php";
        include $xcart_dir."/modules/Special_Offers/calculate.php";
if ($products[$key]['taxed_price'] != $val['taxed_price'])
$variants[$key]['discount_price']=$products[$key]['taxed_price'];
}
}
$product_info['discount_price']=$variants[$product_info['variantid']]['discount_price'];
} 
else{
    $products[0]=$product_info;
	if (!empty($active_modules["Special_Offers"])) {
	        include $xcart_dir."/modules/Special_Offers/calculate_prepare.php";
            include $xcart_dir."/modules/Special_Offers/calculate.php";
	}
	if ($product_info['taxed_price'] != $products[0]['taxed_price'])
	   $product_info['discount_price']=$products[0]['taxed_price'];
}


$smarty->assign("product",$product_info);
$smarty->assign("variants",$variants);

if ($active_modules["Bestsellers"])
	include $xcart_dir."/modules/Bestsellers/bestsellers.php";

# Assign the current location line
$smarty->assign("location", $location);

func_display("customer/home.tpl",$smarty);
?>
