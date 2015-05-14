{* $Id: related_products.tpl,v 1.15 2005/12/07 14:07:32 max Exp $ *}
{if $product_links ne ""}
{capture name=dialog}
<table cellspacing="5">
{section name=cat_num loop=$product_links}
<tr class="ItemsList">
	<td width="1%">#{$product_links[cat_num].productid}</td>
	<td width="99%"><a href="product.php?productid={ $product_links[cat_num].productid }"{if $config.Upselling_Products.upselling_new_window eq 'Y'} target="_blank"{/if} class="ItemsList">{$product_links[cat_num].product}</a></td>
</tr>
{/section}
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_related_products content=$smarty.capture.dialog extra='width="100%"'}
{/if}
