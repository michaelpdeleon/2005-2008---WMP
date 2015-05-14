<?php /* Smarty version 2.6.12, created on 2014-09-07 21:32:10
         compiled from dialog.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'dialog.tpl', 11, false),)), $this); ?>
<?php if ($this->_tpl_vars['printable'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_printable.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" <?php echo $this->_tpl_vars['extra']; ?>
>
<tr> 
<td class="DialogTitle"><?php echo $this->_tpl_vars['title']; ?>
</td>
</tr>
<tr><td class="DialogBorder"><table cellspacing="1" class="DialogBox" cellpadding="10" width="100%" border="0">
<tr><td class="DialogBox" valign="<?php echo ((is_array($_tmp=@$this->_tpl_vars['valign'])) ? $this->_run_mod_handler('default', true, $_tmp, 'top') : smarty_modifier_default($_tmp, 'top')); ?>
"><?php echo $this->_tpl_vars['content']; ?>

&nbsp;
</td></tr>
</table></td></tr>
</table>
<br>
<!-- End edit by Michael de Leon 09.15.06 -->
<?php endif; ?>