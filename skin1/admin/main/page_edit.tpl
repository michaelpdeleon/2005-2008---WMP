{* $Id: page_edit.tpl,v 1.16.2.1 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_static_pages}
{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}

<script type="text/javascript" language="JavaScript 1.2">
<!--
var txt_fill_page_name_field = "{$lng.txt_fill_page_name_field|strip_tags}";
var txt_fill_page_content_field = "{$lng.txt_fill_page_content_field|strip_tags}";
var txt_fill_page_file_field = "{$lng.txt_fill_page_file_field|strip_tags}";
var is_empty_filename = {if $page_data.filename eq ""}true{else}false{/if};
{literal}
function formSubmit() {
	if (document.pagesform.pagetitle.value == "") {
		document.pagesform.pagetitle.focus();
		alert(txt_fill_page_name_field);
		return false;

     } else if (document.pagesform.pagecontent.value == "") {
		document.pagesform.pagecontent.focus();
		alert(txt_fill_page_content_field);
		return false;

	} else if (is_empty_filename && document.pagesform.filename && document.pagesform.filename.value == "") {
		document.pagesform.filename.focus();
		alert(txt_fill_page_file_field);
		return false;
	}
	return true;
}
{/literal}
-->
</script>

{$lng.txt_edit_static_page_top_text}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_pages_list href="pages.php"}</div>

<form action="pages.php" method="post" name="pagesform" onsubmit="javascript: return formSubmit();">
<input type="hidden" name="mode" value="modified" />
<input type="hidden" name="pageid" value="{$smarty.get.pageid|escape:"html"}" />
<input type="hidden" name="level" value="{$level}" />

<table cellpadding="3" cellspacing="1">

<tr>
	<td>{$lng.lbl_level}:</td>
	<td><font class="Star"></font></td>
	<td>{if $level eq "E"}{$lng.lbl_embedded}{elseif $level eq "R"}{$lng.lbl_root}{/if}</td>
</tr>

<tr>
	<td>{$lng.lbl_page_file}:</td>
	<td><font class="Star"></font></td>
	<td><i>{$page_path}</i>{if $page_data.filename eq ""}<input type="text" size="25" name="filename" value="{$default_filename}" />{/if}</td>
</tr>

<tr>
	<td>{$lng.lbl_page_name}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="text" name="pagetitle" value="{$page_data.title|default:"Page$default_index"}" size="45" /></td>
</tr>

<tr>
	<td>{$lng.lbl_page_content}:</td>
	<td><font class="Star">*</font></td>
	<td>
{if $page_content eq ''}{assign var="page_content" value="Page$default_index content"}{/if}
{include file="main/textarea.tpl" name="pagecontent" cols=50 rows=20 data=$page_content btn_rows=4 html_editor_mode="XHTML"}
	</td>
</tr>

<tr>
	<td>{$lng.lbl_status}:</td>
	<td><font class="Star">*</font></td>
	<td>
<select name="active">
<option value="Y"{if $page_data.active eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
<option value="N"{if $page_data.active eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
	</td>
</tr>

<tr>
	<td>{$lng.lbl_position}:</td>
	<td><font class="Star"></font></td>
	<td><input type="text" name="orderby" value='{$page_data.orderby|default:"$default_orderby"}' size="5" /></td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
	<td class="SubmitBox"><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_static_page_details content=$smarty.capture.dialog extra='width="100%"'}

