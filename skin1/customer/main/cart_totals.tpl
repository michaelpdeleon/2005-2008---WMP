{* $Id: cart_totals.tpl,v 1.91.2.1 2006/06/16 10:47:41 max Exp $ *}
<div align="right">
{if $config.Shipping.disable_shipping ne "Y"}
	{if $link_shipping eq "Y"}
	<!-- Deleted by Michael de Leon 11.15.06
		<font class="FormButton">{* $lng.lbl_delivery *}:</font>
		{* section name=ship_num loop=$shipping *}
		{* if $shipping[ship_num].shippingid eq $cart.shippingid *}
			<a href="cart.php?mode=checkout" class="ShippingMethod">{* $shipping[ship_num].shipping|trademark:$insert_trademark:"alt" *}</a>{* if $shipping[ship_num].warning ne '' *}<br /><font class="ErrorMessage">{* $shipping[ship_num].warning *}</font>{* /if *}
		{* /if *}
		{* /section *}
		<br /><br />
	-->
	{else}
		{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}
			{if $active_modules.UPS_OnLine_Tools and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and $show_carriers_selector eq "Y"}
				<font class="FormButton">{$lng.lbl_shipping_carrier}: </font>
				<select name="selected_carrier" onchange="javascript: document.cartform.submit();">
				<option value="UPS"{if $current_carrier eq "UPS"} selected="selected"{/if}>{$lng.lbl_ups_carrier}</option>
				<option value=""{if $current_carrier ne "UPS"} selected="selected"{/if}>{$lng.lbl_other_carriers}</option>
				</select>
				<br /><br />
			{/if}
		{/if}
		{if $shipping_calc_error ne ""}
			{$shipping_calc_service} {$lng.lbl_err_shipping_calc}<br />
			<font class="ErrorMessage">{$shipping_calc_error}</font><br />
		{/if}
		{if $shipping eq "" and $need_shipping}
			<font class="ErrorMessage">{$lng.lbl_no_shipping_for_location}:</font><br />
			{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}
				{$userinfo.s_address}<br />
				{$userinfo.s_city}, {$userinfo.s_statename}<br />
				{$userinfo.s_zipcode}<br />
				{$userinfo.s_countryname}
			{else}
				{$lng.lbl_anonymous}
			{/if}
			{if $login ne ""}
				<br />
				{include file="buttons/modify.tpl" href="register.php?mode=update&action=cart"}
			{/if}
			<hr noshade="noshade" size="1" width="50%" />
		{/if}
		{if $shipping ne "" and $need_shipping}
			{if $arb_account_used}
				{$lng.txt_arb_account_checkout_note}
				<br />
			{/if}{* $arb_account_used *}
			{if $active_modules.UPS_OnLine_Tools ne "" and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and $current_carrier eq "UPS" and $force_delivery_dropdown_box ne "Y"}
				{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top"><font class="FormButton">{$lng.lbl_delivery}: </font>
						<br />
						<table cellpadding="1" cellspacing="0">
						{section name=ship_num loop=$shipping}
						<tr>
							<td width="5"{if $shipping[ship_num].shippingid eq $cart.shippingid} class="TableHead"{/if}><input type="radio" name="shippingid" value="{$shipping[ship_num].shippingid}" {if $shipping[ship_num].shippingid eq $cart.shippingid}checked{else}onclick="document.cartform.submit()"{/if} /></td>
							<td {if $shipping[ship_num].shippingid eq $cart.shippingid} class="TableHead"{/if}{$shipping[ship_num].shipping|trademark:$insert_trademark}{if $shipping[ship_num].shipping_time ne ""} - {$shipping[ship_num].shipping_time}{/if}{if $config.Appearance.display_shipping_cost eq "Y" and ($login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0)} ({include file="currency.tpl" value=$shipping[ship_num].rate}){/if}</td>
						</tr>
						{if $shipping[ship_num].shippingid eq $cart.shippingid and $shipping[ship_num].warning ne ""}
							{assign var="warning" value=$shipping[ship_num].warning}
						{/if}
						{if $shipping[ship_num].warning ne ''}
						<tr>
							<td>&nbsp;</td>
							<td class="SmallText">{$shipping[ship_num].warning}</td>
						</tr>
						{/if}
						{/section}
						</table>
						</td>
					</tr>
					</table>
					{if $warning ne ""}
						<div align="right" class="ErrorMessage">{$warning}</div>
					{/if}
					<br /><br />
				{/if}
			{else}{* $active_modules.UPS_OnLine_Tools *}
				{if $use_airborne_account}
					{$lng.lbl_arb_account}: <input type="text" name="arb_account" value="{$airborne_account}" /><br />
				{/if}
				<font class="FormButton">{$lng.lbl_delivery}: </font>
				<select name="shippingid" onchange="document.cartform.submit()">
				{section name=ship_num loop=$shipping}
				<option value="{$shipping[ship_num].shippingid}"{if $shipping[ship_num].shippingid eq $cart.shippingid} selected="selected"{/if}>{$shipping[ship_num].shipping|trademark:$insert_trademark:"alt"}{if $config.Appearance.display_shipping_cost eq "Y" and ($login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0)} ({include file="currency.tpl" value=$shipping[ship_num].rate plain_text_message=1}){/if}{if $shipping[ship_num].shipping_time ne ""} - {$shipping[ship_num].shipping_time}{/if}</option>
				{if $shipping[ship_num].shippingid eq $cart.shippingid and $shipping[ship_num].warning ne ""}
					{assign var="warning" value=$shipping[ship_num].warning}
				{/if}
				{/section}
				</select>
				{if $warning ne ''}
					<div align="right" class="ErrorMessage">{$lng.lbl_note}: {$warning}</div><br />
				{/if}
			{/if}
		{else}
			<input type="hidden" name="shippingid" value="0" />
		{/if}
	{/if}
{else}
	<input type="hidden" name="shippingid" value="0" />
{/if}
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}
{assign var="shipping_cost" value=$cart.display_shipping_cost}

<table cellpadding="2" cellspacing="2" width="30%">

<!-- Deleted by Michael de Leon 11.15.06
<tr>
<td nowrap="nowrap"><font class="FormButton">{* $lng.lbl_subtotal *}:</font></td>
<td><img src="{* $ImagesDir *}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{* include file="currency.tpl" value=$cart.display_subtotal *}</font></td>
<td nowrap="nowrap" align="right">{* include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_subtotal *}</td>
</tr>
-->

{if $cart.taxes and $config.Taxes.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$cart.taxes}
<tr>
	<td nowrap="nowrap"><font class="FormButton">{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</font></td>
	<!-- Deleted by Michael de Leon 11.15.06
	<td><img src="{* $ImagesDir *}/null.gif" width="5" height="1" alt="" /><br /></td>
	-->
	<td class="wwmp_cartplaceorder_totalsbox" nowrap="nowrap" align="right"><font class="ProductPriceSmall">{if $login ne "" or $config.General.apply_default_country eq "Y"}{include file="currency.tpl" value=$tax.tax_cost}</font></td>
	<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$tax.tax_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}</font></td><td>{/if}</td>
</tr>
{/foreach}
{/if}

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

{if $cart.display_discounted_subtotal ne $cart.display_subtotal}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_discounted_subtotal}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.display_discounted_subtotal}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.display_discounted_subtotal}</td>
</tr>
{/if}

{if $config.Shipping.disable_shipping ne "Y"}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_shipping_cost}{if $cart.coupon_discount ne 0 and $cart.coupon_type eq "free_ship"} ({$lng.lbl_discounted} <a href="cart.php?mode=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>){/if}
:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}{include file="currency.tpl" value=$shipping_cost}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$shipping_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}</font></td><td>{/if}</td>
</tr>
{/if}

{if $cart.payment_surcharge}
<tr>
<td nowrap="nowrap"><font class="FormButton">{if $cart.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.payment_surcharge}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.payment_surcharge}</td>
</tr>
{/if}

{if $cart.applied_giftcerts}
<tr>
<td nowrap="nowrap"><font class="FormButton">{$lng.lbl_giftcert_discount}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.giftcert_discount}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.giftcert_discount}</font></td>
</tr>
{/if}

<tr>
<td colspan="4" height="1"><img src="{$ImagesDir}/spacer_black.gif" width="100%" height="1" alt="" /><br /></td>
</tr>

<tr>
<td nowrap="nowrap"><font class="FormButton" style="text-transform: uppercase;">{$lng.lbl_cart_total}:</font></td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right"><font class="ProductPriceSmall">{include file="currency.tpl" value=$cart.total_cost}</font></td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$cart.total_cost}</td>
</tr>

{if $cart.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}

<tr>
<td colspan="4" align="right"><b>{$lng.lbl_including}:</b></td>
</tr>

{foreach key=tax_name item=tax from=$cart.taxes}
<tr class="TableSubHead">
<td nowrap="nowrap" align="right">{$tax.tax_display_name}:</td>
<td><img src="{$ImagesDir}/null.gif" width="5" height="1" alt="" /><br /></td>
<td nowrap="nowrap" align="right">{include file="currency.tpl" value=$tax.tax_cost}</td>
<td nowrap="nowrap" align="right">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$tax.tax_cost}</td>
</tr>
{/foreach}

{/if}


</table>
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
