{* $Id: product.tpl,v 1.9 2005/11/21 12:42:06 max Exp $ *}
{section name=field loop=$extra_fields}
{if $extra_fields[field].active eq "Y" && $extra_fields[field].field_value}
<tr>
	<td width="30%">{$extra_fields[field].field}</td>
	<td>{$extra_fields[field].field_value}</td>
</tr>
{/if}
{/section}
