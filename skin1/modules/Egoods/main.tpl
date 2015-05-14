{* $Id: main.tpl,v 1.9 2005/12/15 12:59:13 max Exp $ *}
<p />
{capture name=dialog}
<p />
{if $product}
<table width="100%">
<tr>
	<td colspan="2" class="ProductTitle"><a href="product.php?productid={$product.productid}">#{$product.productid}. {$product.product}</a></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td valign="top" align="left" rowspan="2" width="120">
<a href="product.php?productid={$product.productid}">{include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x image_y=$product.image_y product=$product.product}</a>&nbsp;
	</td>
	<td valign="top">

<span>{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}</span>
	<p />

{include file="main/subheader.tpl" title=$lng.lbl_details}

<table width="100%" cellpadding="0" cellspacing="0">
{if $product.weight ne "0.00"}
<tr>
	<td width="30%">{$lng.lbl_weight}</td>
	<td nowrap="nowrap">{$product.weight} {$config.General.weight_symbol}</td>
</tr>
{/if}
{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/product.tpl"}
{/if}
</table>

	</td>
</tr>
<tr>
	<td>
<br /><br />
{$lng.lbl_download_msg}
<br /><br />
{assign var="title_length" value=""}
{if $product.length > 0}
{assign var="title_length" value=$lng.lbl_file_size|cat:": `$product.length` `$lng.lbl_byte`"}
{/if}
{include file="buttons/button.tpl" button_title=$lng.lbl_download href=$url title=$title_length}<br />
{$title_length}
	</td>
</tr>
</table>
{else}
{$lng.lbl_download_errmsg}
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_download content=$smarty.capture.dialog extra='width="100%"'}
