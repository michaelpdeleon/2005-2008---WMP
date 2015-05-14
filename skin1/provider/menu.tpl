{* $Id: menu.tpl,v 1.72 2006/03/17 12:30:51 svowl Exp $ *}
{capture name=menu}
{if $active_modules.Manufacturers and $active_modules.Simple_Mode eq ""}
<a href="{$catalogs.provider}/manufacturers.php" class="VertMenuItems">{$lng.lbl_manufacturers}</a><br />
{/if}
{if $active_modules.Wishlist and $active_modules.Simple_Mode eq ""}
<a href="{$catalogs.provider}/wishlists.php" class="VertMenuItems">{$lng.lbl_wish_lists}</a><br />
{/if}
{if $active_modules.Feature_Comparison ne "" && $active_modules.Simple_Mode eq ''}
{include file="modules/Feature_Comparison/admin_menu.tpl"}
{/if}
{if $active_modules.Product_Configurator ne ""}
{include file="modules/Product_Configurator/pconf_menu_provider.tpl"}<br />
{/if}
<a href="{$catalogs.provider}/search.php" class="VertMenuItems">{$lng.lbl_search_products}</a><br />
<a href="{$catalogs.provider}/product_modify.php" class="VertMenuItems">{$lng.lbl_add_new_product}</a><br />
<a href="{$catalogs.provider}/search.php" class="VertMenuItems">{$lng.lbl_product_modify}</a><br />
<a href="{$catalogs.provider}/search.php" class="VertMenuItems">{$lng.lbl_delete_product}</a><br />
{if $active_modules.Simple_Mode eq ''}
<a href="{$catalogs.provider}/import.php" class="VertMenuItems">{$lng.lbl_import_export}</a><br />
{/if}
<a href="{$catalogs.provider}/inv_update.php" class="VertMenuItems">{$lng.lbl_update_inventory}</a><br />
{if $active_modules.Extra_Fields ne ""}
<a href="{$catalogs.provider}/extra_fields.php" class="VertMenuItems">{$lng.lbl_extra_fields}</a><br />
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_products.gif" menu_title=$lng.lbl_products menu_content=$smarty.capture.menu }
<br />
{capture name=menu}
{if $active_modules.Simple_Mode eq ""}
<a href="{$catalogs.provider}/general.php" class="VertMenuItems">{$lng.lbl_summary}</a><br />
{/if}
<a href="{$catalogs.provider}/zones.php" class="VertMenuItems">{$lng.lbl_destination_zones}</a><br />
{if $config.Shipping.disable_shipping ne "Y"}
<a href="{$catalogs.provider}/shipping_rates.php" class="VertMenuItems">{$lng.lbl_shipping_charges}</a><br />
{if $config.Shipping.realtime_shipping eq "Y"}
<a href="{$catalogs.provider}/shipping_rates.php?type=R" class="VertMenuItems">{$lng.lbl_shipping_markups}</a><br />
{/if}
{/if}
{if $active_modules.Simple_Mode eq ""}
<a href="{$catalogs.provider}/taxes.php" class="VertMenuItems">{$lng.lbl_tax_rates}</a><br />
{/if}
<a href="{$catalogs.provider}/discounts.php" class="VertMenuItems">{$lng.lbl_discounts}</a><br />
{if $active_modules.Discount_Coupons ne ""}
<a href="{$catalogs.provider}/coupons.php" class="VertMenuItems">{$lng.lbl_coupons}</a><br />
{/if}
{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/menu_provider.tpl"}<br />
{/if}
{if $active_modules.Simple_Mode eq ""}
<a href="{$catalogs.provider}/file_manage.php" class="VertMenuItems">{$lng.lbl_files}</a><br />
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_products.gif" menu_title=$lng.lbl_inventory menu_content=$smarty.capture.menu }
