<?php /* Smarty version 2.6.12, created on 2014-10-11 21:11:53
         compiled from modules/Users_online/menu_users_online.tpl */ ?>
<?php func_load_lang($this, "modules/Users_online/menu_users_online.tpl","lbl_users_online,lbl_admin_s,lbl_provider_s,lbl_partner_s,lbl_registered_customer_s,lbl_anonymous_customer_s,lbl_unregistered_customer_s"); ?><?php if ($this->_tpl_vars['users_online']): ?>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="DialogBorder"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="1" alt="" /></td>
</tr>
<tr>
	<td class="BottomDialogBox">
<b><?php echo $this->_tpl_vars['lng']['lbl_users_online']; ?>
:</b>&nbsp;
<?php $_from = $this->_tpl_vars['users_online']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['_users'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['_users']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['v']):
        $this->_foreach['_users']['iteration']++;
?>
<font class="VertMenuItems" style="WHITE-SPACE: nowrap;"><?php echo $this->_tpl_vars['v']['count']; ?>

<?php echo '';  if ($this->_tpl_vars['v']['usertype'] == 'A' || ( $this->_tpl_vars['v']['usertype'] == 'P' && $this->_tpl_vars['active_modules']['Simple_Mode'] )):  echo '';  echo $this->_tpl_vars['lng']['lbl_admin_s'];  echo '';  elseif ($this->_tpl_vars['v']['usertype'] == 'P'):  echo '';  echo $this->_tpl_vars['lng']['lbl_provider_s'];  echo '';  elseif ($this->_tpl_vars['v']['usertype'] == 'B'):  echo '';  echo $this->_tpl_vars['lng']['lbl_partner_s'];  echo '';  elseif ($this->_tpl_vars['v']['usertype'] == 'C' && $this->_tpl_vars['v']['is_registered'] == 'Y'):  echo '';  echo $this->_tpl_vars['lng']['lbl_registered_customer_s'];  echo '';  elseif ($this->_tpl_vars['v']['usertype'] == 'C' && $this->_tpl_vars['v']['is_registered'] == 'A'):  echo '';  echo $this->_tpl_vars['lng']['lbl_anonymous_customer_s'];  echo '';  elseif ($this->_tpl_vars['v']['usertype'] == 'C' && $this->_tpl_vars['v']['is_registered'] == ''):  echo '';  echo $this->_tpl_vars['lng']['lbl_unregistered_customer_s'];  echo '';  endif;  echo '';  if (! ($this->_foreach['_users']['iteration'] == $this->_foreach['_users']['total'])):  echo ', ';  endif;  echo ''; ?>

</font>
<?php endforeach; endif; unset($_from); ?>
	</td>
</tr>
</table>
<?php endif; ?>