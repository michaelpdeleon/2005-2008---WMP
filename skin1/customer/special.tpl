{if $active_modules.Gift_Certificates ne "" or $active_modules.Gift_Registry ne "" or $active_modules.Special_Offers ne "" or ($active_modules.Manufacturers ne "" and $config.Manufacturers.manufacturers_menu ne "Y") or ($active_modules.Feature_Comparison ne "" && $is_fc_display_menu eq 'Y')}
{capture name=menu}
{if $active_modules.Manufacturers ne "" and $config.Manufacturers.manufacturers_menu ne "Y"}
<a href="manufacturers.php" class="VertMenuItems">{$lng.lbl_manufacturers}</a><br />
{/if}
{if $active_modules.Gift_Certificates ne ""}
{ include file="modules/Gift_Certificates/gc_menu.tpl" }
{/if}
{if $active_modules.Gift_Registry ne ""}
{ include file="modules/Gift_Registry/giftreg_menu.tpl" }
{/if}
{if $active_modules.Feature_Comparison ne "" && $is_fc_display_menu eq 'Y'}
{ include file="modules/Feature_Comparison/customer_menu.tpl" }
{/if}
{if $active_modules.Survey && $surveys_is_avail}
{include file="modules/Survey/menu_special.tpl"}
{/if}
{if $active_modules.Special_Offers ne ""}
{ include file="modules/Special_Offers/menu_special.tpl" }
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_special menu_content=$smarty.capture.menu }
<br />
{/if}
