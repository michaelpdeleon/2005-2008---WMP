{* $Id: cart_subtotal.tpl,v 1.5.2.1 2006/06/16 10:47:44 max Exp $ *}
<div align="right">
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}

<table cellpadding="3" cellspacing="0" width="30%">

<tr>
<td nowrap="nowrap"><font class="FormButton" style="text-transform: uppercase;">{$lng.lbl_subtotal}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.display_subtotal}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_subtotal}</td>
</tr>

{if $cart.discount gt 0}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_discount}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.discount}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.discount}</td>
</tr>
{/if}

{if $cart.coupon_discount ne 0 and $cart.coupon_type ne "free_ship"}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_discount_coupon} <a href="cart.php?mode=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a> :</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.coupon_discount}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.coupon_discount}</td>
</tr>
{/if}

{if $cart.discounted_subtotal ne $cart.subtotal}
<tr>
<td colspan="4" height="1"><img src="{$ImagesDir}/spacer_black.gif" width="100%" height="1" alt="" /><br /></td>
</tr>

<tr>
<td nowrap="nowrap"><font class="FormButton" style="text-transform: uppercase;">{$lng.lbl_discounted_subtotal}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.display_discounted_subtotal}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_discounted_subtotal}</td>
</tr>
{/if}

{if $cart.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}

<tr>
<td colspan="4" align="right"><b>{$lng.lbl_including}:</b></td>
</tr>

{foreach key=tax_name item=tax from=$cart.taxes}
<tr class="TableSubHead">
<td nowrap="nowrap" align="right">{$tax.tax_display_name}:</td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right">{include file="currency.tpl" value=$tax.tax_cost_no_shipping}</td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$tax.tax_cost_no_shipping}</td>
</tr>
{/foreach}

{/if}

{if $cart.applied_giftcerts}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_giftcert_discount}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.giftcert_discount}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.giftcert_discount}</font></td>
</tr>
{/if}

</table>

{if $cart.applied_giftcerts}
<br />
<br />
<font class="FormButton">{$lng.lbl_applied_giftcerts}:</font>
<br />
{section name=gc loop=$cart.applied_giftcerts}
{$cart.applied_giftcerts[gc].giftcert_id} <a href="cart.php?mode=unset_gc&gcid={$cart.applied_giftcerts[gc].giftcert_id}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_gc|escape}" /></a> : <font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.applied_giftcerts[gc].giftcert_cost}</font><br />
{/section}
{/if}

{if $not_logged_message eq "1"}{$lng.txt_order_total_msg}{/if}

</div>
<input type="hidden" name="action" value="update" />
<hr width="100%" size="1" noshade="noshade" color="#aaaaaa" />
{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/customer/cart_bonuses.tpl"}
{/if}
