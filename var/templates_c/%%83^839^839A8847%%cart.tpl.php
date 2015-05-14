<?php /* Smarty version 2.6.12, created on 2014-09-08 04:59:39
         compiled from customer/main/cart.tpl */ ?>
<?php func_load_lang($this, "customer/main/cart.tpl","lbl_sku,lbl_selected_options,lbl_quantity,lbl_price,txt_your_shopping_cart_is_empty,lbl_items_in_cart"); ?><!--
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
-->
<?php if ($this->_tpl_vars['active_modules']['Product_Options']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/include_js.tpl", 'smarty_include_vars' => array('src' => "modules/Product_Options/edit_product_options.js")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<!-- Deleted by Michael de Leon 11.02.06
<h3></h3>
<p />
-->
<div align="center">
<?php ob_start(); ?>
<br />
<!-- Deleted by Michael de Leon 11.02.06
-->
<?php if ($this->_tpl_vars['products'] != ""): ?>
<form action="cart.php" method="post" name="cartform">
<table width="100%">
<?php unset($this->_sections['product']);
$this->_sections['product']['name'] = 'product';
$this->_sections['product']['loop'] = is_array($_loop=$this->_tpl_vars['products']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['product']['show'] = true;
$this->_sections['product']['max'] = $this->_sections['product']['loop'];
$this->_sections['product']['step'] = 1;
$this->_sections['product']['start'] = $this->_sections['product']['step'] > 0 ? 0 : $this->_sections['product']['loop']-1;
if ($this->_sections['product']['show']) {
    $this->_sections['product']['total'] = $this->_sections['product']['loop'];
    if ($this->_sections['product']['total'] == 0)
        $this->_sections['product']['show'] = false;
} else
    $this->_sections['product']['total'] = 0;
if ($this->_sections['product']['show']):

            for ($this->_sections['product']['index'] = $this->_sections['product']['start'], $this->_sections['product']['iteration'] = 1;
                 $this->_sections['product']['iteration'] <= $this->_sections['product']['total'];
                 $this->_sections['product']['index'] += $this->_sections['product']['step'], $this->_sections['product']['iteration']++):
$this->_sections['product']['rownum'] = $this->_sections['product']['iteration'];
$this->_sections['product']['index_prev'] = $this->_sections['product']['index'] - $this->_sections['product']['step'];
$this->_sections['product']['index_next'] = $this->_sections['product']['index'] + $this->_sections['product']['step'];
$this->_sections['product']['first']      = ($this->_sections['product']['iteration'] == 1);
$this->_sections['product']['last']       = ($this->_sections['product']['iteration'] == $this->_sections['product']['total']);
?>
<?php if ($this->_tpl_vars['products'][$this->_sections['product']['index']]['hidden'] == ""): ?>
<tr><td class="PListImgBox">
<a href="product.php?productid=<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['productid']; ?>
"><?php if ($this->_tpl_vars['products'][$this->_sections['product']['index']]['is_pimage'] == 'W'):  $this->assign('imageid', $this->_tpl_vars['products'][$this->_sections['product']['index']]['variantid']);  else:  $this->assign('imageid', $this->_tpl_vars['products'][$this->_sections['product']['index']]['productid']);  endif;  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "product_thumbnail.tpl", 'smarty_include_vars' => array('productid' => $this->_tpl_vars['imageid'],'image_x' => $this->_tpl_vars['config']['Appearance']['thumbnail_width'],'product' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['product'],'tmbn_url' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['pimage_url'],'type' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['is_pimage'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>
<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != "" && $this->_tpl_vars['products'][$this->_sections['product']['index']]['have_offers']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/product_offer_thumb.tpl", 'smarty_include_vars' => array('product' => $this->_tpl_vars['products'][$this->_sections['product']['index']])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</td>
<td valign="top" align="left">
<font class="ProductTitle"><?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['product']; ?>
</font>
<br /><br />
<table cellpadding="0" cellspacing="0" width="100%">
<!-- Start addition by Michael de Leon 09.18.06 -->
<tr><td>
<strong><?php echo $this->_tpl_vars['lng']['lbl_sku']; ?>
:</strong> <?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['productcode']; ?>
<br />
<?php if ($this->_tpl_vars['active_modules']['Extra_Fields'] != ""): ?>
	<?php unset($this->_sections['field']);
$this->_sections['field']['name'] = 'field';
$this->_sections['field']['loop'] = is_array($_loop=$this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_fields']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['field']['show'] = true;
$this->_sections['field']['max'] = $this->_sections['field']['loop'];
$this->_sections['field']['step'] = 1;
$this->_sections['field']['start'] = $this->_sections['field']['step'] > 0 ? 0 : $this->_sections['field']['loop']-1;
if ($this->_sections['field']['show']) {
    $this->_sections['field']['total'] = $this->_sections['field']['loop'];
    if ($this->_sections['field']['total'] == 0)
        $this->_sections['field']['show'] = false;
} else
    $this->_sections['field']['total'] = 0;
if ($this->_sections['field']['show']):

            for ($this->_sections['field']['index'] = $this->_sections['field']['start'], $this->_sections['field']['iteration'] = 1;
                 $this->_sections['field']['iteration'] <= $this->_sections['field']['total'];
                 $this->_sections['field']['index'] += $this->_sections['field']['step'], $this->_sections['field']['iteration']++):
$this->_sections['field']['rownum'] = $this->_sections['field']['iteration'];
$this->_sections['field']['index_prev'] = $this->_sections['field']['index'] - $this->_sections['field']['step'];
$this->_sections['field']['index_next'] = $this->_sections['field']['index'] + $this->_sections['field']['step'];
$this->_sections['field']['first']      = ($this->_sections['field']['iteration'] == 1);
$this->_sections['field']['last']       = ($this->_sections['field']['iteration'] == $this->_sections['field']['total']);
?>
		<strong><?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_fields'][$this->_sections['field']['index']]['field']; ?>
:</strong>
	<?php endfor; endif; ?>
	<?php if ($this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_field1'] && $this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_field1'] != ""): ?>
		<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_field1']; ?>

	<?php else: ?>
		<?php unset($this->_sections['field']);
$this->_sections['field']['name'] = 'field';
$this->_sections['field']['loop'] = is_array($_loop=$this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_fields']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['field']['show'] = true;
$this->_sections['field']['max'] = $this->_sections['field']['loop'];
$this->_sections['field']['step'] = 1;
$this->_sections['field']['start'] = $this->_sections['field']['step'] > 0 ? 0 : $this->_sections['field']['loop']-1;
if ($this->_sections['field']['show']) {
    $this->_sections['field']['total'] = $this->_sections['field']['loop'];
    if ($this->_sections['field']['total'] == 0)
        $this->_sections['field']['show'] = false;
} else
    $this->_sections['field']['total'] = 0;
if ($this->_sections['field']['show']):

            for ($this->_sections['field']['index'] = $this->_sections['field']['start'], $this->_sections['field']['iteration'] = 1;
                 $this->_sections['field']['iteration'] <= $this->_sections['field']['total'];
                 $this->_sections['field']['index'] += $this->_sections['field']['step'], $this->_sections['field']['iteration']++):
$this->_sections['field']['rownum'] = $this->_sections['field']['iteration'];
$this->_sections['field']['index_prev'] = $this->_sections['field']['index'] - $this->_sections['field']['step'];
$this->_sections['field']['index_next'] = $this->_sections['field']['index'] + $this->_sections['field']['step'];
$this->_sections['field']['first']      = ($this->_sections['field']['iteration'] == 1);
$this->_sections['field']['last']       = ($this->_sections['field']['iteration'] == $this->_sections['field']['total']);
?>
			<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['extra_fields'][$this->_sections['field']['index']]['field_value']; ?>

		<?php endfor; endif; ?>
	<?php endif; ?>
<?php endif; ?><br /><br />
<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['descr']; ?>
<br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<?php if ($this->_tpl_vars['products'][$this->_sections['product']['index']]['product_options'] != ""): ?>
<font class="wwmp_cart_label"><?php echo $this->_tpl_vars['lng']['lbl_selected_options']; ?>
:</font><br />
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="left"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Options/display_options.tpl", 'smarty_include_vars' => array('options' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['product_options'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
	</tr>
	<tr>
		<td align="left"><?php if ($this->_tpl_vars['products'][$this->_sections['product']['index']]['product_options'] != ''): ?>
		<?php if ($this->_tpl_vars['config']['UA']['platform'] == 'MacPPC' && $this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/edit_product_options.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['cartid'],'js_to_href' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/edit_product_options.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['products'][$this->_sections['product']['index']]['cartid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
		<?php endif; ?></td>
	</tr>
</table>
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<?php endif; ?>
<?php $this->assign('price', $this->_tpl_vars['products'][$this->_sections['product']['index']]['display_price']); ?>
<?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] != "" && $this->_tpl_vars['products'][$this->_sections['product']['index']]['product_type'] == 'C'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Configurator/pconf_customer_cart.tpl", 'smarty_include_vars' => array('main_product' => $this->_tpl_vars['products'][$this->_sections['product']['index']])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->assign('price', $this->_tpl_vars['products'][$this->_sections['product']['index']]['pconf_display_price']); ?>
<br />
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Subscriptions'] != "" && $this->_tpl_vars['products'][$this->_sections['product']['index']]['sub_plan'] != "" && $this->_tpl_vars['products'][$this->_sections['product']['index']]['product_type'] != 'C'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Subscriptions/subscription_priceincart.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<?php if ($this->_tpl_vars['active_modules']['Special_Offers']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/cart_price_special.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<table width="200" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="wwmp_cart_label" align="center"><?php echo $this->_tpl_vars['lng']['lbl_quantity']; ?>
:</td>
		<td class="wwmp_cart_label" align="center"><?php echo $this->_tpl_vars['lng']['lbl_price']; ?>
:</td>
	</tr>
	<tr>
		<td align="center"><font class="wwmp_cart_quantitybox"><?php if ($this->_tpl_vars['active_modules']['Egoods'] && $this->_tpl_vars['products'][$this->_sections['product']['index']]['distribution']): ?>1<input type="hidden"<?php else: ?><input type="text" size="3"<?php endif; ?> name="productindexes[<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['cartid']; ?>
]" value="<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['amount']; ?>
" /></font></td>
		<td align="center"><font class="wwmp_cart_price"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "currency.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></font></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript: document.cartform.submit()"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_updatebtn11.07.06.jpg" border="0"></a></td>
		<td align="center"><a href="cart.php?mode=delete&amp;productindex=<?php echo $this->_tpl_vars['products'][$this->_sections['product']['index']]['cartid']; ?>
"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_removebtn11.07.06.jpg" border="0"></a></td>
	</tr>
</table>
<!-- Deleted by Michael de Leon 11.07.06
BIG SECTION DELETED
-->
<?php if ($this->_tpl_vars['active_modules']['Special_Offers']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/customer/cart_free.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>
</td></tr>
<!-- End addition by Michael de Leon 09.18.06 -->
</table>
<!-- Deleted by Michael de Leon 11.07.06
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow"></td>
	<td class="ButtonsRow">
								</td>
</tr>
</table>
-->
</td></tr>
<tr><td colspan="2"><hr width="100%" size="1" noshade="noshade" color="#aaaaaa" /></td></tr>
<?php endif; ?>
<?php endfor; endif; ?>
</table>
<?php if ($this->_tpl_vars['active_modules']['Gift_Certificates'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Gift_Certificates/gc_cart.tpl", 'smarty_include_vars' => array('giftcerts_data' => $this->_tpl_vars['cart']['giftcerts'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<!-- Deleted by Michael de Leon 11.07.06
-->
<input type="hidden" name="action" value="update" />
<br />
<br />
<!-- Deleted by Michael de Leon 11.08.06
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="left">
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow"></td>
	<td class="ButtonsRow"></td>
</tr>
</table>
</td>
<td>
<table cellpadding="5" cellspacing="0" align="right">
<tr>
<td>
</td>
<td>
</td>
</tr>
</table>
</td>
<td align="right">
</td>
</tr>
</table>
<input type="hidden" name="mode" value="checkout" />
-->
</form>
<?php else: ?>
<div align="center">
<?php echo $this->_tpl_vars['lng']['txt_your_shopping_cart_is_empty']; ?>

</div>
<?php endif; ?>
<!-- Start addition by Michael de Leon 11.07.06 -->
<?php if ($this->_tpl_vars['cart']['coupon_discount'] == 0 && $this->_tpl_vars['products'] != ""): ?>
<?php if ($this->_tpl_vars['active_modules']['Discount_Coupons'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Discount_Coupons/add_coupon.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>
<!-- End addition by Michael de Leon 11.07.06 -->
<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog_shoppingcart.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_items_in_cart'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="634"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- Deleted by Michael de Leon 11.06.06
-->
</div>