{* $Id: news_subscribers.tpl,v 1.14.2.2 2006/07/11 08:39:32 svowl Exp $ *}
{include file="check_email_script.tpl"}

{if $subscribers ne ""}

{if $total_pages gt 2}
<br />
{ include file="customer/main/navigation.tpl" }
{/if}

<script type="text/javascript" language="JavaScript 1.2">
<!--
checkboxes_form = 'subscribersform';
checkboxes = new Array({foreach from=$subscribers item=v key=k}{if $k > 0},{/if}"to_delete[{$v.email|replace:'"':'\"'}]"{/foreach});
 
--> 
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="news.php" method="post" name="subscribersform" enctype="multipart/form-data">
<input type="hidden" name="mode" value="subscribers" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="targetlist" value="{$targetlist}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
<td width="10">&nbsp;</td>
<td width="50%">{$lng.lbl_email}</td>
<td width="50%">{$lng.lbl_since_date}</td>
</tr>

{if $subscribers ne ""}

{section name=idx loop=$subscribers}
<tr>
<td><input type="checkbox" name="to_delete[{$subscribers[idx].email|escape}]" /></td>
<td>{$subscribers[idx].email}</td>
<td>{$subscribers[idx].since_date|date_format:$config.Appearance.date_format}</td>
</tr>
{/section}

{if $total_pages gt 2}
<tr>
<td colspan="3">
<br />
{ include file="customer/main/navigation.tpl" }
</td>
</tr>
{/if}

<tr>
<td colspan="3"><br />
<input type="button" value="{$lng.lbl_export_selected|strip_tags:false|escape}" onclick='javascript: if (!checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi"))) return; document.subscribersform.action.value="export"; document.subscribersform.submit();' />&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi"))) if (confirm("{$lng.txt_news_list_subscribers_delete|strip_tags}")) {ldelim} document.subscribersform.action.value="delete"; document.subscribersform.submit(); {rdelim}' />&nbsp;&nbsp;
</td>
</tr>

{else}

<tr>
<td colspan="3" align="center">{$lng.txt_no_subscribers}</td>
</tr>

{/if}

<tr>
<td colspan="3"><br /><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_to_maillist}</td>
</tr>

<tr>
<td colspan="3">
{$lng.lbl_email}: <input type="text" id="new_email" name="new_email" size="40" />&nbsp;
<input type="button" value=" {$lng.lbl_add|strip_tags:false|escape} " onclick="javascript: if (!checkEmailAddress(document.getElementById('new_email'), 'Y')) return false; document.subscribersform.action.value='add'; document.subscribersform.submit();" />
</td>
</tr>

<tr>
	<td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_news_list_subscribers_import}</td>
</tr>

<tr>
<td colspan="3">
{$lng.lbl_file_for_upload}: <input type="file" size="32" name="userfile" />
<input type="button" value="{$lng.lbl_import|strip_tags:false|escape}" onclick="javascript: document.subscribersform.action.value='import'; document.subscribersform.submit();" />
</td>
</tr>

</table>
</form>

