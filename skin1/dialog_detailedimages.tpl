{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<!-- Start edit by Michael de Leon 02.16.07 -->
<div class="wwmp_recommends_title">{$title}</div>
<table cellspacing="0" cellpadding="10" border="1" bordercolor="#000000" align="center" {$extra}>
<tr><td>{$content}</td></tr>
</table>
<br>
<!-- End edit by Michael de Leon 02.16.07 -->
{/if}
