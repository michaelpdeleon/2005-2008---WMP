{* $Id: products_t.tpl,v 1.30.2.2 2006/08/11 12:18:08 max Exp $ *}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>

<table width="100%" cellpadding="5" cellspacing="1">

{math equation="floor(100/x)" x=$config.Appearance.products_per_row assign="width"}

{section name=product loop=$products}
{assign var="discount" value=0}

{if %product.index% is div by $config.Appearance.products_per_row}
<tr>
{assign var="cell_counter" value=0}
{/if}

{math equation="x+1" x=$cell_counter assign="cell_counter" }

	<td width="{$width}%" class="PListCell">

<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}" class="ProductTitle">{$products[product].product}</a><br />
{if $config.Appearance.display_productcode_in_list eq "Y" and $products[product].productcode ne ""}
{$lng.lbl_sku}: {$products[product].productcode}<br />
{/if}
<table cellpadding="3" cellspacing="0" width="100%">
<tr>
	<td height="100" nowrap="nowrap">
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}">{include file="product_thumbnail.tpl" productid=$products[product].productid image_x=$config.Appearance.thumbnail_width product=$products[product].product tmbn_url=$products[product].tmbn_url}</a>
{if $active_modules.Special_Offers ne "" and $products[product].have_offers}
{include file="modules/Special_Offers/customer/product_offer_thumb.tpl" product=$products[product]}
{/if}
	</td>
</tr>
</table>
<a href="product.php?productid={$products[product].productid}&amp;cat={$cat}&amp;page={$navigation_page}">{$lng.lbl_see_details}</a>
{if $products[product].product_type ne "C"}
<br />
<br />
{if $active_modules.Subscriptions ne "" and $products[product].catalogprice}
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
<font class="ProductPrice">{$lng.lbl_our_price}: {include file="currency.tpl" value=$products[product].taxed_price}</font><br /><font class="MarketPrice">{include file="customer/main/alter_currency_value.tpl" alter_currency_value=$products[product].taxed_price}</font>{if $discount gt 0}{if $config.General.alter_currency_symbol ne ""},{/if} {$lng.lbl_save_price} {$discount}%{/if}
{if $products[product].taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$products[product].taxes}{/if}
{if $active_modules.Special_Offers ne "" and $products[product].use_special_price ne ""}
{include file="modules/Special_Offers/customer/product_special_price.tpl" product=$products[product]}
{/if}
{else}
<font class="ProductPrice">{$lng.lbl_enter_your_price}</font>
{/if}
{/if}
{if $active_modules.Feature_Comparison ne '' && $products[product].fclassid > 0}
<div align="center" style="width: 100%; padding-top: 10px;">
{include file="modules/Feature_Comparison/compare_checkbox.tpl" id=$products[product].productid}
</div>
{/if}
{*** Uncomment it if you need 'Buy Now' button ***
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file="customer/main/buy_now.tpl" product=$products[product]}
{/if}
*** Uncomment it if you need 'Buy Now' button ***}
{/if}
	</td>

{capture name=prod_index}
{math equation="index+x+1" index=%product.index% x=$config.Appearance.products_per_row}
{/capture}
{if $smarty.capture.prod_index is div by $config.Appearance.products_per_row }
</tr>
{/if}

{/section}

{if $cell_counter lt $config.Appearance.products_per_row}
{section name=rest_cells loop=$config.Appearance.products_per_row start=$cell_counter}
	<td class="SectionBox">&nbsp;</td>
{/section}
</tr>
{/if}

</table>
	</td>
</tr>
</table>
{if $active_modules.Feature_Comparison ne '' && $products && $printable ne 'Y' && $products_has_fclasses}
{include file="modules/Feature_Comparison/compare_selected_button.tpl"}
{/if}

