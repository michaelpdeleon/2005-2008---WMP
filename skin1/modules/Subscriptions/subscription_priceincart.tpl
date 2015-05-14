<table cellpadding="0" cellspacing="0">

<tr>
<td>{$lng.lbl_subscription_plan}:</td>
<td>&nbsp;</td>
<td>{$products[product].sub_plan}{if $products[product].sub_plan eq "By Period"} ({$products[product].sub_days_remain} {$lng.lbl_days}){/if}</td>
</tr>

{if $products[product].sub_onedayprice > 0}
<tr>
<td>{$lng.lbl_day_cost_by_subscr_plan}:</td>
<td>&nbsp;</td>
<td>{include file="currency.tpl" value=$products[product].sub_onedayprice}</td>
</tr>
{/if}

{if $products[product].sub_days_remain > 0}
<tr>
<td>{$lng.lbl_days_remain}:</td>
<td>&nbsp;</td>
<td>{$products[product].sub_days_remain}</td>
</tr>
{/if}

</table>
<br />

{if $products[product].sub_onedayprice > 0 and $products[product].sub_days_remain > 0}
<font class="ProductPriceConverting">({include file="currency.tpl" value=$products[product].catalogprice} + {include file="currency.tpl" value=$products[product].sub_onedayprice} x {$products[product].sub_days_remain}) x {$products[product].amount} = </font><font class="ProductPrice">{math equation="(price+days*day_cost)*amount" price=$products[product].catalogprice amount=$products[product].amount days=$products[product].sub_days_remain day_cost=$products[product].sub_onedayprice format="%.2f" assign=unformatted}{include file="currency.tpl" value=$unformatted}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$unformatted}</font> 
{else}
<font class="ProductPriceConverting">{include file="currency.tpl" value=$products[product].catalogprice} x {$products[product].amount} = </font><font class="ProductPrice">{math equation="price*amount" price=$price amount=$products[product].amount format="%.2f" assign=unformatted}{include file="currency.tpl" value=$unformatted}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$unformatted}</font> 
{/if}
