{* $Id: news_lists_select.tpl,v 1.13.2.2 2006/07/11 08:39:32 svowl Exp $ *}
{capture name="dialog"}

{if $lists ne ""}

<script type="text/javascript" language="JavaScript 1.2">
<!--
checkboxes_form = 'selectlistsform';
checkboxes = new Array({foreach from=$lists item=v key=k}{if $k > 0},{/if}'to_delete[{$v.listid}]'{/foreach});
 
--> 
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}

<table width="100%">
<tr>
<td>
<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
</td>
<td>{include file="main/language_selector.tpl" script="news.php?"}</td>
</tr>
</table>

{/if}

<form action="news.php" method="post" name="selectlistsform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	<td width="15">&nbsp;</td>
	<td width="60%">{$lng.lbl_list_name}</td>
	<td width="20%" align="center">{$lng.lbl_show_as_news}</td>
	<td width="20%" align="center">{$lng.lbl_active}</td>
</tr>

{if $lists ne ""}

{section name=idx loop=$lists}

<tr>
	<td>
	<input type="hidden" name="posted_data[{$lists[idx].listid}][listid]" value="{$lists[idx].listid}" />
	<input type="checkbox" name="to_delete[{$lists[idx].listid}]" />
	</td>
	<td><b><a href="news.php?mode=modify&amp;targetlist={$lists[idx].listid}" title="Click for details">{$lists[idx].name}</a></b></td>
	<td align="center"><input type="checkbox" name="posted_data[{$lists[idx].listid}][show_as_news]"{if $lists[idx].show_as_news eq "Y"} checked="checked"{/if} /></td>
	<td align="center"><input type="checkbox" name="posted_data[{$lists[idx].listid}][avail]"{if $lists[idx].avail eq "Y"} checked="checked"{/if} /></td>
</tr>

{/section}

{else}

<tr>
	<td colspan="4" align="center">{$lng.txt_no_newslists}</td>
</tr>

{/if}

<tr>
	<td colspan="4"><br />
{if $lists ne ""}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi"))) if (confirm("{$lng.txt_delete_new_list_text|strip_tags}")) {ldelim} document.selectlistsform.mode.value="delete"; document.selectlistsform.submit(); {rdelim}' />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick='javascript: document.selectlistsform.submit();' />
<br /><br />
{/if}
<br />
<input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: self.location='news.php?mode=create';" />
	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_news_lists content=$smarty.capture.dialog extra='width="100%"'}
