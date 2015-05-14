<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from main/register_billing_address.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'main/register_billing_address.tpl', 203, false),)), $this); ?>
<?php func_load_lang($this, "main/register_billing_address.tpl","lbl_billing_address,lbl_title,lbl_first_name,lbl_last_name,lbl_address,lbl_address_2,lbl_city,lbl_county,lbl_state,lbl_zip_code,lbl_country"); ?><?php if ($this->_tpl_vars['is_areas']['B'] == 'Y'): ?>
<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
<!-- Deleted by Michael de Leon 02.06.07
<tr>
<td colspan="3" class="RegSectionTitle"><hr size="1" noshade="noshade" /></td>
</tr>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
<td align="center" colspan="3">
<br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
</td>
</tr>
<tr>
<td class="wwmp_yourinfo_title2" align="left" colspan="3"><?php echo $this->_tpl_vars['lng']['lbl_billing_address']; ?>
 <font class="wwmp_yourinfo_required">(<font class="wwmp_yourinfo_star">*</font> required)</font></td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
<?php endif; ?>

<?php if ($this->_tpl_vars['action'] == 'cart'): ?>
<tr style="display: none;">
<td>
<input type="hidden" name="action" value="cart" />
<input type="hidden" name="paymentid" value="<?php echo $this->_tpl_vars['paymentid']; ?>
" />
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_title']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_title']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<select name="b_title">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/title_selector.tpl", 'smarty_include_vars' => array('field' => $this->_tpl_vars['userinfo']['b_titleid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_firstname']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_firstname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="text" name="b_firstname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['b_firstname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_firstname'] == "" && $this->_tpl_vars['default_fields']['b_firstname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_lastname']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_lastname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="text" name="b_lastname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['b_lastname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_lastname'] == "" && $this->_tpl_vars['default_fields']['b_lastname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_address']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_address']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_address']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['b_address']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_address'] == "" && $this->_tpl_vars['default_fields']['b_address']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_address_2']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_address_2']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_address_2']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['b_address_2']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_address_2'] == "" && $this->_tpl_vars['default_fields']['b_address_2']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_city']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_city']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_city']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['b_city']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_city'] == "" && $this->_tpl_vars['default_fields']['b_city']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_county']['avail'] == 'Y' && $this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_county']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_county']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/counties.tpl", 'smarty_include_vars' => array('counties' => $this->_tpl_vars['counties'],'name' => 'b_county','default' => $this->_tpl_vars['userinfo']['b_county'],'country_name' => 'b_country')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_county'] == "" && $this->_tpl_vars['default_fields']['b_county']['required'] == 'Y' ) || $this->_tpl_vars['error'] == 'b_county'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_state']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_state']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_state']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/states.tpl", 'smarty_include_vars' => array('states' => $this->_tpl_vars['states'],'name' => 'b_state','default' => $this->_tpl_vars['userinfo']['b_state'],'default_country' => $this->_tpl_vars['userinfo']['b_country'],'country_name' => 'b_country')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['error'] == 'b_statecode' || ( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_state'] == "" && $this->_tpl_vars['default_fields']['b_state']['required'] == 'Y' )): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<!-- Start addition by Michael de Leon 02.06.07 -->
<?php if ($this->_tpl_vars['default_fields']['b_zipcode']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_zip_code']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_zipcode']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['b_zipcode']; ?>
" onchange="check_zip_code()"  />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_zipcode'] == "" && $this->_tpl_vars['default_fields']['b_zipcode']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>
<!-- End addition by Michael de Leon 02.06.07 -->

<?php if ($this->_tpl_vars['default_fields']['b_country']['avail'] == 'Y'): ?>
<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_country']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['b_country']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<select name="b_country" id="b_country" onchange="check_zip_code()">
<?php unset($this->_sections['country_idx']);
$this->_sections['country_idx']['name'] = 'country_idx';
$this->_sections['country_idx']['loop'] = is_array($_loop=$this->_tpl_vars['countries']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['country_idx']['show'] = true;
$this->_sections['country_idx']['max'] = $this->_sections['country_idx']['loop'];
$this->_sections['country_idx']['step'] = 1;
$this->_sections['country_idx']['start'] = $this->_sections['country_idx']['step'] > 0 ? 0 : $this->_sections['country_idx']['loop']-1;
if ($this->_sections['country_idx']['show']) {
    $this->_sections['country_idx']['total'] = $this->_sections['country_idx']['loop'];
    if ($this->_sections['country_idx']['total'] == 0)
        $this->_sections['country_idx']['show'] = false;
} else
    $this->_sections['country_idx']['total'] = 0;
if ($this->_sections['country_idx']['show']):

            for ($this->_sections['country_idx']['index'] = $this->_sections['country_idx']['start'], $this->_sections['country_idx']['iteration'] = 1;
                 $this->_sections['country_idx']['iteration'] <= $this->_sections['country_idx']['total'];
                 $this->_sections['country_idx']['index'] += $this->_sections['country_idx']['step'], $this->_sections['country_idx']['iteration']++):
$this->_sections['country_idx']['rownum'] = $this->_sections['country_idx']['iteration'];
$this->_sections['country_idx']['index_prev'] = $this->_sections['country_idx']['index'] - $this->_sections['country_idx']['step'];
$this->_sections['country_idx']['index_next'] = $this->_sections['country_idx']['index'] + $this->_sections['country_idx']['step'];
$this->_sections['country_idx']['first']      = ($this->_sections['country_idx']['iteration'] == 1);
$this->_sections['country_idx']['last']       = ($this->_sections['country_idx']['iteration'] == $this->_sections['country_idx']['total']);
?>
<option value="<?php echo $this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country_code']; ?>
"<?php if ($this->_tpl_vars['userinfo']['b_country'] == $this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country_code']): ?> selected="selected"<?php elseif ($this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country_code'] == $this->_tpl_vars['config']['General']['default_country'] && $this->_tpl_vars['userinfo']['b_country'] == ""): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
</option>
<?php endfor; endif; ?>
</select>
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['b_country'] == "" && $this->_tpl_vars['default_fields']['b_country']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['b_state']['avail'] == 'Y' && $this->_tpl_vars['default_fields']['b_country']['avail'] == 'Y' && $this->_tpl_vars['js_enabled'] == 'Y' && $this->_tpl_vars['config']['General']['use_js_states'] == 'Y'): ?>
<tr style="display: none;">
	<td>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_states.tpl", 'smarty_include_vars' => array('state_name' => 'b_state','country_name' => 'b_country','county_name' => 'b_county','state_value' => $this->_tpl_vars['userinfo']['b_state'],'county_value' => $this->_tpl_vars['userinfo']['b_county'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
<?php endif; ?>

<!-- Deleted by Michael de Leon 02.06.07
<tr>
<td align="right"></td>
<td><font class="Star">*</font>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="" onchange="check_zip_code()"  />
<font class="Star">&lt;&lt;</font></td>
</tr>
-->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'B')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>