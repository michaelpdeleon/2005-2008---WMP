{* $Id: html_catalog.tpl,v 1.17.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_html_catalog}

{$lng.txt_html_catalog_top_text}

<br /><br />

{capture name=dialog}

<form action="html_catalog.php" method="post" name="htmlcatalogform" onsubmit="javascript: return !document.htmlcatalogform.drop_pages.checked || confirm('{$lng.txt_drop_old_catalog_warning|strip_tags}');">
<input type="hidden"  name="mode" value="catalog_gen" />

<table cellpadding="5" cellspacing="1" width="100%">

<tr>
<td colspan="2">
{$lng.txt_html_catalog_will_generated_in|substitute:"cat_dir":$cat_dir:"cat_url":$cat_url}
<br /><br />
</td>
</tr>

<tr>
	<td>{$lng.lbl_drop_old_catalog}</td>
	<td>
<input type="checkbox" name="drop_pages" /><br />
<font class="SmallText">{$lng.txt_drop_old_catalog_note}</font>
	</td>
</tr>

<tr>
<td>{$lng.lbl_category}</td>
<td>
<select name="start_category">
<option value="">{$lng.lbl_root_categories}</option>
{foreach from=$categories item=c}
<option value="{$c.categoryid_path}">{$c.category}</option>
{/foreach}
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_walk_through_subcategories}</td>
<td><input type="checkbox" name="process_subcats" checked="checked" /></td>
</tr>

<tr>
<td>{$lng.lbl_generate_html_pages_for}</td>
<td>
<select name="gen_action">
<option value="3">{$lng.lbl_categories_and_products}</option>
<option value="1">{$lng.lbl_categories_only}</option>
<option value="2">{$lng.lbl_products_only}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_pages_per_pass}<br />
<font class="SmallText">{$lng.txt_html_pages_per_pass_note}</font>
</td>
<td>
<select name="pages_per_pass">
<option value="0">{$lng.lbl_all}</option>
<option>100</option>
<option>50</option>
<option>20</option>
<option>10</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_catalog_filenames_style}</td>
<td>
<select name="namestyle">
<option value="hyphen_4">{$lng.lbl_with_hyphens_in_filenames_4}</option>
<option value="hyphen">{$lng.lbl_with_hyphens_in_filenames}</option>
<option value="new">{$lng.lbl_new_3_5_x}</option>
<option value="old">{$lng.lbl_old_pre_3_5_x}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_convert_embedded_static_pages}</td>
<td><input type="checkbox" name="process_staticpages" checked="checked" /></td>
</tr>

<tr valign="top">
<td>{$lng.lbl_gen_catalogs_for_langs}:</td>
<td>
<table>
<tr class="TableHead">
	<th>{$lng.lbl_language}</th>
	<th>{$lng.lbl_catalog_path}</th>
</tr>
{section name=idx loop=$all_languages}
<tr>
	<td>{$all_languages[idx].language}</td>
	<td><input type="text" name="lngcat[{$all_languages[idx].code}]"{if $all_languages[idx].code eq $config.default_customer_language} value="{$default_catalog_path}"{/if} /></td>
</tr>
{/section}
</table>
</td>
</tr>

<tr>
	<td colspan="2" align="center" class="SubmitBox"><input type="submit" value="{$lng.lbl_generate_catalog|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_html_catalog content=$smarty.capture.dialog extra='width="100%"'}
