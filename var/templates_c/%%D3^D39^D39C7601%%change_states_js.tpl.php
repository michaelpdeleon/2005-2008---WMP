<?php /* Smarty version 2.6.12, created on 2014-09-08 04:59:45
         compiled from change_states_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'change_states_js.tpl', 4, false),array('modifier', 'replace', 'change_states_js.tpl', 18, false),)), $this); ?>
<?php func_load_lang($this, "change_states_js.tpl","lbl_country_doesnt_require_state,lbl_country_doesnt_require_county"); ?><script type="text/javascript" language="JavaScript 1.2">
<!--
var txt_no_states = "<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_country_doesnt_require_state'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var txt_no_counties = "<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_country_doesnt_require_county'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
";
var use_counties = <?php if ($this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?>true<?php else: ?>false<?php endif; ?>;
var states_sort_override = <?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Opera'): ?>true<?php else: ?>false<?php endif; ?>;

var countries = {};
<?php $this->assign('cnt', 0);  $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
 = {states: <?php if ($this->_tpl_vars['v']['display_states'] == 'Y'): ?>[]<?php else: ?>false<?php endif; ?>};
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['states'] != ''): ?>
var i = 0;
<?php $_from = $this->_tpl_vars['states']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
.states[<?php echo $this->_tpl_vars['v']['stateid']; ?>
] = {code: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['state_code'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
", name: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['state'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
", counties: []<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Opera'): ?>, order: i++<?php endif; ?>};
<?php endforeach; endif; unset($_from);  endif; ?>

<?php if ($this->_tpl_vars['config']['General']['use_counties'] == 'Y' && $this->_tpl_vars['counties'] != ''): ?>
i = 0;
<?php $_from = $this->_tpl_vars['counties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
countries.<?php echo $this->_tpl_vars['v']['country_code']; ?>
.states[<?php echo $this->_tpl_vars['v']['stateid']; ?>
].counties[<?php echo $this->_tpl_vars['v']['countyid']; ?>
] = {name: "<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['v']['county'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')))) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", ' ') : smarty_modifier_replace($_tmp, "\n", ' ')); ?>
"<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'Opera'): ?>, order: i++<?php endif; ?>};
<?php endforeach; endif; unset($_from);  endif; ?>

-->
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "change_states.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
