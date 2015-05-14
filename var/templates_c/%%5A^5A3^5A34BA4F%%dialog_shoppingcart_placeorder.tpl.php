<?php /* Smarty version 2.6.12, created on 2014-08-28 00:35:15
         compiled from dialog_shoppingcart_placeorder.tpl */ ?>
<?php if ($this->_tpl_vars['printable'] != ''): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_printable.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" <?php echo $this->_tpl_vars['extra']; ?>
>
<tr>
<td background="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_dialogshoppingcart_title11.06.06.jpg" height="32" align="left">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="wwmp_cart_label_title" align="left"><?php echo $this->_tpl_vars['title']; ?>
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
<!-- End edit by Michael de Leon 09.15.06 -->
<?php endif; ?>