{* $Id: subscription_info_inlist.tpl,v 1.10.2.1 2006/06/29 06:34:20 max Exp $ *}
{if $products[product].catalogprice gt 0 or $products[product].sub_priceplan gt 0}
<table cellpadding="0" cellspacing="0">
<tr>
<td valign="top" class="ProductPriceConverting">{$lng.lbl_price}:&nbsp;</td>
<td><font class="ProductPrice">
{include file="currency.tpl" value=$products[product].catalogprice}</font>{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].catalogprice} {$lng.lbl_setup_fee}
{if $products[product].taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}{else}<br />{/if}
+ <font class="ProductPrice">
{include file="currency.tpl" value=$products[product].sub_priceplan}</font>{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].sub_priceplan} / {if $products[product].sub_plan eq "Monthly"}{$lng.lbl_month}{elseif $products[product].sub_plan eq "Annually"}{$lng.lbl_year}{elseif $products[product].sub_plan eq "Weekly"}{$lng.lbl_week}{elseif $products[product].sub_plan eq "Quarterly"}{$lng.lbl_quarter}{elseif $products[product].sub_plan eq "By Period"}{$pay_period} {$lng.lbl_days}{/if}
{if $products[product].subscription.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$products[product].subscription.taxes is_subtax=true}{/if}
</td>
</tr>
</table>
{/if}
