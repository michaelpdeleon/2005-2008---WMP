{* $Id: patch.tpl,v 1.22.2.2 2006/07/11 08:39:26 svowl Exp $ *}

{if $all_files_to_patch ne ""}
{include file="admin/main/patch_apply.tpl"}
{else}
{include file="page_title.tpl" title=$lng.lbl_patch_upgrade_center}

{$lng.txt_patch_upgrade_center_top_text}

<br /><br />

{capture name=dialog}
<form action="patch.php" method="post">
<input type="hidden" name="mode" value="upgrade" />

<table>

<tr>
	<td>{$lng.lbl_current_version}:</td>
	<td><b>{$config.version}</b></td>
</tr>

<tr>
	<td>{$lng.lbl_target_version}:</td>
	<td>
	<select name="patch_filename">
{if $target_versions eq ""}
		<option>{$lng.lbl_no_available_patches}</option>
{else}
{section name=ver loop=$target_versions}
		<option value="{$config.version|replace:' ':'_'}-{$target_versions[ver]|replace:' ':'_'}">{$target_versions[ver]}</option>
{/section}
{/if}
	</select>
	</td>
</tr>

{if $target_versions ne ""}
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>
{/if}

<tr>
	<td>&nbsp;</td>
	<td>
	<a href="https://secure.qualiteam.biz/customer.php?area=filearea&amp;target=upgrade_pack&amp;brand=xcart&amp;version={$config.version|escape:"url"}&amp;shop_url={$xcart_http_host|escape:"url"}">{$lng.lbl_check_for_upgrade_patches}</a>
	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_upgrade content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

{$lng.txt_patch_apply_note}

<br /><br />

{capture name=dialog}

<form action="patch.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="mode" value="normal" />

<table>

<tr>
	<td>{$lng.lbl_patch_file}:</td>
	<td><input type="file" name="patch_file" /></td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
	<td>{$lng.lbl_patch_url}:</td>
	<td><input type="text" name="patch_url" size="32" /></td>
</tr>

<tr>
	<td>{$lng.lbl_reverse}:</td>
	<td>
	<select name="reverse">
		<option value="N">{$lng.lbl_no}</option>
		<option value="Y">{$lng.lbl_yes}</option>
	</select>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_apply_patch content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

<a name="patch_sql" />
{$lng.txt_apply_sql_patch_note}

<br /><br />

{capture name=dialog}

<form action="patch.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="mode" value="sql" />

<table>

<tr>
	<td>{$lng.lbl_patch_file}:</td>
	<td><input type="file" name="patch_file" /></td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
	<td>{$lng.lbl_patch_url}:</td>
	<td><input type="text" name="patch_url" size="32" /></td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
	<td>{$lng.lbl_sql_queries}:</td>
	<td><textarea cols="48" rows="5" name="patch_query"></textarea></td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_apply_sql_patch content=$smarty.capture.dialog extra='width="100%"'}

{/if}

