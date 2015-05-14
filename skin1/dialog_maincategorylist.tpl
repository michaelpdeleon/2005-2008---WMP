{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" {$extra}>
<!-- Deleted by Michael de Leon 11.17.06
<tr> 
<td class="wwmp_categorytitlefont">{* $title *}</td>
</tr>
<tr>
<td align="center"><img src="{* $ImagesDir *}/wwmp_lp_categorytitleline11.17.06.jpg"></td>
</tr>
-->
<tr><td><table cellspacing="1" class="DialogBox" cellpadding="5" width="100%" border="0">
<tr><td class="DialogBox" valign="{$valign|default:"top"}">{$content}
&nbsp;
</td></tr>
</table></td></tr>
</table>
<br>
<!-- End edit by Michael de Leon 09.15.06 -->
{/if}
