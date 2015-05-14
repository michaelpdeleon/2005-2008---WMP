{* $Id: visiblebox_link.tpl,v 1.5.2.2 2006/06/16 10:47:41 max Exp $ *}
{if $js_enabled ne 'Y'}{assign var="visible" value=true}{/if}
<table cellspacing="1" cellpadding="2">
<tr>
	<td class="ExpandSectionMark" id="close{$mark}" style="{if $visible}display: none; {/if}" onclick="javascript: visibleBox('{$mark}');"><img src="{$ImagesDir}/plus.gif" alt="{$lng.lbl_click_to_open|escape}" /></td>
	<td class="ExpandSectionMark" id="open{$mark}" style="{if !$visible}display: none; {/if}" onclick="javascript: visibleBox('{$mark}');"><img src="{$ImagesDir}/minus.gif" alt="{$lng.lbl_click_to_close|escape}" /></td>
	<td><a href="javascript: void(0);" onclick="javascript: visibleBox('{$mark}');"><b>{$title}</b></a></td>
</tr>
</table>

