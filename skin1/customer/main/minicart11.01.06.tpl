{* $Id: minicart.tpl,v 1.17 2006/03/28 08:21:07 max Exp $ *}
<table cellpadding="1" cellspacing="0">
{if $minicart_total_items > 0}
<tr>
	<td rowspan="2" width="23"><a href="cart.php"><img src="{$ImagesDir}/cart_full.gif" width="19" height="16" alt="" /></a></td>
	<td class="VertMenuItems"><b>{$lng.lbl_cart_items}: </b></td>
	<td class="VertMenuItems">{$minicart_total_items}</td>
</tr>
<tr>
	<td class="VertMenuItems"><b>{$lng.lbl_total}: </b></td>
	<td class="VertMenuItems">{include file="currency.tpl" value=$minicart_total_cost}
</td>
</tr>
{else}
<tr>
	<td rowspan="2" width="23"><img src="{$ImagesDir}/cart_empty.gif" width="19" height="16" alt="" /></td>
	<td class="VertMenuItems" align="center"><b>{$lng.lbl_cart_is_empty}</b></td>
</tr>
<tr>
	<td class="VertMenuItems">&nbsp;</td>
</tr>
{/if}
</table>
<hr class="VertMenuHr" size="1" />
