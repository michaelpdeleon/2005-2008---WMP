{* $Id: checkout_2_method.tpl,v 1.9.2.1 2006/08/08 08:29:46 max Exp $ *}
<!-- Deleted by Michael de Leon 11.08.06
<h3>{* $lng.lbl_shipping_and_payment *}</h3>
-->

<script type="text/javascript">
<!--
{literal}
function display_cod(flag) {
	for (var i = 0; i < paymentsCOD.length; i++) {
		if (!paymentsCOD[i] || !document.getElementById('cod_tr'+paymentsCOD[i]))
			continue;

		document.getElementById('cod_tr'+paymentsCOD[i]).style.display = flag ? "" : "none";
	}

	return true;
}
{/literal}
-->
</script>
<div align="center">
{capture name=dialog}

{if $smarty.get.err eq 'gc_not_enough_money'}
<div style="text-align: center;">
<font class="ErrorMessage">{$lng.txt_gc_not_enough_money}</font>
</div>
<br />
{/if}
<form action="cart.php" method="post" name="cartform">

<input type="hidden" name="mode" value="checkout" />
<input type="hidden" name="cart_operation" value="cart_operation" />
<input type="hidden" name="action" value="update" />


{if $config.Shipping.disable_shipping ne "Y"}
{include file="modules/Fast_Lane_Checkout/shipping_methods.tpl"}

<br /><br />
{/if}

<table cellpadding="5" cellspacing="5" width="100%">

<tr>
<td valign="top" width="30%" align="left">
<font class="wwmp_shippingpayment_label">{$lng.lbl_billing_address}</font>
<br /><br />
<!-- Deleted by Michael de Leon 11.08.06
{include file="customer/main/subheader.tpl" title=$lng.lbl_billing_address}
-->
{if $userinfo} 
<!-- Start of edit by Michael de Leon 11.08.06 -->
{$userinfo.b_address}<br /> 
{$userinfo.b_city}, {$userinfo.b_statename}<br />
{$userinfo.b_zipcode}<br />
{$userinfo.b_countryname}<br />
<!-- End of edit by Michael de Leon 11.08.06 -->
{else} 
No data 
{/if} 
 
{if $login ne ""}
<br />
<a href="register.php?mode=update&amp;action=cart"><img src="{$ImagesDir}/wwmp_editbtn11.07.06.jpg" border="0"></a>
<!-- Delete by Michael de Leon 11.07.06
{* include file="buttons/modify.tpl" href="register.php?mode=update&amp;action=cart" *}
-->
{/if}

</td>
<td valign="top" width="70%" align="left">
<font class="wwmp_shippingpayment_label">{$lng.lbl_payment_method}</font>
<br /><br />
<!-- Deleted by Michael de Leon 11.08.06
{* include file="customer/main/subheader.tpl" title=$lng.lbl_payment_method *}
-->

<table cellspacing="0" cellpadding="2" width="100%">
{foreach from=$payment_methods item=payment}
<tr {cycle values=' class="TableSubHead", '}{if $payment.is_cod eq "Y"} id="cod_tr{$payment.paymentid}"{/if}>
<td width="1"><input type="radio" name="paymentid" id="pm{$payment.paymentid}" value="{$payment.paymentid}"{if $payment.is_default eq "1"} checked="checked"{/if} /></td>
{if $payment.processor eq "ps_paypal_pro.php"}
<td colspan="2">
<table cellpadding="0" cellspacing="0"><tr>
	<td>{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="logo"}</td>
	<td>&nbsp;&nbsp;</td>
	<td><label for="pm{$payment.paymentid}">{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="text"}</label></td>
</tr>
</table>
</td>
{else}
<td width="20%" nowrap="nowrap" style="padding-right: 15px;"><label for="pm{$payment.paymentid}"><b>{$payment.payment_method}</b></label></td>
<td width="80%">{$payment.payment_details}</td>
{/if}
</tr>
{/foreach}
</table>

</td>
</tr>
</table>
<div align="center">
{if $js_enabled}
<a href="javascript: document.cartform.submit()"><img src="{$ImagesDir}/wwmp_continuebtnsmall11.08.06.jpg" border="0"></a>
<!-- Deleted by Michael de Leon 11.08.06
{* include file="buttons/continue.tpl" style="button" href="javascript: document.cartform.submit()" *}
-->
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_continue}
{/if}
</div>
</form>

<script type="text/javascript">
<!--
var paymentsCOD = [{strip}
{foreach from=$payment_methods item=payment}
{if $payment.is_cod eq "Y"}
{$payment.paymentid},
{/if}
{/foreach}
0
{/strip}];
display_cod({if $display_cod eq 'Y'}true{else}false{/if});
-->
</script>

{/capture}
{include file="dialog_shoppingcart.tpl" title=$lng.lbl_shipping_and_payment content=$smarty.capture.dialog extra='width="634"'}
</div>