<?php /* Smarty version 2.6.12, created on 2014-09-07 21:54:53
         compiled from help/general.tpl */ ?>
<?php func_load_lang($this, "help/general.tpl","txt_help_zone_title,lbl_providers_zone,lbl_recover_password,lbl_contact_us,lbl_faq_long,lbl_privacy_statement,lbl_terms_n_conditions,lbl_about_our_site,lbl_help_zone"); ?><?php echo $this->_tpl_vars['lng']['txt_help_zone_title']; ?>

<p />
<?php ob_start(); ?>
<table cellspacing="0" cellpadding="0" width="100%">

<?php if ($this->_tpl_vars['usertype'] == 'P'): ?>
<tr> 
<td height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_providers_zone'],'href' => ($this->_tpl_vars['catalogs']['provider'])."/home.php")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>
<?php endif; ?>

<tr>
<td height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_recover_password'],'href' => "help.php?section=Password_Recovery")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>

<?php if ($this->_tpl_vars['usertype'] == 'C' || $this->_tpl_vars['usertype'] == 'B'): ?>
<tr> 
<td height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_contact_us'],'href' => "help.php?section=contactus&mode=update")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>
<?php endif; ?>


<tr> 
<td height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_faq_long'],'href' => "help.php?section=FAQ")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_privacy_statement'],'href' => "help.php?section=business")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td height="10" width="1" class="Text" colspan="4">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_terms_n_conditions'],'href' => "help.php?section=conditions")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr><td height="10" width="1" class="Text" colspan="4">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_about_our_site'],'href' => "help.php?section=about")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

</table>
<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean();  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_help_zone'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>