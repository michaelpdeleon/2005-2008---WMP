{* $Id: product_links.tpl,v 1.22 2006/03/21 07:17:16 svowl Exp $ *}
{if $product}
{assign var="product_title" value=$product.product|truncate:30:"...":false}
{assign var="page_title" value="`$lng.lbl_product_links`<br /><span class='ProductTitle'>`$product_title`</span>"}
{/if}

{include file="page_title.tpl" title=$page_title}

{$lng.txt_product_links_top_text}

<br /><br />

{capture name=dialog}

<form action="" onsubmit="javascript:alert('{$lng.txt_this_form_is_for_demo_purposes|strip_tags}'); return false;">

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td valign="top" align="left" rowspan="2" width="100">
{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.tmbn_url id="product_thumbnail"}&nbsp;
	</td>
	<td valign="top">
{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_details}

<table cellpadding="0" cellspacing="0" width="100%">

{if $config.Appearance.show_in_stock eq "Y" and $config.General.unlimited_products ne "Y" and $product.distribution eq ""}
<tr>
	<td width="30%">{$lng.lbl_in_stock}</td>
	<td nowrap="nowrap"><span id="product_avail_txt">{if $product.avail gt 0}{$lng.txt_items_available|substitute:"items":$product.avail}{else}{$lng.lbl_no_items_available}{/if}</span></td>
</tr>
{/if}

{if $product.weight ne "0.00"}
<tr>
	<td width="30%"><span id="product_weight">{$lng.lbl_weight}</span></td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
{/if}

{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/product.tpl"}
{/if}
{if $active_modules.Subscriptions ne "" and $subscription}
{include file="modules/Subscriptions/subscription_info.tpl"}
{else}
<tr>
	<td class="ProductPriceConverting">{$lng.lbl_price}:</td>
<td>
{if $product.taxed_price ne 0}
<font class="ProductDetailsTitle"><span id="product_price">{include file="currency.tpl" value=$product.taxed_price}</span></font><font class="MarketPrice"> <span id="product_alt_price">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$product.taxed_price}</span></font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
	</td>
</tr>
{/if}

</table>

<br /><br />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_options}</font></b></td>
</tr>

<tr>
	<td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc"  alt="" /></td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

{if $active_modules.Product_Options ne ""}
{ include file="modules/Product_Options/customer_options.tpl"}
{/if}

<tr>
	<td height="25" width="30%">
	{$lng.lbl_quantity}{if $product.min_amount gt 1}<br /><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font>{/if}
	</td>
	<td>
{if $config.General.unlimited_products eq "N" and ($product.avail le 0 or $product.avail lt $product.min_amount)}
<b>{$lng.txt_out_of_stock}</b>
{else}
{if $config.General.unlimited_products eq "Y"}
{math equation="x+1" assign="mq" x=$config.Appearance.max_select_quantity}
{else}
{math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
{if $tmp<2}
{assign var="minamount" value=$product.min_amount}
{else}
{assign var="minamount" value=0}
{/if}
{math equation="min(maxquantity+minamount, productquantity)+1" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$product.min_amount productquantity=$product.avail}
{/if}
{if $product.distribution eq ""}
{if $product.min_amount le 1}
{assign var="start_quantity" value=1}
{else}
{assign var="start_quantity" value=$product.min_amount}
{/if}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var min_avail = {$start_quantity|default:1};
var avail = {$mq|default:1}-1;
var product_avail = {$product.avail|default:"0"};
-->
</script>
<select name="amount" id="product_avail">
{section name=quantity loop=$mq start=$start_quantity}
	<option value="{%quantity.index%}" {if $smarty.get.quantity eq %quantity.index%}selected{/if}>{%quantity.index%}</option>
{/section}
</select>
{else}
<font class="ProductDetailsTitle">1</font><input type="hidden" name="amount" value="1" /> {$lng.txt_product_downloadable}
{/if}
{/if}
	</td>
</tr>

<tr>
	<td colspan="2">
{include file="customer/main/product_prices.tpl"}
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
{if $js_enabled}
{assign var="alert_warning" value=$lng.txt_this_form_is_for_demo_purposes|strip_tags}
{include file="buttons/add_to_cart.tpl" href="javascript:alert('`$alert_warning`')"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_add_to_cart}
{/if}
{/if}
<br /><input type="image" src="{$ImagesDir}/null.gif" width="10" height="10" border="0" valign="top" /><br />
	</td>
</tr>

</table>

	</td>
	<td align="right" valign="top" width="60">
{if $product.list_price gt 0}
<table width="60">
<tr>
	<td class="SaveMoneyLabel">
<br />
{math equation="100-(price/lprice)*100" price=$product.price lprice=$product.list_price format="%d" assign=discount}
&nbsp;<span id="save_percent">{ $discount }</span>%
	</td>
</tr>
</table>
{/if}
	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra='width="100%"'}

{*** THUMBNAIL LINK ***}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td valign="top" width="20%">
{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product full_url="y"}
	</td>
	<td>&nbsp;</td>
	<td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product full_url="y"}</textarea>
	</td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_link_thumbnail content=$smarty.capture.dialog extra='width="100%"'} 

{*** Simple HTML link to add 1 product to cart ***}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="20%">
{include file="buttons/add_to_cart.tpl" href="`$http_customer_locaton`/cart.php?mode=add&productid=`$product.productid`&amount=1"}
	</td>
	<td>&nbsp;</td>
	<td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">
{include file="buttons/add_to_cart.tpl" href="`$http_customer_locaton`/cart.php?mode=add&productid=`$product.productid`&amount=1"}
</textarea>
	</td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_add_1_product_link content=$smarty.capture.dialog extra='width="100%"'} 

{*** Full functionallity 'Add to cart' button ***}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="20%">
{capture name=add2cart}

<form name="orderform_{$product.productid}" method="post" action="{$http_customer_locaton}/cart.php">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="productid" value="{$product.productid}" />

<table width="100%">
<tr>
	<td valign="top">
<br />
<table width="100%" cellpadding="0" cellspacing="0">
{if $active_modules.Subscriptions ne "" and $subscription}
{include file="modules/Subscriptions/subscription_info.tpl"}
{else}
<tr>
	<td class="ProductPriceConverting" valign="top">{$lng.lbl_price}:</td>
	<td valign="top">
{if $product.taxed_price ne 0 || $variants ne ''}
<font class="ProductDetailsTitle">{include file="currency.tpl" value=$product.taxed_price}</font><font class="MarketPrice"> {include file="customer/main/alter_currency_value.tpl" alter_currency_value=$product.taxed_price}</font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
	</td>
</tr>
{/if}

{if $active_modules.Product_Options ne ""}
{ include file="modules/Product_Options/customer_options.tpl" nojs="Y"}
{/if}
<tr>
	<td height="25" width="30%">
	{$lng.lbl_quantity}{if $product.min_amount gt 1}<br /><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font>{/if}
	</td>
	<td>
{if $config.General.unlimited_products eq "N" and ($product.avail le 0 or $product.avail lt $product.min_amount)}
<b>{$lng.txt_out_of_stock}</b>
{else}
{if $config.General.unlimited_products eq "Y"}
{math equation="x+1" assign="mq" x=$config.Appearance.max_select_quantity}
{else}
{math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
{if $tmp<2}
{assign var="minamount" value=$product.min_amount}
{else}
{assign var="minamount" value=0}
{/if}
{math equation="min(maxquantity+minamount, productquantity)+1" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$product.min_amount productquantity=$product.avail}
{/if}
{if $product.distribution eq ""}
{if $product.min_amount le 1}
{assign var="start_quantity" value=1}
{else}
{assign var="start_quantity" value=$product.min_amount}
{/if}
<select name="amount">
{section name=quantity loop=$mq start=$start_quantity}
	<option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
{/section}
</select>
{else}
<font class="ProductDetailsTitle">1</font><input type="hidden" name="amount" value="1" /> {$lng.txt_product_downloadable}
{/if}
{/if}
	</td>
</tr>
<tr>
	<td colspan="2">
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
<br />{include file="buttons/add_to_cart.tpl" href="javascript: document.orderform_`$product.productid`.submit();" js_to_href="Y"}
{/if}
	</td>
</tr>
</table>
	</td>
</tr>
</table>
</form>

{/capture}

{$smarty.capture.add2cart} 

	</td>
</tr>

<tr>
	<td>
<br />
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="75" rows="10">{$smarty.capture.add2cart}</textarea>
	</td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_advanced_product_link content=$smarty.capture.dialog extra='width="100%"'} 

