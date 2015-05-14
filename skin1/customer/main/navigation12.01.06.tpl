{* $Id: navigation.tpl,v 1.16.2.1 2006/06/16 10:47:41 max Exp $ *}
{assign var="navigation_script" value=$navigation_script|amp}
{if $total_pages gt 2}
<table cellpadding="0">
<tr>
	<td class="NavigationTitle">{$lng.lbl_result_pages}:</td>
{if $current_super_page gt 1}
	<td><a href="{$navigation_script}&amp;page={math equation="page-1" page=$start_page}"><img src="{$ImagesDir}/larrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_prev_group_pages|escape}" /></a></td>
{/if}
{section name=page loop=$total_pages start=$start_page}
{if %page.first%}
{if $navigation_page gt 1}
	<td valign="middle"><a href="{$navigation_script}&amp;page={math equation="page-1" page=$navigation_page}"><img src="{$ImagesDir}/larrow.gif" class="NavigationArrow" alt="{$lng.lbl_prev_page|escape}" /></a>&nbsp;</td>
{/if}
{/if}
{if %page.index% eq $navigation_page}
	<td class="NavigationCellSel" title="{$lng.lbl_current_page|escape}: #{%page.index%}">{%page.index%}</td>
{else}
{if %page.index% ge 100}
{assign var="suffix" value="Wide"}
{else}
{assign var="suffix" value=""}
{/if}
	<td class="NavigationCell{$suffix}"><a href="{$navigation_script}&amp;page={%page.index%}" title="{$lng.lbl_page|escape} #{%page.index%}">{%page.index%}</a><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
{/if}
{if %page.last%}
{math equation="pages-1" pages=$total_pages assign="total_pages_minus"}
{if $navigation_page lt $total_super_pages*$config.Appearance.max_nav_pages}
	<td valign="middle">&nbsp;<a href="{$navigation_script}&amp;page={math equation="page+1" page=$navigation_page}"><img src="{$ImagesDir}/rarrow.gif" class="NavigationArrow" alt="{$lng.lbl_next_page|escape}" /></a></td>
{/if}
{/if}
{/section}
{if $current_super_page lt $total_super_pages}
	<td><a href="{$navigation_script}&amp;page={math equation="page+1" page=$total_pages_minus}"><img src="{$ImagesDir}/rarrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_next_group_pages|escape}" /></a></td>
{/if}
</tr>
</table>
<p />
{/if}
