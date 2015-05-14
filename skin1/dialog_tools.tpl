{if $dialog_tools_data}

{assign var="left" value=$dialog_tools_data.left}
{assign var="right" value=$dialog_tools_data.right}
{if $dialog_tools_data.columns}
{assign var="columns" value=$dialog_tools_data.columns}
{else}
{assign var="columns" value=1}
{/if}

<table cellpadding="0" cellspacing="0" width="100%">

<tr> 
<td class="NavDialogBorder" height="15" valign="bottom">
<table width="100%" cellspacing="0" cellpadding="0">

<tr>
{if $left or $dialog_tools_data.mc_left}
<td class="NavDialogTitle">{$lng.lbl_in_this_section}:</td>
{/if}
{if $right}
<td class="NavDialogTitle">{$lng.lbl_see_also}:</td>
{/if}
</tr>

</table></td>
</tr>

<tr> 
<td class="NavDialogBorder">
<table cellpadding="10" cellspacing="1" width="100%">

<tr> 
<td valign="top" class="NavDialogBox">
<table cellpadding="0" cellspacing="1" width="100%">

<tr>
{if $left or $dialog_tools_data.mc_left}{* If left or multi-column left is defined *}
<td width="50%" valign="top">
{if $columns gt 1}

{if $dialog_tools_data.mc_left}
{assign_ext var="table_rows" value=$dialog_tools_data.mc_left}
{else}
{assign_ext var="table_rows[0]" value=$left}
{/if}

{foreach from=$table_rows item=table_row}

{if $table_row.title ne ""}
<br />
{include file="main/subheader.tpl" title=$table_row.title class="red"}
{/if}

{assign var="left" value=$table_row.data}

{section name=dt1 loop=$left}
{/section}

{assign var="total_rows" value=$smarty.section.dt1.total}
{math equation="ceil(x/y)" x=$total_rows y=$columns assign="rows"}
{math equation="floor(x/y)" x=100 y=$columns assign="cell_width"}

<table cellpadding="1" cellspacing="1" width="100%">

<tr>
{section name=col loop=$columns}
<td width="{$cell_width}%" valign="top">

{math equation="x*y" x=$rows y=%col.index% assign="start_row"}

{section name=dt1 loop=$left start=$start_row max=$rows}
{include file="dialog_tools_cell.tpl" cell=$left[dt1]}
{/section}

</td>
{/section}
</tr>

</table>

{/foreach}

{else}

{foreach from=$left item=cell}
{include file="dialog_tools_cell.tpl" cell=$cell}
{/foreach}

{/if}
</td>
{/if}

{if $right}

<td valign="top">
{foreach from=$right item=cell}
{include file="dialog_tools_cell.tpl" cell=$cell}
{/foreach}
</td>

{/if}

</tr>

</table></td>
</tr>

</table></td>
</tr>

</table>

{/if}

