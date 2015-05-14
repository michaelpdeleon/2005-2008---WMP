{* $Id: import_results.tpl,v 1.1.2.2 2006/07/11 08:39:31 svowl Exp $ *}
{if $import_stats.pass eq "test"}
{assign var=title value=$lng.lbl_import_products|cat:": "|cat:$lng.lbl_test_run}
{else}
{assign var=title value=$lng.lbl_import_products|cat:": "|cat:$lng.lbl_completed_small}
{/if}

{capture name="dialog"}

{if $import_stats.warnings}
<p class="AdminTitle">{$lng.lbl_warnings_generated_during_test}:</p>
{include file="modules/Import_3x_4x/import_errors.tpl" data=$import_stats.warnings}
{/if}

{if $import_stats.errors}
<p class="AdminTitle">{$lng.lbl_errors_that_occured_during_test}:</p>
{include file="modules/Import_3x_4x/import_errors.tpl" data=$import_stats.errors}
{/if}

{if $import_stats.errors or $import_stats.stop}

<hr />

<div style="text-align: center;">
<form action="import_3x_4x.php" method="get">
<input type="submit" value="{$lng.lbl_go_back|strip_tags:false|escape}" />
</form>
</div>

{elseif $import_stats.pass == "test"}

<div style="text-align: center;">
<p><b>{$lng.lbl_import_summary}</b></p>
</div>
<table cellspacing="1" cellpadding="2">
<tr>
	<td>{$lng.lbl_total_products_to_import}:</td>
	<td>{$import_stats.total_products}</td>
</tr>
<tr>
	<td>{$lng.lbl_new_products}:</td>
	<td>{$import_stats.products}</td>
</tr>
<tr>
	<td>{$lng.lbl_products_to_update}:</td>
	<td>{$import_stats.products_updated}</td>
</tr>
<tr>
	<td>{$lng.lbl_new_categories}:</td>
	<td>{$import_stats.categories}</td>
</tr>
<tr>
	<td>{$lng.lbl_products_to_delete}:</td>
	<td>{$import_stats.products_deleted}</td>
</tr>
</table>

<hr />
<div style="text-align: center;">
<form action="import_3x_4x.php" method="post">
<input type="hidden" name="mode" value="import" />
<input type="submit" value="{$lng.lbl_continue_import|strip_tags:false|escape}" />
</form>
</div>

{else}

<div style="text-align: center;">
<p><b>{$lng.lbl_import_summary}</b></p>
</div>

<table cellspacing="1" cellpadding="2">
<tr>
	<td>{$lng.lbl_new_products_imported}:</td>
	<td>{$import_stats.products}</td>
</tr>
<tr>
	<td>{$lng.lbl_updated_products}:</td>
	<td>{$import_stats.products_updated}</td>
</tr>
<tr>
	<td>{$lng.lbl_new_categories_created}:</td>
	<td>{$import_stats.categories}</td>
</tr>
<tr>
	<td>{$lng.lbl_products_deleted}:</td>
	<td>{$import_stats.products_deleted}</td>
</tr>
</table>

<hr />
<div style="text-align: center;">
<form action="import_3x_4x.php" method="get">
<input type="submit" value="{$lng.lbl_finish|strip_tags:false|escape}" />
</form>
</div>

{/if}

{/capture}
{include file="dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}
