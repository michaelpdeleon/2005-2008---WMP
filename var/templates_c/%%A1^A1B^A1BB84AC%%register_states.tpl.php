<?php /* Smarty version 2.6.12, created on 2014-09-07 21:51:36
         compiled from main/register_states.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'main/register_states.tpl', 5, false),)), $this); ?>
<?php if ($this->_tpl_vars['country_id'] == ''):  $this->assign('country_id', $this->_tpl_vars['country_name']);  endif; ?>
<script type="text/javascript" language="JavaScript 1.2">
<!--
init_js_states(document.getElementById('<?php echo $this->_tpl_vars['country_id']; ?>
'), '<?php echo $this->_tpl_vars['state_name']; ?>
', '<?php echo $this->_tpl_vars['county_name']; ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['state_value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['county_value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
');
-->
</script>
