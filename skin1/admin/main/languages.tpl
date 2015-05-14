{* $Id: languages.tpl,v 1.44.2.2 2006/07/11 08:39:26 svowl Exp $ *}

{include file="page_title.tpl" title=$lng.lbl_edit_languages}

{capture name=dialog}

<table cellpadding="5" cellspacing="0">

<tr>
	<td class="FormButton">{$lng.lbl_language}:</td>
	<td><select name="language" onchange='javascript: self.location="languages.php?language="+this.value;'>
<option value=""{if $smarty.get.language eq ""} selected="selected"{/if}>{$lng.lbl_select_one}</option>
{foreach from=$languages item=l}
<option value="{$l.code}"{if $smarty.get.language eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/foreach}
</select>
{if $smarty.get.language ne "" and $smarty.get.language ne $shop_language}
	<td>
<input type="button" value="{if $lang_disabled eq "Y"}{$lng.lbl_enable|strip_tags:false|escape}{else}{$lng.lbl_disable|strip_tags:false|escape}{/if}" onclick="javascript: self.location='languages.php?language={$smarty.get.language|escape:"html"}&amp;mode=change'" />
<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: if (confirm('{$lng.txt_are_you_sure|strip_tags}')) self.location='languages.php?language={$smarty.get.language|escape:"html"}&amp;mode=del_lang';" />
	</td>
{/if}
</tr>

</table>

{if $smarty.get.language ne ""}

<br />
<br />

<form method="get" action="languages.php" name="dl_form">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />

<table cellpadding="5" cellspacing="0">
<tr>
	<td>{$lng.lbl_csv_delimiter}:</td>
	<td>{include file="provider/main/ie_delimiter.tpl"}</td>
	<td><input type="button" value="{$lng.lbl_export|strip_tags:false|escape}" onclick="javascript: document.dl_form.mode.value = 'export'; document.dl_form.submit();" /></td>
</tr>
</table>

<br />
<br />

{include file="main/subheader.tpl" title=$lng.lbl_language_options}

<table cellpadding="5" cellspacing="0">
<tr>
	<td>{$lng.lbl_charset}:</td>
	<td colspan="2"><input type="text" name="charset" value="{$default_charset}" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2">
	<table cellspacing="2" cellpadding="0">
	<tr>
		<td><input type="checkbox" id="text_dir" name="text_dir" value="Y"{if $config.r2l_languages[$smarty.get.language]} checked="checked"{/if} /></td>
		<td><label for="text_dir">{$lng.lbl_r2l_text_direction}</label></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: document.dl_form.mode.value = 'update_charset'; document.dl_form.submit();" /></td>
</tr>
</table>

</form>

{/if}

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_language extra='width="100%"'}

<br />

{if $smarty.get.language ne ""}

{$lng.txt_edit_language_note}

<form method="get" action="languages.php" name="topic_form">
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />
<br />
{$lng.lbl_select_topic}:
<select name="topic" onchange='javascript: document.topic_form.submit();'>
	<option value=""{if $smarty.get.topic eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
{foreach from=$topics item=t}
	<option value="{$t}"{if $smarty.get.topic eq $t} selected="selected"{/if}>{$t}</option>
{/foreach}
</select>
&nbsp;
&nbsp;
&nbsp;
{$lng.lbl_apply_filter}:
<input type="text" size="16" name="filter" value="{$filter|escape:"html"}" />&nbsp;<input type="submit" value="{$lng.lbl_go|strip_tags:false|escape}" />
</form>

<br />

{include file="customer/main/navigation.tpl"}

{$lng.lbl_total_labels_found}: {$total_labels_found}

<script type="text/javascript" language="JavaScript 1.2">
<!--
var msg_new_label_empty = "{$lng.msg_new_label_empty|strip_tags}";
var delete_link = 'languages.php?mode=delete&page={$page}&language={$smarty.get.language}&filter={$filter}&topic={$smarty.get.topic}&var=';

{literal}
function func_checklang() {
	if (document.addlblform.new_var_name.value != '' && document.addlblform.new_var_value.value == '') {
		alert(msg_new_label_empty);
		return false;
	}
	return true;
}

{/literal}
-->
</script>

{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}

<form action="languages.php" method="post" name="languagespostform">

<input type="hidden" name="mode" value="update" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
<input type="hidden" name="topic" value="{$smarty.get.topic|escape:"html"}" />
<input type="hidden" name="filter" value="{$filter|escape:"html"}" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />

<br />

{capture name=dialog}
<div valign="top">
<table cellpadding="0" cellspacing="2" width="100%">

{assign var="current_topic" value=""}

<tr>
	<td>

<table cellspacing="0" cellpadding="2" width="100%">
{foreach from=$data item=lbl}
{if $lbl.topic ne $current_topic}

{if $current_topic ne ""}
<tr>
	<td colspan="2"><img src="{$ImagesDir}/spacer.gif" width="1" height="20" alt="" /></td>
</tr>
{/if}

<tr>
	<td colspan="2" class="TableHead">{$lng.lbl_topic}: {$lbl.topic}</td>
</tr>

{assign var="current_topic" value=$lbl.topic}

{/if}

<tr class="TableSubHead">
	<td><input type="checkbox" name="ids[]" value="{$lbl.name}" /></td>
	<td width="100%"><b>{$lbl.name}</b></td>
</tr>
<tr class="TableSubHead">
	<td>&nbsp;</td>
	<td>
{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/popup_link.tpl" id="var_`$lbl.name`" width="99%"}
{/if}
	<textarea id="var_{$lbl.name}" name="var_value[{$lbl.name}]" cols="70" rows="8" style="width: 99%;">{$lbl.value|escape:"html"}</textarea>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>

{/foreach}
<tr>
	<td colspan="2" class="SubmitBox">
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'delete');" />
&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update_all|strip_tags:false|escape}" />
	</td>
</tr>
</table>
</form>

	</td>
</tr>
<tr>
	<td><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new_entry}</td>
</tr>

<tr>
	<td>

<form action="languages.php" method="post" name="addlblform" onsubmit="javascript: return func_checklang();">

<input type="hidden" name="mode" value="add" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
<input type="hidden" name="topic" value="{$smarty.get.topic|escape:"html"}" />
<input type="hidden" name="filter" value="{$filter|escape:"html"}" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />

<table cellpadding="3" cellspacing="0" width="100%">

{if $smarty.get.topic eq ""}
{assign var="new_topic_default" value="Labels"}
{else}
{assign var="new_topic_default" value=$smarty.get.topic}
{/if}
<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_select_topic}: <font class="Star">*</font></td>
	<td>
	<select name="new_topic">
		{foreach from=$topics item=t}
		<option value="{$t}"{if $new_topic_default eq $t} selected="selected"{/if}>{$t}</option>
		{/foreach}
	</select>
	</td>
</tr>

<tr>
	<td class="FormButton" width="10%" nowrap="nowrap">{$lng.lbl_variable}: <font class="Star">*</font></td>
	<td align="left"><input type="text" size="50" name="new_var_name" /></td>
</tr>

<tr>
	<td colspan="2" class="FormButton">{$lng.lbl_value}: <font class="Star">*</font></td>
</tr>

<tr>
	<td colspan="2">
{include file="main/textarea.tpl" name="new_var_value" cols=70 rows=8 data="" width="100%" style="width: 100%;"}
	</td>
</tr>

<tr>
	<td colspan="2"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

	</td>
</tr>
</table>
</div>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_language_entries extra='width="100%"'}
{/if}

<br />

{capture name=dialog}
<form method="post" action="languages.php">

<table>
<tr>
	<td>{$lng.lbl_default_customer_language}:</td>
	<td>
	<select name="new_customer_language">
		<option value="">{$lng.lbl_select_one}</option>
{foreach from=$languages item=l}
		<option value="{$l.code}"{if $config.default_customer_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/foreach}
	</select>
	</td>
</tr>
<tr>
	<td>{$lng.lbl_default_admin_language}:</td>
	<td>
	<select name="new_admin_language">
		<option value="">{$lng.lbl_select_one}</option>
{foreach from=$languages item=l}
		<option value="{$l.code}"{if $config.default_admin_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/foreach}
	</select>
	</td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>

<input type="hidden" name="mode" value="change_defaults" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_default_languages extra='width="100%"'}

<p />
{capture name=dialog}
<form method="post" action="languages.php" enctype="multipart/form-data" name="newlanguageform">
<input type="hidden" name="mode" value="add_lang" />
<table>
<tr>
	<td>{$lng.lbl_choose_language}:</td>
	<td>
	<select name="new_language">
		<option value="">{$lng.lbl_select_one}</option>
{foreach from=$new_languages item=l}
		<option value="{$l.code}">{$l.language}</option>
{/foreach}
	</select>
	</td>
</tr>
<tr>
	<td>{$lng.lbl_import_file_optional}:</td>
	<td><input type="file" name="import_file" /></td>
</tr>
<tr>
	<td>{$lng.lbl_csv_delimiter}:</td>
	<td>{include file="provider/main/ie_delimiter.tpl"}</td>
</tr>
</table>

<p />
{$lng.txt_import_language_note}
<p />
<input type="submit" value="{$lng.lbl_add_update_language|strip_tags:false|escape}" />
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_add_new_language extra='width="100%"'}
