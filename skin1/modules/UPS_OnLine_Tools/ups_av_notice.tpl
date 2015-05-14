{* $Id: ups_av_notice.tpl,v 1.5 2006/02/01 14:26:44 max Exp $ *}
<table>

{if $postoffice eq ""}

<tr>
	<td align="center">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
	<td align="center" class="SmallText">
{$lng.txt_ups_av_notice}
<br /><br />
{$lng.txt_ups_trademark_text}
	</td>
</tr>

{else}

<tr>
	<td colspan="2">
<b>{$lng.txt_note}:</b> {$lng.txt_ups_av_for_customers_only}
<br /><br />
	</td>
</tr>

<tr>
	<td  colspan="2" align="center" class="TableSubhead"><font class="SmallText">{$lng.txt_ups_av_notice2}</font></td>
</tr>

{/if}

</table>
