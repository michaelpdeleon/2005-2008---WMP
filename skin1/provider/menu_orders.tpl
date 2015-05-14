{* $Id: menu_orders.tpl,v 1.16 2005/12/05 15:00:43 max Exp $ *}
{capture name=menu}
<a href="{$catalogs.provider}/orders.php?mode=new" class="VertMenuItems">{$lng.lbl_new_orders}</a><br />
<a href="{$catalogs.provider}/orders.php" class="VertMenuItems">{$lng.lbl_search_orders_menu}</a><br />
{/capture}
{ include file="menu.tpl" dingbats="dingbats_orders.gif" menu_title=$lng.lbl_your_orders menu_content=$smarty.capture.menu }
