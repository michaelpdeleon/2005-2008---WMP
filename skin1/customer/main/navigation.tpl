{* $Id: navigation.tpl,v 1.16.2.1 2006/06/16 10:47:41 max Exp $ *}
{assign var="navigation_script" value=$navigation_script|amp}
{if $total_pages gt 2}
<table border="0" cellpadding="2" cellspacing="0">
<tr>
	<!-- Deleted by Michael de Leon 12.01.06
	<td class="NavigationTitle">{* $lng.lbl_result_pages *}:</td>
	-->
	<td class="wwmp_sortby_resultlabel">{ $lng.lbl_result_pages }:</td>
{if $current_super_page gt 1}
	<td><a href="{$navigation_script}&amp;page={math equation="page-1" page=$start_page}"><img src="{$ImagesDir}/wwmp_sortby_arrowleft.jpg" alt="{$lng.lbl_prev_group_pages|escape}" title="{$lng.lbl_prev_group_pages|escape}" /></a></td>
{/if}
{section name=page loop=$total_pages start=$start_page}
<!-- Deleted by Michael de Leon 12.01.06
{* if %page.first% *}
{* if $navigation_page gt 1 *}
	<td valign="middle"><a href="{* $navigation_script *}&amp;page={* math equation="page-1" page=$navigation_page *}"><img src="{* $ImagesDir *}/larrow.gif" class="NavigationArrow" alt="{* $lng.lbl_prev_page|escape *}" /></a>&nbsp;</td>
{* /if *}
{* /if *}
-->
{if %page.index% eq $navigation_page}
	<td class="wwmp_sortby_currentpage" title="{$lng.lbl_current_page|escape}: #{%page.index%}"><b>{%page.index%}</b></td>
{else}
{if %page.index% ge 100}
{assign var="suffix" value="Wide"}
{else}
{assign var="suffix" value=""}
{/if}
	<!-- Deleted by Michael de Leon 12.01.06
	<td class="NavigationCell{* $suffix *}"><a href="{* $navigation_script *}&amp;page={* %page.index% *}" title="{* $lng.lbl_page|escape *} #{* %page.index% *}">{* %page.index% *}</a><img src="{* $ImagesDir *}/spacer.gif" alt="" /></td>
	-->
	<td><a class="wwmp_sortby_labels" href="{ $navigation_script }&amp;page={ %page.index% }" title="{ $lng.lbl_page|escape } #{ %page.index% }">{ %page.index% }</a><img src="{ $ImagesDir }/spacer.gif" alt="" /></td>
{/if}
{if %page.last%}
{math equation="pages-1" pages=$total_pages assign="total_pages_minus"}
<!-- Deleted by Michael de Leon 12.01.06
{* if $navigation_page lt $total_super_pages*$config.Appearance.max_nav_pages *}
	<td valign="middle">&nbsp;<a href="{* $navigation_script *}&amp;page={* math equation="page+1" page=$navigation_page *}"><img src="{* $ImagesDir *}/rarrow.gif" class="NavigationArrow" alt="{* $lng.lbl_next_page|escape *}" /></a></td>
{* /if *}
-->
{/if}
{/section}
{if $current_super_page lt $total_super_pages}
	<td><a href="{$navigation_script}&amp;page={math equation="page+1" page=$total_pages_minus}"><img src="{$ImagesDir}/wwmp_sortby_arrowright.jpg" alt="{$lng.lbl_next_group_pages|escape}" title="{$lng.lbl_next_group_pages|escape}" /></a></td>
{/if}
</tr>
</table>
<br />
<!-- Deleted by Michael de Leon 12.01.06
<p />
-->
{/if}
