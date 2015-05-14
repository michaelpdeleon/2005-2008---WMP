{* $Id: coupons.tpl,v 1.36.2.2 2006/07/11 08:39:29 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_store_coupons_title}

{include file="main/include_js.tpl" src="main/popup_product.js"}

<script type="text/javascript" language="JavaScript 1.2">
<!--

function changeBox(id, status) {ldelim}
	var elm = document.getElementById("box"+id);
	if (!elm)
		return false;
	
	elm.style.display = (status == true) ? "" : "none";
{rdelim}

-->
</script>

{$lng.txt_discountcoupons_desc}

<br /><br />

{capture name=dialog}

{if $coupons ne ""}

<script type="text/javascript" language="JavaScript 1.2">
<!--
checkboxes_form = 'couponsform';
checkboxes = new Array({foreach from=$coupons item=v key=k}{if $k > 0},{/if}'posted_data[{$v.coupon}][to_delete]'{/foreach});
 
-->
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

{if $coupons ne ""}
<form action="coupons.php" method="post" name="couponsform">
<input type="hidden" name="mode" value="update" />
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	{if $coupons ne ""}<td width="10">&nbsp;</td>{/if}
	<td width="15%">{$lng.lbl_coupon}</td>
	<td width="15%">{$lng.lbl_status}</td>
	<td width="15%" align="center">{$lng.lbl_coupon_disc}</td>
	<td width="15%" align="center">{$lng.lbl_coupon_min}</td>
	<td width="15%" align="center">{$lng.lbl_coupon_times}</td>
	<td width="25%" align="center">{$lng.lbl_coupon_expires}</td>
</tr>

{if $coupons ne ""}
{section name=prod_num loop=$coupons}

<tr{cycle values=", class='TableSubHead'" advance=false}>
	<td><input type="checkbox" name="posted_data[{$coupons[prod_num].coupon}][to_delete]" /></td>
	<td><b>{$coupons[prod_num].coupon}</b></td>
	<td>
	<select name="posted_data[{$coupons[prod_num].coupon}][status]">
	<option value="A"{if $coupons[prod_num].status eq "A"} selected="selected"{/if}>{$lng.lbl_coupon_active}</option>
	<option value="D"{if $coupons[prod_num].status eq "D"} selected="selected"{/if}>{$lng.lbl_coupon_disabled}</option>
	<option value="U"{if $coupons[prod_num].status eq "U"} selected="selected"{/if}>{$lng.lbl_coupon_used}</option>
	</select>
	</td>
	<td align="center">{if $coupons[prod_num].coupon_type eq "absolute"}{include file="currency.tpl" value=$coupons[prod_num].discount}{elseif  $coupons[prod_num].coupon_type eq "percent"}{$coupons[prod_num].discount|formatprice}%{else}{$lng.lbl_coupon_freeship}{/if}</td>
	<td align="center">{include file="currency.tpl" value=$coupons[prod_num].minimum}</td>
	<td align="center">{if $coupons[prod_num].per_user eq "Y"}{$coupons[prod_num].times}/{$lng.lbl_coupon_per_user}{else}{$coupons[prod_num].times_used}/{$coupons[prod_num].times}{/if}</td>
	<td align="center" nowrap="nowrap">{$coupons[prod_num].expire|date_format:$config.Appearance.datetime_format}</td>
</tr>

<tr{cycle values=", class='TableSubHead'"}>
	<td colspan="7">
{if $coupons[prod_num].productid ne 0}
{$lng.lbl_coupon_contains_product|substitute:"productid":$coupons[prod_num].productid}
{elseif $coupons[prod_num].categoryid ne 0}
{if $active_modules.Simple_Mode}
{if $coupons[prod_num].recursive eq "Y"}
{$lng.lbl_coupon_contains_products_cat_rec_href|substitute:"categoryid":$coupons[prod_num].categoryid:"path":$catalogs.admin}
{else}
{$lng.lbl_coupon_contains_products_cat_href|substitute:"categoryid":$coupons[prod_num].categoryid:"path":$catalogs.admin}
{/if}
{else}
{if $coupons[prod_num].recursive eq "Y"}
{$lng.lbl_coupon_contains_products_cat_rec|substitute:"categoryid":$coupons[prod_num].categoryid}
{else}
{$lng.lbl_coupon_contains_products_cat|substitute:"categoryid":$coupons[prod_num].categoryid}
{/if}
{/if}
{else}
{capture name=minamount}{include file="currency.tpl" value=$coupons[prod_num].minimum}{/capture}
{$lng.lbl_coupon_greater_than|substitute:"amount":$smarty.capture.minamount}
{/if}
{if $coupons[prod_num].coupon_type eq "absolute" and ($coupons[prod_num].productid ne 0 or $coupons[prod_num].categoryid ne 0)}
<br />
{if $coupons[prod_num].productid ne 0}
{if $coupons[prod_num].apply_product_once eq "Y"}
{$lng.lbl_coupon_apply_once}
{else}
{$lng.lbl_coupon_apply_each_item}
{/if}
{elseif $coupons[prod_num].categoryid ne 0}
{if $coupons[prod_num].apply_product_once eq "Y" and $coupons[prod_num].apply_category_once eq "Y"}
{$lng.lbl_coupon_apply_once}
{elseif $coupons[prod_num].apply_product_once eq "N" and $coupons[prod_num].apply_category_once eq "N"}
{$lng.lbl_coupon_apply_each_item_cat}
{else}
{$lng.lbl_coupon_apply_each_title_cat}
{/if}
{/if}
{/if}
	</td>
</tr>

{/section}

<tr>
	<td colspan="7"><br />
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript:document.couponsform.mode.value='delete';document.couponsform.submit();" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	</td>
</tr>


{else}

<tr>
<td colspan="6" align="center"><br />{$lng.txt_no_discount_coupons}</td>
</tr>

{/if}

</table>

{if $coupons ne ""}
</form>
{/if}

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_coupon_add_new}

<form action="coupons.php" method="post" name="coupon_form">
<input type="hidden" name="mode" value="add" />

<table cellpadding="3" cellspacing="1">

<tr>
	<td class="FormButton" width="20%">{$lng.lbl_coupon_code}:</td>
	<td width="10"><font class="Star">*</font></td>
	<td width="100%"><input type="text" size="25" maxlength="16" name="coupon_new" value="{$coupon_data.coupon_new}" />{if $smarty.get.error eq "coupon_already_exists"}<font class="ErrorMessage"> &lt;&lt; {$lng.lbl_coupon_already_exists}</font>{/if}</td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_coupon_type}:</td>
	<td>&nbsp;</td>
	<td>
	<select name="coupon_type_new" onchange="javascript: if (this.value == 'absolute') changeBox('1', true); else changeBox('1', false); if (this.value == 'free_ship') changeBox('4', false); else changeBox('4', true);">
	<option value="percent"{if $coupon_data.coupon_type_new eq "percent"} selected="selected"{/if}>{$lng.lbl_coupon_type_percent}</option>
	<option value="absolute"{if $coupon_data.coupon_type_new eq "absolute"} selected="selected"{/if}>{$config.General.currency_symbol} {$lng.lbl_coupon_type_absolute}</option>
	<option value="free_ship"{if $coupon_data.coupon_type_new eq "free_ship"} selected="selected"{/if}>{$lng.lbl_coupon_freeshiping}</option>
	</select>
	</td>
</tr>

<tr style="{if $coupon_data.coupon_type_new eq "free_ship"}display: none;{/if}" id="box4">
	<td class="FormButton">{$lng.lbl_discount}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="text" size="25" name="discount_new" value="{if $coupon_data.discount_new}{$coupon_data.discount_new|formatprice}{else}{$zero}{/if}" /></td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_coupon_times_to_use}:</td>
	<td>&nbsp;</td>
	<td>
	<table cellpadding="1" cellspacing="1">
	<tr>
		<td><input type="text" size="8" name="times_new" value="{$coupon_data.times_new|default:"1"}" /></td>
		<td>&nbsp;</td>
		<td><input type="checkbox" name="per_user"{if $coupon_data.per_user eq "Y"} checked="checked"{/if} /></td>
		<td>{$lng.lbl_coupon_per_customer}</td>
	</tr>
	</table>
	</td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_status}:</td>
	<td>&nbsp;</td>
	<td>
	<select name="status_new">
		<option value="A"{if $coupon_data.status_new eq "A"} selected="selected"{/if}>{$lng.lbl_coupon_active}</option>
		<option value="D"{if $coupon_data.status_new eq "D"} selected="selected"{/if}>{$lng.lbl_coupon_disabled}</option>
	</select>
</td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_coupon_expires}:</td>
	<td>&nbsp;</td>
	<td>
{math equation="x+1" x=$config.Company.end_year assign="endyear"} 
{html_select_date prefix="new_" time=$coupon_data.expire_new start_year=$config.Company.start_year end_year=$endyear month_format="%b"}
	</td>
</tr>

<tr>
	<td class="FormButton" valign="top">{$lng.lbl_coupon_apply_to}:</td>
	<td>&nbsp;</td>
	<td>

<table cellpadding="1" cellspacing="1">

<tr>
	<td valign="top"><input type="radio" name="apply_to" value="any"{if $coupon_data.apply_to eq "" or $coupon_data.apply_to eq "any"} checked="checked"{/if} onclick="javascript: changeBox('2',false); changeBox('3',false);" /></td>
	<td>{$lng.lbl_coupon_apply_order_subtotal} ({$config.General.currency_symbol})<br />
	<input type="text" size="24" name="minimum_new" value="{$zero}" />
	</td>
</tr>

<tr>
	<td valign="top"><input type="radio" name="apply_to" value="product"{if $coupon_data.apply_to eq "product"} checked="checked"{/if} onclick="javascript: changeBox('2',true); changeBox('3',false);" /></td>
	<td>{$lng.lbl_coupon_apply_product}<br />
	<input type="hidden" name="productid_new" value="{$coupon_data.productid_new}" />
	<input type="text" readonly="readonly" size="25" name="productname" value="{$coupon_data.productname}" />
	<input type="button" onclick="javascript: popup_product('coupon_form.productid_new','coupon_form.productname');" value="{$lng.lbl_browse_|strip_tags:false|escape}" />
	</td>
</tr>

<tr>
	<td valign="top"><input type="radio" name="apply_to" value="category"{if $coupon_data.apply_to eq "category"} checked="checked"{/if} onclick="javascript: changeBox('2',false); changeBox('3',true);" /></td>
	<td>{$lng.lbl_coupon_apply_category}<br />{include file="main/category_selector.tpl" field="categoryid_new"}<br />
	<label for="recursive">{$lng.lbl_coupon_apply_category_rec}</label>
	<input type="checkbox" id="recursive" name="recursive"{if $coupon_data.recursive} checked="checked"{/if} />
	</td>
</tr>

</table>

	</td>
</tr>

<tr{if $coupon_data.apply_to ne "product"} style="display: none;"{/if} id="box2">
	<td colspan="2"></td>
	<td>
<font class="FormButton">{$lng.lbl_coupon_how_to_apply}:</font>
<hr />
<table cellpadding="1" cellspacing="1">
<tr>
	<td><input type="radio" name="how_to_apply_p" value="Y"{if $coupon_data.how_to_apply_p eq "Y" or $coupon_data.how_to_apply_p eq ""} checked="checked"{/if} /></td>
	<td>{$lng.lbl_coupon_apply_once}</td>
</tr>
<tr>
	<td><input type="radio" name="how_to_apply_p" value="N"{if $coupon_data.how_to_apply_p eq "N"} checked="checked"{/if} /></td>
	<td>{$lng.lbl_coupon_apply_each_item}</td>
</tr>
</table>
	</td>
</tr>
<tr{if $coupon_data.apply_to ne "category"} style="display: none;"{/if} id="box3">
	<td colspan="2"></td>
	<td>
<font class="FormButton">{$lng.lbl_coupon_how_to_apply}:</font>
<hr />
<table cellpadding="1" cellspacing="1">
<tr>
	<td><input type="radio" name="how_to_apply_c" value="Y"{if $coupon_data.how_to_apply_c eq "Y" or $coupon_data.how_to_apply_c eq ""} checked="checked"{/if} /></td>
	<td>{$lng.lbl_coupon_apply_once}</td>
</tr>
<tr>
	<td><input type="radio" name="how_to_apply_c" value="N1"{if $coupon_data.how_to_apply_c eq "N1"} checked="checked"{/if} /></td>
	<td>{$lng.lbl_coupon_apply_each_item_cat}</td>
</tr>
<tr>
	<td><input type="radio" name="how_to_apply_c" value="N2"{if $coupon_data.how_to_apply_c eq "N2"} checked="checked"{/if} /></td>
	<td>{$lng.lbl_coupon_apply_each_title_cat}</td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td colspan="3"><br />
{$lng.txt_coupon_note}

<br /><br />

<input type="submit" value="{$lng.lbl_add_coupon|strip_tags:false|escape}" />

	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_store_coupons_title content=$smarty.capture.dialog extra='width="100%"'}
