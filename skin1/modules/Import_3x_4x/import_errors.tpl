{* $Id: import_errors.tpl,v 1.1 2006/04/10 06:42:21 max Exp $ *}
<table cellpadding="5" cellspacing="1" width="100%">
<tr class="TableHead">
	<td>{$lng.lbl_line_number}</td>
	<td>{$lng.lbl_message}</td>
</tr>
{section name=idx loop=$data}
<tr{cycle values=", class=TableSubHead"}>
	<td align="center">{$data[idx].line}</td>
	<td>
{if $data[idx].label eq "wrong"}
{$lng.err_data_supplied_is_invalid|substitute:"field":$data[idx].field:"data":$data[idx].data}
{elseif $data[idx].label eq "format"}
{$lng.err_data_format_is_invalid|substitute:"field":$data[idx].field:"data":$data[idx].data}
{elseif $data[idx].label eq "missing"}
{$lng.err_reference_supplied_is_missing|substitute:"field":$data[idx].field:"data":$data[idx].data}
{elseif $data[idx].label eq "fileopen"}
{$lng.err_file_reference_cannot_be_opened|substitute:"field":$data[idx].field:"data":$data[idx].data}
{elseif $data[idx].label ne ''}
{$data[idx].label}
{/if}
	</td>
</tr>
{/section}
</table>
