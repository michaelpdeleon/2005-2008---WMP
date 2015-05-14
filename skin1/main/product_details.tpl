{* $Id: product_details.tpl,v 1.44.2.8 2006/10/13 10:41:21 svowl Exp $ *}

{capture name=dialog}

{if $taxes}
<script type="text/javascript" language="JavaScript 1.2">
<!--
function ChangeTaxesBoxStatus() {ldelim}
	if (document.modifyform && document.modifyform.elements['taxes[]'])
		document.modifyform.elements['taxes[]'].disabled = (document.modifyform.free_tax.value == 'Y');
{rdelim}
-->
</script>
{/if}

<form action="product_modify.php" method="post" name="modifyform">
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="section" value="main" />
<input type="hidden" name="mode" value="product_modify" />
<input type="hidden" name="geid" value="{$geid}" />

<table cellpadding="4" cellspacing="0" width="100%">

{if $geid ne ''}
<tr>
	<td width="15" class="TableSubHead">&nbsp;</td>
	<td class="TableSubHead" colspan="2"><b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}</td>
</tr>
{/if}
{if $config.Appearance.show_thumbnails eq "Y"}

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_product_thumbnail}</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[thumbnail]" /></td>{/if}
	<td class="ProductDetails" valign="top"><font class="FormButton">{$lng.lbl_thumbnail}</font><br />{$lng.lbl_thumbnail_msg}</td>
	{if $product.is_thumbnail}{assign var="no_delete" value=""}{else}{assign var="no_delete" value="Y"}{/if}
	<td class="ProductDetails">
	{include file="main/edit_image.tpl" type="T" id=$product.productid delete_js="submitForm(this, 'delete_thumbnail');" button_name=$lng.lbl_save image=$product.image.T already_loaded=$product.is_image_T}
	</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[product_image]" /></td>{/if}
	<td class="ProductDetails" valign="top"><font class="FormButton">{$lng.lbl_product_image}</font></td>
	{if $product.is_image}{assign var="no_delete" value=""}{else}{assign var="no_delete" value="Y"}{/if}
	<td class="ProductDetails">
	{include file="main/edit_image.tpl" type="P" id=$product.productid delete_js="submitForm(this, 'delete_product_image');" button_name=$lng.lbl_save idtag="edit_product_image" image=$product.image.P already_loaded=$product.is_image_P}
	</td>
</tr>

{/if}

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_product_owner}</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td class="FormButton" width="20%" nowrap="nowrap">{$lng.lbl_provider}:</td>
	<td class="ProductDetails" width="80%">
{if $usertype eq "A" and $new_product eq 1}
	<select name="provider" class="InputWidth">
{section name=prov loop=$providers}
		<option value="{$providers[prov].login}">{$providers[prov].login} ({$providers[prov].title} {$providers[prov].lastname} {$providers[prov].firstname})</option>
{/section}
	</select>
{else}
{$provider_info.title} {$provider_info.lastname} {$provider_info.firstname} ({$provider_info.login})
{/if}
	</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_classification}</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryid]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_main_category}:</td>
	<td class="ProductDetails">{include file="main/category_selector.tpl" field="categoryid" extra=' class="InputWidth"' categoryid=$product.categoryid|default:$default_categoryid}
	{if $top_message.fillerror ne "" and $product.categoryid eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryids]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_additional_categories}:</td>
	<td class="ProductDetails">
	<select name="categoryids[]" class="InputWidth" multiple="multiple" size="8">
{foreach from=$allcategories item=c key=catid}
		<option value="{$catid}"{if ($c.productid eq $product.productid && $product.productid ne "") || ($product.productid eq '' && $product.add_categoryids && $product.add_categoryids[$catid])} selected="selected"{/if}>{$c.category_path}</option>
{/foreach}
	</select>
	</td>
</tr>

{if $active_modules.Manufacturers ne ""}
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[manufacturer]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturer}:</td>
    <td class="ProductDetails">
	<select name="manufacturerid">
	    <option value=''{if $product.manufacturerid eq ''} selected="selected"{/if}>{$lng.lbl_no_manufacturer}</option>
    {foreach from=$manufacturers item=v}
    	<option value='{$v.manufacturerid}'{if $v.manufacturerid eq $product.manufacturerid} selected="selected"{/if}>{$v.manufacturer}</option>
    {/foreach}
    </select>
	</td>
</tr>
{/if}

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[forsale]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_availability}:</td>
	<td class="ProductDetails">
	<select name="forsale">
		<option value="Y"{if $product.forsale eq "Y"} selected="selected"{/if}>{$lng.lbl_avail_for_sale}</option>
		<option value="H"{if $product.forsale eq "H"} selected="selected"{/if}>{$lng.lbl_hidden}</option>
		<option value="N"{if $product.forsale ne "Y" && $product.forsale ne "H" && ($product.forsale ne "B" || not $active_modules.Product_Configurator)} selected="selected"{/if}>{$lng.lbl_disabled}</option>
{if $active_modules.Product_Configurator}
		<option value="B"{if $product.forsale eq "B"} selected="selected"{/if}>{$lng.lbl_bundled}</option>
{/if}
	</select>
	</td>
</tr>

{if $product.forsale eq 'H'}
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryids]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_product_url}:</td>
	<td class="ProductDetails">{$catalogs.customer}/product.php?productid={$product.productid}&cat={$product.categoryid}</td>
</tr>
{/if}

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_details}</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
	<td class="ProductDetails"><input type="text" name="productcode" size="20" value="{$product.productcode}" class="InputWidth" /></td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[product]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_product_name}:</td>
	<td class="ProductDetails"> 
	<input type="text" name="product" size="45" class="InputWidth" value="{$product.product|escape}" />
	{if $top_message.fillerror ne "" and $product.product eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[keywords]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_keywords}:</td>
	<td class="ProductDetails"><input type="text" name="keywords" class="InputWidth" value="{$product.keywords|escape:"html"}" /></td>
</tr>

{if $active_modules.Egoods ne ""}
{include file="modules/Egoods/egoods.tpl"}
{/if}

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[descr]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_short_description}* :</td>
	<td class="ProductDetails">
	{include file="main/textarea.tpl" name="descr" cols=45 rows=8 class="InputWidth" data=$product.descr width="80%" btn_rows=4}
	{if $top_message.fillerror ne "" and $product.descr eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[fulldescr]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_det_description}* :</td>
	<td class="ProductDetails">
	{include file="main/textarea.tpl" name="fulldescr" cols=45 rows=12 class="InputWidth" data=$product.fulldescr width="80%" btn_rows=4}
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
	<td colspan="2">{$lng.txt_html_tags_in_description}</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[price]" />{/if}</td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol})</td>
	<td class="ProductDetails">
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
	<input type="text" name="price" size="18" value="{ $product.price|formatprice|default:$zero}" />
	{if $top_message.fillerror ne "" and $product.price eq ""}<font class="Star">&lt;&lt;</font>{/if}
{/if}
	</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[list_price]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_list_price} <span class="Text">({$config.General.currency_symbol})</span></td>
	<td class="ProductDetails"><input type="text" name="list_price" size="18" value="{$product.list_price|formatprice|default:$zero}" /></td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[avail]" />{/if}</td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_quantity_in_stock}</td>
	<td class="ProductDetails">
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
	<input type="text" name="avail" size="18" value="{if $product.productid eq ""}{$product.avail|default:1000}{else}{$product.avail}{/if}" />
	{if $top_message.fillerror ne "" and $product.avail eq ""}<font class="Star">&lt;&lt;</font>{/if}
{/if}
	</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[low_avail_limit]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_lowlimit_in_stock}</td>
	<td class="ProductDetails"> 
	<input type="text" name="low_avail_limit" size="18" value="{if $product.productid eq ""}10{else}{ $product.low_avail_limit }{/if}" />
	{if $top_message.fillerror ne "" and $product.low_avail_limit le 0}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[min_amount]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_min_order_amount}</td>
	<td class="ProductDetails"><input type="text" name="min_amount" size="18" value="{if $product.productid eq ""}1{else}{$product.min_amount}{/if}" /></td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[weight]" />{/if}</td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol})</td>
	<td class="ProductDetails"> 
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
	<input type="text" name="weight" size="18" value="{ $product.weight|formatprice|default:$zero }" />
{/if}
	</td>
</tr>

{if $active_modules.RMA ne ''}
<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[return_time]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_return_time}</td>
	<td class="ProductDetails"><input type="text" name="return_time" size="18" value="{$product.return_time}" /></td>
</tr>
{/if}

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[membershipids]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_membership}</td>
	<td class="ProductDetails">{include file="main/membership_selector.tpl" data=$product}</td>
</tr>

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_tax]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_tax_exempt}</td>
	<td class="ProductDetails">
	<select name="free_tax"{if $taxes} onchange="javascript: ChangeTaxesBoxStatus();"{/if}>
		<option value='N'{if $product.free_tax eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
		<option value='Y'{if $product.free_tax eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
	</select> 
	</td>
</tr>

{if $taxes}
<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[taxes]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_taxes}</td>
	<td class="ProductDetails"> 
	<select name="taxes[]" multiple="multiple"{if $product.free_tax eq "Y"} disabled="disabled"{/if}>
	{section name=tax loop=$taxes}
	<option value="{$taxes[tax].taxid}"{if $taxes[tax].selected gt 0} selected="selected"{/if}>{$taxes[tax].tax_name}</option>
	{/section}
	</select>
	<br />{$lng.lbl_hold_ctrl_key}
	{if $usertype eq "P" or $active_modules.Simple_Mode ne ""}<br /><a href="{$catalogs.provider}/taxes.php" class="SmallNote" target="_new">{$lng.lbl_click_here_to_manage_taxes}</a>{/if}
	</td>
</tr>
{/if}

<tr> 
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_shipping]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_free_shipping}</td>
	<td class="ProductDetails">
	<select name="free_shipping">
		<option value='N'{if $product.free_shipping eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
		<option value='Y'{if $product.free_shipping eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
	</select> 
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[shipping_freight]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_shipping_freight} ({$config.General.currency_symbol})</td>
	<td class="ProductDetails">
	<input type="text" name="shipping_freight" size="18" value="{$product.shipping_freight|formatprice|default:$zero}" />
	</td>
</tr>

<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[discount_avail]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_global_discounts}</td>
	<td class="ProductDetails">
	<input type="checkbox" name="discount_avail" value="Y"{if $product.productid eq "" || $product.discount_avail eq "Y"} checked="checked"{/if} />
	</td>
</tr>

{if $gcheckout_enabled}

<input type="hidden" name="valid_for_gcheckout" value="N" />
<tr>
	{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[valid_for_gcheckout]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_gcheckout_product_valid}</td>
	<td class="ProductDetails">
	<input type="checkbox" name="valid_for_gcheckout" value="Y"{if $product.productid eq "" || $product.valid_for_gcheckout eq "Y"} checked="checked"{/if} />
	</td>
</tr>

{/if}

<!-- Deleted by Michael de Leon 02.06.07
{* if $active_modules.Extra_Fields ne "" *}
{* include file="modules/Extra_Fields/product_modify.tpl" *}
{* /if *}
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
{ if $product.is_variants ne 'Y' and $active_modules.Extra_Fields ne "" }
{ include file="modules/Extra_Fields/product_modify.tpl" }
{ /if }
<!-- End addition by Michael de Leon 02.06.07 -->

<tr>
	<td colspan="3" align="center"><br /><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_details content=$smarty.capture.dialog extra='width="100%"'}
