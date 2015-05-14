{* $Id: cart.tpl,v 1.95 2006/03/16 15:28:19 svowl Exp $ *}
<!--
{*php*}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{*/php*}
-->
{if $active_modules.Product_Options}
{include file="main/include_js.tpl" src="modules/Product_Options/edit_product_options.js"}
{/if}

<!-- Deleted by Michael de Leon 11.02.06
<h3>{* $lng.lbl_your_shopping_cart *}</h3>
{* if $cart ne '' *}
{* $lng.txt_cart_header *}
{* if $active_modules.Gift_Certificates ne "" *}
{* $lng.txt_cart_note *}
{* /if *}
{* /if *}
<p />
-->
<div align="center">
{capture name=dialog}
<br />
<!-- Deleted by Michael de Leon 11.02.06
{* if $active_modules.Special_Offers *}
{* include file="modules/Special_Offers/customer/cart_offers.tpl" *}
{* /if *}
-->
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
<td valign="top" align="left">
<font class="ProductTitle">{$products[product].product}</font>
<br /><br />
<table cellpadding="0" cellspacing="0" width="100%">
<!-- Start addition by Michael de Leon 09.18.06 -->
<tr><td>
<strong>{$lng.lbl_sku}:</strong> {$products[product].productcode}<br />
{if $active_modules.Extra_Fields ne ""}
	{section name=field loop=$products[product].extra_fields}
		<strong>{$products[product].extra_fields[field].field}:</strong>
	{/section}
	{if $products[product].extra_field1 && $products[product].extra_field1 ne ""}
		{$products[product].extra_field1}
	{else}
		{section name=field loop=$products[product].extra_fields}
			{$products[product].extra_fields[field].field_value}
		{/section}
	{/if}
{/if}<br /><br />
{$products[product].descr}<br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
{if $products[product].product_options ne ""}
<font class="wwmp_cart_label">{$lng.lbl_selected_options}:</font><br />
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="left">{include file="modules/Product_Options/display_options.tpl" options=$products[product].product_options}</td>
	</tr>
	<tr>
		<td align="left">{if $products[product].product_options ne ''}
		{if $config.UA.platform eq 'MacPPC' && $config.UA.browser eq 'MSIE'}
			{include file="buttons/edit_product_options.tpl" id=$products[product].cartid js_to_href="Y"}
		{else}
			{include file="buttons/edit_product_options.tpl" id=$products[product].cartid}
		{/if}
		{/if}</td>
	</tr>
</table>
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
{/if}
{assign var="price" value=$products[product].display_price}
{if $active_modules.Product_Configurator ne "" and $products[product].product_type eq "C"}
{include file="modules/Product_Configurator/pconf_customer_cart.tpl" main_product=$products[product]}
{assign var="price" value=$products[product].pconf_display_price}
<br />
{/if}
{if $active_modules.Subscriptions ne "" and $products[product].sub_plan ne "" and $products[product].product_type ne "C"}
{include file="modules/Subscriptions/subscription_priceincart.tpl"}
{else}
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_price_special.tpl"}
{/if}
<table width="200" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="wwmp_cart_label" align="center">{$lng.lbl_quantity}:</td>
		<td class="wwmp_cart_label" align="center">{$lng.lbl_price}:</td>
	</tr>
	<tr>
		<td align="center"><font class="wwmp_cart_quantitybox">{if $active_modules.Egoods and $products[product].distribution}1<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$products[product].cartid}]" value="{$products[product].amount}" /></font></td>
		<td align="center"><font class="wwmp_cart_price">{include file="currency.tpl" value=$price}</font></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript: document.cartform.submit()"><img src="{$ImagesDir}/wwmp_updatebtn11.07.06.jpg" border="0"></a></td>
		<td align="center"><a href="cart.php?mode=delete&amp;productindex={$products[product].cartid}"><img src="{$ImagesDir}/wwmp_removebtn11.07.06.jpg" border="0"></a></td>
	</tr>
</table>
<!-- Deleted by Michael de Leon 11.07.06
BIG SECTION DELETED
-->
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/cart_free.tpl"}
{/if}
{/if}
</td></tr>
<!-- End addition by Michael de Leon 09.18.06 -->
</table>
<!-- Deleted by Michael de Leon 11.07.06
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow">{* include file="buttons/delete_item.tpl" href="cart.php?mode=delete&amp;productindex=`$products[product].cartid`" *}</td>
	<td class="ButtonsRow">
{* if $products[product].product_options ne '' *}
	{* if $config.UA.platform eq 'MacPPC' && $config.UA.browser eq 'MSIE' *}
		{* include file="buttons/edit_product_options.tpl" id=$products[product].cartid js_to_href="Y" *}
	{* else *}
		{* include file="buttons/edit_product_options.tpl" id=$products[product].cartid *}
	{* /if *}
{* /if *}
	</td>
</tr>
</table>
-->
</td></tr>
<tr><td colspan="2"><hr width="100%" size="1" noshade="noshade" color="#aaaaaa" /></td></tr>
{/if}
{/section}
</table>
{if $active_modules.Gift_Certificates ne ""}
{include file="modules/Gift_Certificates/gc_cart.tpl" giftcerts_data=$cart.giftcerts}
{/if}
<!-- Deleted by Michael de Leon 11.07.06
{* if $main eq "fast_lane_checkout" *}
{* include file="modules/Fast_Lane_Checkout/cart_subtotal.tpl" *}
{* else *}
{* include file="customer/main/cart_totals.tpl" *}
{* /if *}
-->
<input type="hidden" name="action" value="update" />
<br />
<br />
<!-- Deleted by Michael de Leon 11.08.06
{* if $js_enabled *}
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="left">
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow">{* include file="buttons/update.tpl" type="input" href="javascript: document.cartform.submit()" js_to_href="Y" *}</td>
	<td class="ButtonsRow">{* include file="buttons/button.tpl" button_title=$lng.lbl_clear_cart href="cart.php?mode=clear_cart" *}</td>
</tr>
</table>
</td>
{* if $active_modules.Special_Offers *}
{* include file="modules/Special_Offers/customer/cart_checkout_buttons.tpl" *}
{* /if *}
<td>
<table cellpadding="5" cellspacing="0" align="right">
<tr>
<td>
{* include file="buttons/button.tpl" button_title=$lng.lbl_continue_shopping style="button" href="home.php" *}
</td>
<td>
{* include file="buttons/button.tpl" button_title=$lng.lbl_checkout style="button"  href="cart.php?mode=checkout" *}
</td>
</tr>
</table>
</td>
<td align="right">
{* include file="buttons/button.tpl" button_title=$lng.lbl_checkout style="button"  href="cart.php?mode=checkout" *}
</td>
</tr>
</table>
{* else *}
<input type="hidden" name="mode" value="checkout" />
{* include file="submit_wo_js.tpl" value=$lng.lbl_checkout *}
{* /if *}
-->
</form>
{else}
<div align="center">
{$lng.txt_your_shopping_cart_is_empty}
</div>
{/if}
<!-- Start addition by Michael de Leon 11.07.06 -->
{if $cart.coupon_discount eq 0 and $products ne ""}
{if $active_modules.Discount_Coupons ne ""}
{include file="modules/Discount_Coupons/add_coupon.tpl}
{/if}
{/if}
<!-- End addition by Michael de Leon 11.07.06 -->
{/capture}
{include file="dialog_shoppingcart.tpl" title=$lng.lbl_items_in_cart content=$smarty.capture.dialog extra='width="634"'}
<!-- Deleted by Michael de Leon 11.06.06
{* include file="dialog_shoppingcart.tpl" title=$lng.lbl_items_in_cart content=$smarty.capture.dialog extra='width="100%"' *}
-->
</div>
