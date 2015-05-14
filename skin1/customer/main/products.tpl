{* $Id: products.tpl,v 1.72.2.3 2006/11/27 11:40:25 max Exp $ *}
<!--
{* php *}
include_once $xcart_dir."/home/wwmpon2/public_html/cart/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->

{if $active_modules.Feature_Comparison ne '' && $products && $printable ne 'Y' && $products_has_fclasses}
{include file="modules/Feature_Comparison/compare_selected_button.tpl"}
{include file="modules/Feature_Comparison/products_check_js.tpl"}
{/if}
{if $usertype eq "C" and $config.Appearance.products_per_row ne "" and $config.Appearance.products_per_row gt 0 and $config.Appearance.products_per_row lt 4 and ($featured eq "Y" or $config.Appearance.featured_only_multicolumn eq "N")}
{include file="customer/main/products_t.tpl" products=$products}
{else}
{if $products}
{section name=product loop=$products}
{assign var="discount" value=0}
<!-- Deleted by Michael de Leon 02.05.07
<table width="100%">
-->
<!-- Start addition by Michael de Leon 02.05.07 -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<!-- End addition by Michael de Leon 02.05.07 -->
<tr>
<td class="PListImgBox">
<div class="PListImgBox">
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}{if $featured eq 'Y'}&amp;featured{/if}">{include file="product_thumbnail.tpl" productid=$products[product].productid image_x=$config.Appearance.thumbnail_width product=$products[product].product tmbn_url=$products[product].tmbn_url}</a>
{if $active_modules.Special_Offers ne "" and $products[product].have_offers}
{include file="modules/Special_Offers/customer/product_offer_thumb.tpl" product=$products[product]}
{/if}
</div>
<!-- Deleted by Michael de Leon 02.05.07
<a href="product.php?productid={* $products[product].productid *}&amp;cat={* $cat *}&amp;page={* $navigation_page *}{* if $featured eq 'Y' *}&amp;featured{* /if *}">{* $lng.lbl_see_details *}</a>
-->
<!-- Start addition by Michael de Leon 02.05.07 -->
<a class="wwmp_vertmenulink" href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}{if $featured eq 'Y'}&amp;featured{/if}">{$lng.lbl_see_details}</a>
<!-- End addition by Michael de Leon 02.05.07 -->
{if $active_modules.Feature_Comparison ne '' && $products[product].fclassid > 0 && $printable ne 'Y'}
<br />
<br />
<div align="center">
{include file="modules/Feature_Comparison/compare_checkbox.tpl" id=$products[product].productid}
</div>
{/if}
</td>
<td valign="top">
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}{if $featured eq 'Y'}&amp;featured{/if}"><font class="ProductTitle">{$products[product].product}</font></a>
{if $config.Appearance.display_productcode_in_list eq "Y" and $products[product].productcode ne ""}
<br />
<!-- Deleted by Michael de Leon 02.05.07
{* $lng.lbl_sku *}: {* $products[product].productcode *}
-->
<!-- Start addition by Michael de Leon 02.05.07 -->
<br />
<font class="wwmp_product_labels">{$lng.lbl_sku}:</font> {$products[product].productcode}
<br />
<!-- End addition by Michael de Leon 02.05.07 -->
{/if}
<!-- Start addition by Michael de Leon 02.05.07 -->
{if $active_modules.Extra_Fields ne ""}
	{section name=field loop=$products[product].extra_fields}
		<font class="wwmp_product_labels">{$products[product].extra_fields[field].field}:</font> {$products[product].extra_fields[field].value}
	{/section}
{/if}
<!-- End addition by Michael de Leon 02.05.07 -->
<font size="1">
<br />
<br />
{$products[product].descr|truncate:300:"...":true}
<br />
<!-- Start addition by Michael de Leon 09.15.06 -->
<br />
<!-- End addition by Michael de Leon 09.15.06 -->
</font>
<!-- Deleted by Michael de Leon 02.05.07
<hr class="PListLine" size="1" />
-->
{if $products[product].product_type eq "C"}
{include file="buttons/details.tpl" href="product.php?productid=`$products[product].productid`&amp;cat=`$cat`&amp;page=`$navigation_page`"}
{else}
{if $active_modules.Subscriptions ne "" and ($products[product].catalogprice gt 0 or $products[product].sub_priceplan gt 0)}
{include file="modules/Subscriptions/subscription_info_inlist.tpl"}
{else}
{if $config.General.unlimited_products ne "Y" && ($products[product].avail le 0 or $products[product].avail lt $products[product].min_amount) && $products[product].variantid}
&nbsp;
{elseif $products[product].taxed_price ne 0}
{if $products[product].list_price gt 0 and $products[product].taxed_price lt $products[product].list_price}
{math equation="100-(price/lprice)*100" price=$products[product].taxed_price lprice=$products[product].list_price format="%3.0f" assign=discount}
{if $discount gt 0}
<font class="wwmp_product_labels">{$lng.lbl_market_price}:</font> <font class="wwmp_cart_regular_price"><s>{include file="currency.tpl" value=$products[product].list_price}</s></font>
<br />
<!-- Deleted by Michael de Leon 06.21.07
<font class="MarketPrice">{* $lng.lbl_market_price *}: <s>
{* include file="currency.tpl" value=$products[product].list_price *}
</s></font><br />
-->
{/if}
{/if}
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
<s>
{/if}
<!-- Deleted by Michael de Leon 02.05.07
<font class="ProductPrice">{* $lng.lbl_our_price *}: {* include file="currency.tpl" value=$products[product].taxed_price *}</font><font class="MarketPrice">{* include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price *}</font>{* if $discount gt 0 *}{* if $config.General.alter_currency_symbol ne "" *},{* /if *} {* $lng.lbl_save_price *} {* $discount *}%{* /if *}
-->
<!-- Start addition by anakonda 02.05.07 -->
<!--<font class="wwmp_product_labels">{* $lng.lbl_our_price *}:</font> <font class="wwmp_cart_price">{* include file="currency.tpl" value=$products[product].discount_price *}</font><font class="MarketPrice">{* include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price *}</font>{* if $discount gt 0 *}{* if $config.General.alter_currency_symbol ne "" *},{* /if *} {* $lng.lbl_save_price *} {* $discount *}%{* /if *}-->
{if $products[product].discount_price ne ""}
<font class="wwmp_product_labels">{$lng.lbl_sp_common_price}:</font> <font class="wwmp_cart_regular_price"><s>{include file="currency.tpl" value=$products[product].taxed_price}</s></font>
<br />
<font class="wwmp_product_labels">{$lng.lbl_special} {$lng.lbl_price}:</font> <font class="wwmp_cart_price">{include file="currency.tpl" value=$products[product].discount_price}</font>
{else}
<font class="wwmp_product_labels">{$lng.lbl_our_price}:</font> <font class="wwmp_cart_price">{include file="currency.tpl" value=$products[product].taxed_price}</font><font class="MarketPrice">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price}</font>{if $discount gt 0}{if $config.General.alter_currency_symbol ne ""},{/if} <!-- Start addition by Michael de Leon 06.21.07-->(<!-- End addition by Michael de Leon 06.21.07 -->{$lng.lbl_save_price} <!-- Start addition by Michael de Leon 06.21.07--><font class="wwmp_product_labels"><!-- End addition by Michael de Leon 06.21.07 -->{$discount}%<!-- Start addition by Michael de Leon by Michael de Leon 06.21.07 --></font>)<!-- End addition by Michael de Leon by Michael de Leon 06.21.07 -->{/if}
{/if}
<!-- End addition by anakonda 02.05.07 -->
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
</s>
{/if}
<!-- Deleted by Michael de Leon 06.21.07
{* if $products[product].taxes *}
<br />
<div class="PListTaxBox">{* include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes *}</div>
{* /if *}
-->
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
{include file="modules/Special_Offers/customer/product_special_price.tpl" product=$products[product]}
{/if}
{else}
<font class="ProductPrice">{$lng.lbl_enter_your_price}</font>
{/if}
{/if}
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file="customer/main/buy_now.tpl" product=$products[product]}
{/if}
{/if}
<!-- Start addition by Michael de Leon 02.05.07 -->
<br />
<br />
<hr class="PListLine" size="1" />
<!-- End addition by Michael de Leon 02.05.07 -->
	</td>
</tr>
</table>
<!-- Deleted by Michael de Leon 02.05.07
<br />
<br />
<br />
-->
{/section}
{if $active_modules.Feature_Comparison ne '' && $products && $printable ne 'Y' && $products_has_fclasses}
{include file="modules/Feature_Comparison/compare_selected_button.tpl" no_form=true}
{/if}
{else}
{$lng.txt_no_products_found}
{/if}
{/if}
