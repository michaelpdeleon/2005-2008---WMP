{* $Id: customer_manufacturer_products.tpl,v 1.12 2005/12/29 08:43:52 max Exp $ *}
{if $manufacturer.is_image eq 'Y' || $manufacturer.descr ne '' || $manufacturer.url ne ''}
<table>
<tr>
{if $manufacturer.is_image eq 'Y'}
	<td valign="top">{if $manufacturer.url ne ''}<a href="{$manufacturer.url}">{/if}<img src="{if $manufacturer.image_path ne ''}{$manufacturer.image_path}{else}{$xcart_web_dir}/image.php?id={$manufacturer.manufacturerid}&amp;type=M{/if}" alt="{$manufacturer.manufacturer|escape}" />{if $manufacturer.url ne ''}</a>{/if}</td>
{elseif $manufacturer.url ne ''}
	<td>{$lng.lbl_url}: <a href="{$manufacturer.url}">{$manufacturer.url}</a></td>
</tr>
<tr>
{/if}
	<td valign="top">{$manufacturer.descr}</td>
</tr>
</table>
<br />
{/if}
{capture name=dialog}
{if $products ne ''}
{if $sort_fields}
<div align="right">{include file="main/search_sort_by.tpl" url="manufacturers.php?manufacturerid=`$manufacturer.manufacturerid`&page=`$navigation_page`&" sort_fields=$sort_fields selected=$sort direction=$sort_direction}</div>
<br />
{/if}
{ include file="customer/main/navigation.tpl" }
{include file="customer/main/products.tpl" products=$products}
{else}
{$lng.txt_no_products_in_man}
{/if}
{/capture}
{include file="dialog.tpl" title=$manufacturer.manufacturer content=$smarty.capture.dialog extra='width="100%"'}
{ include file="customer/main/navigation.tpl" }

