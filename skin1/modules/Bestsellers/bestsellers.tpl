{* $Id: bestsellers.tpl,v 1.8 2005/11/21 12:42:00 max Exp $ *}
{if $bestsellers}
{capture name=bestsellers}
<table cellpadding="0" cellspacing="2">
{foreach from=$bestsellers item=bestseller}
<tr>
{if $config.Bestsellers.bestsellers_thumbnails eq "Y"}
	<td width="30">
	<a href="product.php?productid={$bestseller.productid}&cat={$cat}&bestseller">{include file="product_thumbnail.tpl" productid=$bestseller.productid image_x=25 product=$bestseller.product}</a>
	</td>
{/if}
	<td>
	<b><a href="product.php?productid={$bestseller.productid}&amp;cat={$cat}&amp;bestseller">{$bestseller.product}</a></b><br />
{$lng.lbl_our_price}: {include file="currency.tpl" value=$bestseller.price}<br />
	</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra='width="100%"'}
{/if}
