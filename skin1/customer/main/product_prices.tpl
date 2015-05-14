{* $Id: product_prices.tpl,v 1.16 2006/03/21 07:17:17 svowl Exp $ *}
{if !$no_span}<div id="wl_table">{/if}
{if $product_wholesale ne ""}
<br />
<table cellpadding="2" cellspacing="2">
<tr class="TableHead">
	<td align="right"><b>{$lng.lbl_quantity}:&nbsp;</b></td>
{section name=wi loop=$product_wholesale}
	<td>
{$product_wholesale[wi].quantity}{if $smarty.section.wi.last}+{elseif $product_wholesale[wi].quantity ne $product_wholesale[wi].next_quantity}-{$product_wholesale[wi].next_quantity}{/if}&nbsp;{if $product_wholesale[wi].quantity eq "1"}{$lng.lbl_item}{else}{$lng.lbl_items}{/if}
&nbsp;
	</td>
{/section}
</tr>
{if $product.taxes}
{capture name=taxdata}
{include file="customer/main/taxed_price.tpl" taxes=$product.taxes display_info="N"}
{/capture}
{/if}
<tr bgcolor="#EEEEEE">
{section name=wi loop=$product_wholesale}
{if $smarty.section.wi.first}<td align="right"><b>{$lng.lbl_price_per_item}{if $smarty.capture.taxdata}*{/if}:&nbsp;</b></td>{/if}
<td>{if !$no_span}<span id="wp{%wi.index%}">{/if}{include file="currency.tpl" value=$product_wholesale[wi].taxed_price}{if !$no_span}</span>{/if}</td>
{/section}
</tr>
</table>
{if $smarty.capture.taxdata}
<br />
<table border="0">
<tr>
<td class="FormButton" valign="top"><b>*{$lng.txt_note}:</b>&nbsp;</td>
<td nowrap="nowrap" valign="top">{$smarty.capture.taxdata}</td>
</tr>
</table>
{/if}
<br />
{/if}
{if !$no_span}</div>{/if}
