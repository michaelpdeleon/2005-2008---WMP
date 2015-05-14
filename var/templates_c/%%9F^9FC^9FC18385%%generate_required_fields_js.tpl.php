<?php /* Smarty version 2.6.12, created on 2014-10-01 08:25:17
         compiled from generate_required_fields_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip', 'generate_required_fields_js.tpl', 7, false),array('modifier', 'replace', 'generate_required_fields_js.tpl', 7, false),)), $this); ?>
<?php func_load_lang($this, "generate_required_fields_js.tpl","lbl_username,lbl_password,lbl_confirm_password"); ?><script type="text/javascript" language="JavaScript 1.2">
<!--
var requiredFields = [
<?php $_from = $this->_tpl_vars['default_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
 if ($this->_tpl_vars['v']['required'] == 'Y' && $this->_tpl_vars['v']['avail'] == 'Y'): ?>
	["<?php echo $this->_tpl_vars['k']; ?>
", "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['title'])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
"],
<?php endif;  endforeach; endif; unset($_from);  $_from = $this->_tpl_vars['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
 if ($this->_tpl_vars['v']['required'] == 'Y' && $this->_tpl_vars['v']['type'] == 'T' && $this->_tpl_vars['v']['avail'] == 'Y'): ?> 
	["additional_values_<?php echo $this->_tpl_vars['v']['fieldid']; ?>
", "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['title'])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
"],
<?php endif; ?> 
<?php endforeach; endif; unset($_from);  if ($this->_tpl_vars['anonymous'] == "" || $this->_tpl_vars['config']['General']['disable_anonymous_checkout'] == 'Y'): ?>
	["uname", "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_username'])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
"],
	["passwd1", "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_password'])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
"],
	["passwd2", "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_confirm_password'])) ? $this->_run_mod_handler('strip', true, $_tmp) : smarty_modifier_strip($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
"],
<?php endif; ?>
];
-->
</script>