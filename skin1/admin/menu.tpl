{* $Id: menu.tpl,v 1.68.2.3 2006/07/19 10:19:35 max Exp $ *}
{capture name=menu}
<a href="{$catalogs.admin}/users.php" class="VertMenuItems">{$lng.lbl_users}</a><br />
<a href="{$catalogs.admin}/categories.php" class="VertMenuItems">{$lng.lbl_categories}</a><br />
{if $active_modules.Manufacturers}
<a href="{$catalogs.admin}/manufacturers.php" class="VertMenuItems">{$lng.lbl_manufacturers}</a><br />
{/if}
{if $active_modules.Wishlist}
<a href="{$catalogs.admin}/wishlists.php" class="VertMenuItems">{$lng.lbl_wish_lists}</a><br />
{/if}
{if $active_modules.Simple_Mode eq ""}<a href="{$catalogs.admin}/search.php" class="VertMenuItems">{$lng.lbl_products}</a><br />{/if}
<a href="{$catalogs.admin}/orders.php" class="VertMenuItems">{$lng.lbl_orders}</a><br />
{if $active_modules.News_Management}
<a href="{$catalogs.admin}/news.php" class="VertMenuItems">{$lng.lbl_news_management}</a><br />
{/if}
<a href="{$catalogs.admin}/statistics.php" class="VertMenuItems">{$lng.lbl_statistics}</a><br />
<a href="{$catalogs.admin}/shipping.php" class="VertMenuItems">{$lng.lbl_shipping_methods}</a><br />
<a href="{$catalogs.admin}/taxes.php" class="VertMenuItems">{$lng.lbl_taxing_system}</a><br />
{if $active_modules.Customer_Reviews ne ""}
<a href="{$catalogs.admin}/ratings_edit.php" class="VertMenuItems">{$lng.lbl_edit_ratings}</a><br />
{/if}
<a href="{$catalogs.admin}/countries.php" class="VertMenuItems">{$lng.lbl_countries}</a><br />
<a href="{$catalogs.admin}/states.php" class="VertMenuItems">{$lng.lbl_states}</a><br />
{if $active_modules.Stop_List ne ""}
{include file="modules/Stop_List/stop_list_menu.tpl"}<br />
{/if}
{if $active_modules.Benchmark ne ""}
{include file="modules/Benchmark/menu.tpl"}<br />
{/if}
{if $active_modules.Feature_Comparison ne ""}
{include file="modules/Feature_Comparison/admin_menu.tpl"}
{/if}
{if $active_modules.RMA ne ""}
{include file="modules/RMA/admin_menu.tpl"}<br />
{/if}
{if $active_modules.Gift_Certificates ne ""}
{include file="modules/Gift_Certificates/gc_admin_menu.tpl"}<br />
{/if}
{if $active_modules.Subscriptions ne ""}
{include file="modules/Subscriptions/subscriptions_menu.tpl"}<br />
{/if}
{if $active_modules.UPS_OnLine_Tools ne ""}
<a href="{$catalogs.admin}/ups.php" class="VertMenuItems">{$lng.lbl_ups_online_tools}</a><br />
{/if}
{if $active_modules.Survey ne ""}
{include file="modules/Survey/admin_menu.tpl"}
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_management menu_content=$smarty.capture.menu }
