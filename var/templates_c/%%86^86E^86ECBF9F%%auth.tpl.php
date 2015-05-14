<?php /* Smarty version 2.6.12, created on 2015-04-02 08:17:34
         compiled from auth.tpl */ ?>
<?php func_load_lang($this, "auth.tpl","lbl_username,lbl_password,lbl_insecure_login,lbl_authentication"); ?><?php ob_start(); ?>
<?php if ($this->_tpl_vars['config']['Security']['use_https_login'] == 'Y'): ?>
<?php $this->assign('form_url', $this->_tpl_vars['https_location']); ?>
<?php else: ?>
<?php $this->assign('form_url', $this->_tpl_vars['current_location']); ?>
<?php endif; ?>
<form action="<?php echo $this->_tpl_vars['form_url']; ?>
/include/login.php" method="post" name="authform">
<input type="hidden" name="<?php echo $this->_tpl_vars['XCARTSESSNAME']; ?>
" value="<?php echo $this->_tpl_vars['XCARTSESSID']; ?>
" />
<table cellpadding="0" cellspacing="0" width="100%">
<?php if ($this->_tpl_vars['config']['Security']['use_secure_login_page'] == 'Y'): ?> <tr>
<td>
<?php $this->assign('slogin_url_add', ""); ?>
<?php if ($this->_tpl_vars['usertype'] == 'C'): ?>
<?php $this->assign('slogin_url', $this->_tpl_vars['catalogs_secure']['customer']); ?>
<?php if ($this->_tpl_vars['catalogs_secure']['customer'] != $this->_tpl_vars['catalogs']['customer']): ?>
<?php $this->assign('slogin_url_add', "?".($this->_tpl_vars['XCARTSESSNAME'])."=".($this->_tpl_vars['XCARTSESSID'])); ?>
<?php endif; ?>
<?php elseif ($this->_tpl_vars['usertype'] == 'P' && $this->_tpl_vars['active_modules']['Simple_Mode'] == 'Y' || $this->_tpl_vars['usertype'] == 'A'): ?>
<?php $this->assign('slogin_url', $this->_tpl_vars['catalogs_secure']['admin']); ?>
<?php elseif ($this->_tpl_vars['usertype'] == 'P'): ?>
<?php $this->assign('slogin_url', $this->_tpl_vars['catalogs_secure']['provider']); ?>
<?php elseif ($this->_tpl_vars['usertype'] == 'B'): ?>
<?php $this->assign('slogin_url', $this->_tpl_vars['catalogs_secure']['partner']); ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/secure_login.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</td>
</tr>
<?php else: ?> <tr>
<td class="VertMenuItems">
<!-- Deleted by Michael de Leon 10.31.06
<font class="VertMenuItems"></font><br />
-->
<!-- Start addition by Michael de Leon 10.31.06 -->
<font class="wwmp_loginlabel"><?php echo $this->_tpl_vars['lng']['lbl_username']; ?>
</font><br />
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="text" name="username" size="16" value="<?php echo $this->_config[0]['vars']['default_login']; ?>
" /><br />
<!-- Deleted by Michael de Leon 10.31.06
<font class="VertMenuItems"></font><br />
-->
<!-- Start addition by Michael de Leon 10.31.06 -->
<font class="wwmp_loginlabel"><?php echo $this->_tpl_vars['lng']['lbl_password']; ?>
</font><br />
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="password" name="password" size="16" value="<?php echo $this->_config[0]['vars']['default_password']; ?>
" />
<!-- Start addition by Michael de Leon 10.31.06 -->
<td valign="bottom" align="left">
<a href="javascript: document.authform.submit();"><input class="wwmp_logingobtn" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_logingobtn10.31.06.jpg" type="image"></a><br />
</td>
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="hidden" name="mode" value="login" />
<?php if ($this->_tpl_vars['active_modules']['Simple_Mode'] != "" && $this->_tpl_vars['usertype'] != 'C' && $this->_tpl_vars['usertype'] != 'B'): ?>
<input type="hidden" name="usertype" value="P" />
<?php else: ?>
<input type="hidden" name="usertype" value="<?php echo $this->_tpl_vars['usertype']; ?>
" />
<?php endif; ?>
<input type="hidden" name="redirect" value="<?php echo $this->_tpl_vars['redirect']; ?>
" />
</td></tr>
<!-- Deleted by Michael de Leon 10.31.06
<tr>
<td height="24" class="VertMenuItems"></td>
</tr>
-->
<?php endif; ?> <?php if ($this->_tpl_vars['usertype'] == 'C' || ( $this->_tpl_vars['usertype'] == 'B' && $this->_tpl_vars['config']['XAffiliate']['partner_register'] == 'Y' )): ?>
<tr>
<!-- Start addition by Michael de Leon 10.31.06 -->
<td colspan="2" height="24" class="VertMenuItems"><a href="register.php" class="wwmp_vertmenulink">Create a new account?</a></td>
<!-- End addition by Michael de Leon 10.31.06 -->
<!-- Deleted by Michael de Leon 10.31.06
<td height="24" nowrap="nowrap" class="VertMenuItems"></td>
-->
</tr>
<?php endif; ?>
<?php if ($this->_tpl_vars['login'] == ""): ?>
<tr>
<!-- Start addition by Michael de Leon 10.31.06 -->
<td colspan="2" height="24" class="VertMenuItems"><a href="help.php?section=Password_Recovery" class="wwmp_vertmenulink">Forgot your username or password?</a></td>
<!-- End addition by Michael de Leon 10.31.06 -->
<!-- Deleted by Michael de Leon 10.31.06
<td height="24" nowrap="nowrap" class="VertMenuItems"><a href="help.php?section=Password_Recovery" class="VertMenuItems"></a></td>
-->
</tr>
<?php endif; ?>

<?php if ($this->_tpl_vars['usertype'] == 'P' && $this->_tpl_vars['active_modules']['Simple_Mode'] == 'Y' || $this->_tpl_vars['usertype'] == 'A'): ?>
<!-- insecure login form link -->
<tr>
<td class="VertMenuItems">
<br />
<div align="left"><a href="insecure_login.php" class="SmallNote"><?php echo $this->_tpl_vars['lng']['lbl_insecure_login']; ?>
</a></div>
</td>
</tr>
<!-- insecure login form link -->
<?php endif; ?>
<!-- Deleted by Michael de Leon 10.31.06
<tr>
<td class="VertMenuItems" align="right">
<br />
<a href="" class="SmallNote"></a>
<a href="" class="SmallNote"></a>
</td>
</tr>
-->
</table>
</form>
<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
<!-- Start addition by Michael de Leon 10.26.06 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.tpl", 'smarty_include_vars' => array('dingbats' => "wwmp_login_icon10.26.06.jpg",'menu_title' => $this->_tpl_vars['lng']['lbl_authentication'],'menu_content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- End addition by Michael de Leon 10.26.06 -->
<!-- Deleted by Michael de Leon 10.26.06
-->