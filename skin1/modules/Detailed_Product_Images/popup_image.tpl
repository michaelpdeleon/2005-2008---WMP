{* $Id: popup_image.tpl,v 1.2.2.1 2006/06/16 10:47:43 max Exp $ *}
<table cellspacing="0" cellpadding="0">
<tr>
	<td align="center">
<a href="javascript: void(0);" onclick="javascript: popup_image('D', '{$product.productid}', '{$max_x}', '{$max_y}', '{$product.product|escape:"url"}');">{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.tmbn_url id="product_thumbnail" type="P"}</a>
	</td>
</tr>
<tr>
	<td align="center">

<table cellspacing="0" cellpadding="0">
<tr>
	<td>
<a href="javascript: void(0);" onclick="javascript: popup_image('D', '{$product.productid}', '{$max_x}', '{$max_y}', '{$product.product|escape:"url"}');"><img src="{$ImagesDir}/zoom_image.gif" alt="{$lng.lbl_click_to_enlarge|escape}" /></a>
	</td>
	<td>&nbsp;</td>
	<td width="100%" nowrap="nowrap" align="left">
<a href="javascript: void(0);" style="text-decoration: underline;" onclick="javascript: popup_image('D', '{$product.productid}', '{$max_x}', '{$max_y}', '{$product.product|escape:"url"}');">{$lng.lbl_click_to_enlarge}</a>
	</td>
</tr>
</table>

	</td>
</tr>
</table>
