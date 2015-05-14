{* $Id: subcategories.tpl,v 1.55.2.1 2006/06/27 08:20:37 svowl Exp $ *}
<!--
{* php *}
include_once $xcart_dir."include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->
{* $current_category.category *}
<!-- Start edit by Michael de Leon 09.15.06 -->
{if $active_modules.Bestsellers ne "" and $config.Bestsellers.bestsellers_menu eq "Y"}
<!-- End edit by Michael de Leon 09.15.06 -->
{include file="modules/Bestsellers/bestsellers.tpl"}
{/if}
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
<table cellspacing="0" width="100%" cellpadding="0" border="0" align="center">
{/if}
{ if $subcat_count is div by 3 }
	<tr>
{/if}
		<td align="center" valign="top" width="634">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>
    		<td align="center"><a href="home.php?cat={$c.categoryid}"><img class="wwmp_subcatpics" src="{$xcart_web_dir}/image.php?type=C&amp;id={$c.categoryid}" border="0"></a></td>
  			</tr>
  			<tr>
    		<td align="center"><a href="home.php?cat={$c.categoryid}"><font class="wwmp_vertmenu_black" size="2"><b>{$c.category|escape}</b></font></a></td>
  			</tr>
			<tr>
    		<td class="wwmp_subcat_rowspacing" align="center">{$c.description|escape}&nbsp;</td>
  			</tr>
		</table>
		</td>
{ if $smarty.foreach.second_foreach.last }
	</tr>
</table>
{/if}
{ math equation="subcat_count_tmp + one" assign="subcat_count" subcat_count_tmp=$subcat_count one=1 }
{/foreach}
<!-- End addition by Michael de Leon 09.15.06 -->
{if $tmp and $products ne "" }
<br clear="left" />
<hr size="1" noshade="noshade" />
{/if}
{if $products}
{if $sort_fields}
<div align="center">{include file="main/search_sort_by.tpl" sort_fields=$sort_fields selected=$search_prefilled.sort_field direction=$search_prefilled.sort_direction url="home.php?cat=`$cat`&"}</div>
<hr size="1" width="100%" />
{/if}
{if $total_pages gt 2}
{ include file="customer/main/navigation.tpl" }
<br />
{/if}
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
