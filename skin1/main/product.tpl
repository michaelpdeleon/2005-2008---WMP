{* $Id: product.tpl,v 1.46.2.1 2006/06/16 10:47:41 max Exp $ *}
{capture name=dialog}
<table width="100%">
<tr>
	<td valign="top" align="left" width="30%">
{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product}
<p />
{if $smarty.get.mode ne "printable"}
<a href="product.php?productid={$product.productid}&amp;mode=printable" target="_blank"><img src="{$ImagesDir}/print.gif" width="23" height="22" name="print" alt="{$lng.lbl_printable_version|escape}" /></a>
{/if}
	</td>
	<td valign="top">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>{$lng.lbl_sku}</td>
	<td>{$product.productcode}</td>
</tr>
<tr>
	<td>{$lng.lbl_category}</td>
	<td>{$product.category_text}</td>
</tr>
{if $usertype eq "A"}
<tr>
	<td>{$lng.lbl_vendor}</td>
	<td>{$product.provider}</td>
</tr>
{/if}
<tr>
	<td>{$lng.lbl_availability}</td>
	<td>{if $product.forsale eq "Y"}{$lng.lbl_avail_for_sale}{elseif $product.forsale eq "B"}{$lng.lbl_pconf_avail_for_sale_bundled}{elseif $product.forsale eq "H"}{$lng.lbl_hidden}{else}{$lng.lbl_disabled}{/if}</td>
</tr>
<tr>
	<td colspan="2">
<br />
<br />
<span class="Text">{$product.descr}</span>
<br />
<br />
	</td>
</tr>
<tr>
	<td colspan="2"><b><font class="ProductDetailsTitle">{$lng.lbl_price_info}</font></b></td>
</tr>
<tr>
	<td class="Line" height="1" colspan="2"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_price}</td>
	<td nowrap="nowrap"><font class="ProductPriceSmall">{include file="currency.tpl" value=$product.price}</font></td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_in_stock}</td>
	<td nowrap="nowrap">{$lng.lbl_items_available|substitute:"items":$product.avail}</td>
</tr>
<tr>
	<td width="50%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
</table>
<br />

<table cellspacing="0" cellpadding="0">
<tr>
	<td>{include file="buttons/modify.tpl" href="product_modify.php?productid=`$product.productid`"}</td>
	<td>&nbsp;&nbsp;</td>
	<td>{include file="buttons/clone.tpl" href="process_product.php?mode=clone&productid=`$product.productid`"}</td>
	<td>&nbsp;&nbsp;</td>
	<td>{include file="buttons/delete.tpl" href="process_product.php?mode=delete&productid=`$product.productid`"}</td>
</tr>
</table>

	</td>
</tr>
</table>
{/capture}
{if $smarty.get.mode eq "printable"}
{include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra="width=440"}
{else}
{include file="dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog extra='width="100%"'}
{/if}
