<?php /* Smarty version 2.6.12, created on 2014-10-11 21:15:03
         compiled from meta.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'meta.tpl', 2, false),array('modifier', 'truncate', 'meta.tpl', 30, false),array('modifier', 'escape', 'meta.tpl', 30, false),)), $this); ?>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
" />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "presets_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "common.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  if ($this->_tpl_vars['config']['Adaptives']['isJS'] == '' && $this->_tpl_vars['config']['Adaptives']['is_first_start'] == 'Y'): ?>
<script type="text/javascript">
<!--
var usertype = "<?php echo $this->_tpl_vars['usertype']; ?>
";
-->
</script>
<script id="adaptives_script" type="text/javascript" language="JavaScript 1.2"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "browser_identificator.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  endif;  if ($this->_tpl_vars['usertype'] == 'P' || $this->_tpl_vars['usertype'] == 'A'): ?>
<meta name="ROBOTS" content="NOINDEX" />
<meta name="ROBOTS" content="NOFOLLOW" />
<?php else:  $this->assign('_meta_descr', "");  $this->assign('_meta_keywords', "");  if ($this->_tpl_vars['product']['meta_descr'] != "" && $this->_tpl_vars['config']['SEO']['include_meta_products'] == 'Y'):  $this->assign('_meta_descr', ($this->_tpl_vars['product']['meta_descr'])." ");  $this->assign('_meta_keywords', ($this->_tpl_vars['product']['meta_keywords'])." ");  endif;  if ($this->_tpl_vars['current_category']['meta_descr'] != "" && $this->_tpl_vars['config']['SEO']['include_meta_categories'] == 'Y'):  $this->assign('_meta_descr', ($this->_tpl_vars['_meta_descr']).($this->_tpl_vars['current_category']['meta_descr'])." ");  $this->assign('_meta_keywords', ($this->_tpl_vars['_meta_keywords']).($this->_tpl_vars['current_category']['meta_keywords'])." ");  endif;  $this->assign('_meta_descr', ($this->_tpl_vars['_meta_descr']).($this->_tpl_vars['config']['SEO']['meta_descr']));  $this->assign('_meta_keywords', ($this->_tpl_vars['_meta_keywords']).($this->_tpl_vars['config']['SEO']['meta_keywords'])); ?>
<meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['_meta_descr'])) ? $this->_run_mod_handler('truncate', true, $_tmp, '500', "...", false) : smarty_modifier_truncate($_tmp, '500', "...", false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<meta name="keywords" content="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['_meta_keywords'])) ? $this->_run_mod_handler('truncate', true, $_tmp, '500', "", false) : smarty_modifier_truncate($_tmp, '500', "", false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<?php endif;  if ($this->_tpl_vars['webmaster_mode'] == 'editor'): ?>
<script type="text/javascript" language="JavaScript 1.2">
<!--
var store_language = "<?php if (( $this->_tpl_vars['usertype'] == 'P' || $this->_tpl_vars['usertype'] == 'A' ) && $this->_tpl_vars['current_language'] != ""):  echo $this->_tpl_vars['current_language'];  else:  echo $this->_tpl_vars['store_language'];  endif; ?>";
var catalogs = new Object();
catalogs.admin = "<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
";
catalogs.provider = "<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
";
catalogs.customer = "<?php echo $this->_tpl_vars['catalogs']['customer']; ?>
";
catalogs.partner = "<?php echo $this->_tpl_vars['catalogs']['partner']; ?>
";
catalogs.images = "<?php echo $this->_tpl_vars['ImagesDir']; ?>
";
catalogs.skin = "<?php echo $this->_tpl_vars['SkinDir']; ?>
";
var lng_labels = [];
<?php $_from = $this->_tpl_vars['webmaster_lng']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lbl_name'] => $this->_tpl_vars['lbl_val']):
?>
lng_labels['<?php echo $this->_tpl_vars['lbl_name']; ?>
'] = '<?php echo $this->_tpl_vars['lbl_val']; ?>
';
<?php endforeach; endif; unset($_from); ?>
var page_charset = "<?php echo ((is_array($_tmp=@$this->_tpl_vars['default_charset'])) ? $this->_run_mod_handler('default', true, $_tmp, "iso-8859-1") : smarty_modifier_default($_tmp, "iso-8859-1")); ?>
";
-->
</script>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/editor_common.js"></script>
<?php if ($this->_tpl_vars['user_agent'] == 'ns'): ?>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/editorns.js"></script>
<?php else: ?>
<script type="text/javascript" language="JavaScript 1.2" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/editor.js"></script>
<?php endif;  endif; ?>