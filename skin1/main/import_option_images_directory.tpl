{* $Id: import_option_images_directory.tpl,v 1.6 2005/11/28 14:19:30 max Exp $ *}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td><b>{$lng.txt_directory_where_images_are_located}:</b></td>
</tr>
<tr>
	<td><input type="text" size="55" name="options[images_directory]" value="{$import_data.options.images_directory|default:""}" /></td>
</tr>
<tr>
	<td>{$lng.txt_directory_where_images_are_located_expl|substitute:"my_files_location":$my_files_location}</td>
</tr>
</table>
