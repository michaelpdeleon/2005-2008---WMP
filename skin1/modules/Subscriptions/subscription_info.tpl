{* $Id: subscription_info.tpl,v 1.9.2.1 2006/06/29 06:34:20 max Exp $ *}
{if $subscription}
<tr>
<td width="30%" class="ProductPriceConverting">{$lng.lbl_price}:</td>
<td><font class="ProductPrice"><span id="product_price">{include file="currency.tpl" value=$product.taxed_price}</span></font>
<span id="product_alt_price">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$product.taxed_price}</span> {$lng.lbl_setup_fee}
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{else}<br />{/if}
+ <font class="ProductPrice">
{include file="currency.tpl" value=$subscription.taxed_price_period}</font>{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$subscription.taxed_price_period} / {if $subscription.pay_period_type eq "Monthly"}{$lng.lbl_month}{elseif $subscription.pay_period_type eq "Annually"}{$lng.lbl_year}{elseif $subscription.pay_period_type eq "Weekly"}{$lng.lbl_week}{elseif $subscription.pay_period_type eq "Quarterly"}{$lng.lbl_quarter}{elseif $subscription.pay_period_type eq "By Period"}{$pay_period} {$lng.lbl_days}{/if}
{if $subscription.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$subscription.taxes is_subtax=true}{/if}
</td>
</tr>
{/if}
