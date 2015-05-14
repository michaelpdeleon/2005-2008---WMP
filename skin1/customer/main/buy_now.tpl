{* $Id: buy_now.tpl,v 1.30.2.2 2006/06/14 11:06:25 max Exp $ *}
{if $product.price gt 0}
<form name="orderform_{$product.productid}_{$product.add_date}" method="post" action="{if $product.is_product_options eq 'Y' && $config.Product_Options.buynow_with_options_enabled eq 'Y'}product.php?productid={$product.productid}{else}cart.php?mode=add{/if}">
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
{/if}

<table width="100%" cellpadding="0" cellspacing="0">
{if $product.price eq 0}
<tr>
	<td height="25">
{assign var="button_href" value=$smarty.get.page|escape:"html"}
{include file="buttons/buy_now.tpl" style="button" href="product.php?productid=`$product.productid`&cat=`$cat`&page=`$button_href`"}
	</td>
</tr>
{else}
{if $product.is_product_options ne 'Y' || $config.Product_Options.buynow_with_options_enabled ne 'Y'}
<tr>
{if $product.distribution eq "" and !($active_modules.Subscriptions ne "" and $products[product].catalogprice)}
	<td class="BuyNowQuantity">{$lng.lbl_quantity}</td>
	<td width="20%" nowrap="nowrap">
{if $config.General.unlimited_products ne "Y" and ($product.avail le 0 or $product.avail lt $product.min_amount)}
<b>{$lng.txt_out_of_stock}</b>
{else}
{if $config.General.unlimited_products eq "Y"}
{assign var="mq" value=$config.Appearance.max_select_quantity}
{else}
{math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"} 
{if $tmp<2}
{assign var="minamount" value=$product.min_amount} 
{else} 
{assign var="minamount" value=1}
{/if} 
{math equation="min(maxquantity+minamount, productquantity+1)" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$minamount productquantity=$product.avail}
{/if}
{if $product.min_amount le 1}
{assign var="start_quantity" value=1}
{else}
{assign var="start_quantity" value=$product.min_amount}
{/if}
{if $config.General.unlimited_products eq "Y"}
{math equation="x+y" assign="mq" x=$mq y=$start_quantity}
{/if}
<select name="amount">
{section name=quantity loop=$mq start=$start_quantity}
	<option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
{/section}
</select>
{/if}
	</td>
{else}
<tr style="display: none;">
	<td><input type="hidden" name="amount" value="1" /></td>
</tr>
{/if}
	<td class="BuyNowPrices">
<input type="hidden" name="mode" value="add" />
{include file="customer/main/product_prices.tpl" no_span=true}
	</td>
</tr>
{/if}
<tr>
	<td colspan="3">
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
<br />

<table cellpadding="0" cellspacing="0">
<tr>
{if $js_enabled}
{if $special_offers_add_to_cart eq 'Y'}
	<td>{include file="buttons/add_to_cart.tpl" style="button" href="javascript: document.orderform_`$product.productid`_`$product.add_date`.submit();"}</td>
{else}
	<td>{include file="buttons/buy_now.tpl" style="button" href="javascript: document.orderform_`$product.productid`_`$product.add_date`.submit();"}</td>
{/if}
{if ($login ne "" || $config.Wishlist.add2wl_unlogged_user eq 'Y') && $active_modules.Wishlist ne "" && $special_offers_add_to_cart eq "" && ($product.is_product_options ne 'Y' || $config.Product_Options.buynow_with_options_enabled ne 'Y')}
	<td style="padding-left: 20px;">
{include file="buttons/add_to_wishlist.tpl" style="button" href="javascript:document.orderform_`$product.productid`_`$product.add_date`.mode.value='add2wl'; document.orderform_`$product.productid`_`$product.add_date`.submit()"}
	</td>
{/if}
{else}
	<td>{include file="submit_wo_js.tpl" value=$lng.lbl_buy_now}</td>
{/if}
</tr>
</table>

{/if}
	</td>
</tr>
{if $product.min_amount gt 1}
<tr>
	<td colspan="3"><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font></td>
</tr>
{/if}
{/if}
</table>
{if $product.price gt 0}
</form>
{/if}

