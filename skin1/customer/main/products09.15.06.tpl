{* $Id: products.tpl,v 1.72.2.2 2006/08/11 12:18:08 max Exp $ *}
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
<table width="100%">
<tr>
<td class="PListImgBox">
<div class="PListImgBox">
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}{if $featured eq 'Y'}&amp;featured{/if}">{include file="product_thumbnail.tpl" productid=$products[product].productid image_x=$config.Appearance.thumbnail_width product=$products[product].product tmbn_url=$products[product].tmbn_url}</a>
{if $active_modules.Special_Offers ne "" and $products[product].have_offers}
{include file="modules/Special_Offers/customer/product_offer_thumb.tpl" product=$products[product]}
{/if}
</div>
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}{if $featured eq 'Y'}&amp;featured{/if}">{$lng.lbl_see_details}</a>
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
{$lng.lbl_sku}: {$products[product].productcode}
{/if}
<font size="1">
<br />
<br />
{$products[product].descr|truncate:300:"...":true}
<br />
</font>
<hr class="PListLine" size="1" />
{if $products[product].product_type eq "C"}
{include file="buttons/details.tpl" href="product.php?productid=`$products[product].productid`&amp;cat=`$cat`&amp;page=`$navigation_page`"}
{else}
{if $active_modules.Subscriptions ne "" and ($products[product].catalogprice gt 0 or $products[product].sub_priceplan gt 0)}
{include file="modules/Subscriptions/subscription_info_inlist.tpl"}
{else}
{if $products[product].taxed_price ne 0}
{if $products[product].list_price gt 0 and $products[product].taxed_price lt $products[product].list_price}
{math equation="100-(price/lprice)*100" price=$products[product].taxed_price lprice=$products[product].list_price format="%3.0f" assign=discount}
{if $discount gt 0}
<font class="MarketPrice">{$lng.lbl_market_price}: <s>
{include file="currency.tpl" value=$products[product].list_price}
</s></font><br />
{/if}
{/if}
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
<s>
{/if}
<font class="ProductPrice">{$lng.lbl_our_price}: {include file="currency.tpl" value=$products[product].taxed_price}</font><font class="MarketPrice">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price}</font>{if $discount gt 0}{if $config.General.alter_currency_symbol ne ""},{/if} {$lng.lbl_save_price} {$discount}%{/if}
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
</s>
{/if}
{if $products[product].taxes}
<br />
<div class="PListTaxBox">{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}</div>
{/if}
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
	</td>
</tr>
</table>
<br />
<br />
<br />
{/section}
{if $active_modules.Feature_Comparison ne '' && $products && $printable ne 'Y' && $products_has_fclasses}
{include file="modules/Feature_Comparison/compare_selected_button.tpl" no_form=true}
{/if}
{else}
{$lng.txt_no_products_found}
{/if}
{/if}
