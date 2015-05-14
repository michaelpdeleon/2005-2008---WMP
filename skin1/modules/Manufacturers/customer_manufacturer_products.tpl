{* $Id: customer_manufacturer_products.tpl,v 1.12 2005/12/29 08:43:52 max Exp $ *}
{if $manufacturer.is_image eq 'Y' || $manufacturer.descr ne '' || $manufacturer.url ne ''}
<table cellpadding="0" cellspacing="0" border="0">
{if $manufacturer.is_image eq 'Y'}
<tr>
	<td valign="top" class="wwmp_manufacturer_banner">{if $manufacturer.url ne ''}<a href="{$manufacturer.url}">{/if}<img src="{if $manufacturer.image_path ne ''}{$manufacturer.image_path}{else}{$xcart_web_dir}/image.php?id={$manufacturer.manufacturerid}&amp;type=M{/if}" alt="{$manufacturer.manufacturer|escape}" />{if $manufacturer.url ne ''}</a>{/if}</td>
</tr>
{/if}
{if $manufacturer.url ne ''}
<tr>
	<td class="wwmp_manufacturer_link"><b>{$lng.lbl_url}:</b> <a class="wwmp_vertmenulink" href="{$manufacturer.url}">{$manufacturer.url}</a></td>
</tr>
{/if}
<tr>
	<td valign="top">{$manufacturer.descr}</td>
</tr>
</table>
<br /><br />
{/if}
<!-- Deleted by Michael de Leon 12.20.06
{* capture name=dialog *}
-->
{if $products ne ''}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
{if $sort_fields}
<tr>
	<td align="center">{include file="main/search_sort_by.tpl" url="manufacturers.php?manufacturerid=`$manufacturer.manufacturerid`&page=`$navigation_page`&" sort_fields=$sort_fields selected=$sort direction=$sort_direction}
	<hr size="1" width="100%" /></td>
</tr>
{/if}
<tr>
<td>

{ include file="customer/main/navigation.tpl" }<br />
{include file="customer/main/products.tpl" products=$products}

</td>
</tr>
</table>
{else}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align="center">{$lng.txt_no_products_in_man}</td>
</tr>
</table>
{/if}
<br />
<!-- Deleted by Michael de Leon 12.20.06
{* /capture *}
{* include file="dialog.tpl" title=$manufacturer.manufacturer content=$smarty.capture.dialog extra='width="100%"' *}
-->
{ include file="customer/main/navigation.tpl" }

