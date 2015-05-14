{* $Id: menu.tpl,v 1.13 2005/11/17 06:55:38 max Exp $ *}
{capture name=menu}
<a href="{$catalogs.admin}/orders.php" class="VertMenuItems">{$lng.lbl_orders}</a><br />
<a href="{$catalogs.admin}/statistics.php" class="VertMenuItems">{$lng.lbl_statistics}</a><br />
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_management menu_content=$smarty.capture.menu }
