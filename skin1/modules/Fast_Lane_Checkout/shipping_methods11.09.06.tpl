{* $Id: shipping_methods.tpl,v 1.7.2.1 2006/05/15 13:10:46 max Exp $ *}
<table cellpadding="5" cellspacing="5" width="100%">

<tr>
<td valign="top" width="30%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_shipping_address}
{if $userinfo}
{$userinfo.s_address}<br />
{$userinfo.s_city}<br />
{$userinfo.s_statename}<br />
{$userinfo.s_countryname}<br />
{$userinfo.s_zipcode}
{else}
No data
{/if}

{if $login ne ""}
<br /><br />
{include file="buttons/modify.tpl" href="register.php?mode=update&action=cart"}
{/if}

</td>
<td valign="top" width="70%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_delivery}

{*  ERROR: no shipping methods available [begin]  *}
{if $shipping_calc_error ne ""}
{$shipping_calc_service} {$lng.lbl_err_shipping_calc}<br />
<font class="ErrorMessage">{$shipping_calc_error}</font><br />
{/if}
{if $shipping eq "" and $need_shipping}
<font class="ErrorMessage">{$lng.lbl_no_shipping_for_location}</font><br />
<br />
{/if}
{*  ERROR: no shipping methods available [end]  *}

{*  Select the shipping carrier [begin]  *}
{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}

{if $active_modules.UPS_OnLine_Tools and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and $show_carriers_selector eq "Y"}
<font class="FormButton"><label for="">{$lng.lbl_shipping_carrier}:</label> </font>
<select name="selected_carrier" id="selected_carrier" onchange="javascript: self.location='cart.php?mode=checkout&amp;action=update&amp;selected_carrier='+this.options[this.selectedIndex].value;">
<option value="UPS"{if $current_carrier eq "UPS"} selected="selected"{/if}>{$lng.lbl_ups_carrier}</option>
<option value=""{if $current_carrier ne "UPS"} selected="selected"{/if}>{$lng.lbl_other_carriers}</option>
</select>
<br /><br />
{/if}

{/if}
{*  Select the shipping carrier: [end]  *}

{*  Select the shipping method: [begin]  *}
{if $shipping ne "" and $need_shipping}

{if $arb_account_used}
{$lng.txt_arb_account_checkout_note}
<br />
{elseif $use_airborne_account}
{$lng.lbl_arb_account}: <input type="text" name="arb_account" value="{$airborne_account}" /><br />
{/if}{* $arb_account_used *}

{if $login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0}
{foreach from=$shipping item=s}
<table cellpadding="1" cellspacing="0" width="100%"{cycle values=" class='TableSubHead', "}>
<tr>
	<td width="5"><input type="radio" id="shippingid{$s.shippingid}" name="shippingid" value="{$s.shippingid}"{if $s.shippingid eq $cart.shippingid} checked="checked"{/if}{if $allow_cod} onclick="javascript: display_cod({if $s.is_cod eq 'Y'}true{else}false{/if});"{/if} /></td>
	<td><label for="shippingid{$s.shippingid}">{$s.shipping|trademark:$insert_trademark}{if $s.shipping_time ne ""} - {$s.shipping_time}{/if}{if $config.Appearance.display_shipping_cost eq "Y" and ($login ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0)} ({include file="currency.tpl" value=$s.rate}){/if}</label></td>
</tr>
{if $s.warning ne ""}
<tr>
	<td>&nbsp;</td>
	<td class="{if $s.shippingid eq $cart.shippingid}ErrorMessage{else}SmallText{/if}">{$s.warning}</td>
</tr>
{/if}
</table>
{/foreach}

<br /><br />
{/if}
{else}
<input type="hidden" name="shippingid" value="0" />
{/if}
{*  Select the shipping method: [end]  *}

</td>
</tr>

</table>

{if $display_ups_trademarks}
{include file="modules/UPS_OnLine_Tools/ups_notice.tpl"}
{/if}

