{* $Id: menu_cart.tpl,v 1.35 2006/03/29 10:42:15 max Exp $ *}
{capture name=menu}
{include file="customer/main/minicart.tpl"}
<a href="cart.php" class="wwmp_vertmenulink">{$lng.lbl_view_cart}</a><br />
<!-- Deleted by Michael de Leon 09.14.06
<A href="home.php" class="VertMenuItems">{* $lng.lbl_continue_shopping *}</A><BR>
-->
<a href="cart.php?mode=checkout" class="wwmp_vertmenulink">{$lng.lbl_checkout}</a><br />
{if $active_modules.Wishlist ne "" and $wlid ne ""}
<a href="cart.php?mode=friend_wl&amp;wlid={$wlid}" class="wwmp_vertmenulink">{$lng.lbl_friends_wish_list}</a><br />
{/if}

<!-- Start addition by Michael de Leon 09.14.06 -->
{if $anonymous_login eq "" && $login ne ""}
<!-- End addition by Michael de Leon 09.14.06 -->
{if $active_modules.Wishlist ne ""}
<a href="cart.php?mode=wishlist" class="wwmp_vertmenulink">{$lng.lbl_wish_list}</a><br />
{if $active_modules.Gift_Registry ne ""}
<a href="giftreg_manage.php" class="wwmp_vertmenulink">{$lng.lbl_gift_registry}</a><br />
{/if}
{/if}
<!-- Deleted by Michael de Leon 09.14.06
{* if $anonymous_login eq "" && $login ne "" *}
<a href="register.php?mode=update" class="wwmp_vertmenulink">{* $lng.lbl_modify_profile *}</a><br />
<a href="register.php?mode=delete" class="wwmp_vertmenulink">{* $lng.lbl_delete_profile *}</a><br />
{* /if* }
<a href="orders.php" class="wwmp_vertmenulink">{* $lng.lbl_orders_history *}</a><br />
-->
{if $user_subscription ne ""}
{include file="modules/Subscriptions/subscriptions_menu.tpl"}<br />
{/if}
{if $active_modules.RMA ne ""}
{include file="modules/RMA/customer_menu.tpl"}<br />
{/if}
<!-- Start addition by Michael de Leon 07.06.07
{* if $active_modules.Special_Offers ne "" *}
{* include file="modules/Special_Offers/menu_cart.tpl" *}<br />
{* /if *}
-->
<!-- Start addition by Michael de Leon 09.14.06 -->
{/if}
<!-- End addition by Michael de Leon 09.14.06 -->
{/capture}
<!-- Start addition by Michael de Leon 10.26.06 -->
{ include file="menu.tpl" dingbats="wwmp_yc_icon10.26.06.jpg" menu_title=$lng.lbl_your_cart menu_content=$smarty.capture.menu }
<!-- End addition by Michael de Leon 10.26.06 -->
<!-- Deleted by Michael de Leon 10.26.06
{* include file="menu.tpl" dingbats="dingbats_orders.gif" menu_title=$lng.lbl_your_cart menu_content=$smarty.capture.menu *}
-->