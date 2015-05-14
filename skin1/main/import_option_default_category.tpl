{* $Id: import_option_default_category.tpl,v 1.7 2005/11/28 14:19:30 max Exp $ *}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td><b>{$lng.txt_default_category}:</b></td>
</tr>
<tr>
	<td>{include file="main/category_selector.tpl" field="options[categoryid]" categoryid=$import_data.options.categoryid|default:0}</td>
</tr>
<tr>
	<td>{$lng.txt_default_category_explain}</td>
</tr>
</table>
