{* $Id: checkout.tpl,v 1.70.2.1 2006/07/29 07:27:43 max Exp $ *}
{capture name=checkout_dialog}

<form action="cart.php" method="post" name="cartform">

<input type="hidden" name="cart_operation" value="cart_operation" />

{if $config.Appearance.show_cart_details eq "Y" or ($config.Appearance.show_cart_details eq "L" and $smarty.get.paymentid ne "" and $smarty.get.mode eq "checkout")} 
{include file="customer/main/cart_details.tpl"}
{else}
{include file="customer/main/cart_contents.tpl"}
{/if}

<hr noshade="noshade" size="1" /><br />
{include file="customer/main/cart_totals.tpl"}
<br /><br />

{if $js_enabled}
{include file="buttons/update.tpl" href="javascript: document.cartform.submit()" js_to_href="Y"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_update}
{/if}

</form>

{/capture}
{assign var="_dlg_title" value=$lng.lbl_checkout_step_x_of_y|substitute:"X":$checkout_step:"Y":$total_checkout_steps}
{include file="dialog.tpl" title=$_dlg_title content=$smarty.capture.checkout_dialog extra='width="100%"'}
<p />
{if $smarty.get.mode eq "auth"}
{include file="main/error_login_incorrect.tpl"}
{/if}
{if $payment_data.payment_method ne ""}
<h5>{$lng.lbl_payment_method}: {$payment_data.payment_method}</h5>
{capture name=dialog}
<form action="{$payment_data.payment_script_url}" method="post" name="checkout_form">
<input type="hidden" name="paymentid" value="{$payment_data.paymentid}" />
<input type="hidden" name="action" value="place_order" />
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>

<pre>
{include file="customer/main/customer_details.tpl"}
</pre>

{if $paypal_express_active}
&nbsp;&nbsp;&nbsp;&nbsp;
{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="return"}
{else}
{include file="buttons/modify.tpl" href="register.php?mode=update&action=cart&paymentid=`$smarty.get.paymentid`"}
{/if}

<p />

{if $ignore_payment_method_selection eq ""}
<div align="right">
{include file="buttons/button.tpl" button_title=$lng.lbl_change_payment_method href="cart.php?mode=checkout"}
</div>
{/if}

<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
<script type="text/javascript">
<!--
requiredFields = new Array();
-->
</script>

{include file="check_required_fields_js.tpl"}

{if $payment_data.payment_template ne ""}
{include file=$payment_data.payment_template}
{/if}

{if $payment_cc_data.cmpi eq 'Y' && $config.CMPI.cmpi_enabled eq 'Y'}
{include file="main/cmpi.tpl"}
{/if}

{include file="customer/main/checkout_notes.tpl"}

<br />
<input type="hidden" name="payment_method" value="{$payment_data.payment_method_orig}" />

<center>
{$lng.txt_terms_and_conditions_note}
</center>

<br /><br />

<center>
{if $js_enabled}
{assign var="button_href" value="javascript: "}
{if $config.General.check_cc_number eq "Y" and ($payment_cc_data.type eq "C" or $payment_data.paymentid eq 1 or  ($payment_data.processor_file eq "ps_paypal_pro.php" and $payment_cc_data.paymentid ne $payment_data.paymentid)) and $payment_cc_data.disable_ccinfo ne "Y"}
{assign var="button_href" value=$button_href|cat:"if(checkCCNumber(document.checkout_form.card_number,document.checkout_form.card_type) && checkExpirationDate(document.checkout_form.card_expire_Month,document.checkout_form.card_expire_Year)"}
{if $payment_cc_data.disable_ccinfo ne "C"}
{assign var="button_href" value=$button_href|cat:" && checkCVV2(document.checkout_form.card_cvv2,document.checkout_form.card_type)"}
{/if}
{assign var="button_href" value=$button_href|cat:")"}
{/if}

<script type="text/javascript">
<!--
var so_click = false;
{literal}
function checkDBClick() {
	if (so_click)
		return false;
	so_click = true;
	return true;
}
{/literal}
-->
</script>

{assign var="button_href" value=$button_href|cat:" if(checkRequired(requiredFields)) if(checkDBClick()) document.checkout_form.submit()"}

{if $payment_data.processor_file eq 'ps_gcheckout.php'}
{include file="buttons/gcheckout.tpl" onclick=$button_href}
{else}
{include file="buttons/button.tpl" button_title=$lng.lbl_submit_order style="button" href=$button_href}
{/if}

{else}

{if $payment_data.processor_file eq 'ps_gcheckout.php'}
{include file="buttons/gcheckout.tpl"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_submit_order}
{/if}

{/if}
</center>
</td></tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_payment_details content=$smarty.capture.dialog extra='width="100%"'}

{elseif $payment_methods ne ""}

{capture name=dialog}
<form method="get" action="cart.php" name="checkout_form">
<table align="center" width="400">
{section name=payment loop=$payment_methods}
{if not ($display_cod eq "" and $payment_methods[payment].is_cod eq "Y")}
<tr>
<td><input type="radio" name="paymentid" id="pm{$payment_methods[payment].paymentid}" value="{$payment_methods[payment].paymentid}"{if $payment_methods[payment].is_default eq "1"} checked="checked"{/if} /></td>
{if $payment_methods[payment].processor eq "ps_paypal_pro.php"}
<td colspan="2">
<table cellpadding="0" cellspacing="0"><tr>
<td>{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="logo"}</td>
<td>&nbsp;&nbsp;</td>
<td><label for="pm{$payment_methods[payment].paymentid}">{include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="text"}</label></td>
</tr></table>
</td>
{else}
<td nowrap="nowrap" style="padding-left: 15px;"><label for="pm{$payment_methods[payment].paymentid}"><b>{$payment_methods[payment].payment_method}</b></label></td>
<td>{$payment_methods[payment].payment_details|default:"&nbsp;"}</td>
{/if}
</tr>
{/if}
{/section}
</table>
<input type="hidden" name="mode" value="checkout" />
<br />
<div align="center">
{if $js_enabled}
{include file="buttons/continue.tpl" style="button" href="javascript: document.checkout_form.submit()"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_continue}
{/if}
</div>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_payment_method content=$smarty.capture.dialog extra='width="100%"'}
{/if}
