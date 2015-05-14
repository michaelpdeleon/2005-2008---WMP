<?php
############################################################
# X-CART-SEO Mod :: http://code.google.com/p/x-cart-seo/
############################################################

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

# X-Cart Smarty output filter to convert dynamic URL's to bogus static HTML pages
# which will be converted back by mod_rewrite directives

class seo_filter {

	var $_name_delim;
	var $_cur_name;
	var $_keyword_insert;
	var $_printable_insert;
	var $_cat_prefix;
	var $_man_prefix;
	var $_page_prefix;
	var $_prod_prefix;
	var $_pages_prefix;
	var $_search_prefix;
	var $_norm_match;
	var $_norm_repl;
	var $_max_name_length;

	function seo_filter() {
		global $config;

		$this->_name_delim  = '-';
		$this->_keyword_insert = $config["XC_SEO"]["xcseo_keyword_injection"];
		$this->_printable_insert = $this->_name_delim.'print';
		$this->_cat_prefix  = $this->_name_delim.'c'.$this->_name_delim;
		$this->_man_prefix  = $this->_name_delim.'m'.$this->_name_delim;
		$this->_page_prefix = $this->_name_delim.'p'.$this->_name_delim;
		$this->_prod_prefix = $this->_name_delim.'pr'.$this->_name_delim;
		$this->_pages_prefix = $this->_name_delim.'pg'.$this->_name_delim;
		$this->_search_prefix = $this->_name_delim.'search';
		$this->_norm_match  = array(
									"/[ \/".$this->_name_delim."]+/S",
									"/[^A-Za-z0-9_".$this->_name_delim."]+/S",
									"/[".$this->_name_delim."]+/S"
								  );
		$this->_norm_repl   = array($this->_name_delim, "", $this->_name_delim);
		$this->_max_name_length = 64;
	}

	#
	# Remove/replace characters in names to make valid for use in URL
	#
	function _normalize_name($name, $search=false) {
		$this->_cur_name = '';
		$name = trim($name);
		$name=strtr($name,"ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ",
			"SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
		if (!$search){
			$this->_cur_name = preg_replace($this->_norm_match, $this->_norm_repl, substr($name, 0, $this->_max_name_length));
			return strtolower($this->_cur_name);
		}else{
			$string = explode(' ',$name);
			$name = array();
			foreach($string AS $k=>$v){
				$name[] = $this->_normalize_name($v);
			}
			$name = substr(implode('+',$name), 0, $this->_max_name_length);
			$this->_cur_name = strtolower(str_replace('+',' ',$name));
			return strtolower($name);
		}
	}

	#
	# Generate filename for a category page
	#
	function _category_filename($options) {
		global $sql_tbl;

		if (!$options['result_title'])
			$options['result_title'] = func_query_first_cell("SELECT category
											 FROM $sql_tbl[categories]
											 WHERE categoryid='".$options['cat']."'");
		if (!$options['result_title'])
			$options['result_title'] = $options['cat'];
		$options['result_title']  = $this->_normalize_name($options['result_title']);
		if ($this->_keyword_insert && !substr_count($options['result_title'],$this->_keyword_insert))
			$options['result_title'] .= $this->_name_delim.$this->_keyword_insert;
		if ($options['printable'] && !defined("IS_ROBOT"))
			$options['result_title'] .= $this->_printable_insert;
		if ($options['sort'] && !defined("IS_ROBOT")){
			$options['result_title'] .= $this->_name_delim.$options['sort'];
			if ($options['sort_direction']!==false){
				$options['result_title'] .= $this->_name_delim.($options['sort_direction']?'down':'up');
			}
		}
		$options['result_title'] .= $this->_cat_prefix.$options['cat'];
		if ($options['page'] > 1)
			$options['result_title'] .= $this->_page_prefix.$options['page'];

		return $options['result_title'].'.html';
	}

	#
	# Generate filename for a manufacturers page
	#
	function _manufacturer_filename($options) {
		global $sql_tbl;

		if (!$options['result_title'])
			$options['result_title'] = func_query_first_cell("SELECT manufacturer
											 FROM $sql_tbl[manufacturers]
											 WHERE manufacturerid='".$options['manufacturerid']."'");
		if (!$options['result_title'])
			$options['result_title'] = $options['manufacturerid'];
		$options['result_title'] = $this->_normalize_name($options['result_title']);
		if ($this->_keyword_insert && !substr_count($options['result_title'],$this->_keyword_insert))
			$options['result_title'] .= $this->_name_delim.$this->_keyword_insert;
		if ($options['printable'] && !defined("IS_ROBOT"))
			$options['result_title'] .= $this->_printable_insert;
		if ($options['sort'] && !defined("IS_ROBOT")){
			$options['result_title'] .= $this->_name_delim.$options['sort'];
			if ($options['sort_direction']!==false){
				$options['result_title'] .= $this->_name_delim.($options['sort_direction']?'down':'up');
			}
		}
		$options['result_title'] .= $this->_man_prefix.$options['manufacturerid'];
		if ($options['page'] > 1)
		  $options['result_title'] .= $this->_page_prefix.$options['page'];

		return $options['result_title'].'.html';
	}

	#
	# Generate filename for a product page
	#
	function _product_filename($options) {
		global $sql_tbl;

		if (!$options['result_title'])
			$options['result_title'] = func_query_first_cell("SELECT product
											  FROM $sql_tbl[products]
											  WHERE productid = '".$options['productid']."'");
		if (!$options['result_title'])
			$options['result_title'] = $options['productid'];
		$options['result_title'] = $this->_normalize_name($options['result_title']);
		if ($this->_keyword_insert && !substr_count($options['result_title'],$this->_keyword_insert))
			$options['result_title'] .= $this->_name_delim.$this->_keyword_insert;
		if ($options['printable'] && !defined("IS_ROBOT"))
			$options['result_title'] .= $this->_printable_insert;
		if ($options['cat'] && !defined("IS_ROBOT")){
			$options['result_title'] .= $this->_cat_prefix.$options['cat'];
			if ($options['page'])
				$options['result_title'] .= $this->_page_prefix.$options['page'];
		}
		$options['result_title'] .= $this->_prod_prefix.$options['productid'];
		  
		return $options['result_title'].'.html';
	}

	#
	# Generate filename for a static page
	#
	function _pages_filename($options) {
		global $sql_tbl;

		if (!$options['result_title'])
			$options['result_title'] = func_query_first_cell("SELECT title
											  FROM $sql_tbl[pages]
											  WHERE pageid = '".$options['pageid']."'");
		if (!$options['result_title'])
			$options['result_title'] = $options['pageid'];
		$options['result_title'] = $this->_normalize_name($options['result_title']);
		if ($this->_keyword_insert && !substr_count($options['result_title'],$this->_keyword_insert))
			$options['result_title'] .= $this->_name_delim.$this->_keyword_insert;
		if ($options['printable'] && !defined("IS_ROBOT"))
			$options['result_title'] .= $this->_printable_insert;
		$options['result_title'] .= $this->_pages_prefix.$options['pageid'];
		  
		return $options['result_title'].'.html';
	}

	#
	# Generate filename for a search results page
	#
	function _search_filename($options) {
		global $sql_tbl;

		$options['result_title'] = $this->_normalize_name($options['substring'],true);
		$options['result_title'] .= $this->_search_prefix;
		if ($this->_keyword_insert && !substr_count($options['result_title'],$this->_keyword_insert))
			$options['result_title'] .= $this->_name_delim.$this->_keyword_insert;
		if ($options['cat'])
			$options['result_title'] .= $this->_cat_prefix.$options['cat'];

		  
		return $options['result_title'].'.html';
	}

	#
	# Category url found callback for preg_replace_callback
	#   $found[1]     <a href=
	#   $found[2]     " or '
	#   not passed    home.php?
	#   $found[3]     cat=###&page=###
	#   $found[4]     " or '
	#   $found[5]      style="###" title="###">
	#
	function _category_callback($found, $uri_only = false, $title = false) {

		$options = $this->_check_uri($found[3]);
		$options['result_title'] = $title;
		if($options['cat']){
			if($uri_only)
				return $this->_category_filename($options);
			else
				return $found[1].$found[2].$this->_category_filename($options). $found[4].$this->_insert_href_title($found[5]);
		}else{
			return $found[0];
		}
	}

	#
	# Manufacturers url found callback for preg_replace_callback
	#   $found[1]     <a href=
	#   $found[2]     " or '
	#   not passed    manufacturers.php?
	#   $found[3]     manufacturerid=###&page=###
	#   $found[4]     " or '
	#   $found[5]      style="###" title="###">
	#
	function _manufacturer_callback($found, $uri_only = false, $title = false) {

		$options = $this->_check_uri($found[3]);
		$options['result_title'] = $title;
		if($options['manufacturerid']){
			if($uri_only)
				return $this->_manufacturer_filename($options);
			else
				return $found[1].$found[2].$this->_manufacturer_filename($options). $found[4].$this->_insert_href_title($found[5]);
		}else{
			return $found[0];
		}
	}

	#
	# Product url found callback for preg_replace_callback
	#   $found[1]     <a href=
	#   $found[2]     " or '
	#   not passed    product.php?
	#   $found[3]     productid=###
	#   $found[4]     " or '
	#   $found[5]      style="###" title="###">
	#
	function _product_callback($found, $uri_only = false, $title = false) {

		$options = $this->_check_uri($found[3]);
		$options['result_title'] = $title;
		if($options['productid'] && !$options['mode']){
			if($uri_only)
				return $this->_product_filename($options);
			else
				return $found[1].$found[2].$this->_product_filename($options). $found[4].$this->_insert_href_title($found[5]);
		}else{
			return $found[0];
		}
	}

	#
	# Page url found callback for preg_replace_callback
	#   $found[1]     <a href=
	#   $found[2]     " or '
	#   not passed    pages.php?
	#   $found[3]     pageid=###
	#   $found[4]     " or '
	#   $found[5]      style="###" title="###">
	#
	function _pages_callback($found, $uri_only = false, $title = false) {

		$options = $this->_check_uri($found[3]);
		$options['result_title'] = $title;
		if($options['pageid']){
			if($uri_only)
				return $this->_pages_filename($options);
			else
				return $found[1].$found[2].$this->_pages_filename($options). $found[4].$this->_insert_href_title($found[5]);
		}else{
			return $found[0];
		}
	}

	#
	# Search url found callback for preg_replace_callback
	#   $found[1]     <a href=
	#   $found[2]     " or '
	#   not passed    search.php?
	#   $found[3]     pageid=###
	#   $found[4]     " or '
	#   $found[5]      style="###" title="###">
	#
	function _search_callback($found, $uri_only = false) {

		$options = $this->_check_uri($found[3]);
		if($options['substring']){
			if($uri_only)
				return $this->_search_filename($options);
			else
				return $found[1].$found[2].$this->_search_filename($options). $found[4].$this->_insert_href_title($found[5]);
		}else{
			return $found[0];
		}
	}

	#
	# Parses query string into an array
	#
	function _check_uri($uri){

		$return = array();
		parse_str(str_replace('&amp;','&',$uri),$return);

		if(array_key_exists('redirect',$return))
			return false;

		return $return;
	}

	#
	# Adds title="###" text to <a href=""> structures
	# Is passed $found[4]
	#
	function _insert_href_title($found){
		if((strpos(" title=",$found)===false)){
			$found = substr_replace($found,' title="'.str_replace($this->_name_delim,' ',$this->_cur_name).'">',-1);
		}
		return $found;
	}

	#
	# Modify hyperlinks to point to bogus HTML pages.  mod_rewrite will convert back when pages are referenced
	#
	function outputfilter($page_src, &$template_object){
		global $config;

		# Modify links to categories
		if($config["XC_SEO"]["xcseo_category_rewrite"])
		$page_src = preg_replace_callback(
					  '/(<a[^<>]+href[ ]*=[ ]*)(["\'])[^"\']*home.php\?((?:printable=[^"\'>]+)*cat=[^"\'>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_category_callback"),
					  $page_src);
		# FancyCategories links
		if($config["XC_SEO"]["xcseo_category_rewrite"])
		$page_src = preg_replace_callback(
					  '/(window.location[ ]*=[ ]*)(["\'])[^"\']*home.php\?((?:printable=[^"\'>]+)*cat=[^"\'>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_category_callback"),
					  $page_src);
		# Modify links to manufacturer pages
		if($config["XC_SEO"]["xcseo_manufacturer_rewrite"])
		$page_src = preg_replace_callback(
					  '/(<a[^<>]+href[ ]*=[ ]*)(["\'])[^"\']*manufacturers.php\?((?:printable=[^"\'>]+)*manufacturerid=[^"\'>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_manufacturer_callback"),
					  $page_src);
		# Modify links to products
		if($config["XC_SEO"]["xcseo_product_rewrite"])
		$page_src = preg_replace_callback(
					  '/(<a[^<>]+href[ ]*=[ ]*)(["\'])[^"\']*product.php\?((?:printable=[^"\'>]+)*productid=[^"\'>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_product_callback"),
					  $page_src);
		# Modify links to pages
		if($config["XC_SEO"]["xcseo_staticpage_rewrite"])
		$page_src = preg_replace_callback(
					  '/(<a[^<>]+href[ ]*=[ ]*)(["\'])[^"\']*pages.php\?((?:printable=[^"\'>]+)*pageid=[^"\'>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_pages_callback"),
					  $page_src);
		# Modify links to pages, search REGEX is different from others
		if($config["XC_SEO"]["xcseo_simplesearch_rewrite"])
		$page_src = preg_replace_callback(
					  '/(<a[^<>]+href[ ]*=[ ]*)(["\'])[^"\']*search.php\?(mode=search[^>]+)(\2)((?:[^<>]+)*\>)/iUS',
					  array($this,"_search_callback"),
					  $page_src);

		return $page_src; 
	}

}
?>