{* $Id: minicart.tpl,v 1.17 2006/03/28 08:21:07 max Exp $ *}
<table cellpadding="1" cellspacing="0">
{if $minicart_total_items > 0}
<tr>
	<!-- Deleted by Michael de Leon 11.01.06
	<td rowspan="2" width="23"><a href="cart.php"><img src="{* $ImagesDir *}/cart_full.gif" width="19" height="16" alt="" /></a>	</td>
	-->
	<!-- Start addition by Michael de Leon 11.01.06 -->
	<td rowspan="3" valign="top"><a href="cart.php"><img class="wwmp_cartpic" src="{$ImagesDir}/wwmp_cartfull11.01.06.jpg" /></a></td>
	<!-- End addition by Michael de Leon 11.01.06 -->
	<td class="wwmp_vertmenu_black"><strong>{$lng.lbl_cart_items}:</strong> </td>
	<td class="wwmp_vertmenu_black">{$minicart_total_items}</td>
</tr>
<tr>
	<td class="wwmp_vertmenu_black"><strong>{$lng.lbl_total}:</strong> </td>
	<td class="wwmp_vertmenu_black">{include file="currency.tpl" value=$minicart_total_cost}</td>
</tr>
<tr>
	<td><img class="wwmp_minicartsection" src="{$ImagesDir}/spacer.gif"></td>
</tr>
{else}
<tr>
	<!-- Deleted by Michael de Leon 11.01.06
	<td rowspan="2" width="23"><img src="{* $ImagesDir *}/cart_empty.gif" width="19" height="16" alt="" /></td>
	-->
	<!-- Start addition by Michael de Leon 11.01.06 -->
	<td rowspan="3" valign="top"><img class="wwmp_cartpic" src="{$ImagesDir}/wwmp_cartempty11.01.06.jpg" /></td>
	<!-- End addition by Michael de Leon 11.01.06 -->
	<td class="wwmp_vertmenu_black" align="center"><b>{$lng.lbl_cart_is_empty}</b></td>
</tr>
<!-- Deleted by Michael de Leon 11.01.06
<tr>
	<td class="VertMenuItems">&nbsp;</td>
</tr>
-->
<!-- Start addition by Michael de Leon 11.01.06 -->
<tr>
	<td><img class="wwmp_minicartsection" src="{$ImagesDir}/spacer.gif"></td>
</tr>
<!-- End addition by Michael de Leon 11.01.06 -->
{/if}
</table>
<!-- Deleted by Michael de Leon 11.01.06
<hr class="VertMenuHr" size="1" />
-->
