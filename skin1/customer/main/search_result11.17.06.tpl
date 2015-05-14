{* $Id: search_result.tpl,v 1.49.2.4 2006/08/04 11:20:47 max Exp $ *}
{* include file="page_title.tpl" title=$lng.lbl_advanced_search *}

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

<br />

{if $mode ne "search" or $products eq ""}

{include file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var searchform_def = new Array();
searchform_def[0] = new Array('posted_data[category_main]', true);
searchform_def[1] = new Array('posted_data[search_in_subcategories]', true);
searchform_def[2] = new Array('posted_data[by_title]', true);
searchform_def[3] = new Array('posted_data[by_shortdescr]', true);
searchform_def[4] = new Array('posted_data[by_fulldescr]', true);
searchform_def[5] = new Array('posted_data[by_keywords]', true);
searchform_def[6] = new Array('posted_data[price_min]', '{$zero}');
searchform_def[7] = new Array('posted_data[avail_min]', '0');
searchform_def[8] = new Array('posted_data[weight_min]', '{$zero}');
-->
</script>

{capture name=dialog}

<br />

<form name="searchform" action="search.php" method="post">
<input type="hidden" name="mode" value="search" />
<table cellpadding="1" cellspacing="5" width="100%">

<tr>
<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
	<td width="10" height="10"></td>
	<td height="10" width="80%">
<input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring}" />
&nbsp;
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
	</td>
</tr>

{if $config.General.allow_search_by_words eq 'Y'}
<tr>
	<td height="10" colspan="2"></td>
	<td>

<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" id="including_all" name="posted_data[including]" value="all"{if $search_prefilled eq "" or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="including_all">{$lng.lbl_all_word}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="radio" id="including_any" name="posted_data[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="including_any">{$lng.lbl_any_word}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="radio" id="including_phrase" name="posted_data[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="including_phrase">{$lng.lbl_exact_phrase}</label></td>
</tr>
</table>

	</td>
</tr>
{/if}

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td>

<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_shortdescr" name="posted_data[by_shortdescr]"{if $search_prefilled eq "" or $search_prefilled.by_shortdescr} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_shortdescr">{$lng.lbl_short_description}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_fulldescr" name="posted_data[by_fulldescr]"{if $search_prefilled eq "" or $search_prefilled.by_fulldescr} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_fulldescr">{$lng.lbl_det_description}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_sku" name="posted_data[by_sku]"{if $search_prefilled eq "" or $search_prefilled.by_sku} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_sku">{$lng.lbl_sku}</label></td>
</tr>
</table>

	</td>
</tr>

{if $active_modules.Extra_Fields && $extra_fields ne ''}
<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_search_also_in}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td>

<table cellpadding="0" cellspacing="0">
{foreach from=$extra_fields item=v}
<tr>
	<td width="5"><input type="checkbox" id="ef_{$v.fieldid}" name="posted_data[extra_fields][{$v.fieldid}]"{if $v.selected eq "Y"} checked="checked"{/if} /></td>
	<td><label for="ef_{$v.fieldid}">{$v.field}</label></td>
</tr>
{/foreach}
</table>

	</td>
</tr>
{/if}

</table>

<br />
{if $config.Search_products.search_products_category eq 'Y' || ($active_modules.Manufacturers && $config.Search_products.search_products_manufacturers eq 'Y') || $config.Search_products.search_products_price eq 'Y' || $config.Search_products.search_products_weight eq 'Y'}
{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="0" cellspacing="0" width="100%"{if $js_enabled eq 'Y'} style="display: none;"{/if} id="box1">
<tr>
	<td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
	<td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
</tr>

{if $config.Search_products.search_products_category eq 'Y'}
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in_category}:</td>
	<td height="10"></td>
	<td height="10">
	<select name="posted_data[categoryid]" style="width: 70%;">
		<option value=""></option>
{foreach from=$search_categories item=v}
		<option value="{$v.categoryid}" {if $search_prefilled.categoryid eq $v.categoryid}selected{/if}>{$v.category_path}</option>
{/foreach}
	</select>
	</td>
</tr>

<tr>
	<td colspan="2" height="10">&nbsp;</td>
	<td height="10">

<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5" nowrap="nowrap">{$lng.lbl_as}&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="category_main" name="posted_data[category_main]"{if $search_prefilled eq "" or $search_prefilled.category_main} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="category_main">{$lng.lbl_main_category}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="category_extra" name="posted_data[category_extra]"{if $search_prefilled.category_extra} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="category_extra">{$lng.lbl_additional_category}</label></td>
</tr>
</table>

	</td>
</tr>

<tr>
	<td colspan="2" width="10" height="10">&nbsp;</td>
	<td height="10">

<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="search_in_subcategories" name="posted_data[search_in_subcategories]"{if $search_prefilled eq "" or $search_prefilled.search_in_subcategories} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="search_in_subcategories">{$lng.lbl_search_in_subcategories}</label></td>
</tr>
</table>

	</td>
</tr>
{/if}


{if $active_modules.Manufacturers && $manufacturers ne '' && $config.Search_products.search_products_manufacturers eq 'Y'}
{capture name=manufacturers_items} 
{section name=mnf loop=$manufacturers}
	<option value="{$manufacturers[mnf].manufacturerid}"{if $manufacturers[mnf].selected eq 'Y'} selected="selected"{/if}>{$manufacturers[mnf].manufacturer}</option>
{/section}
{/capture}
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturers}:</td>
	<td height="10"></td>
	<td height="10">
	<select name="posted_data[manufacturers][]" style="width: 70%;" multiple="multiple" size="{if $smarty.section.mnf.total gt 5}5{else}{$smarty.section.mnf.total}{/if}">
{$smarty.capture.manufacturers_items}
	</select>
	</td>
</tr>
{/if}

{if $config.Search_products.search_products_price eq 'Y'}
<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">

<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|default:"0"|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max}" /></td>
</tr>
</table>

	</td>
</tr>
{/if}

{if $config.Search_products.search_products_weight eq 'Y'}
<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol}):</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">

<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="10" name="posted_data[weight_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.weight_min|default:"0"|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="10" name="posted_data[weight_max]" value="{$search_prefilled.weight_max|formatprice}" /></td>
</tr>
</table>

	</td>
</tr>
{/if}

<tr>
	<td>&nbsp;</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td colspan="3" class="SubmitBox">
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: reset_form('searchform', searchform_def);" />
	</td>
</tr>

</table>

	</td>
</tr>
</table>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2"><!--
visibleBox('1');
--></script>
{/if}
{/if}
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_products content=$smarty.capture.dialog extra='width="100%"'}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{if $total_items gt "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{elseif $total_items eq "0"}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

{if $mode eq "search" and $products ne ""}

<!-- SEARCH RESULTS START -->

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="search.php"}</div>

{if $total_pages gt 2}
{assign var="navpage" value=$navigation_page}
{/if}


<table cellpadding="0" cellspacing="0" width="100%">
{if $sort_fields}
<tr>
	<td>{include file="main/search_sort_by.tpl" sort_fields=$sort_fields selected=$search_prefilled.sort_field direction=$search_prefilled.sort_direction url="search.php?mode=search&"}
	<br /><br /><br />
	</td>
</tr>
{/if}
<tr>
<td>

{include file="customer/main/navigation.tpl"}

{include file="customer/main/products.tpl" products=$products}

<br />

{include file="customer/main/navigation.tpl"}

</td>
</tr>
</table>

<br />

{if $search_url ne ""}
<div align="right"><a href="{$search_url|amp}" class="SmallNote">{$lng.lbl_this_page_url}</a></div>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}

{/if}

<br /><br />
