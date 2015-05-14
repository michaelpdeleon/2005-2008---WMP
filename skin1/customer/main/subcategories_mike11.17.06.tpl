{* $Id: subcategories.tpl,v 1.55.2.1 2006/06/27 08:20:37 svowl Exp $ *}
<!--
{*php*}
include_once $xcart_dir."include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{*/php*}
-->
<!-- Start edit by Michael de Leon 09.15.06 -->
{if $active_modules.Bestsellers ne "" and $config.Bestsellers.bestsellers_menu eq "Y"}
<!-- End edit by Michael de Leon 09.15.06 -->
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
<!-- Start addition by Michael de Leon 09.15.06 -->
{assign var="subcat_count" value="0"}
{foreach from=$subcategories item=c key=catid name=second_foreach}
{ if $smarty.foreach.second_foreach.first }
<table cellspacing="5" width="100%" cellpadding="0" border="0" align="center">
{/if}
{ if $subcat_count is div by 3 }
	<tr>
{/if}
		<td align="center" valign="top" width="600">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>
    		<td align="center"><a href="home.php?cat={$c.categoryid}"><img class="wwmp_subcatpics" src="{$xcart_web_dir}/image.php?type=C&amp;id={$c.categoryid}" border="0"></a></td>
  			</tr>
  			<tr>
    		<td align="center"><a href="home.php?cat={$c.categoryid}"><font size="2"><b>{$c.category|escape}</b></font></a></td>
  			</tr>
			<tr>
    		<td align="center">{$c.description|escape}</td>
  			</tr>
		</table>
<!-- Deleted by Michael de Leon 11.10.06
{* if $location[1].1|replace:"home.php?cat=":"" eq  436 *}
{* Food Science *}
<a href="home.php?cat={* $c.categoryid *}" class="Food_IndustryLink"><img src="{* $xcart_web_dir *}/icon.php?categoryid={* $c.categoryid *}" border="0"><br /><br /><font size="2"><b>{* $c.category|escape *}</b></font></a><br />
{* $c.description|escape *}<br /><br /><br />
{* elseif $location[1].1|replace:"home.php?cat=":"" eq  342 *}
{* General Practice *}
<a href="home.php?cat={* $c.categoryid *}" class="General_PracticeLink"><img src="{* $xcart_web_dir *}/icon.php?categoryid={* $c.categoryid *}" border="0"><br /><br /><font size="2"><b>{* $c.category|escape *}</b></font></a><br />
{* $c.description|escape *}<br /><br /><br />
{* else *}
<a href="home.php?cat={* $c.categoryid *}"><img src="{* $xcart_web_dir *}/icon.php?categoryid={* $c.categoryid *}" border="0"><br /><br /><font size="2"><b>{* $c.category|escape *}</b></font></a><br />
{* $c.description|escape *}<br /><br /><br />
{* /if *}
-->
		</td>
{ if $smarty.foreach.second_foreach.last }
	</tr>
</table>
{/if}
{ math equation="subcat_count_tmp + one" assign="subcat_count" subcat_count_tmp=$subcat_count one=1 }
{/foreach}
<!-- End addition by Michael de Leon 09.15.06 -->
<!-- Deleted by Michael de Leon 09.15.06
{* if $subcategories *}
<table cellspacing="5" width="100%">
{* foreach from=$subcategories item=subcat *}
<tr>
{* if $tmp and $first_subcat ne "Y" *}
	<td valign="top" rowspan="{count value=$subcategories print="Y"}"><img src="{* if $current_category.icon_url *}{* $current_category.icon_url *}{* else *}{* $xcart_web_dir *}/image.php?id={* $cat *}&amp;type=C{* /if *}" alt="" /></td>
{* assign var="first_subcat" value="Y" *}
{* /if *}
	<td class="SubcatTitle"><a href="home.php?cat={ $subcat.categoryid }"><font class="ItemsList">{ $subcat.category|escape }</font></a><br /></td>
	<td class="SubcatInfo">{* if $config.Appearance.count_products eq "Y" *}
{* if $subcat.product_count *}{* $subcat.product_count *} {* $lng.lbl_products *}
{*elseif $subcat.subcategory_count *}{* $subcat.subcategory_count *} {* $lng.lbl_categories|lower *}
{* /if *}
	{* /if *}</td>
</tr>
{* /foreach *}
</table>
{* /if *}
-->
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
<!-- Start addition by Michael de Leon 09.15.06 -->
{include file="dialog_maincategorylist.tpl" title=$current_category.category content=$smarty.capture.dialog extra="width=100%"}
<!-- End addition by Michael de Leon 09.15.06 -->
{if $products eq ""}
{if $f_products ne ""}
<p />
{include file="customer/main/featured.tpl"}
{/if}
{/if}
{ include file="customer/main/navigation.tpl" }
