{* $Id: gc_checkout.tpl,v 1.10 2005/11/17 06:55:46 max Exp $ *}
{if $cart.giftcerts ne ""}
{section name=giftcert loop=$cart.giftcerts}
<tr>
<td class="ProductPriceSmall">1</td>
<td></td>
<td>{$lng.lbl_gc_for} {$cart.giftcerts[giftcert].recipient|truncate:30:"...":true}</td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td>&nbsp;</td>
{/if}
<td class="ProductPriceSmall" align="right">{include file="currency.tpl" value=$cart.giftcerts[giftcert].amount}</td>
<td class="ProductPriceSmall" align="right">{include file="currency.tpl" value=$cart.giftcerts[giftcert].amount}</td>
</tr>
{/section}
{/if}
