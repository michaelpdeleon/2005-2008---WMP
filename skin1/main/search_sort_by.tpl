{* $Id: search_sort_by.tpl,v 1.5.2.1 2006/06/16 10:47:41 max Exp $ *}
<!--
{* php *}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->
{if $url eq '' && $navigation_script ne ''}{assign var="url" value=$navigation_script|replace:"&":"&amp;"|cat:"&amp;"}{elseif $url ne ''}{assign var="url" value=$url|amp}{/if}
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<!-- Deleted by Michael de Leon 12.01.06
	<td class="SearchSortTitle">{* $lng.lbl_sort_by *}:</td>
	-->
{foreach from=$sort_fields key=name item=field}
	{assign var="cur_url" value=$url|cat:"sort="|cat:$name|cat:"&amp;sort_direction="}
	{if $name eq $selected}
	<td><table class="wwmp_sortby_labelbox" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="wwmp_sortby_labelspacing"><a class="wwmp_sortby_labelselected" href="{$cur_url}{if $direction eq 1}0{else}1{/if}" title="{$lng.lbl_sort_by|escape}: {$field}"><img src="{$ImagesDir}/{if $direction}wwmp_sortby_arrowdown.gif{else}wwmp_sortby_arrowup.gif{/if}" alt="{$lng.lbl_sort_direction|escape}" /></a> <a class="wwmp_sortby_labelselected" href="{$cur_url}{if $direction eq 1}0{else}1{/if}" title="{$lng.lbl_sort_by|escape}: {$field}"><b>{$field}</b></a></td>
	</tr>
	</table></td>
	{else}
		<td class="wwmp_sortby_labelspacing"><a class="wwmp_sortby_labels" href="{$cur_url} title="{$lng.lbl_sort_by|escape}: {$field}">{$field}</a></td>
	{/if}
{/foreach}
</tr>
</table>
