{capture name=dialog}
{if $config.General.display_all_products_on_1_page eq 'Y'}<div align="right"><a href="#main">{$lng.lbl_top}</a></div>{/if}
<form action="product_modify.php" method="post" name="modifylng">
<input type="hidden" name="section" value="lng" />
<input type="hidden" name="mode" value="update_lng" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

{include file="main/language_selector.tpl" script="`$navigation_script`&"}
<br />

<table width="100%" {if $geid ne ''}cellspacing="0" cellpadding="4"{else}cellspacing="1" cellpadding="2"{/if}>
{if $geid ne ''}
<tr>
    <td width="15" class="TableSubHead">&nbsp;</td>
    <td class="TableSubHead" colspan="3"><b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}</td>
</tr>
{/if}
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][product]" /></td>{/if}
	<td width="20%">{$lng.lbl_product_title}:</td>
	<td>&nbsp;</td>
	<td width="80%"><input type="text" size="45" name="product_lng[product]" value="{$product_languages.product|escape:"html"}" class="InputWidth" /></td>
</tr>
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][keywords]" /></td>{/if}
	<td>{$lng.lbl_keywords}:</td>
	<td>&nbsp;</td>
	<td><input type="text" size="45" name="product_lng[keywords]" value="{$product_languages.keywords|escape:"html"}" class="InputWidth" /></td>
</tr>
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][descr]" /></td>{/if}
	<td>{$lng.lbl_short_description}:</td>
	<td>&nbsp;</td>
	<td>
{include file="main/textarea.tpl" name="product_lng[descr]" cols=45 rows=8 class="InputWidth" data=$product_languages.descr width="80%" btn_rows=4}
	</td>
</tr>
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][fulldescr]" /></td>{/if}
	<td>{$lng.lbl_det_description}:</td>
	<td>&nbsp;</td>
	<td>
{include file="main/textarea.tpl" name="product_lng[fulldescr]" cols=45 rows=12 class="InputWidth" data=$product_languages.fulldescr width="80%" btn_rows=4}
	</td>
</tr>
</table>

<br />
<hr />
<br />

<input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" />&nbsp;&nbsp;&nbsp;

{if $geid}
<br /><br />
<table>
<tr>
	<td><input type="checkbox" id="del_lang_all" name="del_lang_all" value="Y" /></td>
	<td><label for="del_lang_all">{$lng.lbl_delete_int_description_for_all_products}</label></td>
</tr>
</table>
{/if}
<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'del_lang');" />

</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.txt_international_descriptions extra='width="100%"'}
