{if $subscription}
<tr>
	<td>{$lng.lbl_subscription_plan}:</td>
	<td>
{math equation="onedayprice*daysremain" onedayprice=$subscription.oneday_price daysremain=$subscription.days_remain assign="subprice"}
<font class="ProductPrice">{include file="currency.tpl" value=$subprice}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$subprice}</font>
	</td>
</tr>

<tr>
	<td><b><font style="TEXT-TRANSFORM: uppercase;">{$lng.lbl_total}:</font></b></td>
	<td>
{math equation="price+subprice" price=$product.price subprice=$subprice assign="totalprice"}
<font class="ProductPrice">{include file="currency.tpl" value=$totalprice}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$totalprice}</font>
	</td>
</tr>
{/if}
