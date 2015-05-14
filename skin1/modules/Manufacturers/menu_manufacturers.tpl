{* $Id: menu_manufacturers.tpl,v 1.5 2005/11/17 06:55:47 max Exp $ *}
{if $manufacturers_menu ne ''}
{capture name=menu}
{section name=mid loop=$manufacturers_menu}
<a href="manufacturers.php?manufacturerid={$manufacturers_menu[mid].manufacturerid}" class="VertMenuItems">{$manufacturers_menu[mid].manufacturer}</a><br />
{/section}
{if $show_other_manufacturers}
<br />
<a href="manufacturers.php" class="VertMenuItems">{$lng.lbl_other_manufacturers}</a><br />
{/if}
{/capture}
{include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_manufacturers menu_content=$smarty.capture.menu}
<br />
{/if}
