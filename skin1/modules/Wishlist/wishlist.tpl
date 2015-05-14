{* $Id: wishlist.tpl,v 1.28 2005/11/17 06:55:59 max Exp $ *}
{if $smarty.get.send2friend ne ""}
{include file="modules/Wishlist/send2friend_message.tpl" message=$smarty.get.send2friend}
{elseif $smarty.get.sendall2friend ne ""}
{include file="modules/Wishlist/sendall2friend_message.tpl" message=$smarty.get.sendall2friend}
{/if}
{capture name=dialog}

{include file="modules/Wishlist/wl_products.tpl" wl_products=$wl_products}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_wish_list content=$smarty.capture.dialog extra='width="100%"'}

{if $active_modules.Gift_Registry}
<p />
{include file="modules/Gift_Registry/events_list.tpl"}
{/if}
