{* $Id: location.tpl,v 1.14 2005/11/17 06:55:36 max Exp $ *}
{if $location}
<font class="NavigationPath">
{strip}
{section name=position loop=$location}
{if $location[position].1 ne "" }<a href="{$location[position].1|amp}" class="NavigationPath">{/if}
{$location[position].0}
{if $location[position].1 ne "" }</a>{/if}
{if not %position.last%}&nbsp;::&nbsp;{/if}
{/section}
{/strip}
</font>
<br /><br />
{/if}
