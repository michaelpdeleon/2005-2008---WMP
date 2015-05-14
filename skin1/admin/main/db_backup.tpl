{* $Id: db_backup.tpl,v 1.17.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_database_backup_restore}

{$lng.txt_database_backup_restore_top_text}

<br /><br />

{capture name=dialog}
<form action="db_backup.php" method="post">

{include file="main/subheader.tpl" title=$lng.lbl_backup_database}

<br />

{$lng.txt_backup_database_text}

<br /><br />

<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="checkbox" id="write_to_file" name="write_to_file" value="Y" /></td>
	<td><label for="write_to_file">{$lng.txt_write_sql_dump_to_file|substitute:"file":$sqldump_file}</label></td>
</tr>
</table>

<br />
<input type="submit" value="{$lng.lbl_generate_sql_file|strip_tags:false|escape}" />
<input type="hidden" name="mode" value="backup" />
</form>
{$lng.txt_backup_database_note}
<br />
<br />
<br />
<form action="db_backup.php" method="post" name="dbrestoreform" enctype="multipart/form-data" onsubmit='javascript: return confirm("{$lng.txt_operation_is_irreversible_warning|strip_tags}")'>

{include file="main/subheader.tpl" title=$lng.lbl_restore_database}

<br />

{$lng.txt_restore_database_text}

<br /><br />

{if $file_exists}
<input type="hidden" name="local_file" value="" />
<table cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><input type="submit" value="{$lng.lbl_restore|strip_tags:false|escape}" onclick="javascript: document.dbrestoreform.local_file.value = 'on';" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td>{$lng.txt_restore_database_from_file|substitute:"file":$sqldump_file}</td>
</tr>
</table>
<br />
{/if}
<input type="file" name="userfile" />&nbsp;
<input type="submit" value="{$lng.lbl_restore_from_file|strip_tags:false|escape}" /><br />
{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}
<input type="hidden" name="mode" value="restore" />
</form>
{$lng.txt_restore_database_note}
<br /><br />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_database_backup_restore content=$smarty.capture.dialog extra='width="100%"'}
