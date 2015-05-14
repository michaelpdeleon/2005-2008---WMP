<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:14
         compiled from customer/main/register.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/main/register.tpl', 138, false),array('modifier', 'strip_tags', 'customer/main/register.tpl', 199, false),array('modifier', 'escape', 'customer/main/register.tpl', 199, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/register.tpl","txt_create_customer_profile,txt_modify_customer_profile,lbl_return_to_search_results,txt_registration_error,txt_email_already_exists,txt_user_already_exists,err_billing_state,err_shipping_state,err_billing_county,err_shipping_county,txt_email_invalid,lbl_save,txt_profile_modified,txt_partner_created,txt_profile_created,lbl_profile_details"); ?><?php if ($this->_tpl_vars['av_error'] == 1): ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/register.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php else: ?>

<?php if ($this->_tpl_vars['js_enabled'] == 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_email_script.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_zipcode_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "generate_required_fields_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_required_fields_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['config']['General']['use_js_states'] == 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "change_states_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['action'] != 'cart'): ?>

<!-- Deleted by Michael de Leon 11.29.06
 

-->

<!-- IN THIS SECTION -->

<?php if ($this->_tpl_vars['newbie'] != 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_tools.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<!-- IN THIS SECTION -->

<?php if ($this->_tpl_vars['usertype'] != 'C'): ?>
<br />
<?php if ($this->_tpl_vars['main'] == 'user_add'): ?>
<?php echo $this->_tpl_vars['lng']['txt_create_customer_profile']; ?>

<?php else: ?>
<?php echo $this->_tpl_vars['lng']['txt_modify_customer_profile']; ?>

<?php endif; ?>
<br /><br />
<?php endif; ?>

<?php endif; ?>
<font class="Text">

<!-- Deleted by Michael de Leon 11.16.06
															<br /><br />

-->

</font>

<!-- Deleted by Michael de Leon 11.29.06
<br /><br />
-->
<div align="center">
<?php ob_start(); ?>

<?php if ($this->_tpl_vars['newbie'] != 'Y' && $this->_tpl_vars['main'] != 'user_add' && ( $this->_tpl_vars['usertype'] == 'P' && $this->_tpl_vars['active_modules']['Simple_Mode'] == 'Y' || $this->_tpl_vars['usertype'] == 'A' )): ?>
<div align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_return_to_search_results'],'href' => "users.php?mode=search")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
<?php endif; ?>

<?php $this->assign('reg_error', $this->_tpl_vars['top_message']['reg_error']); ?>
<?php $this->assign('error', $this->_tpl_vars['top_message']['error']); ?>
<?php $this->assign('emailerror', $this->_tpl_vars['top_message']['emailerror']); ?>

<?php if ($this->_tpl_vars['registered'] == ""): ?>
<?php if ($this->_tpl_vars['reg_error']): ?>
<font class="Star">
<?php if ($this->_tpl_vars['reg_error'] == 'F'): ?>
<?php echo $this->_tpl_vars['lng']['txt_registration_error']; ?>

<?php elseif ($this->_tpl_vars['reg_error'] == 'E'): ?>
<?php echo $this->_tpl_vars['lng']['txt_email_already_exists']; ?>

<?php elseif ($this->_tpl_vars['reg_error'] == 'U'): ?>
<?php echo $this->_tpl_vars['lng']['txt_user_already_exists']; ?>

<?php endif; ?>
</font>
<br />
<?php endif; ?>

<?php if ($this->_tpl_vars['error'] != ""): ?>
<font class="Star">
<?php if ($this->_tpl_vars['error'] == 'b_statecode'): ?>
<?php echo $this->_tpl_vars['lng']['err_billing_state']; ?>

<?php elseif ($this->_tpl_vars['error'] == 's_statecode'): ?>
<?php echo $this->_tpl_vars['lng']['err_shipping_state']; ?>

<?php elseif ($this->_tpl_vars['error'] == 'b_county'): ?>
<?php echo $this->_tpl_vars['lng']['err_billing_county']; ?>

<?php elseif ($this->_tpl_vars['error'] == 's_county'): ?>
<?php echo $this->_tpl_vars['lng']['err_shipping_county']; ?>

<?php elseif ($this->_tpl_vars['error'] == 'email'): ?>
<?php echo $this->_tpl_vars['lng']['txt_email_invalid']; ?>

<?php endif; ?>
</font>
<br />
<?php endif; ?>

<script type="text/javascript" language="JavaScript 1.2">
<!--
var is_run = false;
function check_registerform_fields() {
	if(is_run)
		return false;
	is_run = true;
	if (check_zip_code()<?php if ($this->_tpl_vars['default_fields']['email']['avail'] == 'Y'): ?> && checkEmailAddress(document.registerform.email, '<?php echo $this->_tpl_vars['default_fields']['email']['required']; ?>
')<?php endif; ?> <?php if ($this->_tpl_vars['config']['General']['check_cc_number'] == 'Y' && $this->_tpl_vars['config']['General']['disable_cc'] != 'Y'): ?>&& checkCCNumber(document.registerform.card_number,document.registerform.card_type) <?php endif; ?>&& checkRequired(requiredFields)) {
		document.registerform.submit();
		return true;
	}
	is_run = false;
	return false;
}
-->
</script>

<form action="<?php echo $this->_tpl_vars['register_script_name']; ?>
?<?php echo ((is_array($_tmp=$GLOBALS['HTTP_SERVER_VARS']['QUERY_STRING'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" method="post" name="registerform" onsubmit="javascript: check_registerform_fields(); return false;">
<?php if ($this->_tpl_vars['config']['Security']['use_https_login'] == 'Y'): ?>
<input type="hidden" name="<?php echo $this->_tpl_vars['XCARTSESSNAME']; ?>
" value="<?php echo $this->_tpl_vars['XCARTSESSID']; ?>
" />
<?php endif; ?>
<table cellspacing="2" cellpadding="2" width="100%">
<tbody>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_personal_info.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_billing_address.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_shipping_address.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_contact_info.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'A')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['config']['General']['disable_cc'] != 'Y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_ccinfo.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/register_account.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] && $this->_tpl_vars['usertype'] != 'C'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/register_bonuses.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['active_modules']['News_Management'] && $this->_tpl_vars['newslists']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/News_Management/register_newslists.tpl", 'smarty_include_vars' => array('userinfo' => $this->_tpl_vars['userinfo'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<!-- Deleted by Michael de Leon 11.16.06
<tr>
<td colspan="3" align="center">
<br /><br />
</td>
</tr>
-->

<tr>
<td colspan="2">&nbsp;</td>
<td align="left">

<?php if ($GLOBALS['HTTP_GET_VARS']['mode'] == 'update'): ?>
<input type="hidden" name="mode" value="update" />
<?php endif; ?>

<input type="hidden" name="anonymous" value="<?php echo $this->_tpl_vars['anonymous']; ?>
" />

<br /><br />
<?php if ($this->_tpl_vars['js_enabled'] && $this->_tpl_vars['usertype'] == 'C'): ?>
<!-- Deleted by Michael de Leon 11.16.06
-->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/submit_yourinfo_continuebtn.tpl", 'smarty_include_vars' => array('type' => 'input','style' => 'image','href' => "javascript: return check_registerform_fields();")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<!-- Deleted by Michael de Leon 11.16.06
<input type="submit" value="  " />
-->
<input class="wwmp_logingobtn" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_continuebtnsmall11.08.06.jpg" type="image" value=" <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['lng']['lbl_save'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp, false) : smarty_modifier_strip_tags($_tmp, false)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
 ">
<?php endif; ?>

</td>
</tr>

</tbody>
</table>
<input type="hidden" name="usertype" value="<?php if ($GLOBALS['HTTP_GET_VARS']['usertype'] != ""):  echo ((is_array($_tmp=$GLOBALS['HTTP_GET_VARS']['usertype'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html'));  else:  echo $this->_tpl_vars['usertype'];  endif; ?>" />
</form>

<!-- Deleted by Michael de Leon 11.16.06
<br /><br />

<br /><a href="help.php?section=conditions" target="_blank"><font class="Text" style="white-space: nowrap;"><b></b>&nbsp;</font></a>

<br />
-->
<?php if ($this->_tpl_vars['is_areas']['S'] == 'Y' || $this->_tpl_vars['is_areas']['B'] == 'Y'): ?>
<?php if ($this->_tpl_vars['active_modules']['UPS_OnLine_Tools'] && $this->_tpl_vars['av_enabled'] == 'Y'): ?>
<br />
<br />
<br />
<table cellpadding="1" cellspacing="1" width="100%">
<tbody>
<tr>
<td colspan="3">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/ups_av_notice.tpl", 'smarty_include_vars' => array('postoffice' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/ups_av_notice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br /><br />
</td>
</tr>
</tbody>
</table>
<?php endif; ?>
<?php endif; ?>


<?php else: ?>

<?php if ($GLOBALS['HTTP_POST_VARS']['mode'] == 'update' || $GLOBALS['HTTP_GET_VARS']['mode'] == 'update'): ?>
<?php echo $this->_tpl_vars['lng']['txt_profile_modified']; ?>

<?php elseif ($GLOBALS['HTTP_GET_VARS']['usertype'] == 'B' || $this->_tpl_vars['usertype'] == 'B'): ?>
<?php echo $this->_tpl_vars['lng']['txt_partner_created']; ?>

<?php else: ?>
<?php echo $this->_tpl_vars['lng']['txt_profile_created']; ?>

<?php endif; ?>
<?php endif; ?>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_shoppingcart_placeorder.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_profile_details'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="634"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<!-- Deleted by Michael de Leon 11.16.06
-->

<?php endif; ?>