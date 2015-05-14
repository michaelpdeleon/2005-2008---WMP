{* $Id: patch_apply_tbl.tpl,v 1.4 2005/11/30 13:29:35 max Exp $ *}
<table cellpadding="1" cellspacing="2" width="100%" valign="top">
<tr>
	<td height="14" class="TableHead" nowrap="nowrap">{$lng.lbl_file}</td>
	<td height="14" class="TableHead" nowrap="nowrap" width="100">{$lng.lbl_status}</td>
</tr>
{section name=index loop=$files}
<tr {if %index.index% mod 2 eq 0} class="TableLine"{/if}>
	<td>{$files[index].orig_file}</td>
	<td>
{if $files[index].status eq "OK"}
<font color="green">{$lng.lbl_ok}</font>
{else}
<font color="red">{$files[index].status}</font>
{/if}
	</td>
</tr>
{/section}
</table>

<br /><br />

{$lng.txt_patch_status_legend}

<br /><br />

