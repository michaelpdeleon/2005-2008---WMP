{* $Id: error_disabled_cookies.tpl,v 1.6 2006/03/25 08:11:21 max Exp $ *}
<table>
<tr>
	<td rowspan="2" width="20"><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td><font class="ErrorMessage">{$lng.txt_browser_doesnt_accept_cookies}</font></td>
</tr>
{if $save_data ne ""}
<tr>
	<td>{$lng.txt_enable_cookies_to_continue}</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>{include file="buttons/continue.tpl" href="`$save_data.PHP_SELF`?NO_COOKIE_WARNING=1&amp;ti=`$ti`"}</td>
</tr>
{/if}
</table>
