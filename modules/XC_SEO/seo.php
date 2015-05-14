<?php
############################################################
# X-CART-SEO Mod :: http://code.google.com/p/x-cart-seo/
############################################################

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$seo_req_uri = array();
parse_str($_SERVER['QUERY_STRING'],$seo_req_uri);
$seo_path = parse_url($_SERVER['REQUEST_URI']);
$seo_path = $seo_path['path'];

if(!array_key_exists('redirect',$seo_req_uri)){ # Ignore 'redirect' requests in X-Cart
	$seo_exec = false;

	## Defining array of url structures that need to be rewritten
	$seo_regex_arr = array();
	if($config["XC_SEO"]["xcseo_category_rewrite"])
		$seo_regex_arr[] = array($xcart_web_dir.'/home.php','cat','cat');
	if($config["XC_SEO"]["xcseo_manufacturer_rewrite"])
		$seo_regex_arr[] = array($xcart_web_dir.'/manufacturers.php','manufacturerid','man');
	if($config["XC_SEO"]["xcseo_product_rewrite"])
		$seo_regex_arr[] = array($xcart_web_dir.'/product.php','productid','prod');
	if($config["XC_SEO"]["xcseo_staticpage_rewrite"])
		$seo_regex_arr[] = array($xcart_web_dir.'/pages.php','pageid','pages');

	## Testing REQUEST_URI, if url needs to be rewritten, $seo_exec will specify
	## which type of url will need a rewrite
	foreach($seo_regex_arr AS $v){
		if(strpos($seo_path,$v[0])!==false){
			if(array_key_exists($v[1],$seo_req_uri)!==false){
				$seo_exec = $v[2];
				break;
			}
		}
	}

	## If url is not scheduled for rewrite, and if user agent is a robot
	## then remove references to the printable & sortable parts of the url
	## also strips session id
	## and redirect bot to the new url structure
	if($config["XC_SEO"]["xcseo_redirect_robots"] && defined("IS_ROBOT") && !$seo_exec){
		$bot_url = $_SERVER["REQUEST_URI"];
		$bot_url = eregi_replace('-print-','-',$bot_url);
		$bot_url = eregi_replace('-(productcode|title|price|orderby)-','-',$bot_url);
		$bot_url = eregi_replace('-(up|down)-','-',$bot_url);
		$bot_url = preg_replace('/(.*)(-c-(?:[0-9]+)-p-(?:[0-9]+))(-pr-(?:[0-9]+)\.html)/','$1$3',$bot_url);
		$bot_url = preg_replace('/(.*)([?&])'.$XCART_SESSION_NAME.'=(?:[^&][\d\w]+)(.*)*/','$1$2$3',$bot_url);
		if(substr($bot_url, -1, 1) == '?' || substr($bot_url, -1, 1) == '&')
			$bot_url = substr_replace($bot_url,'',-1,1);
		if($bot_url!=$_SERVER["REQUEST_URI"]){
			header( "HTTP/1.1 301 Moved Permanently" );
			func_header_location($bot_url);
			exit;
		}
	}

	## Rewrite url as needed and redirect user to the new alias
	if($seo_exec!==false){

		$seo_qs = array();
		foreach($_GET AS $k=>$v){ // $_GET has already been sanitized by X-Cart
			if($k!=$XCART_SESSION_NAME)
				$seo_qs[] = $k.'='.$v;
		}
		$seo_qs = array(false,'','',implode('&',$seo_qs),'','');

		$seo_url = false;
		if(class_exists('seo_filter')) {
			$seo = new seo_filter;
			switch($seo_exec){
				case 'cat':
					$seo_url = $seo->_category_callback($seo_qs, true);
				break;
				case 'man':
					$seo_url = $seo->_manufacturer_callback($seo_qs, true);
				break;
				case 'prod':
					$seo_url = $seo->_product_callback($seo_qs, true);
				break;
				case 'pages':
					$seo_url = $seo->_pages_callback($seo_qs, true);
				break;
			}
		}

		if($seo_url){
			header( "HTTP/1.1 301 Moved Permanently" );
			func_header_location($seo_url);
			exit;
		}
	}
}

unset($seo,$seo_exec,$seo_regex_arr,$seo_cat,$seo_page,$seo_req_uri,$seo_path,
$seo_manufacturerid,$seo_productid,$seo_url,$seo_qs,$bot_url);
?>