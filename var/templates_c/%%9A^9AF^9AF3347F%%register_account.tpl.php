<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from main/register_account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'main/register_account.tpl', 144, false),)), $this); ?>
<?php func_load_lang($this, "main/register_account.tpl","lbl_username,lbl_password,lbl_confirm_password,lbl_username,lbl_password,lbl_confirm_password,lbl_account_status,lbl_account_status_suspended,lbl_account_status_enabled,lbl_account_status_not_approved,lbl_account_status_declined,lbl_account_activity,lbl_account_activity_enabled,lbl_account_activity_disabled,lbl_reg_chpass"); ?><?php if ($this->_tpl_vars['hide_account_section'] != 'Y'): ?>

<?php if ($this->_tpl_vars['hide_header'] == ""): ?>
<!-- Deleted by Michael de Leon 02.06.07
<tr>
<td colspan="3" class="RegSectionTitle"><hr size="1" noshade="noshade" /></td>
</tr>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
<td align="center" colspan="3">
<!-- Start addition by Michael de Leon 11.16.06 -->
<br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<!-- End addition by Michael de Leon 11.16.06 --></td>
</tr>
<tr>
<td class="wwmp_yourinfo_title" align="left" colspan="3">Login Information 
<?php if ($this->_tpl_vars['anonymous'] != "" && $this->_tpl_vars['config']['General']['disable_anonymous_checkout'] != 'Y'): ?>
<font class="wwmp_yourinfo_required">(if you want to create an account fill out the fields below)</font>
<?php else: ?>
<font class="wwmp_yourinfo_required">(<font class="wwmp_yourinfo_star">*</font> required)</font>
<?php endif; ?>
</td>
</tr>
<tr>
<td class="wwmp_yourinfo_notice" align="center" colspan="3">Passwords must be 5 or more characters long and different from your username.</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
<?php endif; ?>

<!-- Deleted by Michael de Leon 02.06.07


<tr>
<td colspan="3"></td>
</tr>

-->

<!-- Deleted by Michael de Leon 02.06.07


<tr style="display: none;">
<td>
<input type="hidden" name="membershipid" value="" />
<input type="hidden" name="pending_membershipid" value="" />
</td>
</tr>





-->

<?php if ($this->_tpl_vars['anonymous'] != "" && $this->_tpl_vars['config']['General']['disable_anonymous_checkout'] != 'Y'): ?>


<!-- Deleted by Michael de Leon 02.06.07
<tr>
<td align="right"></td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" id="uname" name="uname" size="32" maxlength="32" value="" />
<font class="Star">&lt;&lt;</font></td>
</tr>

<tr>
<td align="right"></td>
<td>&nbsp;</td>
<td nowrap="nowrap"><input type="password" name="passwd1" size="32" maxlength="64" value="" />
</td>
</tr>

<tr>
<td align="right"></td>
<td>&nbsp;</td>
<td nowrap="nowrap"><input type="password" name="passwd2" size="32" maxlength="64" value="" />
</td>
</tr>
-->

<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_username']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left">
<input type="text" id="uname" name="uname" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['userinfo']['uname']):  echo $this->_tpl_vars['userinfo']['uname'];  else:  echo $this->_tpl_vars['userinfo']['login'];  endif; ?>" />
<?php if (( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['uname'] == "" && $this->_tpl_vars['userinfo']['login'] == "" ) || $this->_tpl_vars['reg_error'] == 'U'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
</td>
</tr>

<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left"><input type="password" name="passwd1" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['passwd1']; ?>
" />
</td>
</tr>

<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_confirm_password']; ?>
</td>
<td align="left">&nbsp;</td>
<td align="left"><input type="password" name="passwd2" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['passwd2']; ?>
" />
</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->


<?php else: ?>


<tr>
<!-- Deleted by Michael de Leon 02.06.07
<td align="right"></td>
<td class="Star">*</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_username']; ?>
</td>
<td class="Star" align="left">*</td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<?php if ($this->_tpl_vars['userinfo']['login'] != "" || ( $this->_tpl_vars['login'] == $this->_tpl_vars['userinfo']['uname'] && $this->_tpl_vars['login'] != '' )): ?>
<b><?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])); ?>
</b>
<input type="hidden" name="uname" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])); ?>
" />
<?php else: ?>
<input type="text" id="uname" name="uname" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['userinfo']['uname']):  echo $this->_tpl_vars['userinfo']['uname'];  else:  echo $this->_tpl_vars['userinfo']['login'];  endif; ?>" />
<?php if (( $this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['uname'] == "" && $this->_tpl_vars['userinfo']['login'] == "" ) || $this->_tpl_vars['reg_error'] == 'U'): ?><font class="Star">&lt;&lt;</font><?php endif; ?>
<?php endif; ?>
</td>
</tr>

<!-- Deleted by Michael de Leon 02.06.07
<tr>
<td align="right"></td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap"><input type="password" id="passwd1" name="passwd1" size="32" maxlength="64" value="" />
<font class="Star">&lt;&lt;</font> 
</td>
</tr>

<tr>
<td align="right"></td>
<td class="Star">*</td>
<td nowrap="nowrap"><input type="password" id="passwd2" name="passwd2" size="32" maxlength="64" value="" />
<font class="Star">&lt;&lt;</font> 
</td>
</tr>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</td>
<td align="left"><font class="Star">*</font></td>
<td align="left"><input type="password" id="passwd1" name="passwd1" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['passwd1']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['passwd1'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?> 
</td>
</tr>

<tr>
<td class="wwmp_yourinfo_label" align="right"><?php echo $this->_tpl_vars['lng']['lbl_confirm_password']; ?>
</td>
<td class="Star" align="left">*</td>
<td align="left"><input type="password" id="passwd2" name="passwd2" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['userinfo']['passwd2']; ?>
" />
<?php if ($this->_tpl_vars['reg_error'] != "" && $this->_tpl_vars['userinfo']['passwd2'] == ""): ?><font class="Star">&lt;&lt;</font><?php endif; ?> 
</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->


<?php endif; ?>

<?php if (( ( $this->_tpl_vars['active_modules']['Simple_Mode'] != "" && $this->_tpl_vars['usertype'] == 'P' ) || $this->_tpl_vars['usertype'] == 'A' ) && ( $this->_tpl_vars['userinfo']['uname'] && $this->_tpl_vars['userinfo']['uname'] != $this->_tpl_vars['login'] || ! $this->_tpl_vars['userinfo']['uname'] && $this->_tpl_vars['userinfo']['login'] != $this->_tpl_vars['login'] )): ?>

<?php if ($this->_tpl_vars['userinfo']['status'] != 'A'): ?><tr valign="middle">
<td align="right"><?php echo $this->_tpl_vars['lng']['lbl_account_status']; ?>
:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<select name="status">
<option value="N"<?php if ($this->_tpl_vars['userinfo']['status'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_suspended']; ?>
</option>
<option value="Y"<?php if ($this->_tpl_vars['userinfo']['status'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_enabled']; ?>
</option>
<?php if ($this->_tpl_vars['active_modules']['XAffiliate'] != "" && ( $this->_tpl_vars['userinfo']['usertype'] == 'B' || $GLOBALS['HTTP_GET_VARS']['usertype'] == 'B' )): ?>
<option value="Q"<?php if ($this->_tpl_vars['userinfo']['status'] == 'Q'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_not_approved']; ?>
</option>
<option value="D"<?php if ($this->_tpl_vars['userinfo']['status'] == 'D'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_status_declined']; ?>
</option>
<?php endif; ?>
</select>
</td>
</tr>

<?php if ($this->_tpl_vars['display_activity_box'] == 'Y'): ?>
<tr valign="middle">
<td align="right"><?php echo $this->_tpl_vars['lng']['lbl_account_activity']; ?>
:</td>
<!-- Deleted by Michael de Leon 02.06.07
<td>&nbsp;</td>
<td nowrap="nowrap">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td align="left">&nbsp;</td>
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<select name="activity">
<option value="Y"<?php if ($this->_tpl_vars['userinfo']['activity'] == 'Y'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_activity_enabled']; ?>
</option>
<option value="N"<?php if ($this->_tpl_vars['userinfo']['activity'] == 'N'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lng']['lbl_account_activity_disabled']; ?>
</option>
</select>
</td>
</tr>
<?php endif; ?>

<?php endif; ?>
<tr valign="middle">
	<td colspan="2">&nbsp;</td>
	<td nowrap="nowrap">

<table>
<tr>
	<!-- Deleted by Michael de Leon 02.06.07
	<td><input type="checkbox" id="change_password" name="change_password" value="Y" checked="checked" /></td>
	<td><label for="change_password"></label></td>
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td align="left"><input type="checkbox" id="change_password" name="change_password" value="Y"<?php if ($this->_tpl_vars['userinfo']['change_password'] == 'Y'): ?> checked="checked"<?php endif; ?> /></td>
	<td align="left"><label for="change_password"><?php echo $this->_tpl_vars['lng']['lbl_reg_chpass']; ?>
</label></td>
	<!-- End addition by Michael de Leon 02.06.07 -->
</tr>
</table>

	</td>
</tr>

<?php endif; ?>

<?php else: ?>
<tr style="display: none;">
<!-- Deleted by Michael de Leon 02.06.07
<td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td align="left">
<!-- End addition by Michael de Leon 02.06.07 -->
<input type="hidden" name="uname" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['userinfo']['login'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['userinfo']['uname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['userinfo']['uname'])); ?>
" />
<input type="hidden" name="passwd1" value="<?php echo $this->_tpl_vars['userinfo']['passwd1']; ?>
" />
<input type="hidden" name="passwd2" value="<?php echo $this->_tpl_vars['userinfo']['passwd2']; ?>
" />
</td>
</tr>
<?php endif; ?>