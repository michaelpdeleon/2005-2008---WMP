<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from main/register_contact_info.tpl */ ?>
<?php func_load_lang($this, "main/register_contact_info.tpl","lbl_contact_information,lbl_phone,lbl_email,lbl_fax,lbl_web_site"); ?><?php if ($this->_tpl_vars['is_areas']['C'] == 'Y'): ?>
<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
<tr>
<td align="center" colspan="3">
<!-- Start addition by Michael de Leon 11.16.06 -->
<br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<!-- End addition by Michael de Leon 11.16.06 --></td>
</tr>
<tr>
<td class="wwmp_yourinfo_title2" align="left" colspan="3"><?php echo $this->_tpl_vars['lng']['lbl_contact_information']; ?>
 <font class="wwmp_yourinfo_required">(<font class="wwmp_yourinfo_star">*</font> required)</font></td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['phone']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_phone']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['phone']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="phone" name="phone" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['phone']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['phone'] == "" && $this->_tpl_vars['default_fields']['phone']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['email']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_email']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['email']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="email" name="email" size="32" maxlength="128" value="<?php echo $this->_tpl_vars['userinfo']['email']; ?>
" />
<?php if ($this->_tpl_vars['emailerror'] != "" || ( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['email'] == "" && $this->_tpl_vars['default_fields']['email']['required'] == 'Y' )): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['fax']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_fax']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['fax']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="fax" name="fax" size="32" maxlength="128" value="<?php echo $this->_tpl_vars['userinfo']['fax']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['fax'] == "" && $this->_tpl_vars['default_fields']['fax']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['url']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_web_site']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['url']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="url" name="url" size="32" maxlength="128" value="<?php echo $this->_tpl_vars['userinfo']['url']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['url'] == "" && $this->_tpl_vars['default_fields']['url']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'C')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
