{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<table cellspacing="0" {$extra}>
<tr> 
<td class="DialogTitle">{$title}</td>
</tr>
<tr><td class="DialogBorder"><table cellspacing="1" class="DialogBox">
<tr><td class="DialogBox" valign="{$valign|default:"top"}">{$content}
&nbsp;
</td></tr>
</table></td></tr>
</table>
{/if}
