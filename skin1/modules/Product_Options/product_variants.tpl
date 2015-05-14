{* $Id: product_variants.tpl,v 1.24.2.5 2006/07/25 14:25:32 max Exp $ *}
<!--
{* php *}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->
{if $active_modules.Product_Options ne ""}

{include file="main/include_js.tpl" src="modules/Product_Options/product_variants.js"}

<script type="text/javascript">
<!--
var vwprices = [];
{foreach from=$variants item=v key=k}
{if $v.wholesale ne ''}
vwprices[{$k}] = [{foreach from=$v.wholesale item=vw name=whlv}
{if $vw.quantity > 0}

	[{$vw.quantity}, {$vw.membershipid}, '{$vw.price|formatprice}']{if not $smarty.foreach.whlv.last},{/if}
{/if}
{/foreach}];
{/if}
{/foreach}
var memberships = [];
{foreach from=$memberships item=m}
memberships[{$m.membershipid}] = "{$m.membership|replace:'"':'\"'}";
{/foreach}
var lbl_delete = "{$lng.lbl_delete|replace:'"':'\"'}";
var lbl_all = "{$lng.lbl_all|replace:'"':'\"'}";
var current_location = "{$current_location|replace:'"':'\"'}";
var pwindow;
var dateObj = new Date();
var imgTStmap = null;
var oldTStmap;
-->
</script>


{$lng.txt_product_variants_note_1}<br />
<br />
{capture name=dialog}
{if $config.General.display_all_products_on_1_page eq 'Y'}<div align="right"><a href="#main">{$lng.lbl_top}</a></div>{/if}
{$lng.txt_product_variants_note_2}<br />
<br />

<div align="right">{include file="main/visiblebox_link.tpl" mark="fpv" title=$lng.lbl_filter_product_variants}</div>
<form action="product_modify.php" method="post" name="productvariantssearchform">
<input type="hidden" id="imageW_onunload" name="imageW_onunload" value="" />
<input type="hidden" name="section" value="variants" />
<input type="hidden" name="mode" value="product_variants_search" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="boxfpv">
<tr><td>{include file="main/subheader.tpl" title=$lng.lbl_filter_product_variants}</td></tr>
<tr><td>{$lng.txt_filter_product_variants_note}</td></tr>
<tr><td>{$lng.lbl_select_options}:</td></tr>
<tr><td><hr /></td></tr>
<tr><td>
<table width="100%" cellspacing="1" cellpadding="2">
{foreach from=$product_options item=v}
{if $v.is_modifier eq ''}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
	<td><b>{$v.class}</b>:</td>
{assign var="classid" value=$v.classid}
	<td>{foreach from=$v.options item=o}
{assign var="optionid" value=$o.optionid}
{assign var="tmp_class" value=$search_variants[$classid]}
	<span style="white-space: nowrap;"><input type="checkbox" name="search[{$classid}][{$optionid}]" value="{$optionid}"{if $tmp_class[$optionid] ne '' || $is_search_all eq 'Y'} checked="checked"{/if} />&nbsp;{$o.option_name}</span>&nbsp;&nbsp;
	{/foreach}</td>
</tr>
{/if}
{/foreach}
</table>
</td></tr>
<tr><td><hr /></td></tr>
<tr><td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td></tr>
</table>
</form>
<br />

{if $def_variant_failure}
<div class="ErrorMessage">{$lng.lbl_warning}: {$lng.txt_default_variant_failure_note}</div>
<br />
{/if}

<script type="text/javascript" language="JavaScript 1.2">
<!--
var checkboxes_form = 'productvariantsform';
var checkboxes = [{foreach from=$variants item=v key=k}'v{$k}',{/foreach}''];

var vids = [];
-->
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}
<div style="line-height: 170%;"><a href="javascript: void(0);" onclick="javascript: change_all(true); rebuildWP();">{$lng.lbl_check_all}</a> / <a href="javascript: void(0);" onclick="javascript: change_all(false); rebuildWP();">{$lng.lbl_uncheck_all}</a></div>

<form action="product_modify.php" method="post" name="productvariantsform">
<input type="hidden" name="section" value="variants" />
<input type="hidden" name="mode" value="product_variants_modify" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellspacing="0" cellpadding="3" width="100%">
{if $geid ne ''}
<tr>
    <td width="15" class="TableSubHead"><img src="{$ImagesDir}/spacer.gif" width="15" height="1" alt="" /></td>
    <td class="TableSubHead" colspan="8"><b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}</td>
</tr>
{/if}
<tr class="TableHead"> 
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td width="15" class="DataTable">&nbsp;</td>
	<td width="80%" class="DataTable">{$lng.lbl_variants}</td>
	<td class="DataTable">{$lng.lbl_image}</td>
	<td class="DataTable">{$lng.lbl_sku}</td>
	<td class="DataTable">{$lng.lbl_weight}</td>
	<td class="DataTable">{$lng.lbl_in_stock}</td>
<!-- Start addition by Michael de Leon 09.19.06 -->
{if $active_modules.Extra_Fields ne ""}
	<td class="DataTable">
		{section name=field loop=$extra_fields}
			{$extra_fields[field].field}
		{/section}
	</td>
{/if}
<!-- End addition by Michael de Leon 09.19.06 -->	
	<td class="DataTable">{$lng.lbl_price}</td>
	<td>{$lng.lbl_def}</td>
</tr>
{foreach from=$variants item=v key=k}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[variants][{$k}]" /></td>{/if}
	<td class="DataTable" width="15"><input type="checkbox" id="v{$k}" name="vids[{$k}]" value="{$k}" onclick="javascript: rebuildWP(); displayImage(this, {$k|default:0});" /></td>
	<td class="DataTable"><table cellspacing="1" cellpadding="0">
	{foreach from=$v.options item=o}
	<tr>
		<td>{$o.class}:</td>
		<td>{$o.option_name}</td>
	</tr>
	{/foreach}
	</table></td>
	<td align="center" class="DataTable"><img id="image_W_{$k}" src="{$current_location}/image.php?type=W&amp;id={$k}&amp;timestamp={$smarty.now}" width="25" height="25" alt="" onclick="javascript: window.open(this.src,'_wpreview','location=no,direction=no,menubar=no,toolbar=no,status=no');" style="cursor: pointer;" /></td>
	<td class="DataTable"><input type="text" size="10" name="vs[{$k}][productcode]" value="{$v.productcode}" /></td>
	<td class="DataTable"><input type="text" size="5" name="vs[{$k}][weight]" value="{$v.weight|formatprice}" /></td>
	<td class="DataTable"><input type="text" size="5" name="vs[{$k}][avail]" value="{$v.avail|formatnumeric}" /></td>
<!-- Start addition by Michael de Leon 09.19.06 -->
	{if $active_modules.Extra_Fields ne ""}
		{if $v.productcode eq $product.productcode}
			{section name=field loop=$extra_fields}
				{if $extra_fields[field].fieldid eq '1'}
					<TD><select name="efields[{$extra_fields[field].fieldid}]" size="1">
					<option value="1-2 days" {if $extra_fields[field].field_value eq '1-2 days'}selected{/if}>1-2 days</option>
					<option value="3-5 days" {if $extra_fields[field].field_value eq '3-5 days'}selected{/if}>3-5 days</option>
					</select>
					</TD>
				{/if}
			{/section}
		{else}
			<TD class="DataTable"><select name="vs[{$k}][extra_field1]" size="1">
			<option value="1-2 days" {if $v.extra_field1 eq '1-2 days'}selected{/if}>1-2 days</option>
			<option value="3-5 days" {if $v.extra_field1 eq '3-5 days'}selected{/if}>3-5 days</option>
			</select>
			</TD>
		{/if}
	{/if}
<!-- End addition by Michael de Leon 09.19.06 -->
	<td nowrap="nowrap" class="DataTable"><input type="text" size="7" name="vs[{$k}][price]" value="{$v.price|formatprice}" />
{if $v.wholesale ne ''}
<img id="close{$k}wp" src="{$ImagesDir}/plus.gif" alt="{$lng.lbl_click_to_open|escape}" onclick="javascript: visibleBox('{$k}wp');" />
<img id="open{$k}wp" style="display: none" src="{$ImagesDir}/minus.gif" alt="{$lng.lbl_click_to_close|escape}" onclick="javascript: visibleBox('{$k}wp');" />
{/if}
</td>
	<td><input type="radio" name="def_variant" value="{$k}"{if $v.def eq 'Y'} checked="checked"{/if} />
<script type="text/javascript">
<!--
vids[{$k}] = [document.getElementById('v{$k}'), document.getElementById('image_W_{$k}')];
-->
</script>
	</td>
</tr>
{if $v.wholesale ne ''}
<tr id="box{$k}wp" style="display: none;">
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td>&nbsp;</td>
	<td colspan="6" align="right" width="100%"><table cellspacing="1" cellpadding="2">
	<tr class="TableHead">
		<td>{$lng.lbl_quantity}</td>
		<td>{$lng.lbl_membership}</td>
		<td>{$lng.lbl_price}</td>
	</tr>
	{foreach from=$v.wholesale item=wp}
	{if $wp.quantity > 1 || $wp.membershipid > 0}
	<tr>
		<td align="center">{$wp.quantity|formatnumeric}</td>
		<td align="center">{$memberships_keys[$wp.membershipid].membership|default:$lng.lbl_all}</td>
		<td align="center">{include file="currency.tpl" value=$wp.price}</td>
	</tr>
	{/if}
	{/foreach}
	</table></td>
</tr>
{/if}
{foreachelse}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td align="center" colspan="8">{$lng.lbl_variants_list_empty}</td>
</tr>
{/foreach}
{if $variants ne ''}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td>&nbsp;</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="8">
	<table width="100%" cellspacing="1" cellpadding="2">
	<tr>
		<td width="30%"><b>{$lng.lbl_edit_selected_images}:</b></td>
		<td><input type="button" value="{$lng.lbl_add_update|strip_tags:false|escape}" onclick="javascript: if (vidsChecked() && (!pwindow || pwindow.closed)) {ldelim} dateObj = new Date(); oldTStmap = imgTStmap; imgTStmap = dateObj.getTime(); pwindow = popup_image_selection('W', imgTStmap, 'imageW'); addWImage(); {rdelim}" /><span style="display: none;" id="imageW_reset">&nbsp;&nbsp;&nbsp;<input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: resetWImage();" /></span>&nbsp;&nbsp;&nbsp;<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: document.productvariantsform.mode.value='delete_image'; document.productvariantsform.submit();" /></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td>&nbsp;</td>
</tr>

{if $active_modules.Wholesale_Trading}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="8">{include file="main/visiblebox_link.tpl" mark="mwpv" title=$lng.lbl_modify_wholesale_prices_for_selected_variants}</td>
</tr>
<tr id="boxmwpv" style="display: none;">
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[wp_variant]" /></td>{/if}
	<td colspan="8">
<br />
{$lng.txt_modify_wholesale_prices_for_selected_variants_note}<br />
<br />
<table cellspacing="1" cellpadding="2" id="wp_table">
<tr class="TableHead">
	<td>{$lng.lbl_quantity}</td>
	<td>{$lng.lbl_price}</td>
	<td>{$lng.lbl_membership}</td>
</tr>
<tr id="wp_tr">
	<td id="wp_box_1"><input type="text" size="5" name="new_wprice[quantity][0]" value="1" /></td>
	<td id="wp_box_2"><input type="text" size="7" name="new_wprice[price][0]" value="{$product.price|formatprice}" /></td>
	<td id="wp_box_3"><select name="new_wprice[membershipid][0]">
	<option value="">{$lng.lbl_all}</option>
	{foreach from=$memberships item=m}
	<option value="{$m.membershipid}">{$m.membership}</option>
	{/foreach}
	</select></td>
	<td>{include file="buttons/multirow_add.tpl" mark="wp"}</td>
</tr>
</table>
<hr width="100%" />
	</td>
</tr>
{/if}
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td>&nbsp;</td>
</tr>
<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
{/if}
</table>

<input type="hidden" name="delete_wprice_quantity" />
<input type="hidden" name="delete_wprice_membershipid" />
<input type="hidden" name="tstamp" value="" />
<input type="hidden" id="skip_image_W" name="skip_image[W]" value="" />
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_variants content=$smarty.capture.dialog extra='width="100%"'}
{/if}
