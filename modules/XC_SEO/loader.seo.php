<?php
############################################################
# X-CART-SEO Mod :: http://code.google.com/p/x-cart-seo/
############################################################

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# The following turns on support for regular Customer mode and Admin area Froogle export
#

if(!$smarty->webmaster_mode && (AREA_TYPE=='C' && $HTTPS != "on") || ((AREA_TYPE=='A'||AREA_TYPE=='P') && $mode == "fcreate")){
	include $xcart_dir."/modules/XC_SEO/outputfilter.seo.php";
	if(AREA_TYPE=='C' && $_SERVER['REQUEST_METHOD']=='GET')
		include $xcart_dir."/modules/XC_SEO/seo.php";
	if(class_exists('seo_filter')) {
		$seo = new seo_filter;
		$smarty->register_outputfilter(array($seo,"outputfilter"));
	}
}

?>