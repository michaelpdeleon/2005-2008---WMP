<?php /* Smarty version 2.6.12, created on 2014-10-11 21:14:10
         compiled from location.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'location.tpl', 13, false),array('modifier', 'amp', 'location.tpl', 28, false),)), $this); ?>
<!--
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
-->
<?php if ($this->_tpl_vars['location']): ?>
<font class="NavigationPath">
<!-- Start addition by Michael de Leon 09.14.06 -->
<?php if ($this->_tpl_vars['cat'] != '0' || $this->_tpl_vars['cat'] != ""): ?>
	<div align="center">
	<?php $this->assign('subcat_rootparent', ((is_array($_tmp=$this->_tpl_vars['current_category']['categoryid_path'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 3, "", true) : smarty_modifier_truncate($_tmp, 3, "", true))); ?>
	<?php if ($this->_tpl_vars['subcat_rootparent'] != ""): ?>
		<img src="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/image.php?type=C&amp;id=<?php echo $this->_tpl_vars['subcat_rootparent']; ?>
" border="0">
<!-- Deleted by Michael de Leon 11.02.06
<img src="/image.php?categoryid=" border="0">
-->
	<?php else: ?>
		<IMG src="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/image.php?categoryid=<?php echo $this->_tpl_vars['cat']; ?>
&rand=<?php echo $this->_tpl_vars['rand'];  if ($this->_tpl_vars['file_upload_data']['file_path']): ?>&tmp=y<?php endif; ?>" border="0">
	<?php endif; ?>
	</div>
<?php endif; ?>
<!-- End addition by Michael de Leon 09.14.06 -->
<!-- Deleted by Michael de Leon 09.14.06
<a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['location'][$this->_sections['position']['index']]['1'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" class="NavigationPath"></a>&nbsp;::&nbsp;-->
</font>
<!-- Deleted by Michael de Leon 09.14.06
<br /><br />
-->
<?php endif; ?>