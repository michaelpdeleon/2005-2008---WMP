{* $Id: export_option_category_path_sep.tpl,v 1.4 2005/12/01 14:28:20 max Exp $ *}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td><b>{$lng.txt_category_path_sep}:</b></td>
</tr>
<tr>
	<td><input type="text" name="options[category_sep]" value="{$export_data.category_sep|default:"/"}" /></td>
</tr>
<tr>
	<td>{$lng.txt_category_path_sep_explain}</td>
</tr>
</table>
