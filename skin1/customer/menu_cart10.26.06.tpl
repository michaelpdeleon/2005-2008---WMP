{* $Id: menu_cart.tpl,v 1.35 2006/03/29 10:42:15 max Exp $ *}
{capture name=menu}
{include file="customer/main/minicart.tpl"}
<a href="cart.php" class="VertMenuItems">{$lng.lbl_view_cart}</a><br />
<a href="cart.php?mode=checkout" class="VertMenuItems">{$lng.lbl_checkout}</a><br />
{if $active_modules.Wishlist ne "" and $wlid ne ""}
<a href="cart.php?mode=friend_wl&amp;wlid={$wlid}" class="VertMenuItems">{$lng.lbl_friends_wish_list}</a><br />
{/if}

{if $active_modules.Wishlist ne ""}
<a href="cart.php?mode=wishlist" class="VertMenuItems">{$lng.lbl_wish_list}</a><br />
{if $active_modules.Gift_Registry ne ""}
<a href="giftreg_manage.php" class="VertMenuItems">{$lng.lbl_gift_registry}</a><br />
{/if}
{/if}
{if $anonymous_login eq "" && $login ne ""}
<a href="register.php?mode=update" class="VertMenuItems">{$lng.lbl_modify_profile}</a><br />
<a href="register.php?mode=delete" class="VertMenuItems">{$lng.lbl_delete_profile}</a><br />
{/if}
<a href="orders.php" class="VertMenuItems">{$lng.lbl_orders_history}</a><br />
{if $user_subscription ne ""}
{include file="modules/Subscriptions/subscriptions_menu.tpl"}<br />
{/if}
{if $active_modules.RMA ne ""}
{include file="modules/RMA/customer_menu.tpl"}<br />
{/if}
{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/menu_cart.tpl"}<br />
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_orders.gif" menu_title=$lng.lbl_your_cart menu_content=$smarty.capture.menu }
