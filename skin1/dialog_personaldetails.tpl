{* $Id: dialog.tpl,v 1.25 2005/12/20 08:50:49 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<!-- Start edit by Michael de Leon 09.15.06 -->
<table cellspacing="0" cellpadding="0" border="0" {$extra}>
<tr>
<td background="{$ImagesDir}/wwmp_dialogpersonaldetails_title11.13.06.jpg" height="31" align="left">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="wwmp_cart_label_title" align="left">{$title}</td>
  </tr>
</table>
</td>
</tr>
<tr>
<td colspan="3" background="{$ImagesDir}/wwmp_dialogpersonaldetails_m11.13.06.jpg">{$content}</td>
</tr>
<tr>
<td background="{$ImagesDir}/wwmp_dialogpersonaldetails_footer11.13.06.jpg" height="31"></td>
</tr>
</table>
<!-- End edit by Michael de Leon 09.15.06 -->
{/if}
