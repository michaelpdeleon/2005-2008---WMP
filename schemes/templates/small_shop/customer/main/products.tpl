{* $Id: products.tpl,v 1.6 2005/12/13 15:01:06 max Exp $ *}
<table cellpadding="10">
{section name=product loop=$products}
{if %product.index% is not odd}
<tr>
{/if}
	<td valign="top">
<table width="100%">
<tr>
	<td width="90" align="center" valign="top">
<a href="product.php?productid={$products[product].productid}">{include file="product_thumbnail.tpl" productid=$products[product].productid image_x=$config.Appearance.thumbnail_width product=$products[product].product tmbn_url=$products[product].tmbn_url}<br>{$lng.lbl_see_details}</a>
	</td>
	<td valign="top">
<a href="product.php?productid={$products[product].productid}"><font class="ProductTitle">{$products[product].product}</font></a>
<font size="1">
<br>
<br>
{$products[product].descr|truncate:300:"...":true}
<br>
</font>
<hr size="1" noshade="noshade" width="230" align="left" />
{if $products[product].product_type eq "C"}
{include file="buttons/details.tpl" href="product.php?productid=`$products[product].productid`&cat=`$cat`&page=`$navigation_page`"}
{else}
{if $active_modules.Subscriptions ne "" and $products[product].catalogprice}
{include file="modules/Subscriptions/subscription_info_inlist.tpl"}
{else}
{if $products[product].taxed_price ne 0}
{if $products[product].list_price gt 0 and $products[product].taxed_price lt $products[product].list_price}
{math equation="100-(price/lprice)*100" price=$products[product].taxed_price lprice=$products[product].list_price format="%d" assign=discount}
{if $discount gt 0}
<font class="MarketPrice">{$lng.lbl_market_price}: <s>
{include file="currency.tpl" value=$products[product].list_price}
</s></font><br>
{/if}
{/if}
<font class="ProductPrice">{$lng.lbl_our_price}: {include file="currency.tpl" value=$products[product].taxed_price}</font><font class="MarketPrice">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price}</font>{if $discount gt 0}, {$lng.lbl_save_price} {$discount}%{/if}
{if $products[product].taxes}<br>{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}{/if}
{else}
<font class="ProductPrice">{$lng.lbl_enter_your_price}</font>
{/if}
{/if}
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file="customer/main/buy_now.tpl" product=$products[product]}
{/if}
{/if}
	</td>
</tr>
</table>
<br>
<br>
	</td>
{if %product.index% is odd}
</tr>
{/if}
{/section}
</table>
