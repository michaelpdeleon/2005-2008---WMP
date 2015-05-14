{* $Id: cart.tpl,v 1.95 2006/03/16 15:28:19 svowl Exp $ *}
{if $active_modules.Product_Options}
{include file="main/include_js.tpl" src="modules/Product_Options/edit_product_options.js"}
{/if}

<h3>{$lng.lbl_your_shopping_cart}</h3>
{if $cart ne ''}
{$lng.txt_cart_header}
{if $active_modules.Gift_Certificates ne ""}
{$lng.txt_cart_note}
{/if}
{/if}
<p />
{capture name=dialog}
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_offers.tpl"}
{/if}
<p />
{if $products ne ""}
<form action="cart.php" method="post" name="cartform">
<table width="100%">
{section name=product loop=$products}
{if $products[product].hidden eq ""}
<tr><td class="PListImgBox">
<a href="product.php?productid={$products[product].productid}">{if $products[product].is_pimage eq 'W' }{assign var="imageid" value=$products[product].variantid}{else}{assign var="imageid" value=$products[product].productid}{/if}{include file="product_thumbnail.tpl" productid=$imageid image_x=$config.Appearance.thumbnail_width product=$products[product].product tmbn_url=$products[product].pimage_url type=$products[product].is_pimage}</a>
{if $active_modules.Special_Offers ne "" and $products[product].have_offers}
{include file="modules/Special_Offers/customer/product_offer_thumb.tpl" product=$products[product]}
{/if}
</td>
<td valign="top">
<font class="ProductTitle">{$products[product].product}</font>
<p />
<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
{$products[product].descr}
</td></tr></table>
<br />
<br />
{if $products[product].product_options ne ""}
<b>{$lng.lbl_selected_options}:</b><br />
{include file="modules/Product_Options/display_options.tpl" options=$products[product].product_options}
<br />
<br />
{/if}
{assign var="price" value=$products[product].display_price}
{if $active_modules.Product_Configurator ne "" and $products[product].product_type eq "C"}
{include file="modules/Product_Configurator/pconf_customer_cart.tpl" main_product=$products[product]}
{assign var="price" value=$products[product].pconf_display_price}
<br /><br />
{/if}
<div align="left">
{if $active_modules.Subscriptions ne "" and $products[product].sub_plan ne "" and $products[product].product_type ne "C"}
{include file="modules/Subscriptions/subscription_priceincart.tpl"}
{else}
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_price_special.tpl"}
{/if}
<font class="ProductPriceConverting">{include file="currency.tpl" value=$price} x {if $active_modules.Egoods and $products[product].distribution}1<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$products[product].cartid}]" value="{$products[product].amount}" /> = </font><font class="ProductPrice">{math equation="price*amount" price=$price amount=$products[product].amount format="%.2f" assign=unformatted}{include file="currency.tpl" value=$unformatted}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$unformatted}</font>
{if $config.Taxes.display_taxed_order_totals eq "Y" and $products[product].taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}
{/if}
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_free.tpl"}
{/if}
{/if}
<br />
<br />
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow">{include file="buttons/delete_item.tpl" href="cart.php?mode=delete&amp;productindex=`$products[product].cartid`"}</td>
	<td class="ButtonsRow">
{if $products[product].product_options ne ''}
{if $config.UA.platform eq 'MacPPC' && $config.UA.browser eq 'MSIE'}
{include file="buttons/edit_product_options.tpl" id=$products[product].cartid js_to_href="Y"}
{else}
{include file="buttons/edit_product_options.tpl" id=$products[product].cartid}
{/if}
{/if}
	</td>
</tr>
</table>
</div>
</td></tr>
<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>
{/if}
{/section}
</table>
{if $active_modules.Gift_Certificates ne ""}
{include file="modules/Gift_Certificates/gc_cart.tpl" giftcerts_data=$cart.giftcerts}
{/if}
{if $main eq "fast_lane_checkout"}
{include file="modules/Fast_Lane_Checkout/cart_subtotal.tpl"}
{else}
{include file="customer/main/cart_totals.tpl"}
{/if}
<br />
<br />
{if $js_enabled}
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td>
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow">{include file="buttons/update.tpl" type="input" href="javascript: document.cartform.submit()" js_to_href="Y"}</td>
	<td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_clear_cart href="cart.php?mode=clear_cart"}</td>
</tr>
</table>
</td>
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_checkout_buttons.tpl"}
{/if}
<td align="right">
{include file="buttons/button.tpl" button_title=$lng.lbl_checkout style="button"  href="cart.php?mode=checkout"}
</td>
</tr>
</table>
{else}
<input type="hidden" name="mode" value="checkout" />
{include file="submit_wo_js.tpl" value=$lng.lbl_checkout}
{/if}
</form>
{else}
{$lng.txt_your_shopping_cart_is_empty}
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_items_in_cart content=$smarty.capture.dialog extra='width="100%"'}
{if $cart.coupon_discount eq 0 and $products ne ""}
<p />
{if $active_modules.Discount_Coupons ne ""}
{include file="modules/Discount_Coupons/add_coupon.tpl}
{/if}
{/if}
