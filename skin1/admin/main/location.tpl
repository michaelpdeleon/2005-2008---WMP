{* $Id: location.tpl,v 1.4 2005/11/28 14:19:29 max Exp $ *}
{if $category_location and $cat ne ""}
<font class="NavigationPath">
{strip}
{section name=position loop=$category_location}
{if $category_location[position].1 ne "" }<a href="{$category_location[position].1|amp}" class="NavigationPath">{/if}
{$category_location[position].0}
{if $category_location[position].1 ne "" }</a>{/if}
{if %position.last% ne "true"}&nbsp;&gt;&nbsp;
{/if}
{/section}
</font>
{/strip}
<br /><br />
{/if}
