{* $Id: wl_products.tpl,v 1.19 2006/03/16 15:28:22 svowl Exp $ *}
{if $active_modules.Product_Options}
{include file="main/include_js.tpl" src="modules/Product_Options/edit_product_options.js"}
{/if}

<p />
{if $script_name eq ""}
{assign var="script_name" value="cart"}
{/if}
{if $wl_products ne "" or ($active_modules.Gift_Certificates ne "" and $wl_giftcerts ne "")}
{if $wl_products ne ""}
<table width="100%">
{section name=product loop=$wl_products}
<tr><td width="50" valign="top">
<a href="product.php?productid={$wl_products[product].productid}&amp;quantity={$wl_products[product].amount}">{if $wl_products[product].is_pimage eq 'W' }{assign var="imageid" value=$wl_products[product].variantid}{else}{assign var="imageid" value=$wl_products[product].productid}{/if}{include file="product_thumbnail.tpl" productid=$imageid image_x=$wl_products[product].pimage_x image_y=$wl_products[product].pimage_y product=$wl_products[product].product tmbn_url=$wl_products[product].pimage_url type=$wl_products[product].is_pimage}</a>&nbsp;
</td>
<td valign="top">
{if $wl_products[product].amount_purchased ge $wl_products[product].amount}
<font class="ProductDetailsTitle">{$lng.lbl_purchased}</font>
<br />
{/if}
<a href="product.php?productid={$wl_products[product].productid}&amp;quantity={$wl_products[product].amount}"><font class="ProductTitle">{$wl_products[product].product}</font></a>
<br />
<table width="100%"><tr><td>
{$wl_products[product].descr|truncate:150:"...":true}
</td></tr></table>
{if $wl_products[product].product_options ne ""}
<br />
<br />
<b>{$lng.lbl_selected_options}:</b><br />
{include file="modules/Product_Options/display_options.tpl" options=$wl_products[product].product_options}
{if $wl_products[product].product_options ne "" && $giftregistry eq "" && $source ne "giftreg"}
<br />
{include file="buttons/edit_product_options.tpl" target="wishlist" id=$wl_products[product].wishlistid|cat:"&eventid="|cat:$eventid}
{/if}
<br />
{/if}
{if $active_modules.Product_Configurator ne "" and $wl_products[product].product_type eq "C"}
<p />
{include file="modules/Product_Configurator/pconf_customer_cart.tpl" products=$wl_products[product].subproducts main_product=$wl_products[product]}
{/if}
<p />
{if $active_modules.Subscriptions ne "" and $wl_products[product].catalogprice}
{include file="modules/Subscriptions/subscription_priceincart.tpl" products=$wl_products}
{else}
<form action="cart.php" method="post" name="update{$wl_products[product].wishlistid}_form">
<input type="hidden" name="mode" value="wishlist" />
<input type="hidden" name="eventid" value="{$eventid}" />
<input type="hidden" name="wlitem" value="{$wl_products[product].wishlistid}" />
<input type="hidden" name="action" value="update_quantity" />
<table>
<tr><td nowrap="nowrap">
{assign var="price" value=$wl_products[product].taxed_price}
<font class="ProductPriceConverting">{include file="currency.tpl" value=$price} x
{if $allow_edit eq "Y" && ($wl_products[product].distribution eq '' || $active_modules.Egoods eq '')}
<input type="text" size="3" name="quantity" value="{$wl_products[product].amount}" />
{else}
{$wl_products[product].amount}
{/if}
= </font><font class="ProductPrice">{math equation="price*amount" price=$price amount=$wl_products[product].amount format="%.2f" assign=unformatted}{include file="currency.tpl" value=$unformatted}</font>
</td>
{if $allow_edit eq "Y"}
<td nowrap="nowrap" style="padding-left: 30px;">
{include file="buttons/update.tpl" href="javascript:document.update`$wl_products[product].wishlistid`_form.submit();" js_to_href="Y"}
</td>{/if}
</tr>

{if $wl_products[product].taxes}
<tr>
<td colspan="2">
{include file="customer/main/taxed_price.tpl" taxes=$wl_products[product].taxes}
</td>
</tr>
{/if}

</table>
</form>
{/if}
{if $wl_products[product].amount_purchased gt 0}
{if $wl_products[product].amount_purchased ge $wl_products[product].amount}
&nbsp;({$lng.txt_all_items_already_purchased})
{else}
&nbsp;({$lng.txt_items_already_purchased|substitute:"items":$wl_products[product].amount_purchased})
{/if}
{/if}
{include file="modules/Wishlist/wl_buttons.tpl"}
</td></tr>
<tr><td colspan="2"><hr noshade="noshade" size="1" /></td></tr>
{/section}
</table>
{/if}
{if $active_modules.Gift_Certificates ne ""}
{include file="modules/Gift_Certificates/gc_cart.tpl" giftcerts_data=$wl_giftcerts}
{/if}
{if $giftregistry eq "" and $source ne "giftreg"}
<br />
<form method="post" action="{$script_name}.php" name="sendall_form">
<input type="hidden" name="mode" value="send_friend" />
<input type="hidden" name="action" value="entire_list" />
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="FormButton">{$lng.lbl_send_entire_wishlist}: <input type="text" size="18" name="friend_email" />&nbsp;</td>
	<td>{include file="buttons/button.tpl" href="javascript:document.sendall_form.submit()" js_to_href="Y"}</td>
</tr>
</table>
</form>
<br />
{include file="buttons/button.tpl" button_title=$lng.lbl_wl_clear href="`$script_name`.php?mode=wlclear"}
{/if}
{else}
{$lng.lbl_wl_empty}
{/if}
