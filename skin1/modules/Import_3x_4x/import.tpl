{* $id$ *}
{include file="page_title.tpl" title=$lng.lbl_import_products}

<br />
<br />

{$lng.txt_import_products_note} 

<br />
<br />

{capture name=dialog}

<form action="import_3x_4x.php" method="post" enctype="multipart/form-data" name="import_form">
<input type="hidden" name="mode" value="" />

<table cellpadding="5" cellspacing="1" width="100%">

<tr>
	<td><font class="AdminTitle">{$lng.lbl_column_order}:</font></td>
</tr>

<tr>
	<td style="padding-top: 15px;">{$lng.txt_import_products_column_order_note}</td>
</tr>

<tr>
	<td style="padding-top: 15px;">

<table cellpadding="1" cellspacing="1" width="100%">

<tr>
{section name=cols loop=3}
	<td width="30%" valign="top">

{math equation="x*y" x=$rows y=%cols.index% assign="start_row"}

<table cellpadding="1" cellspacing="1">

{section name=col loop=$columns start=$start_row max=$rows}

{assign var="idx" value=$smarty.section.col.index}

<tr>
	<td width="1">{$smarty.section.col.index}:</td>
	<td width="99%">
<select name="import_columns[{$smarty.section.col.index}]">
	<option value=""{if $layout[$idx] eq ""} selected="selected"{/if}>{$lng.lbl_null}</option>
{section name=col2 loop=$columns2}
	<option value="{$columns2[col2]}"{if $layout[$idx] eq $columns2[col2]} selected="selected"{/if}>{$columns2[col2]}</option>
{/section}
</select>
	</td>
</tr>

{/section}

</table>

	</td>

{/section}
</tr>

</table>

	</td>
</tr>

<tr>
	<td style="padding-top: 20px;">
<table cellspacing="0" cellpadding="2">
<tr>
	<td><input type="checkbox" id="read_csv_header" name="read_csv_header" value="Y"{if $import_3x_4x_saved.read_csv_header} checked="checked"{/if} /></td>
	<td><label for="read_csv_header">{$lng.lbl_get_columns_names_from_first_row}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td style="padding-top: 20px;">{$lng.lbl_csv_delimiter}:<br />{include file="provider/main/ie_delimiter.tpl" saved_delimiter=$import_3x_4x_saved.delimiter}</td>
</tr>

<tr>
	<td style="padding-top: 20px;">{$lng.txt_default_category}:<br />{include file="main/category_selector.tpl" categoryid=$import_3x_4x_saved.default_categoryid}</td>
</tr>

<tr>
	<td style="padding-top: 20px;">{$lng.txt_category_path_sep}:<br /><input type="text" name="category_sep" value="{$import_3x_4x_saved.category_sep|default:"///"}" /></td>
</tr>

<tr>
	<td style="padding-top: 20px;">{$lng.txt_directory_where_images_are_located}:<br /><input type="text" size="32" name="images_directory" value="{$import_3x_4x_saved.images_directory|default:$default_imagepath}" /></td>
</tr>

<tr>
	<td style="padding-top: 20px;">{$lng.txt_csv_file_is_located_on_the_server}:<br /><input type="text" size="32" name="localfile" /></td>
</tr>

<tr>
	<td style="padding-top: 20px;">
{$lng.lbl_csv_file_for_upload}:
<br />
<input type="file" size="32" name="userfile" />
{if $upload_max_filesize}
<br />
{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}
{/if}
	</td>
</tr>

<tr>
	<td style="padding-top: 20px;">
{if $active_modules.Simple_Mode}
<b>{$lng.lbl_note}:</b> {$lng.txt_import_products_backup_note_general}
{else}
<b>{$lng.lbl_note}:</b> {$lng.txt_import_products_backup_note}
{/if}
	</td>
</tr>

<tr>
	<td style="padding-top: 20px;">

<table cellspacing="0" cellpadding="2">
<tr>
	<td>{include file="buttons/rarrow.tpl"}</td>
	<td style="padding-right: 10px;"><input type="submit" value="{$lng.lbl_import_products|strip_tags:false|escape}" /></td>
	<td><input type="checkbox" id="delete_products" value="yes" name="delete_products" onclick='javascript: this.checked = (this.checked && confirm("{$lng.txt_are_you_sure|strip_tags|escape}"));' /></td>
	<td><label for="delete_products">{$lng.lbl_drop_all_products_before_import}</label></td>
</tr>
</table>

<br />
<br />

<table cellspacing="0" cellpadding="2">
<tr>
	<td>{include file="buttons/rarrow.tpl"}</td>
	<td><input type="button" value="{$lng.lbl_store_layout|strip_tags:false|escape}" onclick='javascript: submitForm(this, "layout");' /></td>
</tr>
</table>

	</td>
</tr>

</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_import_products content=$smarty.capture.dialog extra='width="100%"'}
