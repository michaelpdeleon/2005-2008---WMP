{* $Id: export_specs.tpl,v 1.5.2.1 2006/06/15 07:01:26 max Exp $ *}
{foreach from=$export_spec item=v key=k}
<tr{if $level eq 0} class="TableSubHead"{/if}>
	<td>{if $level eq 0}<input type="checkbox" id="check_{$k|lower}" name="check[{$k|lower}]" value="Y" />{/if}</td>
	<td nowrap="nowrap"{if $level > 0} style="padding-left: {math equation="(x-1)*25" x=$level}px;"{/if}>
		<table cellspacing="0" cellpadding="0">
		<tr>
		{if $level > 0}
			<td width="25"><input type="checkbox" id="check_{$k|lower}" name="check[{$k|lower}]" value="Y" /></td>
		{/if}
			<td nowrap="nowrap"><label for="check_{$k|lower}">{$k|replace:"_":" "}</label></td>
		</tr>
		</table>
	</td>
	{if $level > 0 || $v.is_range eq ''}
	<td colspan="2">&nbsp;</td>
	{else}
	<td width="25" align="center" nowrap="nowrap">{if $v.range_count eq -1}{$lng.lbl_all}{else}{$v.range_count}{/if}</td>
	<td nowrap="nowrap"><a href="{$v.is_range}">{$lng.lbl_change_data_range}</a>{if $v.range_count ne -1}&nbsp;/&nbsp;<a href="import.php?mode=export&amp;action=clear_range&amp;section={$k|lower}">{$lng.lbl_remove_data_range}</a>{/if}</td>
	{/if}
</tr>
{if $level eq 0}
<tr>
	<td colspan="4" class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/if}
{if $v.subsections ne ''}
{include file="main/export_specs.tpl" export_spec=$v.subsections level=$level+1}
{/if}
{/foreach}

