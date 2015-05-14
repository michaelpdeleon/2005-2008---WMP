<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from main/register_shipping_address.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/register_shipping_address.tpl', 137, false),)), $this); ?>
<?php func_load_lang($this, "main/register_shipping_address.tpl","lbl_ship_to_different_address,lbl_title,lbl_first_name,lbl_last_name,lbl_address,lbl_address_2,lbl_city,lbl_county,lbl_state,lbl_zip_code,lbl_country"); ?><?php if ($this->_tpl_vars['is_areas']['S'] == 'Y'): ?>
<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
<tr>
	<td height="20" colspan="3" align="center">
<script type="text/javascript">
<!--
<?php echo '
function ship2diffOpen() {
	var obj = document.getElementById(\'ship2diff\');
	var box = document.getElementById(\'ship_box\');
	if (!obj || !box)
		return;

	box.style.display = obj.checked ? "" : "none";
	if (obj.checked && window.start_js_states && document.getElementById(\'s_country\') && localBFamily == \'Opera\')
		start_js_states(document.getElementById(\'s_country\'));
}
'; ?>

-->
</script>
	
	<br />
	<!-- Deleted by Michael de Leon 02.16.07
	<table cellpadding="0" cellspacing="0">
	-->
	<!-- Start addition by Michael de Leon 02.16.07 -->
	<table cellpadding="0" cellspacing="0" align="center">
	<!-- End addition by Michael de Leon 02.16.07 -->
	<tr>
	<!-- Deleted by Michael de Leon 02.16.07
		<td><label for="ship2diff" class="RegSectionTitle"></label></td>
		<td>&nbsp;</td>
		<td><input type="checkbox" id="ship2diff" name="ship2diff" value="Y" onclick="javascript: ship2diffOpen();" checked="checked" /></td>
	-->
	<!-- Start addition by Michael de Leon 02.16.07 -->
		<td><input type="checkbox" id="ship2diff" name="ship2diff" value="Y" onclick="javascript: ship2diffOpen();"<?php if ($this->_tpl_vars['ship2diff']): ?> checked="checked"<?php endif; ?> /></td>
		<td>&nbsp;</td>
		<td><label for="ship2diff" class="wwmp_yourinfo_label"><?php echo $this->_tpl_vars['lng']['lbl_ship_to_different_address']; ?>
</label></td>
	<!-- End addition by Michael de Leon 02.16.07 -->
		
	</tr>
	</table>
	<!-- Deleted by Michael de Leon 11.16.06
	<hr size="1" noshade="noshade" />
	<br />
	-->
	</td>
</tr>
<?php endif; ?>

</tbody>
<tbody id="ship_box"<?php if (! $this->_tpl_vars['ship2diff']): ?> style="display: none;"<?php endif; ?>>
<?php if ($this->_tpl_vars['default_fields']['s_title']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_title']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left"> 
<select name="s_title">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/title_selector.tpl", 'smarty_include_vars' => array('field' => $this->_tpl_vars['userinfo']['s_titleid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select> 
</td> 
</tr> 
 <?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_firstname']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_first_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_firstname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left"> 
<input type="text" id="s_firstname" name="s_firstname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['s_firstname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_firstname'] == "" && $this->_tpl_vars['default_fields']['s_firstname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
 <?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_lastname']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_last_name']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_lastname']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="s_lastname" name="s_lastname" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['s_lastname']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_lastname'] == "" && $this->_tpl_vars['default_fields']['s_lastname']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_address']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_address']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_address']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="s_address" name="s_address" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['s_address']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_address'] == "" && $this->_tpl_vars['default_fields']['s_address']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_address_2']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_address_2']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_address_2']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="s_address_2" name="s_address_2" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['s_address_2']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_address_2'] == "" && $this->_tpl_vars['default_fields']['s_address_2']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_city']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_city']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_city']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="s_city" name="s_city" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['userinfo']['s_city']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_city'] == "" && $this->_tpl_vars['default_fields']['s_city']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_county']['avail'] == 'Y' && $this->_tpl_vars['config']['General']['use_counties'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_county']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_county']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/counties.tpl", 'smarty_include_vars' => array('counties' => $this->_tpl_vars['counties'],'name' => 's_county','default' => $this->_tpl_vars['userinfo']['s_county'],'country_name' => 's_country')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_county'] == "" && $this->_tpl_vars['default_fields']['s_county']['required'] == 'Y' ) || $this->_tpl_vars['error'] == 's_county'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_state']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_state']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_state']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/states.tpl", 'smarty_include_vars' => array('states' => $this->_tpl_vars['states'],'name' => 's_state','default' => ((is_array($_tmp=@$this->_tpl_vars['userinfo']['s_state'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_state']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_state'])),'default_country' => ((is_array($_tmp=@$this->_tpl_vars['userinfo']['s_country'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['config']['General']['default_country']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['config']['General']['default_country'])),'country_name' => 's_country')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_state'] == "" && $this->_tpl_vars['default_fields']['s_state']['required'] == 'Y' ) || $this->_tpl_vars['error'] == 's_statecode'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_zipcode']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_zip_code']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_zipcode']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<input type="text" id="s_zipcode" name="s_zipcode" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['s_zipcode']; ?>
" onchange="check_zip_code()" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_zipcode'] == "" && $this->_tpl_vars['default_fields']['s_zipcode']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_country']['avail'] == 'Y'): ?>
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_country']; ?>
</td>
<td align="left"><?php if ($this->_tpl_vars['default_fields']['s_country']['required'] == 'Y'): ?><font class="Star">*</font><?php else: ?>&nbsp;<?php endif; ?></td>
<td align="left">
<select name="s_country" id="s_country" size="1" onchange="check_zip_code()">
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
"<?php if ($this->_tpl_vars['userinfo']['s_country'] == $this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country_code']): ?> selected="selected"<?php elseif ($this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country_code'] == $this->_tpl_vars['config']['General']['default_country'] && $this->_tpl_vars['userinfo']['s_country'] == ""): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['countries'][$this->_sections['country_idx']['index']]['country']; ?>
</option>
<?php endfor; endif; ?>
</select>
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['s_country'] == "" && $this->_tpl_vars['default_fields']['s_country']['required'] == 'Y'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_fields']['s_state']['avail'] == 'Y' && $this->_tpl_vars['default_fields']['s_country']['avail'] == 'Y' && $this->_tpl_vars['js_enabled'] == 'Y' && $this->_tpl_vars['config']['General']['use_js_states'] == 'Y'): ?>
<tr style="display: none;">
	<td align="left">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_states.tpl", 'smarty_include_vars' => array('state_name' => 's_state','country_name' => 's_country','county_name' => 's_county','state_value' => $this->_tpl_vars['userinfo']['s_state'],'county_value' => $this->_tpl_vars['userinfo']['s_county'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'S')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</tbody>
<tbody>
<?php endif; ?>