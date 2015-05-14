<!-- Edited by Michael de Leon 09.14.06 -->
{if $printable ne ''}
{include file="customer/top_menu_printable.tpl"}
{else}
<table cellpadding="0" cellspacing="0" width="100%" border="0">
{if $speed_bar}
<tr>
<td valign="top" align="right">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
{section name=sb loop=$speed_bar}
{if $speed_bar[sb].active eq "Y"}
<td valign="top">{include file="customer/tab.tpl" tab_title="<a href=\"`$speed_bar[sb].link`\">`$speed_bar[sb].title`</a>"}</td>
<td width="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
{/if}
{/section}
</tr>
</table>
</td>
</tr>
{/if}
<tr>
<td height="35" align="center" class="HeadText">
{if $config.Company.company_phone}{$lng.lbl_phone_1_title}: {$config.Company.company_phone}{/if}{if $config.Company.company_phone_2}&nbsp;&nbsp;&nbsp;{$lng.lbl_phone_2_title}: {$config.Company.company_phone_2}{/if}&nbsp;&nbsp;</td>
</tr>
</table>
{/if}
