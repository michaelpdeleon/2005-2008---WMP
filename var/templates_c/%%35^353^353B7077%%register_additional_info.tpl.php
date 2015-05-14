<?php /* Smarty version 2.6.12, created on 2014-09-07 21:51:37
         compiled from main/register_additional_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/register_additional_info.tpl', 11, false),array('modifier', 'escape', 'main/register_additional_info.tpl', 15, false),)), $this); ?>
<?php func_load_lang($this, "main/register_additional_info.tpl","lbl_additional_information"); ?><?php if ($this->_tpl_vars['section'] != '' && $this->_tpl_vars['additional_fields'] != '' && ( ( $this->_tpl_vars['is_areas']['A'] == 'Y' && $this->_tpl_vars['section'] == 'A' ) || $this->_tpl_vars['section'] != 'A' )):  if ($this->_tpl_vars['hide_header'] == "" && $this->_tpl_vars['section'] == 'A'): ?>
<tr>
<td height="20" colspan="3"><font class="RegSectionTitle"><?php echo $this->_tpl_vars['lng']['lbl_additional_information']; ?>
</font><hr size="1" noshade="noshade" /></td>
</tr>
<?php endif;  $_from = $this->_tpl_vars['additional_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
 if ($this->_tpl_vars['section'] == $this->_tpl_vars['v']['section'] && $this->_tpl_vars['v']['avail'] == 'Y'): ?>
<tr>
<td align="right"><?php echo ((is_array($_tmp=@$this->_tpl_vars['v']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['v']['field']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['v']['field'])); ?>
</td>
<td><?php if ($this->_tpl_vars['v']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td nowrap="nowrap">
<?php if ($this->_tpl_vars['v']['type'] == 'T'): ?>
<input type="text" name="additional_values[<?php echo $this->_tpl_vars['v']['fieldid']; ?>
]" id="additional_values_<?php echo $this->_tpl_vars['v']['fieldid']; ?>
" size="32" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<?php elseif ($this->_tpl_vars['v']['type'] == 'C'): ?>
<input type="checkbox" name="additional_values[<?php echo $this->_tpl_vars['v']['fieldid']; ?>
]" id="additional_values_<?php echo $this->_tpl_vars['v']['fieldid']; ?>
" value="Y"<?php if ($this->_tpl_vars['v']['value'] == 'Y'): ?> checked="checked"<?php endif; ?> />
<?php elseif ($this->_tpl_vars['v']['type'] == 'S'): ?>
<select name="additional_values[<?php echo $this->_tpl_vars['v']['fieldid']; ?>
]" id="additional_values_<?php echo $this->_tpl_vars['v']['fieldid']; ?>
">
<?php $_from = $this->_tpl_vars['v']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['o']):
?>
<option value='<?php echo ((is_array($_tmp=$this->_tpl_vars['o'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
'<?php if ($this->_tpl_vars['v']['value'] == $this->_tpl_vars['o']): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['o'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>
<?php endif;  if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['v']['value'] == "" && $this->_tpl_vars['v']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif;  endforeach; endif; unset($_from);  endif; ?>