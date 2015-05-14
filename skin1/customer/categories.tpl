{* $Id: categories.tpl,v 1.26 2005/11/17 06:55:37 max Exp $ *}
{capture name=menu}
{if $active_modules.Fancy_Categories ne ""}
{include file="modules/Fancy_Categories/categories.tpl"}
{assign var="fc_cellpadding" value="0"}
{else}
{if $config.General.root_categories eq "Y"}
{foreach from=$categories item=c}
<font class="CategoriesList"><a href="home.php?cat={$c.categoryid}" class="VertMenuItems">{$c.category}</a></font><br />
{/foreach}
{else} {foreach from=$subcategories item=c key=catid}
<font class="CategoriesList"><a href="home.php?cat={$catid}" class="VertMenuItems">{$c.category}</a></font><br />
{/foreach}
{/if}
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_categories menu_content=$smarty.capture.menu cellpadding=$fc_cellpadding}
