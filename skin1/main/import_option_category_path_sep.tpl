{* $Id: import_option_category_path_sep.tpl,v 1.6 2005/11/28 14:19:30 max Exp $ *}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td><b>{$lng.txt_category_path_sep}:</b></td>
</tr>
<tr>
	<td><input type="text" name="options[category_sep]" value="{$import_data.options.category_sep|default:"/"}" /></td>
</tr>
<tr>
	<td>{$lng.txt_category_path_sep_explain}</td>
</tr>
</table>
