{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" {$extra}>
<tr> 
<td class="DialogTitle">{$title}</td>
</tr>
<tr><td class="DialogBorder"><table cellspacing="1" class="DialogBox" cellpadding="10" width="100%" border="0">
<tr><td class="DialogBox" valign="{$valign|default:"top"}">{$content}
&nbsp;
</td></tr>
</table></td></tr>
</table>
<br>
<!-- End edit by Michael de Leon 09.15.06 -->
{/if}
