{* $Id: product.tpl,v 1.147.2.8 2006/08/02 05:39:12 max Exp $ *}
<!--
{* php *}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->
{include file="main/include_js.tpl" src="main/popup_image.js"}
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/product_offers_short_list.tpl" product=$product}
{/if}
{include file="form_validation_js.tpl"}
{if $product.product_type eq "C" && $active_modules.Product_Configurator}
{include file="modules/Product_Configurator/pconf_customer_product.tpl"}
{else}
{capture name=dialog}
<form name="orderform" method="post" action="cart.php?mode=add" onsubmit="javascript: return FormValidation();">
<table width="100%">
<tr>
	<td class="PImgBox" rowspan="2">

{if $active_modules.Detailed_Product_Images ne "" && $config.Detailed_Product_Images.det_image_popup eq 'Y' && $images ne '' && $js_enabled eq 'Y'}
{include file="modules/Detailed_Product_Images/popup_image.tpl"}
{else}
{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.tmbn_url id="product_thumbnail" type="P"}&nbsp;
{/if}
{if $active_modules.Magnifier ne "" && $config.Magnifier.magnifier_image_popup eq 'Y' && $zoomer_images ne '' && $js_enabled eq 'Y'}
{include file="modules/Magnifier/popup_magnifier.tpl"}
{/if}
	</td>
<td valign="top" width="100%">
<span>{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}</span>
<p />
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_details}</font></b></td></tr>
<tr><td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td width="30%">{$lng.lbl_sku}</td>
	<td nowrap="nowrap" id="product_code">{$product.productcode}</td>
</tr>
{if $config.Appearance.show_in_stock eq "Y" and $config.General.unlimited_products ne "Y" and $product.distribution eq ""}
<tr>
	<td width="30%">{$lng.lbl_in_stock}</td>
	<td nowrap="nowrap" id="product_avail_txt">
{if $product.avail gt 0}{$lng.txt_items_available|substitute:"items":$product.avail}{else}{$lng.lbl_no_items_available}{/if}
	</td>
</tr>
{/if}
{if $product.weight ne "0.00" || $variants ne ''}
<tr id="product_weight_box">
	<td width="30%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap"><span id="product_weight">{$product.weight|formatprice}</span> {$config.General.weight_symbol}</td>
</tr>
{/if}
{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/product.tpl"}
{/if}
{if $active_modules.Feature_Comparison ne ""}
{include file="modules/Feature_Comparison/product.tpl"}
{/if}
{if $active_modules.Subscriptions ne "" and $subscription}
{include file="modules/Subscriptions/subscription_info.tpl"}
{else}
<tr><td class="ProductPriceConverting" valign="top">{$lng.lbl_price}:</td>
<td valign="top">
{if $product.taxed_price ne 0 || $variant_price_no_empty}
<font class="ProductDetailsTitle"><span id="product_price">{include file="currency.tpl" value=$product.taxed_price}</span></font><font class="MarketPrice"> <span id="product_alt_price">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$product.taxed_price}</span></font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
</td>
</tr>
{/if}
</table>
<p />
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td colspan="2">
<b><font class="ProductDetailsTitle">{$lng.lbl_options}</font></b>
</td></tr>
<tr><td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
{if $active_modules.Product_Options ne ""}
{ include file="modules/Product_Options/customer_options.tpl"}
{/if}
<tr><td height="25" width="30%">{$lng.lbl_quantity}{if $product.min_amount gt 1}<br /><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font>{/if}</td>
<td>
{if $config.General.unlimited_products eq "N" and ($product.avail le 0 or $product.avail lt $product.min_amount) and $variants eq ''}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var min_avail = 1;
var avail = 0;
var product_avail = 0;
-->
</script>
<b>{$lng.txt_out_of_stock}</b>
{else}
{if $config.General.unlimited_products eq "Y"}
{assign var="mq" value=$config.Appearance.max_select_quantity}
{else}
{math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
{if $tmp<2}
{assign var="minamount" value=$product.min_amount}
{else}
{assign var="minamount" value=1}
{/if}
{math equation="min(maxquantity+minamount, productquantity+1)" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$minamount productquantity=$product.avail}
{/if}
{if $product.distribution eq "" and !($active_modules.Subscriptions ne "" and $subscription)}
{if $product.min_amount le 1}
{assign var="start_quantity" value=1}
{else}
{assign var="start_quantity" value=$product.min_amount}
{/if}
{if $config.General.unlimited_products eq "Y"}
{math equation="x+y" assign="mq" x=$mq y=$start_quantity}
{/if}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var min_avail = {$start_quantity|default:1};
var avail = {$mq|default:1}-1;
var product_avail = {$product.avail|default:"0"};
-->
</script>
<select id="product_avail" name="amount"{if $active_modules.Product_Options ne '' && $product_options ne ''} onchange="check_wholesale(this.value);"{/if}>
{section name=quantity loop=$mq start=$start_quantity}
<option value="{%quantity.index%}" {if $smarty.get.quantity eq %quantity.index%}selected{/if}>{%quantity.index%}</option>
{/section}
</select>
{else}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var min_avail = 1;
var avail = 1;
var product_avail = 1;
-->
</script>
<font class="ProductDetailsTitle">1</font><input type="hidden" name="amount" value="1" /> {if $product.distribution ne ""}{$lng.txt_product_downloadable}{/if}
{/if}
{/if}
</td></tr>
<tr><td colspan="2">
<input type="hidden" name="mode" value="add" />
{include file="customer/main/product_prices.tpl"}
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
{if $js_enabled}
<br />
{if $product.forsale ne "B"}
<table cellspacing="0" cellpadding="0">
<tr>
	<td>{include file="buttons/add_to_cart.tpl" style="button" href="javascript: if(FormValidation()) document.orderform.submit();"}</td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
{if ($login ne "" || $config.Wishlist.add2wl_unlogged_user eq 'Y') && $active_modules.Wishlist ne ""}
{include file="customer/add2wl.tpl"}
{/if}
	</td>
</tr>
</table>
{else}
{$lng.txt_pconf_product_is_bundled}
{/if}
{if $smarty.get.pconf ne ""}
<br /><br />
<input type="hidden" name="slot" value="{$smarty.get.slot}" />
<input type="hidden" name="addproductid" value="{$product.productid}" />
{include file="buttons/button.tpl" button_title=$lng.lbl_pconf_add_to_configuration style="button" href="javascript:if (FormValidation()) `$ldelim`document.orderform.productid.value='`$smarty.get.pconf`';document.orderform.action='pconf.php';document.orderform.submit()`$rdelim`"}
<br />
{$lng.txt_add_to_configuration_note}
<br />
{/if}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_add_to_cart}
{/if}
{/if}
{if $active_modules.Feature_Comparison ne ""}
{include file="modules/Feature_Comparison/product_buttons.tpl"}
{/if}
<br /><br />
</td>
</tr></table>
</td>
<td align="right" valign="top" width="60" id="save_percent_box"{if $product.taxed_price >= $product.list_price} style="display: none;"{/if}>
{if $product.taxed_price gt 0 and $product.list_price gt 0}
<table width="60" cellspacing="1" cellpadding="2">
<tr>
	<td class="SaveMoneyLabel">
<br />
{math equation="100-(price/lprice)*100" price=$product.taxed_price lprice=$product.list_price format="%3.0f" assign=discount}
&nbsp;<span id="save_percent">{ $discount }</span>%
	</td>
</tr>
</table>
{/if}
</td>
</tr>
</table>
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
</form>
{/capture}
<!-- Start addition by Michael de Leon 09.18.06 -->
{if $location[1].1|replace:"home.php?cat=":"" eq  436}
{* Food Science *}
{include file="dialog_food_science.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  342}
{* General Practice *}
{include file="dialog_generalpractice.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  259}
{* Gloves *}
{include file="dialog_gloves.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  378}
{* Infection Control *}
{include file="dialog_infection_control.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  384}
{* Laboratory Consumables *}
{include file="dialog_clinical.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  273}
{* Shipping Materials *}
{include file="dialog_shipping_materials.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  478}
{* Janitorial Supplies *}
{include file="dialog_janitorial_supplies.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  505}
{* BioExcell *}
{include file="dialog_bioexcell.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{else}
{include file="dialog.tpl" title=$product.product content=$smarty.capture.dialog extra="width=100%"}
{/if}
<!-- End addition by Michael de Leon 09.18.06 -->
<!-- Deleted by Michael de Leon 09.18.06
{* include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra='width="100%"' *}
-->
{/if}
{if $active_modules.Magnifier ne "" && ($config.Magnifier.magnifier_image_popup ne 'Y' || $js_enabled ne 'Y')}
<p />
{include file="modules/Magnifier/product_magnifier.tpl" productid=$product.productid}
{/if}
{if $config.Appearance.send_to_friend_enabled eq 'Y'}
<p />
{include file="customer/main/send_to_friend.tpl" }
{/if}
{if $active_modules.Detailed_Product_Images ne "" && ($config.Detailed_Product_Images.det_image_popup ne 'Y' || $js_enabled ne 'Y')}
<p />
{include file="modules/Detailed_Product_Images/product_images.tpl" }
{/if}
{if $active_modules.Upselling_Products ne ""}
<p />
{include file="modules/Upselling_Products/related_products.tpl" }
{/if}
{if $active_modules.Recommended_Products ne ""}
<p />
{include file="modules/Recommended_Products/recommends.tpl" }
{/if}
{if $active_modules.Customer_Reviews ne ""}
<p />
{include file="modules/Customer_Reviews/vote_reviews.tpl" }
{/if}
{if $active_modules.Product_Options ne '' && $product_options ne ''}
<script type="text/javascript" language="JavaScript 1.2">
<!--
check_options();
-->
</script>
{/if}
