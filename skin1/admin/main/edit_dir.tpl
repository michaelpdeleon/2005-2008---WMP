{* $Id: edit_dir.tpl,v 1.47.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{if ($what_to_edit eq "files")}

{include file="page_title.tpl" title=$lng.lbl_browse_files}

{$lng.txt_browse_files_top_text}

{else}

{include file="page_title.tpl" title=$lng.lbl_browse_templates}

{$lng.txt_browse_templates_top_text}

{if $smarty.get.dir eq ""}

<br /><br />

{$lng.txt_using_debug_console_note}

<br /><br />

<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=&amp;file=%2F{$skin_config}">{$lng.lbl_config_file} ({$skin_config})</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=&amp;file=%2F{#CSSFile#}">{$lng.lbl_css_file} ({#CSSFile#})</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=%2Fbuttons">{$lng.lbl_buttons}</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=%2Fhelp">{$lng.lbl_help_files}</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=%2Fmain">{$lng.lbl_error_messages}</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=%2Fmail">{$lng.lbl_mail_templates}</a><br />
<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" alt="" /> <a href="{$action_script}?dir=&amp;file=%2Ftoday_news.tpl">{$lng.lbl_today_news}</a>

{/if}

{/if}

<br /><br />

{capture name=dialog}

<form method="post" action="{$action_script}" name="fm_form" enctype="multipart/form-data">

<input type="hidden" name="dir" value="{if $smarty.get.dir ne ""}{$smarty.get.dir|escape:"html"}{else}{$smarty.post.dir|escape:"html"}{/if}" />
<input type="hidden" name="mode" />
<input type="hidden" name="MAX_FILE_SIZE" value="500000000" />
<input type="hidden" name="my_files" value="{$my_files}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr valign="top">
	<td width="50%">

<table cellspacing="0" cellpadding="2">
{section name=dir_entry loop=$dir_entries}

<tr>
{if $dir_entries[dir_entry].filetype eq "dir"}
	<td><input type="radio" name="filename"{if $dir_entries[dir_entry].file eq ".."} disabled="disabled"{/if} value="{$dir_entries[dir_entry].href|amp}" /></td>
	<td><a href="{$action_script}?dir={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}"><img src="{$ImagesDir}/folder.gif" width="16" height="16" alt="" /></a></td>
	<td><a href="{$action_script}?dir={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}">{$dir_entries[dir_entry].file|truncate:35|amp}/</a></td>

{elseif ($what_to_edit eq "files")}

	<td><input type="radio" name="filename" value="{$dir_entries[dir_entry].href|amp}" /></td>
	<td><a href="getfile.php?file={$dir_entries[dir_entry].href|replace:" ":"%20"|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}"><img src="{$ImagesDir}/doc.gif" width="16" height="16" alt="" /></a></td>
	<td><a href="getfile.php?file={$dir_entries[dir_entry].href|replace:" ":"%20"|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}">{$dir_entries[dir_entry].file|truncate:35|amp}</a></td>

{else}

	<td><input type="radio" name="filename" value="{$dir_entries[dir_entry].href|amp}" /></td>
	<td><a href="{$action_script}?dir={$smarty.get.dir|escape:"url"}&amp;file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}"><img src="{$ImagesDir}/doc.gif" width="16" height="16" alt="" /></a></td>
	<td><a href="{$action_script}?dir={$smarty.get.dir|escape:"url"}&amp;file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp}">{$dir_entries[dir_entry].file|truncate:35|amp}</a></td>

{/if}
</tr>

{if ($smarty.section.dir_entry.index eq $dir_entries_half)}
</table>

	</td>
	<td width="50%">

<table cellspacing="0" cellpadding="2">
{/if}

{/section}

</table>

	</td>
</tr>
</table>

<hr width="100%" align="center" />

{if $is_writeable}

<table cellpadding="3" cellspacing="1">

<tr>
	<td colspan="4">
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (confirm("{$lng.txt_delete_file_warning|strip_tags}")) submitForm(this, "Delete");' />
	</td>
</tr>

{if ($what_to_edit ne "files")}
<tr>
	<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_create_new_file class="grey"}</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_new_filename}:</td>
	<td><input type="text" size="40" name="new_file" value="" /></td>
	<td colspan="2"><input type="button" value="{$lng.lbl_create|strip_tags:false|escape}" onclick='javascript: submitForm(this, "New file");' /></td>
</tr>
{/if}

<tr>
	<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_create_new_directory class="grey"}</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_new_directory_name}:</td>
	<td><input type="text" size="40" name="new_directory" value="" /></td>
	<td colspan="2"><input type="button" value="{$lng.lbl_create|strip_tags:false|escape}" onclick='javascript: submitForm(this, "New directory");' /></td>
</tr>

<tr>
	<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.copy_selected_file_to_ class="grey"}</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_copy_file_name}:</td>
	<td><input type="text" size="40" name="copy_file" value="" /></td>
	<td colspan="2"><input type="button" value=" {$lng.lbl_copy|strip_tags:false|escape} " onclick='javascript: submitForm(this, "Copy to");' /></td>
</tr>

<tr>
	<td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_upload_file_to_directory class="grey"}</td>
</tr>

<tr>
	<td colspan="2"><input type="file" size="50" name="userfile" /></td>
	<td><input type="button" value="{$lng.lbl_upload|strip_tags:false|escape}" onclick='javascript: document.fm_form.mode.value = "Upload"; document.fm_form.submit();' /></td>
	<td>
<input type="checkbox" id="rewrite_if_exists" name="rewrite_if_exists" value="Y" checked="checked" />
<label for="rewrite_if_exists">{$lng.lbl_rewrite_file_if_exists}</label>
	</td>
</tr>

{if $upload_max_filesize}
<tr>
	<td colspan="3">{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}</td>
</tr>
{/if}

</table>

</form>

{if $what_to_edit eq "templates" and $smarty.get.dir eq ""}

<form method="post" action="{$action_script}" name="rc_form">

<input type="hidden" name="mode" />

{$lng.txt_restore_templates_text}

<br />

{$lng.txt_restore_templates_note}

<br />

<input type="button" value="{$lng.lbl_restore_all|strip_tags:false|escape}" onclick='javascript: if (confirm("{$lng.txt_restore_templates_warning|strip_tags}")) {ldelim} document.rc_form.mode.value = "restore_all"; document.rc_form.submit(); {rdelim}' />

<br /><br />

{$lng.txt_compile_templates}

<br /><br />

<input type="button" value="{$lng.lbl_compile_templates|strip_tags:false|escape}" onclick='javascript: if (confirm("{$lng.txt_compile_templates_warning|strip_tags}")) {ldelim} document.rc_form.mode.value = "compile_all"; document.rc_form.submit (); {rdelim}' />
&nbsp;
{$lng.lbl_language}:
<select name="language">
{section name=li loop=$languages}
<option value="{$languages[li].code}">{$languages[li].language}</option>
{/section}
</select>

<br /><br />

{$lng.txt_compile_templates_note}

<br />

</form>

{/if}

{else}

{$lng.txt_directory_is_not_writable}

{/if}

{/capture}

{if ($what_to_edit eq "files")}
{include file="dialog.tpl" title=$lng.lbl_browse_files content=$smarty.capture.dialog extra='width="100%"'}
{else}
{include file="dialog.tpl" title=$lng.lbl_browse_templates content=$smarty.capture.dialog extra='width="100%"'}
{/if}

