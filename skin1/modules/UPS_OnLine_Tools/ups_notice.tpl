{* $Id: ups_notice.tpl,v 1.7 2006/02/01 14:26:44 max Exp $ *}
{if $usertype eq "C" or ($usertype ne "C" and $main eq "order_edit")}
<table>
<tr>
	<td align="center">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
	<td align="center" class="SmallText">
{if $ups_av_error ne ""}
{$lng.txt_ups_av_notice}
{else}
{$lng.txt_ups_rates_notice|substitute:"company":$config.Company.company_name}
{/if}
<br /><br />
{$lng.txt_ups_trademark_text}
	</td>
</tr>
</table>
{else}
<div align="center">{$lng.txt_ups_trademark_text}</div>
{/if}
