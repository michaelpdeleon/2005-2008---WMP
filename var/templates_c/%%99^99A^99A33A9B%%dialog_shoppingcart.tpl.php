<?php /* Smarty version 2.6.12, created on 2014-10-01 08:25:15
         compiled from dialog_shoppingcart.tpl */ ?>
<?php func_load_lang($this, "dialog_shoppingcart.tpl","lbl_discounted_subtotal,lbl_subtotal"); ?><?php if ($this->_tpl_vars['printable'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_printable.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<!-- Start edit by Michael de Leon 09.15.06 -->
<div align="center">
<table cellspacing="0" cellpadding="0" border="0" <?php echo $this->_tpl_vars['extra']; ?>
>
<tr>
<td background="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_dialogshoppingcart_title11.06.06.jpg" height="32" align="left">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="wwmp_cart_label_title" align="left"><?php echo $this->_tpl_vars['title']; ?>
</td>
	<td class="wwmp_cart_label_subtotal" align="right">
		<?php if ($this->_tpl_vars['cart']['discounted_subtotal'] != $this->_tpl_vars['cart']['subtotal']): ?>
			<?php echo $this->_tpl_vars['lng']['lbl_discounted_subtotal']; ?>
:
			<font class="wwmp_cart_subtotal"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "currency.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['cart']['display_discounted_subtotal'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></font>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/alter_currency_value.tpl", 'smarty_include_vars' => array('alter_currency_value' => $this->_tpl_vars['cart']['display_discounted_subtotal'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php echo $this->_tpl_vars['lng']['lbl_subtotal']; ?>
:
			<font class="wwmp_cart_subtotal"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "currency.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['cart']['display_subtotal'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></font>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/alter_currency_value.tpl", 'smarty_include_vars' => array('alter_currency_value' => $this->_tpl_vars['cart']['display_subtotal'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	</td>
  </tr>
</table>
</td>
</tr>
<tr>
<td colspan="3" background="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_dialogshoppingcart_m11.03.06.jpg"><?php echo $this->_tpl_vars['content']; ?>
</td>
</tr>
<tr>
<td background="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_dialogshoppingcart_footer11.06.06.jpg" height="32"></td>
</tr>
</table>
</div>
<!-- End edit by Michael de Leon 09.15.06 -->
<?php endif; ?>