{* $Id: dialog_tools_cell.tpl,v 1.2.2.2 2006/06/16 10:47:40 max Exp $ *}
{if $cell.separator}
<table cellpadding="0" cellspacing="0" width="80%"><tr>
<td class="NavDialogSeparator"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr></table>
{else}
<table cellspacing="0" cellpadding="0"><tr>
<td class="NavDialogCell"><a class="{if $cell.style eq "hl"}VertMenuItemsHL{else}VertMenuItems{/if}" href="{$cell.link|amp}" title="{$cell.title|escape}"{if $cell.target ne ""} target="{$cell.target}"{/if}{if $cell.onclick ne ""} onclick="{$cell.onclick}"{/if}><img src="{$ImagesDir}/rarrow.gif" alt="" /></a></td>
<td><a class="{if $cell.style eq "hl"}VertMenuItemsHL{else}VertMenuItems{/if}" href="{$cell.link|amp}" title="{$cell.title|escape}"{if $cell.target ne ""} target="{$cell.target}"{/if}>{$cell.title}</a></td>
</tr></table>
{/if}

