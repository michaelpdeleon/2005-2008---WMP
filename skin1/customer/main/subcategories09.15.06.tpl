{* $Id: subcategories.tpl,v 1.55.2.1 2006/06/27 08:20:37 svowl Exp $ *}
{if $active_modules.Bestsellers ne "" and $config.Bestsellers.bestsellers_menu ne "Y"}
<p />
{include file="modules/Bestsellers/bestsellers.tpl"}
{/if}
<p />
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/customer/category_offers_short_list.tpl"}
{/if}
{if ($navigation_page eq "")||($navigation_page eq "1")}{$current_category.description}<p />{/if}
{capture name=dialog}
{assign var="tmp" value="0"}
{foreach from=$subcategories item=c key=catid}
{if $c.category}{assign var="tmp" value="1"}{/if}
{/foreach}
{if $subcategories}
<table cellspacing="5" width="100%">
{foreach from=$subcategories item=subcat}
<tr>
{if $tmp and $first_subcat ne "Y"}
	<td valign="top" rowspan="{count value=$subcategories print="Y"}"><img src="{if $current_category.icon_url}{$current_category.icon_url}{else}{$xcart_web_dir}/image.php?id={$cat}&amp;type=C{/if}" alt="" /></td>
{assign var="first_subcat" value="Y"}
{/if}
	<td class="SubcatTitle"><a href="home.php?cat={ $subcat.categoryid }"><font class="ItemsList">{ $subcat.category|escape }</font></a><br /></td>
	<td class="SubcatInfo">{if $config.Appearance.count_products eq "Y"}
{if $subcat.product_count}{ $subcat.product_count } {$lng.lbl_products}
{elseif $subcat.subcategory_count}{ $subcat.subcategory_count } {$lng.lbl_categories|lower}
{/if}
	{/if}</td>
</tr>
{/foreach}
</table>
{/if}
{if $tmp and $products ne "" }
<br clear="left" />
<hr size="1" noshade="noshade" />
{/if}
{if $products}
{if $sort_fields}
<div align="right">{include file="main/search_sort_by.tpl" sort_fields=$sort_fields selected=$search_prefilled.sort_field direction=$search_prefilled.sort_direction url="home.php?cat=`$cat`&"}</div>
{/if}
{if $total_pages gt 2}
<br />
{ include file="customer/main/navigation.tpl" }
{/if}
<hr size="1" width="100%" />
{include file="customer/main/products.tpl" products=$products}
{/if}
{if $products eq "" and $tmp eq "0"}
{$lng.txt_no_products_in_cat}
{/if}
{/capture}
{include file="dialog.tpl" title=$current_category.category content=$smarty.capture.dialog extra='width="100%"'}
{if $products eq ""}
{if $f_products ne ""}
<p />
{include file="customer/main/featured.tpl"}
{/if}
{/if}
{ include file="customer/main/navigation.tpl" }
