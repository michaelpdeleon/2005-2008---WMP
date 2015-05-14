<?php /* Smarty version 2.6.12, created on 2014-09-08 19:53:00
         compiled from check_zipcode_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'check_zipcode_js.tpl', 19, false),array('modifier', 'escape', 'check_zipcode_js.tpl', 19, false),)), $this); ?>
<?php func_load_lang($this, "check_zipcode_js.tpl","txt_error_at_zip_code,txt_error_ca_zip_code,txt_error_ch_zip_code,txt_error_de_zip_code,txt_error_lu_zip_code,txt_error_us_zip_code"); ?><script type="text/javascript" language="JavaScript 1.2">
<!--
var config_default_country = "<?php echo $this->_tpl_vars['config']['General']['default_country']; ?>
";

// used in check_zip_code_field() from check_zipcode.js
// note: you should update language variables after change this table
<?php echo '
var check_zip_code_rules = {
	AT: { lens:{4:true} },
	CA: { lens:{6:true,7:true} },
	CH: { lens:{4:true} },
	DE: { lens:{5:true} },
	LU: { lens:{4:true} },
	US: { lens:{5:true}, re:/\\D/ }
};
'; ?>


check_zip_code_rules.AT.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_at_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.CA.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_ca_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.CH.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_ch_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.DE.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_de_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.LU.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_lu_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
check_zip_code_rules.US.error='<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['txt_error_us_zip_code'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';

-->
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "check_zipcode.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
