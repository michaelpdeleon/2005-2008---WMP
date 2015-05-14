{* $Id: gc_cart.tpl,v 1.15 2006/02/10 14:15:19 max Exp $ *}
{if $giftcerts_data ne ""}
{section name=giftcert loop=$giftcerts_data}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="100" valign="top"><img src="{$ImagesDir}/gift.gif" width="84" height="69" alt="" /></td>
	<td valign="top">

{if $giftcerts_data[giftcert].amount_purchased gt 1}
<font class="ProductDetailsTitle">{$lng.lbl_purchased}</font>
<br />
{/if}

<font class="ProductTitle">{$lng.lbl_gift_certificate}</font>
<p />
<font class="TableCenterCustomerForm">{$lng.lbl_recipient}:</font> {$giftcerts_data[giftcert].recipient}<br />

{if $giftcerts_data[giftcert].send_via eq "E"}
<font class="TableCenterCustomerForm">{$lng.lbl_email}:</font> {$giftcerts_data[giftcert].recipient_email}<br />
{elseif $giftcerts_data[giftcert].send_via eq "P"}
<font class="TableCenterCustomerForm">{$lng.lbl_mail_address}:</font> {$giftcerts_data[giftcert].recipient_address}, {$giftcerts_data[giftcert].recipient_city}, {if $config.General.use_counties eq "Y"}{$giftcerts_data[giftcert].recipient_countyname} {/if}{$giftcerts_data[giftcert].recipient_state} {$giftcerts_data[giftcert].recipient_country} {$giftcerts_data[giftcert].recipient_zipcode}<br />
{if $giftcerts_data[giftcert].recipient_phone}
<font class="TableCenterCustomerForm">{$lng.lbl_phone}:</font> {$giftcerts_data[giftcert].recipient_phone}<br />
{/if}
{/if}
<font class="TableCenterCustomerForm">{$lng.lbl_amount}:</font> <font class="TableCenterProductPriceOrange">{include file="currency.tpl" value=$giftcerts_data[giftcert].amount}</font>
<br />
<br />

{if $active_modules.Wishlist ne "" and $wl_giftcerts ne ""}
{include file="modules/Wishlist/wl_buttons.tpl" buttons_for="giftcerts"}
{else}
<table cellspacing="0" cellpadding="0">
<tr>
	<td class="ButtonsRow">{include file="buttons/delete_item.tpl" href="giftcert.php?mode=delgc&gcindex=`$smarty.section.giftcert.index`"}</td>
	<td class="ButtonsRow">{include file="buttons/modify.tpl" href="giftcert.php?gcindex=`$smarty.section.giftcert.index`"}</td>
</tr>
</table>
{/if}

	</td>
</tr>
</table>
<hr size="1" noshade="noshade" />
{/section}
{/if}
