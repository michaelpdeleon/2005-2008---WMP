{* $Id: cart_details.tpl,v 1.19.2.3 2006/07/20 06:39:07 max Exp $ *}
{assign var="colspan" value=4}
<table cellpadding="2" cellspacing="2" width="100%">

<tr>
	<td class="wwmp_cartplaceorder_section" align="left">1. Items in your cart</td>
</tr>
<tr class="wwmp_cartplaceorder_contentstitle">
	<td align="center">{$lng.lbl_product}</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td align="center">{if $cart.product_tax_name ne ""}{$cart.product_tax_name}{else}{$lng.lbl_tax}{/if}</td>
	{math equation="x+1" x=$colspan assign="colspan"}
	{/if}
	<td align="center">{$lng.lbl_quantity}</td>
	<td align="center">{$lng.lbl_price}</td>
	{if $cart.discount gt 0}
	<td align="center">{$lng.lbl_discount}</td>
	{math equation="x+1" x=$colspan assign="colspan"}
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="center">{$lng.lbl_discount_coupon}</td>
	{math equation="x+1" x=$colspan assign="colspan"}
	{/if}
	<td align="center">{$lng.lbl_subtotal}</td>
</tr>

{assign var="products" value=$cart.products}
{assign var="summary_price" value=0}
{assign var="summary_discount" value=0}
{if $active_modules.Discount_Coupons ne ""}
{assign var="summary_coupon_discount" value=0}
{/if}
{assign var="summary_subtotal" value=0}
{section name=prod_num loop=$products}
{if $products[prod_num].deleted eq ""}
{assign var="have_products" value="Y"}
{math equation="x+y*z" x=$summary_price y=$products[prod_num].display_price z=$products[prod_num].amount assign="summary_price"}
{math equation="x+y" x=$summary_discount y=$products[prod_num].discount assign="summary_discount"}
{if $active_modules.Discount_Coupons ne "" and $products[prod_num].coupon_discount}
{math equation="x+y" x=$summary_coupon_discount y=$products[prod_num].coupon_discount assign="summary_coupon_discount"}
{/if}
{math equation="x+y" x=$summary_subtotal y=$products[prod_num].display_subtotal assign="summary_subtotal"}
<!-- Start addition by Michael de Leon 11.15.06 -->
{assign var="shipping_cost" value=$cart.display_shipping_cost}
<!-- End addition by Michael de Leon 11.15.06 -->

<!-- Deleted by Michael de Leon 11.14.06
<tr{* if $bg eq "" *}{* assign var="bg" value="1" *} bgcolor="#FFFFFF"{* else *}{* assign var="bg" value="" *} bgcolor="#EEEEEE"{* /if *}>
-->
<tr>
	<td class="wwmp_cartplaceorder_contentsbox" align="left">
	{if $current_membership_flag ne 'FS'}
		{capture name=link_title}
		{$products[prod_num].product|escape:"html"}
		{if $products[prod_num].product_options}:
			{include file="modules/Product_Options/display_options.tpl" options=$products[prod_num].product_options is_plain='Y'}
		{/if}
		{/capture}
		<a class="wwmp_cartplaceorder_contentitems" href="product.php?productid={$products[prod_num].productid}" title="{$smarty.capture.link_title|escape}">
	{/if}
	{if $products[prod_num].productcode}{$products[prod_num].productcode}{else}#{$products[prod_num].productid}{/if}
	{$products[prod_num].product|truncate:"60":"...":true}
	{if $current_membership_flag ne 'FS'}
		</a>
	{/if}
	</td>
	<td class="wwmp_cartplaceorder_contentsbox" align="center">{if $products[prod_num].hidden or $config.Appearance.allow_update_quantity_in_cart eq "N" or ($active_modules.Egoods and $products[prod_num].distribution) or ($active_modules.Subscriptions and $products[prod_num].sub_plan)}{$products[prod_num].amount}{else}{if $link_qty eq"Y"}<a href="cart.php">{$products[prod_num].amount}</a>{else}<input type="text" size="5" name="productindexes[{$products[prod_num].cartid}]" value="{$products[prod_num].amount}" />{/if}{/if}</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td class="wwmp_cartplaceorder_contentsbox" align="center">
	{foreach from=$products[prod_num].taxes key=tax_name item=tax}
	{if $tax.tax_value gt 0}
		{if $cart.product_tax_name eq ""}<span style="white-space: nowrap;">{$tax.tax_display_name}</span> {/if}
		{if $tax.rate_type eq "%"}{$tax.rate_value|formatprice}%{else}{include file="currency.tpl" value=$tax.rate_value}{/if}
		<br />
	{/if}
	{/foreach}
	</td>
	{/if}
	<td class="wwmp_cartplaceorder_contentsbox" align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].display_price}</td>
	{if $cart.discount gt 0}
		<td class="wwmp_cartplaceorder_contentsbox" align="right" nowrap="nowrap"><font class="wwmp_cartplaceorder_discountprice_label">-{include file="currency.tpl" value=$products[prod_num].discount}</font></td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
		<td class="wwmp_cartplaceorder_contentsbox" align="right" nowrap="nowrap"><font class="wwmp_cartplaceorder_discountprice_label">-{include file="currency.tpl" value=$products[prod_num].coupon_discount}</font></td>
	{/if}
	<td class="wwmp_cartplaceorder_contentsbox" align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].display_subtotal}</td>
</tr>
{/if}
{/section}

{if $cart.products and $have_products eq "Y"}
<tr class="wwmp_cartplaceorder_contentstitle">
<!-- Deleted by Michael de Leon 11.14.06
<th align="left">{* $lng.lbl_summary *}:</th>
-->
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<!-- Deleted by Michael de Leon 11.14.06
	{* if $cart.discount gt 0 *}
	<td align="right" nowrap="nowrap">{* include file="currency.tpl" value=$summary_discount *}</td>
	{* /if *}
	{* if $active_modules.Discount_Coupons ne "" and $cart.coupon *}
	<td align="right" nowrap="nowrap">{* include file="currency.tpl" value=$summary_coupon_discount *}</td>
	{* /if *}
	-->
	<td align="right">{$lng.lbl_subtotal}:</td>
	<td class="wwmp_cartplaceorder_totalsbox" align="right" nowrap="nowrap"><b>{include file="currency.tpl value=$summary_subtotal}</b></td>
</tr>

<!-- Start addition by Michael de Leon 11.15.06 -->
{if $cart.taxes and $config.Taxes.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$cart.taxes}
<tr class="wwmp_cartplaceorder_contentstitle">
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<td align="right">{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</td>
	<!-- Deleted by Michael de Leon 11.15.06
	<td><img src="{* $ImagesDir *}/null.gif" width="5" height="1" alt="" /><br /></td>
	-->
	<td class="wwmp_cartplaceorder_totalsbox" align="right">{if $login ne "" or $config.General.apply_default_country eq "Y"}{include file="currency.tpl" value=$tax.tax_cost}</td>
	<td align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$tax.tax_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}</td>
	<td>{/if}</td>
</tr>
{/foreach}
{/if}

{if $config.Shipping.disable_shipping ne "Y"}
<tr class="wwmp_cartplaceorder_contentstitle">
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<td align="right">{section name=ship_num loop=$shipping}
		{if $shipping[ship_num].shippingid eq $cart.shippingid}
			<a href="cart.php?mode=checkout" class="wwmp_cartplaceorder_shippingcost">{$lng.lbl_shipping_cost}</a>{if $shipping[ship_num].warning ne ''}<br /><font class="ErrorMessage">{$shipping[ship_num].warning}</font>{/if}
		{/if}
	{/section}
	{if $cart.coupon_discount ne 0 and $cart.coupon_type eq "free_ship"} ({$lng.lbl_discounted} <a href="cart.php?mode=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>){/if}
:</td>
	<td class="wwmp_cartplaceorder_totalsbox" align="right">{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}{include file="currency.tpl" value=$shipping_cost}</td>
	<td align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$shipping_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}</td>
	<td>{/if}</td>
</tr>
{/if}

{if $cart.payment_surcharge}
<tr class="wwmp_cartplaceorder_contentstitle">
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<td align="right">{if $cart.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:</td>
	<td class="wwmp_cartplaceorder_totalsbox" align="right">{include file="currency.tpl" value=$cart.payment_surcharge}</td>
	<td align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.payment_surcharge}</td>
</tr>
{/if}

{if $cart.applied_giftcerts}
<tr class="wwmp_cartplaceorder_contentstitle">
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<td align="right">{$lng.lbl_giftcert_discount}:</td>
	<td class="wwmp_cartplaceorder_totalsbox" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.giftcert_discount}</td>
	<td align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.giftcert_discount}</td>
</tr>
{/if}

<tr class="wwmp_cartplaceorder_contentstitle">
	<td>&nbsp;</td>
	{if $cart.display_cart_products_tax_rates eq "Y"}
	<td>&nbsp;</td>
	{/if}
	<td align="right">&nbsp;</td>
	{if $cart.discount gt 0}
	<td align="right">&nbsp;</td>
	{/if}
	{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
	<td align="right">&nbsp;</td>
	{/if}
	<td class="wwmp_cartplaceorder_finaltotal" align="right">{$lng.lbl_cart_total}:</td>
	<td class="wwmp_cartplaceorder_totalsbox" align="right"><font class="wwmp_cartplaceorder_finaltotal">{include file="currency.tpl" value=$cart.total_cost}</font></td>
	<td align="right"><font class="wwmp_cartplaceorder_finaltotal">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.total_cost}</font></td>
</tr>

{if $cart.applied_giftcerts}
<br />
<br />
<font class="FormButton">{$lng.lbl_applied_giftcerts}:</font>
<br />
{section name=gc loop=$cart.applied_giftcerts}
{$cart.applied_giftcerts[gc].giftcert_id} <a href="cart.php?mode=unset_gc&amp;gcid={$cart.applied_giftcerts[gc].giftcert_id}{if $smarty.get.paymentid}&amp;paymentid={$smarty.get.paymentid}{/if}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_gc|escape}" /></a> : <font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.applied_giftcerts[gc].giftcert_cost}</font><br />
{/section}
{/if}

{if $not_logged_message eq "1"}{$lng.txt_order_total_msg}{/if}

<input type="hidden" name="paymentid" value="{$smarty.get.paymentid|escape:"html"}" />
<input type="hidden" name="mode" value="{$smarty.get.mode|escape:"html"}" />
<input type="hidden" name="action" value="update" />
{if $display_ups_trademarks}
<br />
{include file="modules/UPS_OnLine_Tools/ups_notice.tpl"}
{/if}
</div>
{if $active_modules.Special_Offers ne ""}
<!-- Start of editing by Michael de Leon 11.08.06 -->
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<!-- End of editing by Michael de Leon 11.08.06 -->
{include file="modules/Special_Offers/customer/cart_bonuses.tpl"}
{/if}
<!-- End addition by Michael de Leon 11.15.06 -->

{/if}
{if $active_modules.Gift_Certificates ne "" and $cart.giftcerts}
	{include file="modules/Gift_Certificates/gc_cart_details.tpl"}
{/if}
</table>
{if $cart.products and $have_products eq "Y" and $config.Taxes.display_taxed_order_totals eq "Y"}
	<br />
	<div><b>{$lng.txt_notes}:</b><br />
	{$lng.txt_cart_details_notes}
	{if $cart.taxes and $config.Taxes.display_taxed_order_totals eq "Y" and ( $cart.discount gt 0 or ($active_modules.Discount_Coupons ne "" and $cart.coupon) )}
	<br />
	{$lng.txt_cart_details_discount_note}
	{/if}
	</div>
{/if}
<!-- Start addition by Michael de Leon 11.15.06 -->
<br /><br />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
<!-- End addition by Michael de Leon 11.15.06 -->