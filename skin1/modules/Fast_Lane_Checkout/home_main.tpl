{if $checkout_step eq 0}
{include file="modules/Fast_Lane_Checkout/checkout_0_enter.tpl"}

{elseif $checkout_step eq 1}
<!-- Start addition by Michael de Leon 11.09.06 -->
<div align="center">
<table width="634" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="130" height="18" background="{$ImagesDir}/wwmp_statusyourcart_visited.jpg" align="center">&nbsp;</td>
    <td width="160" height="18" background="{$ImagesDir}/wwmp_statusprofiledetails_visited.jpg" align="center"><img src="{$ImagesDir}/wwmp_cart_checkoutstatus11.13.06.gif" width="17" height="18"></td>
    <td width="190" height="18" background="{$ImagesDir}/wwmp_statusshippingpayment_notvisited.jpg" align="center">&nbsp;</td>
    <td width="154" height="18" background="{$ImagesDir}/wwmp_statusplaceorder_notvisited.jpg" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><a class="wwmp_checkout_label" href="cart.php" target="_self">{$lng.lbl_your_cart}</a></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_my_account}</font></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_shipping_and_payment}</font></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_place_order}</font></td>
  </tr>
</table>
</div>
<br /><br />
<!-- End addition by Michael de Leon 11.09.06 -->
{include file="modules/Fast_Lane_Checkout/checkout_1_profile.tpl"}

{elseif $checkout_step eq 2}
<!-- Start addition by Michael de Leon 11.09.06 -->
<div align="center">
<table width="634" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="130" height="18" background="{$ImagesDir}/wwmp_statusyourcart_visited.jpg" align="center">&nbsp;</td>
    <td width="160" height="18" background="{$ImagesDir}/wwmp_statusprofiledetails_visited.jpg" align="center">&nbsp;</td>
    <td width="190" height="18" background="{$ImagesDir}/wwmp_statusshippingpayment_visited.jpg" align="center"><img src="{$ImagesDir}/wwmp_cart_checkoutstatus11.13.06.gif" width="17" height="18"></td>
    <td width="154" height="18" background="{$ImagesDir}/wwmp_statusplaceorder_notvisited.jpg" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><a class="wwmp_checkout_label" href="cart.php" target="_self">{$lng.lbl_your_cart}</a></td>
    <td align="center"><a class="wwmp_checkout_label" href="register.php?mode=update&action=cart&paymentid=" target="_self">{$lng.lbl_my_account}</a></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_shipping_and_payment}</font></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_place_order}</font></td>
  </tr>
</table>
</div>
<br /><br />
<!-- End addition by Michael de Leon 11.09.06 -->
{include file="modules/Fast_Lane_Checkout/checkout_2_method.tpl"}

{elseif $checkout_step eq 3}
<!-- Start addition by Michael de Leon 11.09.06 -->
<div align="center">
<table width="634" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="130" height="18" background="{$ImagesDir}/wwmp_statusyourcart_visited.jpg" align="center">&nbsp;</td>
    <td width="160" height="18" background="{$ImagesDir}/wwmp_statusprofiledetails_visited.jpg" align="center">&nbsp;</td>
    <td width="190" height="18" background="{$ImagesDir}/wwmp_statusshippingpayment_visited.jpg" align="center">&nbsp;</td>
    <td width="154" height="18" background="{$ImagesDir}/wwmp_statusplaceorder_visited.jpg" align="center"><img src="{$ImagesDir}/wwmp_cart_checkoutstatus11.13.06.gif" width="17" height="18"></td>
  </tr>
  <tr>
    <td align="center"><a class="wwmp_checkout_label" href="cart.php" target="_self">{$lng.lbl_your_cart}</a></td>
    <td align="center"><a class="wwmp_checkout_label" href="register.php?mode=update&action=cart&paymentid=" target="_self">{$lng.lbl_my_account}</a></td>
    <td align="center"><a class="wwmp_checkout_label" href="cart.php?mode=checkout" target="_self">{$lng.lbl_shipping_and_payment}</a></td>
    <td align="center"><font class="wwmp_checkout_label">{$lng.lbl_place_order}</font></td>
  </tr>
</table>
</div>
<br /><br />
<!-- End addition by Michael de Leon 11.09.06 -->
{include file="modules/Fast_Lane_Checkout/checkout_3_place.tpl"}

{else}
<table cellpadding="5" cellspacing="0" width="634" align="center">
	<tr>
		<td align="left"><img src="{$ImagesDir}/wwmp_shoppingcart_title11.02.06.jpg"></td>
		<td align="right">
			<table cellpadding="5" cellspacing="0">
			<tr>
				<td><a href="cart.php?mode=clear_cart"><img src="{$ImagesDir}/wwmp_emptycartbtn11.02.06.jpg" border="0"></a></td>
				<td><a href="home.php"><img src="{$ImagesDir}/wwmp_continueshoppingbtn11.02.06.jpg" border="0"></a></td>
<!-- Deleted by Michael de Leon 11.02.06
<td><img src="{* $ImagesDir *}/spacer.gif" width="10" height="1" alt="" /></td>
-->
				<td>{if $js_enabled}
						<a href="cart.php?mode=checkout"><img src="{$ImagesDir}/wwmp_checkoutbtn11.02.06.jpg" border="0"></a>
					{else}
						<input type="hidden" name="mode" value="checkout" />
						{include file="submit_wo_js.tpl" value=$lng.lbl_checkout}
					{/if}</td>
			</tr>
			</table>
		</td>
	</tr>
</table><br />
{include file="customer/main/cart.tpl"}
<br />
<table cellpadding="5" cellspacing="0" width="634" align="center">
	<tr>
		<td align="right">
			<table cellpadding="5" cellspacing="0">
			<tr>
				<td><a href="cart.php?mode=clear_cart"><img src="{$ImagesDir}/wwmp_emptycartbtn11.02.06.jpg" border="0"></a></td>
				<td><a href="home.php"><img src="{$ImagesDir}/wwmp_continueshoppingbtn11.02.06.jpg" border="0"></a></td>
<!-- Deleted by Michael de Leon 11.02.06
<td><img src="{* $ImagesDir *}/spacer.gif" width="10" height="1" alt="" /></td>
-->
				<td>{if $js_enabled}
						<a href="cart.php?mode=checkout"><img src="{$ImagesDir}/wwmp_checkoutbtn11.02.06.jpg" border="0"></a>
					{else}
						<input type="hidden" name="mode" value="checkout" />
						{include file="submit_wo_js.tpl" value=$lng.lbl_checkout}
					{/if}</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
{/if}
