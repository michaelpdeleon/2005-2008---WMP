{* $Id: wl_buttons.tpl,v 1.12 2005/12/15 12:59:14 max Exp $ *}
{if $buttons_for ne "giftcerts"}
{assign var="wlitem_data" value=$wl_products[product]}
{else}
{assign var="wlitem_data" value=$giftcerts_data[giftcert]}
{/if}
<p />
<table cellspacing="1">
<tr>
{if $buttons_for ne "giftcerts"}
<td class="ButtonsRow">{include file="buttons/details.tpl" href="product.php?productid=`$wlitem_data.productid`&quantity=`$wlitem_data.amount`"}</td>
{elseif $allow_edit eq "Y"}
<td class="ButtonsRow">{include file="buttons/modify.tpl" href="giftcert.php?gcindex=`$wlitem_data.wishlistid`&action=wl"}</td>
{/if}
{if (($wl_products and $wl_products[product].amount_purchased < $wl_products[product].amount and $wl_products[product].avail gt "0") or $config.General.unlimited_products eq "Y") or $main_mode eq "manager" or $buttons_for eq "giftcerts" or $wl_products[product].product_type eq "C"}
{if $login}
{if $giftregistry eq ""}
<td class="ButtonsRow">{include file="buttons/add_to_cart.tpl" href="cart.php?mode=wl2cart&wlitem=`$wlitem_data.wishlistid`"}</td>
{else}
<td class="ButtonsRow">{include file="buttons/add_to_cart.tpl" href="cart.php?mode=wl2cart&fwlitem=`$wlitem_data.wishlistid`&eventid=`$eventid`"}</td>
{/if}
{/if}
{else}
{if $wl_products[product].amount > $wl_products[product].avail}
<td class="ButtonsRow"><b>{$lng.txt_out_of_stock}</b></td>
{/if}
{/if}
{if $giftregistry eq ""}
<td class="ButtonsRow">{include file="buttons/delete_item.tpl" href="cart.php?mode=wldelete&wlitem=`$wlitem_data.wishlistid`&eventid=`$eventid`"}</td>
{/if}
</tr>
</table>
{if $active_modules.Gift_Registry}
<br />
{include file="modules/Gift_Registry/giftreg_wishlist.tpl"}
{/if}
