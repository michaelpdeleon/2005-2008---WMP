{* $Id: current_language.tpl,v 1.7 2005/12/05 15:00:32 max Exp $ *}
<form action="{$smarty.server.REQUEST_URI|amp}" method="post" name="aslform{$form_id|default:"2"}">
<input type="hidden" name="redirect" value="{$smarty.server.QUERY_STRING|amp}{$anchor}" />

<table cellpadding="5" cellspacing="0">
<tr>
	<td class="TableSubHead">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><b>{$lng.lbl_current_language}:</b></td>
	<td>&nbsp;</td>
	<td>
	<select name="asl" onchange="javascript: document.aslform{$form_id|default:"2"}.submit();">
{section name=ai loop=$all_languages}
		<option value="{$all_languages[ai].code}"{if $current_language eq $all_languages[ai].code} selected="selected"{/if}>{$all_languages[ai].language}</option>
{/section}
	</select>
	</td>
</tr>
</table>
{if $smarty.section.ai.total gt 1}
<br />{$lng.txt_current_language}
{/if}
	</td>
</tr>
</table>
</form>

