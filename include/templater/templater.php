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
# $Id: templater.php,v 1.6 2006/03/17 15:02:44 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

include_once 'Smarty.class.php';
include_once 'Smarty_Compiler.class.php';

if (!class_exists('Smarty')) {
	echo "Can't find template engine!";
	exit;
}
		
class Templater extends Smarty {

	function Templater() {
		global $xcart_dir;

		$this->strict_resources = array ();

		$this->request_use_auto_globals = false;
		array_unshift($this->plugins_dir, $xcart_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'templater'.DIRECTORY_SEPARATOR.'plugins');

		$this->compiler_file	= "templater.php";
		$this->compiler_class	= "TemplateCompiler";

		$this->compile_check_md5 = false;

		return parent::Smarty();
	}

	function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false) {
		$this->current_resource_name = $resource_name;
		return parent::fetch($resource_name, $cache_id, $compile_id, $display);
	}

	function _is_compiled($resource_name, $compile_path) {
		if (!empty($this->strict_resources)) {
			foreach ($this->strict_resources as $rule) {
				if (preg_match($rule, $resource_name)) {
					return false;
				}
			}
		}

		$result = parent::_is_compiled($resource_name, $compile_path);
		if ($result && $this->compile_check_md5)
			return $this->_check_compiled_md5($compile_path);

		return $result;
	}

	#
	# Test if compiled resource was changed by third party
	#
	function _check_compiled_md5($compiled_file) {

		if ((rand() % 10) != 5) return true;

		$control_file = $compiled_file.'.md5';

		$compiled_data = $this->_read_file($compiled_file);
		if ($compiled_data === false)
			return false;

		$control_data = $this->_read_file($control_file);
		if ($control_data === false)
			return false;

		$md5 = md5($compiled_file.$compiled_data);
		return !strcmp($md5,$control_data);
	}

	function _compile_resource($resource_name, $compile_path) {
		$result = parent::_compile_resource($resource_name, $compile_path);

		if ($result && $this->compile_check_md5) {
			$tpl_source = $this->_read_file($compile_path);
			if ($tpl_source !== false) {
				$_params = array(
					'filename' => $compile_path.'.md5',
					'contents' => md5($compile_path.$tpl_source),
					'create_dirs' => true
				);
				smarty_core_write_file($_params, $this);
			}
		}

		return $result;
	}
};

class TemplateCompiler extends Smarty_Compiler {
	function _compile_file($resource_name, $source_content, &$compiled_content) {
		$this->current_resource_name = $resource_name;

		return parent::_compile_file($resource_name, $source_content, $compiled_content);
	}
};

?>
