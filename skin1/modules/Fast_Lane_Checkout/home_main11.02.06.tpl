{if $checkout_step eq 0}
{include file="modules/Fast_Lane_Checkout/checkout_0_enter.tpl"}

{elseif $checkout_step eq 1}
{include file="modules/Fast_Lane_Checkout/checkout_1_profile.tpl"}

{elseif $checkout_step eq 2}
{include file="modules/Fast_Lane_Checkout/checkout_2_method.tpl"}

{elseif $checkout_step eq 3}
{include file="modules/Fast_Lane_Checkout/checkout_3_place.tpl"}

{else}

<div align="right">
<table cellpadding="0" cellspacing="0">
<tr>
<td>{include file="modules/Fast_Lane_Checkout/big_button.tpl" button_title=$lng.lbl_continue_shopping style="button" href="home.php"}</td>
<td><img src="{$ImagesDir}/spacer.gif" width="10" height="1" alt="" /></td>
<td align="right">{include file="modules/Fast_Lane_Checkout/big_button.tpl" button_title=$lng.lbl_checkout style="button" href="cart.php?mode=checkout" color="red" arrow="Y"}</td>
</tr>
</table>
</div>

{include file="customer/main/cart.tpl"}

{/if}
