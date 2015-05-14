<?php /* Smarty version 2.6.12, created on 2014-10-11 21:10:17
         compiled from customer/main/minicart.tpl */ ?>
<?php func_load_lang($this, "customer/main/minicart.tpl","lbl_cart_items,lbl_total,lbl_cart_is_empty"); ?><table cellpadding="1" cellspacing="0">
<?php if ($this->_tpl_vars['minicart_total_items'] > 0): ?>
<tr>
	<!-- Deleted by Michael de Leon 11.01.06
	<td rowspan="2" width="23"><a href="cart.php"><img src="/cart_full.gif" width="19" height="16" alt="" /></a>	</td>
	-->
	<!-- Start addition by Michael de Leon 11.01.06 -->
	<td rowspan="3" valign="top"><a href="cart.php"><img class="wwmp_cartpic" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_cartfull11.01.06.jpg" /></a></td>
	<!-- End addition by Michael de Leon 11.01.06 -->
	<td class="wwmp_vertmenu_black"><strong><?php echo $this->_tpl_vars['lng']['lbl_cart_items']; ?>
:</strong> </td>
	<td class="wwmp_vertmenu_black"><?php echo $this->_tpl_vars['minicart_total_items']; ?>
</td>
</tr>
<tr>
	<td class="wwmp_vertmenu_black"><strong><?php echo $this->_tpl_vars['lng']['lbl_total']; ?>
:</strong> </td>
	<td class="wwmp_vertmenu_black"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "currency.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['minicart_total_cost'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<tr>
	<td><img class="wwmp_minicartsection" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif"></td>
</tr>
<?php else: ?>
<tr>
	<!-- Deleted by Michael de Leon 11.01.06
	<td rowspan="2" width="23"><img src="/cart_empty.gif" width="19" height="16" alt="" /></td>
	-->
	<!-- Start addition by Michael de Leon 11.01.06 -->
	<td rowspan="3" valign="top"><img class="wwmp_cartpic" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_cartempty11.01.06.jpg" /></td>
	<!-- End addition by Michael de Leon 11.01.06 -->
	<td class="wwmp_vertmenu_black" align="center"><b><?php echo $this->_tpl_vars['lng']['lbl_cart_is_empty']; ?>
</b></td>
</tr>
<!-- Deleted by Michael de Leon 11.01.06
<tr>
	<td class="VertMenuItems">&nbsp;</td>
</tr>
-->
<!-- Start addition by Michael de Leon 11.01.06 -->
<tr>
	<td><img class="wwmp_minicartsection" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif"></td>
</tr>
<!-- End addition by Michael de Leon 11.01.06 -->
<?php endif; ?>
</table>
<!-- Deleted by Michael de Leon 11.01.06
<hr class="VertMenuHr" size="1" />
-->