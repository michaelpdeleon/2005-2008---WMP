{* $Id: cart_details.tpl,v 1.19.2.3 2006/07/20 06:39:07 max Exp $ *}
{assign var="colspan" value=4}
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
<td>{$lng.lbl_product}</td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td align="center">{if $cart.product_tax_name ne ""}{$cart.product_tax_name}{else}{$lng.lbl_tax}{/if}</td>
{math equation="x+1" x=$colspan assign="colspan"}
{/if}
<td align="right">{$lng.lbl_price}</td>
<td align="center">{$lng.lbl_quantity}</td>
{if $cart.discount gt 0}
<td align="right">{$lng.lbl_discount}</td>
{math equation="x+1" x=$colspan assign="colspan"}
{/if}
{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
<td align="right">{$lng.lbl_discount_coupon}</td>
{math equation="x+1" x=$colspan assign="colspan"}
{/if}
<td align="right">{$lng.lbl_subtotal}</td>
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

<tr{if $bg eq ""}{assign var="bg" value="1"} bgcolor="#FFFFFF"{else}{assign var="bg" value=""} bgcolor="#EEEEEE"{/if}>
	<td>
{if $current_membership_flag ne 'FS'}
{capture name=link_title}
{$products[prod_num].product|escape:"html"}
{if $products[prod_num].product_options}:
{include file="modules/Product_Options/display_options.tpl" options=$products[prod_num].product_options is_plain='Y'}
{/if}
{/capture}
<a href="product.php?productid={$products[prod_num].productid}" title="{$smarty.capture.link_title|escape}">
{/if}
{if $products[prod_num].productcode}{$products[prod_num].productcode}{else}#{$products[prod_num].productid}{/if}. {$products[prod_num].product|truncate:"30":"...":true}
{if $current_membership_flag ne 'FS'}
</a>
{/if}
	</td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td align="center">
{foreach from=$products[prod_num].taxes key=tax_name item=tax}
{if $tax.tax_value gt 0}
{if $cart.product_tax_name eq ""}<span style="white-space: nowrap;">{$tax.tax_display_name}</span> {/if}
{if $tax.rate_type eq "%"}{$tax.rate_value|formatprice}%{else}{include file="currency.tpl" value=$tax.rate_value}{/if}<br />
{/if}
{/foreach}
</td>
{/if}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].display_price}</td>
<td align="center">{if $products[prod_num].hidden or $config.Appearance.allow_update_quantity_in_cart eq "N" or ($active_modules.Egoods and $products[prod_num].distribution) or ($active_modules.Subscriptions and $products[prod_num].sub_plan)}{$products[prod_num].amount}{else}{if $link_qty eq"Y"}<a href="cart.php">{$products[prod_num].amount}</a>{else}<input type="text" size="5" name="productindexes[{$products[prod_num].cartid}]" value="{$products[prod_num].amount}" />{/if}{/if}</td>
{if $cart.discount gt 0}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].discount}</td>
{/if}
{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].coupon_discount}</td>
{/if}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$products[prod_num].display_subtotal}</td>
</tr>
{/if}
{/section}

{if $cart.products and $have_products eq "Y"}
<tr class="TableHead">
<th align="left">{$lng.lbl_summary}:</th>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td>&nbsp;</td>
{/if}
<td align="right">&nbsp;</td>
<td align="right">&nbsp;</td>
{if $cart.discount gt 0}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$summary_discount}</td>
{/if}
{if $active_modules.Discount_Coupons ne "" and $cart.coupon}
<td align="right" nowrap="nowrap">{include file="currency.tpl" value=$summary_coupon_discount}</td>
{/if}
<td align="right" nowrap="nowrap"><b>{include file="currency.tpl value=$summary_subtotal}</b></td>
</tr>
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
