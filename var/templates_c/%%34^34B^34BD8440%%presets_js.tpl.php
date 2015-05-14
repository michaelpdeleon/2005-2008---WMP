<?php /* Smarty version 2.6.12, created on 2015-05-14 06:59:20
         compiled from presets_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'presets_js.tpl', 8, false),)), $this); ?>
<?php func_load_lang($this, "presets_js.tpl","lbl_no_items_have_been_selected"); ?><script type="text/javascript">
<!--
var number_format_dec = '<?php echo $this->_tpl_vars['number_format_dec']; ?>
';
var number_format_th = '<?php echo $this->_tpl_vars['number_format_th']; ?>
';
var number_format_point = '<?php echo $this->_tpl_vars['number_format_point']; ?>
';
var store_language = '<?php echo $this->_tpl_vars['store_language']; ?>
';
var xcart_web_dir = "<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
";
var lbl_no_items_have_been_selected = '<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_no_items_have_been_selected'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "\'") : smarty_modifier_replace($_tmp, "'", "\'")); ?>
';
var current_area = '<?php echo $this->_tpl_vars['usertype']; ?>
';
-->
</script>