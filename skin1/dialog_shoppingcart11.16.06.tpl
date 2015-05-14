{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" {$extra}>
<tr>
<td background="{$ImagesDir}/wwmp_dialogshoppingcart_title11.06.06.jpg" height="32" align="left">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="wwmp_cart_label_title" align="left">{$title}</td>
	<td class="wwmp_cart_label_subtotal" align="right">
		{if $cart.discounted_subtotal ne $cart.subtotal}
			{$lng.lbl_discounted_subtotal}:
			<font class="wwmp_cart_subtotal">{include file="currency.tpl" value=$cart.display_discounted_subtotal}</font>
			{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_discounted_subtotal}
		{else}
			{$lng.lbl_subtotal}:
			<font class="wwmp_cart_subtotal">{include file="currency.tpl" value=$cart.display_subtotal}</font>
			{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_subtotal}
		{/if}
	</td>
  </tr>
</table>
</td>
</tr>
<tr>
<td colspan="3" background="{$ImagesDir}/wwmp_dialogshoppingcart_m11.03.06.jpg">{$content}</td>
</tr>
<tr>
<td background="{$ImagesDir}/wwmp_dialogshoppingcart_footer11.06.06.jpg" height="32"></td>
</tr>
</table>
<!-- End edit by Michael de Leon 09.15.06 -->
{/if}
