<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:14
         compiled from check_email_script.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'check_email_script.tpl', 4, false),)), $this); ?>
<?php func_load_lang($this, "check_email_script.tpl","txt_email_invalid"); ?><script type="text/javascript" language="JavaScript 1.2">
<!--
var txt_email_invalid = "<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_email_invalid'])) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\r", ' ') : smarty_modifier_replace($_tmp, "\r", ' ')))) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '\"') : smarty_modifier_replace($_tmp, '"', '\"')); ?>
";
-->
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "check_email_script.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>