{* $Id: order_data.tpl,v 1.29.2.4 2006/08/12 06:36:04 max Exp $ *}
<table cellspacing="0" cellpadding="0" width="100%" border="0">

<tr>
<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_products_ordered}</font></td>
</tr>

</table>

<table cellspacing="0" cellpadding="3" width="100%" border="1">

<tr>
<th width="60" bgcolor="#cccccc">{$lng.lbl_sku}</th>
<th bgcolor="#cccccc">{$lng.lbl_product}</th>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
<th nowrap="nowrap" width="100" bgcolor="#cccccc">{if $order.extra.tax_info.product_tax_name ne ""}{$order.extra.tax_info.product_tax_name}{else}{$lng.lbl_tax}{/if}</th>
{/if}
<th nowrap="nowrap" width="100" bgcolor="#cccccc" align="center">{$lng.lbl_item_price}</th>
<th width="60" bgcolor="#cccccc">{$lng.lbl_quantity}</th>
<th width="60" bgcolor="#cccccc">{$lng.lbl_total}<br /><img height="1" src="{$ImagesDir}/spacer.gif" width="50" border="0" alt="" /></th>
</tr>

{foreach from=$products item=product}
<tr>
<td align="center">{$product.productcode}</td>
<td><font style="FONT-SIZE: 11px">{$product.product}</font>
{if $product.product_options ne '' && $active_modules.Product_Options}
<table>

<tr>
<td valign="top"><b>{$lng.lbl_options}:</b></td> 
<td>{include file="modules/Product_Options/display_options.tpl" options=$product.product_options options_txt=$product.product_options_txt force_product_options_txt=$product.force_product_options_txt}</td>
</tr>

</table>
{/if}
{if $active_modules.Egoods and $product.download_key and ($order.status eq "P" or $order.status eq "C")}
<br />
<a href="{$catalogs.customer}/download.php?id={$product.download_key}" class="SmallNote" target="_blank">{$lng.lbl_download}</a>
{/if}
</td>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
<td align="center">
{foreach from=$product.extra_data.taxes key=tax_name item=tax}
{if $tax.tax_value gt 0}
{if $order.extra.tax_info.product_tax_name eq ""}{$tax.tax_display_name} {/if}
{if $tax.rate_type eq "%"}{$tax.rate_value|formatprice}%{else}{include file="currency.tpl" value=$tax.rate_value}{/if}<br />
{/if}
{/foreach}
</td>
{/if}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$product.display_price}&nbsp;&nbsp;</td>
<td align="center">{$product.amount}</td>
<td align="right" nowrap="nowrap">{math assign="total" equation="amount*price" amount=$product.amount price=$product.display_price}{include file="currency.tpl" value=$total}&nbsp;&nbsp;</td>
</tr>
{/foreach}

{if $giftcerts ne ''}
{foreach from=$giftcerts item=gc}
<tr>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
{$lng.lbl_gift_certificate}: {$gc.gcid}<br />
<div style="padding-left: 10px; white-space: nowrap;">
{if $gc.send_via eq "P"}
{$lng.lbl_gc_send_via_postal_mail}<br />
{$lng.lbl_mail_address}: {$gc.recipient_firstname} {$gc.recipient_lastname}<br />
{$gc.recipient_address}, {$gc.recipient_city},<br />
{if $gc.recipient_countyname ne ''}{$gc.recipient_countyname} {/if}{$gc.recipient_state} {$gc.recipient_country}, {$gc.recipient_zipcode}<br />
{$lng.lbl_phone}: {$gc.recipient_phone}
{else}
{$lng.lbl_recipient_email}: {$gc.recipient_email}
{/if}
</div>
	</td>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
	<td align="center">&nbsp;-&nbsp;</td>
{/if}
	<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$gc.amount}&nbsp;&nbsp;</td>
	<td align="center">1</td>
	<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$gc.amount}&nbsp;&nbsp;</td>
</tr>
{/foreach}
{/if}

</table>
<table cellspacing="0" cellpadding="0" width="100%" border="0">

<tr>
<td align="right" width="100%" height="20"><b>{$lng.lbl_subtotal}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.display_subtotal}&nbsp;&nbsp;&nbsp;</td>
</tr>

{if $order.discount gt 0}
<tr>
<td align="right" height="20"><b>{$lng.lbl_discount}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.coupon and $order.coupon_type ne "free_ship"}
<tr>
<td align="right" height="20"><b>{$lng.lbl_coupon_saving}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.coupon_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.discounted_subtotal ne $order.subtotal}
<tr>
<td align="right" width="100%" height="20"><b>{$lng.lbl_discounted_subtotal}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.display_discounted_subtotal}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $config.Shipping.disable_shipping ne 'Y'}
<tr>
<td align="right" height="20"><b>{$lng.lbl_shipping_cost}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.display_shipping_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.coupon and $order.coupon_type eq "free_ship"}
<tr>
<td align="right" height="20"><b>{$lng.lbl_coupon_saving}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.coupon_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.applied_taxes and $order.extra.tax_info.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$order.applied_taxes}
<tr>
<td align="right" width="100%" height="20"><b>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value|formatprice}%{/if}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$tax.tax_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}
{/if}

{if $order.payment_surcharge ne 0}
<tr>
<td align="right" height="20"><b>{if $order.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.payment_surcharge}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}


{if $order.giftcert_discount gt 0}
<tr>
<td align="right" height="20"><b>{$lng.lbl_giftcert_discount}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$order.giftcert_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

<tr>
<td bgcolor="#000000" colspan="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
</tr>

<tr>
<td align="right" bgcolor="#cccccc" height="25"><b>{$lng.lbl_total}:</b>&nbsp;</td>
<td align="right" bgcolor="#cccccc"><b>{include file="currency.tpl" value=$order.total}</b>&nbsp;&nbsp;&nbsp;</td>
</tr>

{if $_userinfo.tax_exempt ne "Y"}

{if $order.applied_taxes and $order.extra.tax_info.display_taxed_order_totals eq "Y"}
{foreach key=tax_name item=tax from=$order.applied_taxes}
<tr>
<td align="right" width="100%" height="20"><b>{$lng.lbl_including_tax|substitute:"tax":$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value|formatprice}%{/if}:</b>&nbsp;</td>
<td align="right">{include file="currency.tpl" value=$tax.tax_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}
{/if}

{else}

<tr>
<td align="right" colspan="2" width="100%" height="20">{$lng.txt_tax_exemption_applied}</td>
</tr>

{/if}

</table>

{if $order.applied_giftcerts}
<br />
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
	<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_applied_giftcerts}</font></td>
</tr>
</table>

<table cellspacing="1" cellpadding="0" width="100%" border="0">

<tr>
<th width="60" bgcolor="#cccccc">{$lng.lbl_giftcert_ID}</th>
<th bgcolor="#cccccc">{$lng.lbl_giftcert_cost}</th>
</tr>

{foreach from=$order.applied_giftcerts item=gc}
<tr>
<td align="center">{$gc.giftcert_id}</td>
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$gc.giftcert_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}

</table>
{/if}

{if $order.extra.special_bonuses ne ""}
{include file="mail/html/special_offers_order_bonuses.tpl" bonuses=$order.extra.special_bonuses}
{/if}

