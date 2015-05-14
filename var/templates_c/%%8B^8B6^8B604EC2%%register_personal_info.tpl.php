<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from main/register_personal_info.tpl */ ?>
<?php func_load_lang($this, "main/register_personal_info.tpl","lbl_personal_information,lbl_title,lbl_first_name,lbl_last_name,lbl_company,lbl_ssn,lbl_tax_number,lbl_tax_exemption,txt_tax_exemption_assigned,lbl_referred_by,lbl_unknown"); ?><?php if ($this->_tpl_vars['is_areas']['P'] == 'Y'): ?>
<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
<tr>
<td class="wwmp_yourinfo_notice" align="left" colspan="3">All information will kept strictly confidential. We will never sell, share, rent, or lease your information to other parties in any way.</td>
</tr>
<tr>
<td align="center" colspan="3">
<!-- Start addition by Michael de Leon 11.16.06 -->
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<!-- End addition by Michael de Leon 11.16.06 --></td>
</tr>
<tr>
<td class="wwmp_yourinfo_title" align="left" colspan="3"><?php echo $this->_tpl_vars['lng']['lbl_personal_information']; ?>
 <font class="wwmp_yourinfo_required">(<font class="wwmp_yourinfo_star">*</font> required)</font></td>
</tr>
<tr>
<td class="wwmp_yourinfo_notice" align="center" colspan="3">Email address must be valid for communication purposes (invoices, order notifications, etc.) only.</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['title']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['title']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<select name="title" id="title">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/title_selector.tpl", 'smarty_include_vars' => array('field' => $this->_tpl_vars['userinfo']['titleid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['firstname']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['firstname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="firstname" name="firstname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['firstname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['firstname'] == "" && $this->_tpl_vars['default_fields']['firstname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['lastname']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['lastname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="lastname" name="lastname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['lastname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['lastname'] == "" && $this->_tpl_vars['default_fields']['lastname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['company']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_company']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['company']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="company" name="company" size="32" maxlength="255" value="<?php echo $this->_tpl_vars['userinfo']['company']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['company'] == "" && $this->_tpl_vars['default_fields']['company']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['ssn']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_ssn']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['ssn']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="ssn" name="ssn" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['ssn']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['ssn'] == "" && $this->_tpl_vars['default_fields']['ssn']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['default_fields']['tax_number']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_tax_number']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['tax_number']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<?php if ($this->_tpl_vars['userinfo']['tax_exempt'] != 'Y' || $this->_tpl_vars['config']['Taxes']['allow_user_modify_tax_number'] == 'Y' || $this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?>
<input type="text" id="tax_number" name="tax_number" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['tax_number']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['tax_number'] == "" && $this->_tpl_vars['default_fields']['tax_number']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
<?php else: ?>
<?php echo $this->_tpl_vars['userinfo']['tax_number']; ?>

<?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['config']['Taxes']['enable_user_tax_exemption'] == 'Y'): ?>
<?php if (( ( $this->_tpl_vars['userinfo']['usertype'] == 'C' || $GLOBALS['HTTP_GET_VARS']['usertype'] == 'C' ) && $this->_tpl_vars['userinfo']['tax_exempt'] == 'Y' ) || ( $this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P' )): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_tax_exemption']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left">
<?php if ($this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?> 
<input type="checkbox" id="tax_exempt" name="tax_exempt" value="Y"<?php if ($this->_tpl_vars['userinfo']['tax_exempt'] == 'Y'): ?> checked="checked"<?php endif; ?> />
<?php elseif ($this->_tpl_vars['userinfo']['tax_exempt'] == 'Y'): ?>
<?php echo $this->_tpl_vars['lng']['txt_tax_exemption_assigned']; ?>

<?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['usertype'] == 'A' || $this->_tpl_vars['usertype'] == 'P'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_referred_by']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left">
<?php if ($this->_tpl_vars['userinfo']['referer']): ?>
<a href="<?php echo $this->_tpl_vars['userinfo']['referer']; ?>
"><?php echo $this->_tpl_vars['userinfo']['referer']; ?>
</a>
<?php else: ?>
<?php echo $this->_tpl_vars['lng']['lbl_unknown']; ?>

<?php endif; ?>
</td>
</tr>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'P')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>