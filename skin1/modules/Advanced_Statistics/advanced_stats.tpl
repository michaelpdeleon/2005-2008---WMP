{* $Id: advanced_stats.tpl,v 1.25 2005/11/21 12:41:59 max Exp $ *}

{$lng.txt_category_statistics_top_note}

<br /><br />

{capture name=dialog}

{if $nav_bar}
<a class="NavigationPath" href="statistics.php?mode=shop">{$lng.lbl_root_level}</a>
{else}
<font class="NavigationPath">{$lng.lbl_root_level}</font>
{/if}

{section name=index loop=$nav_bar}
&nbsp;::&nbsp;
<a class="NavigationPath" href="{$nav_bar[index].1}">{$nav_bar[index].0}</a>
{/section}

<table width="100%" cellspacing="2" cellpadding="0">
<tr>
<td colspan="4" align="right"><b>{$lng.lbl_statistics_for_dates}: <i><font color="#000099">{$start_date|date_format:$config.Appearance.datetime_format} - {$end_date|date_format:$config.Appearance.datetime_format}</font></i></b>
</td>
</tr>

<tr>
<td colspan="4" bgcolor="#CCCCCC"><img src="{$ImagesDir}/null.gif" class="Spc" alt="" /><br /></td>
</tr>

{if $category_viewes}

{section name=index loop=$category_viewes}
<tr>
	<td valign="top" width="90%" class="Text"><a href="statistics.php?mode=shop&cat={$category_viewes[index].categoryid}">{$category_viewes[index].category_path}</a></td>
	<td valign="top" class="Text" align="right">{$category_viewes[index].views_stats}</td>
	<td width="5"></td>
	<td width="110">
	<table width="100" cellspacing="0" cellpadding="0">
	<tr>
	<td class="StatisticsBar" width="{math equation="floor(a/b*100)" a=$category_viewes[index].views_stats b=$max_category_viewes}">{if ($category_viewes[index].views_stats ne 0)}&nbsp;{/if}</td>
	<td width="{math equation="100-floor(a/b*100)" a=$category_viewes[index].views_stats b=$max_category_viewes}">&nbsp;</td>
	</tr>
	</table>	
	</td>
</tr>
{/section}

{else}

<tr>
	<td align="center"><br />{$lng.lbl_no_categories_viewed}</td>
</tr>

{/if}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_category_views content=$smarty.capture.dialog extra='width="100%"'}

<br />

{if ($product_viewes != "")}

{capture name=dialog}

<table width="100%" cellspacing="2" cellpadding="0">

<tr>
	<td colspan="5" align="right"><b>{$lng.lbl_statistics_for_dates}: <i><font color="#000099">{$start_date|date_format:$config.Appearance.datetime_format} - {$end_date|date_format:$config.Appearance.datetime_format}</font></i></b></td>
</tr>

<tr>
	<td colspan="5" bgcolor="#CCCCCC"><img src="{$ImagesDir}/null.gif" class="Spc" alt="" /><br /></td>
</tr>

{section name=index loop=$product_viewes}
<tr>
	<td valign="top" class="Text">#{$product_viewes[index].productid}&nbsp;&nbsp;</td>
	<td valign="top" width="90%" class="Text"><a href="product.php?productid={$product_viewes[index].productid}" target=_blank>{$product_viewes[index].product}</a></td>
	<td valign="top" class="Text" align="right">{$product_viewes[index].views_stats}</td>
	<td width="5"></td>
	<td width="110">
	<table width="100" cellspacing="0" cellpadding="0">
	<tr>
	<td class="StatisticsBar" width="{math equation="floor(a/b*100)" a=$product_viewes[index].views_stats b=$max_product_viewes}">{if ($product_viewes[index].views_stats ne 0)}&nbsp;{/if}</td>
	<td width="{math equation="100-floor(a/b*100)" a=$product_viewes[index].views_stats b=$max_product_viewes}">&nbsp;</td>
	</tr>
	</table>	
	</td>
</tr>

{/section}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_products_views content=$smarty.capture.dialog extra='width="100%"'}

<br />

{/if}

{if ($product_sales != "")}

{capture name=dialog}

<table width="100%" cellspacing="2" cellpadding="0">

<tr>
	<td colspan="5" align="right"><b>{$lng.lbl_statistics_for_dates}: <i><font color="#000099">{$start_date|date_format:$config.Appearance.datetime_format} - {$end_date|date_format:$config.Appearance.datetime_format}</font></i></b></td>
</tr>

<tr>
	<td colspan="5" bgcolor="#CCCCCC"><img src="{$ImagesDir}/null.gif" class="Spc" alt="" /><br /></td>
</tr>

{section name=index loop=$product_sales}
<tr>
	<td valign="top" class="Text">#{$product_sales[index].productid}&nbsp;&nbsp;</td>
	<td valign="top" width="90%" class="Text"><a href="product.php?productid={$product_sales[index].productid}" target=_blank>{$product_sales[index].product}</a></td>
	<td valign="top" class="Text" align="right">{$product_sales[index].sales_stats}</td>
	<td width="5"></td>
	<td width="110">
	<table width="100" cellspacing="0" cellpadding="0">
	<tr>
	<td class="StatisticsBar" width="{math equation="floor(a/b*100)" a=$product_sales[index].sales_stats b=$max_product_sales}">{if ($product_sales[index].sales_stats ne 0)}&nbsp;{/if}</td>
	<td width="{math equation="100-floor(a/b*100)" a=$product_sales[index].sales_stats b=$max_product_sales}">&nbsp;</td>
	</tr>
	</table>	
	</td>
</tr>
{/section}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_sales content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{if ($product_deleted != "")}

<br />

{capture name=dialog}

<table width="100%" cellspacing="2" cellpadding="0">

<tr>
	<td colspan="5" align="right"><b>{$lng.lbl_statistics_for_dates}: <i><font color="#000099">{$start_date|date_format:$config.Appearance.datetime_format} - {$end_date|date_format:$config.Appearance.datetime_format}</font></i></b></td>
</tr>

<tr>
	<td colspan="5" bgcolor="#CCCCCC"><img src="{$ImagesDir}/null.gif" width="1" height="1" alt="" /><br /></td>
</tr>

{section name=index loop=$product_deleted}
<tr>
	<td valign="top" class="Text">#{$product_deleted[index].productid}&nbsp;&nbsp;</td>
	<td valign="top" width="90%" class="Text"><a href="product.php?productid={$product_deleted[index].productid}" target=_blank>{$product_deleted[index].product}</a></td>
	<td valign="top" width="67" class="Text" align="right">{$product_deleted[index].del_stats}</td>
	<td width="5"></td>
	<td width="110">
	<table width="100" cellspacing="0" cellpadding="0">
	<tr>
	<td class="StatisticsBar" width="{math equation="floor(a/b*100)" a=$product_deleted[index].del_stats b=$max_product_deleted}">{if ($product_deleted[index].del_stats ne 0)}&nbsp;{/if}</td>
	<td width="{math equation="100-floor(a/b*100)" a=$product_deleted[index].del_stats b=$max_product_deleted}">&nbsp;</td>
	</tr>
	</table>	
	</td>
</tr> 
{/section}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_deleted_from_cart content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{if $referers_array}

<br />

{capture name=dialog}

{include file="customer/main/navigation.tpl"}

<table width="100%" cellspacing="2" cellpadding="0">

{section name=index loop=$referers_array}
<tr>
	<td valign="top" width="90%" class="Text"><a HREF="{$referers_array[index].referer}">{$referers_array[index].referer|truncate:50:"..."}</a></td>
	<td valign="top" width="67" class="Text"><div valign="bottom" align="right">{$referers_array[index].visits}</div></td>
	<td width="5"></td>
	<td width="110">
	<table width="100" cellspacing="0" cellpadding="0">
	<tr>
	<td class="StatisticsBar" width="{math equation="floor(a/b*100)" a=$referers_array[index].visits b=$max_visits}">{if ($referers_array[index].visits ne 0)}&nbsp;{/if}</td>
	<td width="{math equation="100-floor(a/b*100)" a=$referers_array[index].visits b=$max_visits}">&nbsp;</td>
	</tr>
	</table>	
	</td>
</tr> 
{/section}

</table>
{include file="customer/main/navigation.tpl"}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_referred_from content=$smarty.capture.dialog extra='width="100%"'}

{/if}
