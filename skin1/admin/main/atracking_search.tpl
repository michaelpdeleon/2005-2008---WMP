{* $Id: atracking_search.tpl,v 1.7 2006/04/10 11:06:02 svowl Exp $ *}
{if $statistics}
{include file="customer/main/navigation.tpl"}
<table cellspacing="1" class="DataSheet">
<tr class="DataSheet">
	<th align="left">{$lng.lbl_search_string}</th>
	<th>{$lng.lbl_date}</th>
</tr>

{foreach from=$statistics item=v}
<tr>
	<td nowrap="nowrap">{$v.search}</td>
	<td align="center" nowrap="nowrap">{$v.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
{/foreach}

</table>
{include file="customer/main/navigation.tpl"}

{else}

<br />
<div align="center">{$lng.txt_no_statistics}</div>

{/if}

