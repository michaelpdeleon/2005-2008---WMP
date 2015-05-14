{* $Id: orders.tpl,v 1.62.2.9 2007/01/05 09:06:05 max Exp $ *}
<!-- Start addition by Michael de Leon 02.06.07 -->
<img class="wwmp_catalog_banner" src="{$ImagesDir}/wwmp_banner.jpg">
<!-- End addition by Michael de Leon 02.06.07 -->
<!-- Deleted by Michael de Leon 02.06.07
{* include file="page_title.tpl" title=$lng.lbl_orders_management *}

{* if $orders ne "" *}
{* if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode) *}
{* $lng.txt_adm_search_orders_result_header *}
{* elseif $usertype eq "P" *}
{* $lng.txt_search_orders_header *}
{* elseif $usertype eq "C" *}
{* $lng.txt_search_orders_header *}
{* /if *}
{* else *}
{* $lng.txt_search_orders_header *}
{* /if *}
<br />
-->

{if $mode ne "search" or $orders eq ""}

<!-- Deleted by Michael de Leon 02.06.07
<br /><br />
-->

{include file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var searchform_def = [
	['posted_data[date_period]', '{$search_prefilled.date_period}'],
	['StartDay', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%d"}'],
	['StartMonth', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%m"}'],
	['StartYear', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%Y"}'],
	['EndDay', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%d"}'],
	['EndMonth', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%m"}'],
	['EndYear', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%Y"}'],
	['posted_data[total_min]', '{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.total_min|formatprice}{/if}'],
	['posted_data[total_max]', '{$search_prefilled.total_max|formatprice}'],
	['posted_data[by_title]', {if $search_prefilled eq "" or $search_prefilled.by_title}true{else}false{/if}],
	['posted_data[by_options]', {if $search_prefilled eq "" or $search_prefilled.by_options}true{else}false{/if}],
	['posted_data[price_min]', '{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}'],
	['posted_data[price_max]', '{$search_prefilled.price_max|formatprice}'],
	['posted_data[address_type]', '{$search_prefilled.address_type}'],
	['posted_data[is_export]', ''],
	['posted_data[orderid1]', '{$search_prefilled.orderid1}'],
	['posted_data[orderid2]', '{$search_prefilled.orderid2}'],
	['posted_data[payment_method]', '{$search_prefilled.payment_method}'],
	['posted_data[product_substring]', '{$search_prefilled.product_substring|escape:javascript}'],
	['posted_data[features][]', '{foreach from=$search_prefilled.features item=fv key=fk}{$fk},{/foreach}'],
	['posted_data[provider]', '{$search_prefilled.provider}'],
	['posted_data[shipping_method]', '{$search_prefilled.shipping_method}'],
	['posted_data[productcode]', '{$search_prefilled.productcode|escape:javascript}'],
	['posted_data[productid]', '{$search_prefilled.productid|escape:javascript}'],
	['posted_data[customer]', '{$search_prefilled.customer|escape:javascript}'],
	['posted_data[by_username]', {if $search_prefilled eq "" or $search_prefilled.by_username}true{else}false{/if}],
	['posted_data[by_firstname]', {if $search_prefilled eq "" or $search_prefilled.by_firstname}true{else}false{/if}],
	['posted_data[by_lastname]', {if $search_prefilled eq "" or $search_prefilled.by_lastname}true{else}false{/if}],
	['posted_data[city]', '{$search_prefilled.city|escape:javascript}'],
	['posted_data[state]', '{$search_prefilled.state|escape:javascript}'],
	['posted_data[country]', '{$search_prefilled.country|escape:javascript}'],
	['posted_data[zipcode]', '{$search_prefilled.zipcode|escape:javascript}'],
	['posted_data[phone]', '{$search_prefilled.phone|escape:javascript}'],
	['posted_data[email]', '{$search_prefilled.email|escape:javascript}'],
	['posted_data[status]', '{$search_prefilled.status}']
];
{literal}
function managedate(type, status) {
	if (type != 'date')
		var fields = ['posted_data[city]','posted_data[state]','posted_data[country]','posted_data[zipcode]'];
	else
		var fields = ['StartDay','StartMonth','StartYear','EndDay','EndMonth','EndYear'];
	
	for (i in fields)
		if (document.searchform.elements[fields[i]])
			document.searchform.elements[fields[i]].disabled = status;
}
{/literal}
-->
</script>

<!-- Deleted by Michael de Leon 12.04.06
{* capture name=dialog *}
-->

<!-- Start addition by Michael de Leon 02.06.07 -->
{if $total_items eq "0"}
<table border="0" cellpadding="0" cellspacing="0" width="100%">	
	<tr>
		<td class="wwmp_searchresults_status" align="left">{$lng.txt_N_results_found|substitute:"items":0}
		</td>
	</tr>
</table>
{/if}
<br />
<!-- End addition by Michael de Leon 02.06.07 -->

<form name="searchform" action="orders.php" method="post">
<input type="hidden" name="mode" value="" />

<!-- Deleted by Michael de Leon 02.06.07
<table cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
	<td colspan="3">
{* $lng.txt_search_orders_text *}
<br /><br />
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_date_period *}:</td>
	<td width="10">&nbsp;</td>
	<td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td align="left"><font class="wwmp_aboutus_labels">Search Your Order History</font></td>
</tr>
<tr>
	<td>

<table cellpadding="1" cellspacing="5" width="100%">
<tr>
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_date_period}:</td>
	<td width="10">&nbsp;</td>
	<td>
<!-- End addition by Michael de Leon 02.06.07 -->
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" id="date_period_null" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_null">{$lng.lbl_all_dates}</label></td>

	<td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_M">{$lng.lbl_this_month}</label></td>

	<td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_W">{$lng.lbl_this_week}</label></td>

	<td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
	<td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
	<td colspan="7" class="OptionLabel"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
</td>
</tr>

<tr>
	<!-- Deleted by Michael de Leon 02.06.07 
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_order_date_from *}:</td>
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_order_date_from}:</td>
	<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td> 
	{html_select_date prefix="Start" time=$search_prefilled.start_date start_year=$config.Company.start_year end_year=$config.Company.end_year}
	</td>
</tr>

<tr>
	<!-- Deleted by Michael de Leon 02.06.07 
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_order_date_through *}:</td>
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_order_date_through}:</td>
	<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td> 
	{html_select_date prefix="End" time=$search_prefilled.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year display_days=yes}
	</td>
</tr>

{if ($usertype eq 'A' && $current_membership_flag ne 'FS') || $usertype eq 'P'}
<tr>
	<td colspan="2"></td>
	<td>
	<hr />
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="checkbox" id="posted_data_is_export" name="posted_data[is_export]" value="Y" /></td>
	<td>&nbsp;</td>
	<!-- Deleted by Michael de Leon 02.06.07
	<td class="FormButton" nowrap="nowrap"><label for="posted_data_is_export">{* $lng.lbl_search_and_export *}</label></td>
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap"><label for="posted_data_is_export">{$lng.lbl_search_and_export}</label></td>
	<!-- End addition by Michael de Leon 02.06.07 -->
</tr>
</table>
	</td>
</tr>
{/if}

<tr>
	<td colspan="2">&nbsp;</td>
	<td colspan="3" class="SubmitBox">
	<!-- Deleted by Michael de Leon 02.06.07
	<input type="submit" value="{* $lng.lbl_search|strip_tags:false|escape *}" onclick="javascript: document.searchform.mode.value=''; document.searchform.submit();" />
	-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<input type="image" src="{$ImagesDir}/wwmp_search_blue12.04.06.jpg" onclick="javascript: document.searchform.mode.value=''; document.searchform.submit();" />
<!-- End addition by Michael de Leon 02.06.07 -->
{if $search_prefilled.date_period ne "C"}
<script type="text/javascript" language="JavaScript 1.2">
<!--
managedate('date',true);
-->
</script>
{/if}
	</td>
</tr>

</table>

<br />

{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="1" cellspacing="5" width="100%"{if $js_enabled eq 'Y'} style="display: none;"{/if} id="box1">

<!-- Deleted by Michael de Leon 02.06.07
<tr>
	<td colspan="3"><br />{* include file="main/subheader.tpl" title=$lng.lbl_advanced_search_options *}</td>
</tr>

<tr>
	<td colspan="3">{* $lng.txt_adv_search_orders_text *}<br /><br /></td>
</tr>

<tr>
	<td width="25%" class="FormButton" nowrap="nowrap">{* $lng.lbl_order_id *}:</td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_search_label" align="left" width="25%" nowrap="nowrap">{$lng.lbl_order_id}:</td>
<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td width="75%">
<input type="text" name="posted_data[orderid1]" size="10" maxlength="15" value="{$search_prefilled.orderid1}" />
-
<input type="text" name="posted_data[orderid2]" size="10" maxlength="15"value="{$search_prefilled.orderid2}" />
	</td>
</tr>

{if $usertype ne "C"}
<tr>
<!-- Deleted by Michael de Leon 02.06.07
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_order_total* } ({* $config.General.currency_symbol *}):</td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_order_total} ({$config.General.currency_symbol}):</td>
<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td>

<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="15" name="posted_data[total_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.total_min|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="15" name="posted_data[total_max]" value="{$search_prefilled.total_max|formatprice}" /></td>
</tr>
</table>

	</td>
</tr>

<tr>
	<!-- Deleted by Michael de Leon 02.06.07
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_payment_method *}:</td>
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_payment_method}:</td>
	<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td>
	<select name="posted_data[payment_method]" style="width:70%">
		<option value=""></option>
{section name=pm loop=$payment_methods}
		<option value="{$payment_methods[pm].payment_method}"{if $search_prefilled.payment_method eq $payment_methods[pm].payment_method} selected="selected"{/if}>{$payment_methods[pm].payment_method}</option>
{/section}
	</select>
	</td>
</tr>

<tr>
<!-- Deleted by Michael de Leon 02.06.07
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_delivery *}:</td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_delivery}:</td>
<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td>
	<select name="posted_data[shipping_method]" style="width:70%">
		<option value=""></option>
{section name=sm loop=$shipping_methods}
		<option value="{$shipping_methods[sm].shippingid}"{if $search_prefilled.shipping_method eq $shipping_methods[sm].shippingid} selected="selected"{/if}>{$shipping_methods[sm].shipping|trademark}</option>
{/section}
	</select>
	</td>
</tr>

{/if}

<tr>
<!-- Deleted by Michael de Leon 02.06.07 
	<td class="FormButton" nowrap="nowrap">{* $lng.lbl_order_status *}:</td>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
	<td class="wwmp_search_label" align="left" nowrap="nowrap">{$lng.lbl_order_status}:</td>
<!-- End addition by Michael de Leon 02.06.07 -->
	<td width="10">&nbsp;</td>
	<td>{include file="main/order_status.tpl" status=$search_prefilled.status mode="select" name="posted_data[status]" extended="Y" extra="style='width:70%'"}</td>
</tr>

{if $usertype ne "C"}
{if $usertype eq "A"}
<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_provider}:</td>
	<td width="10">&nbsp;</td>
	<td>
	<input type="text" name="posted_data[provider]" size="30" value="{$search_prefilled.provider}" style="width:70%" />
	</td>
</tr>
{/if}

<tr> 
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_order_features}:</td>
	<td width="10">&nbsp;</td>
	<td>
{assign var="features" value=$search_prefilled.features}
	<select name="posted_data[features][]" multiple="multiple" size="7" style="width:70%">
		<option value="gc_applied"{if $features.gc_applied} selected="selected"{/if}>{$lng.lbl_entirely_or_partially_payed_by_gc|strip_tags}</option>
		<option value="discount_applied"{if $features.discount_applied} selected="selected"{/if}>{$lng.lbl_global_discount_applied|strip_tags}</option>
		<option value="coupon_applied"{if $features.coupon_applied} selected="selected"{/if}>{$lng.lbl_discount_coupon_applied|strip_tags}</option>
		<option value="free_ship"{if $features.free_ship} selected="selected"{/if}>{$lng.lbl_free_shipping|strip_tags}</option>
		<option value="free_tax"{if $features.free_tax} selected="selected"{/if}>{$lng.lbl_tax_exempt|strip_tags}</option>
		<option value="gc_ordered"{if $features.gc_ordered} selected="selected"{/if}>{$lng.lbl_gc_purchased|strip_tags}</option>
		<option value="notes"{if $features.notes} selected="selected"{/if}>{$lng.lbl_orders_with_notes_assigned|strip_tags}</option>
	</select><br />
{$lng.lbl_hold_ctrl_key}
	</td>
</tr>

{/if}

{if $usertype ne "C"}

<tr>
	<td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_search_by_ordered_products class="grey"}</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
	<input type="text" name="posted_data[product_substring]" size="30" value="{$search_prefilled.product_substring}" style="width:70%" />
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>

<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_options" name="posted_data[by_options]"{if $search_prefilled eq "" or $search_prefilled.by_options} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_options">{$lng.lbl_options}</label></td>
</tr>
</table>

	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
	<input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode}" style="width:70%" />
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_productid}#:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
	<input type="text" maxlength="64" name="posted_data[productid]" value="{$search_prefilled.productid}" style="width:70%" />
	</td>
</tr>

<tr> 
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
	<td width="10">&nbsp;</td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
	<td>&nbsp;-&nbsp;</td>
	<td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
</tr>
</table>
	</td>
</tr>

{/if}

{if $usertype ne "C"}

<tr>
	<td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_search_by_customer class="grey"}</td>
</tr>

<tr> 
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_customer}:</td>
	<td width="10">&nbsp;</td>
	<td><input type="text" name="posted_data[customer]" size="30" value="{$search_prefilled.customer}" style="width:70%" /></td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_search_in}:</td>
	<td width="10">&nbsp;</td>
	<td>
<table cellspacing="0" cellpadding="0">
<tr>
    <td width="5"><input type="checkbox" id="posted_data_by_username" name="posted_data[by_username]"{if $search_prefilled eq "" or $search_prefilled.by_username} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_username">{$lng.lbl_username}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_firstname" name="posted_data[by_firstname]"{if $search_prefilled eq "" or $search_prefilled.by_firstname} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_firstname">{$lng.lbl_first_name}</label>&nbsp;&nbsp;</td>

	<td width="5"><input type="checkbox" id="posted_data_by_lastname" name="posted_data[by_lastname]"{if $search_prefilled eq "" or $search_prefilled.by_lastname} checked="checked"{/if} /></td>
	<td nowrap="nowrap"><label for="posted_data_by_lastname">{$lng.lbl_last_name}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_search_by_address}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" id="address_type_null" name="posted_data[address_type]" value=""{if $search_prefilled eq "" or $search_prefilled.address_type eq ""} checked="checked"{/if} onclick="javascript:managedate('address',true)" /></td>
	<td class="OptionLabel"><label for="address_type_null">{$lng.lbl_ignore_address}</label></td>

	<td width="5"><input type="radio" id="address_type_B" name="posted_data[address_type]" value="B"{if $search_prefilled.address_type eq "B"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_B">{$lng.lbl_billing}</label></td>

	<td width="5"><input type="radio" id="address_type_S" name="posted_data[address_type]" value="S"{if $search_prefilled.address_type eq "S"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_S">{$lng.lbl_shipping}</label></td>

	<td width="5"><input type="radio" id="address_type_both" name="posted_data[address_type]" value="Both"{if $search_prefilled.address_type eq "Both"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_both">{$lng.lbl_both}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_city}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td><input type="text" maxlength="64" name="posted_data[city]" value="{$search_prefilled.city}" style="width:70%" /></td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_state}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>{include file="main/states.tpl" states=$states name="posted_data[state]" default=$search_prefilled.state required="N" style="style='width:70%'"}</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_country}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
	<select name="posted_data[country]" style="width:70%">
		<option value="">[{$lng.lbl_please_select_one}]</option>
{section name=country_idx loop=$countries}
		<option value="{$countries[country_idx].country_code}"{if $search_prefilled.country eq $countries[country_idx].country_code} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
	</select>
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_zip_code}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td>
<input type="text" maxlength="32" name="posted_data[zipcode]" value="{$search_prefilled.zipcode}" style="width:70%" />
{if $search_prefilled eq "" or $search_prefilled.address_type eq ""}
<script type="text/javascript" language="JavaScript 1.2">
<!--
managedate('address',true);
-->
</script>
{/if}
	</td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_phone}/{$lng.lbl_fax}:</td>
	<td width="10"><font class="CustomerMessage"></font></td>
	<td><input type="text" maxlength="32" name="posted_data[phone]" value="{$search_prefilled.phone}" style="width:70%" /></td>
</tr>

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_email}:</td>
	<td width="10">&nbsp;</td>
	<td><input type="text" maxlength="128" name="posted_data[email]" value="{$search_prefilled.email}" style="width:70%" /></td>
</tr>

{/if}

<tr>
	<td colspan="2">&nbsp;</td>
	<td>
	<!-- Deleted by Michael de Leon 02.06.07
	<br /><br />
	<input type="submit" value="{* $lng.lbl_search|strip_tags:false|escape *}" onclick="javascript: submitForm(this, '');" />
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<input type="image" src="{ $ImagesDir }/wwmp_search_blue12.04.06.jpg" />
	<!-- End addition by Michael de Leon 02.06.07 -->
	&nbsp;&nbsp;&nbsp;
	<!-- Deleted by Michael de Leon 02.06.07
	<input type="button" value="{* $lng.lbl_reset|strip_tags:false|escape *}" onclick="javascript: reset_form('searchform', searchform_def);" />
	-->
	<!-- Start addition by Michael de Leon 02.06.07 -->
	<input type="image" src="{ $ImagesDir }/wwmp_reset_blue12.04.06.jpg" onclick="javascript: reset_form('searchform', searchform_def);" />
	<!-- End addition by Michael de Leon 02.06.07 -->
	</td>
</tr>

</table>

	</td>
</tr>

</table>
</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
<!--
visibleBox('1');
-->
</script>
{/if}

<!-- Deleted by Michael de Leon 12.04.06
{* /capture *}
{* include file="dialog.tpl" title=$lng.lbl_search_orders content=$smarty.capture.dialog extra='width="100%"' *}
-->

{/if}

{if $mode eq "search"}
<br /><br />
{if $total_items >= "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

<br /><br />

{if $orders ne ""}

{include file="main/orders_list.tpl"}

{/if}

{if $usertype ne "C" && $mode ne "search" && $current_membership_flag ne 'FS'}

{capture name=dialog}

<br />

{if $usertype eq "A" or $active_modules.Simple_Mode ne ""}
{$lng.txt_delete_export_all_orders_note_admin}
{else}
{$lng.txt_delete_export_all_orders_note_provider}
{/if}
<br />
<br />

<form name="ordersform" action="orders.php" method="post">
<input type="hidden" name="mode" value="" />

<table cellpadding="1" cellspacing="5">

<tr>
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_export_file_format}:</td>
	<td>&nbsp;</td>
	<td>
	<select name="export_fmt">
		<option value="std">{$lng.lbl_standart}</option>
		<option value="csv_tab">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_tab_delimiter}</option>
		<option value="csv_semi">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_semicolon_delimiter}</option>
		<option value="csv_comma">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_comma_delimiter}</option>
{if $active_modules.QuickBooks eq "Y"}
{include file="modules/QuickBooks/orders.tpl"}
{/if}
	</select>
	</td>
	<td><input type="button" value="{$lng.lbl_export_all|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'export_all');" /></td>
</tr>

<tr> 
	<td colspan="4" class="SubmitBox">
{if $usertype eq "A"}
	<input type="button" value="{$lng.lbl_delete_all_orders|strip_tags:false|escape}" onclick="javascript: if (confirm('{$lng.txt_delete_orders_warning|strip_tags}')) submitForm(this, 'delete_all');" />
{/if}
<br />
	</td>
</tr>

</table>
</form>

{/capture}
{if $usertype eq "A" or $active_modules.Simple_Mode ne ""}
{include file="dialog.tpl" title=$lng.lbl_export_delete_orders content=$smarty.capture.dialog extra='width="100%"'}
{else}
{include file="dialog.tpl" title=$lng.lbl_export_orders content=$smarty.capture.dialog extra='width="100%"'}
{/if}

<br /><br />
{if $active_modules.Order_Tracking}
{include file="main/orders_tracking.tpl"}
{/if}

{/if}

<br /><br />
