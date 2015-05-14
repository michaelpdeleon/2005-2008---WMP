{* $Id: head_printable.tpl,v 1.7 2005/12/02 12:16:27 max Exp $ *}
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td class="HeadLogo"><a href="{$http_location}/"><img src="{$ImagesDir}/xlogo.gif" width="244" height="67" alt="" /></a></td>
	<td valign="top" align="right">
	{if $usertype eq "C"}
	{include file="customer/top_menu_printable.tpl"}
	{/if}
	</td>
</tr>
</table>
	<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td  colspan="2"><hr size="1" noshade="noshade" /></td>
</tr>
</table>
<br /><br />
