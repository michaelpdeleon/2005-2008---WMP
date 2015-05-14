{* $Id: visiblebox_link.tpl,v 1.5.2.3 2006/11/15 14:19:42 max Exp $ *}
{if $js_enabled ne 'Y'}{assign var="visible" value=true}{/if}
<table cellspacing="1" cellpadding="2">
<tr>
	<td class="ExpandSectionMark" id="close{$mark}" style="{if $visible}display: none; {/if}" onclick="javascript: visibleBox('{$mark}');"><img src="{$ImagesDir}/plus.gif" alt="{$lng.lbl_click_to_open|escape}" /></td>
	<td class="ExpandSectionMark" id="open{$mark}" style="{if !$visible}display: none; {/if}" onclick="javascript: visibleBox('{$mark}');"><img src="{$ImagesDir}/minus.gif" alt="{$lng.lbl_click_to_close|escape}" /></td>
<!-- Deleted by Michael de Leon 02.06.07
	<td nowrap="nowrap"><a href="javascript: void(0);" onclick="javascript: visibleBox('{* $mark *}');"><b>{* $title *}</b></a></td>	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td><a class="wwmp_search_moreoptions" href="javascript: void(0);" onclick="javascript: visibleBox('{$mark}');"><font class="wwmp_aboutus_labels">{$title}</font></a></td>
	<!-- End addition by Michael de Leon 02.06.07 -->
</tr>
</table>

