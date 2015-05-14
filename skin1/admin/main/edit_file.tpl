{* $Id: edit_file.tpl,v 1.27.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_edit_file}

{$lng.txt_edit_file_top_text}

<br /><br />

{capture name=dialog}
<a href="file_edit.php?dir={$smarty.get.dir|escape:"url"}">
<img src="{$ImagesDir}/folder.gif" width="16" height="16" alt="" />
{$smarty.get.dir|escape:"html"}</a>
<br /><br />
{$lng.lbl_file}: <b>{$filename}</b>
<p />

{if $file_type eq "image"}

<img src="{$SkinDir}{$filename}" alt="" />

{else}

<form action="file_edit.php" method="post">
<input type="hidden" name="filename" value="{$filename}" />
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="opener" value="{$opener}" />
<input type="hidden" name="mode" value="save_file" />

<textarea cols="65" rows="30" name="filebody">
{section name=file_line loop=$filebody}{$filebody[file_line]|escape:"html"}{/section}
</textarea>
<p />
<input type="submit" value="&nbsp;{$lng.lbl_save|strip_tags:false|escape}&nbsp;" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="&nbsp;{$lng.lbl_cancel|strip_tags:false|escape}&nbsp;" onclick="javascript: history.go(-1);" />
</form>

{if $nopreview eq ""}
<form method="get" action="file_edit.php" target="_blank">
<input type="hidden" name="filename" value="{$filename}" />
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="mode" value="preview" />
<input type="hidden" name="opener" value="{$opener}" />

{$lng.txt_preview_template_note}
<br />
<br />
<table cellspacing="0" cellpadding="0">
<tr>
	<td><input type="checkbox" id="use_default_css" name="use_default_css" value="1" /></td>
	<td><label for="use_default_css">{$lng.lbl_preview_default_css}</label></td>
</tr>
</table>
<input type="submit" value="{$lng.lbl_preview|strip_tags:false|escape}" />
</form>
<br/ >
{/if}

{/if}

<form method="post" action="file_edit.php">
<input type="hidden" name="filename" value="{$filename}" />
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="mode" value="restore" />
<input type="hidden" name="opener" value="{$opener}" />

{$lng.txt_restore_template_note}
<br />
<input type="submit" value="{$lng.lbl_restore_file|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_edit_file content=$smarty.capture.dialog extra='width="100%"'}
