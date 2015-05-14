{* $Id: checkout_3_place.tpl,v 1.8.2.2 2006/08/04 06:07:30 max Exp $ *}

<h3>{$lng.lbl_place_order}</h3>

{capture name=dialog}

{if $config.Appearance.show_cart_details eq "Y" or ($config.Appearance.show_cart_details eq "L" and $smarty.get.paymentid ne "" and $smarty.get.mode eq "checkout")} 
{include file="customer/main/cart_details.tpl" link_qty="Y"}
{else}
{include file="customer/main/cart_contents.tpl" link_qty="Y"}
{/if}

<br />
{include file="customer/main/cart_totals.tpl" link_shipping="Y"}
<br /><br />

<form action="{$payment_data.payment_script_url}" method="post" name="checkout_form">
<input type="hidden" name="paymentid" value="{$payment_data.paymentid}" />
<input type="hidden" name="action" value="place_order" />
<table cellpadding="0" cellspacing="0" width="100%">
<tr><td>
{include file="customer/main/subheader.tpl" title=$lng.lbl_personal_information}
<div align="right">{include file="buttons/modify.tpl" href="register.php?mode=update&amp;action=cart&amp;paymentid=`$smarty.get.paymentid`"}</div>

<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="20"><img src="{$ImagesDir}/spacer.gif" width="20" height="1" alt="" /></td>
<td>
{include file="modules/Fast_Lane_Checkout/customer_details_html.tpl"}
</td>
</tr>
</table>

<br /><br />

{include file="customer/main/subheader.tpl" title="`$lng.lbl_payment_method`: `$payment_data.payment_method`"}
{if $ignore_payment_method_selection eq ""}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_change_payment_method href="cart.php?mode=checkout"}</div>
{/if}

<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
<script type="text/javascript">
<!--
requiredFields = new Array();
-->
</script>
{include file="check_required_fields_js.tpl"}

<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="20"><img src="{$ImagesDir}/spacer.gif" width="20" height="1" alt="" /></td>
<td>
{if $payment_data.payment_template ne ""}
{capture name=payment_template_output}
{include file=$payment_data.payment_template hide_header="Y"}
{/capture}
{if $smarty.capture.payment_template_output ne ""}
{include file="customer/main/subheader.tpl" title=$lng.lbl_payment_details class="grey"}
{$smarty.capture.payment_template_output}
<br />
{/if}
{/if}

{if $payment_cc_data.cmpi eq 'Y' && $config.CMPI.cmpi_enabled eq 'Y'}
{include file="main/cmpi.tpl"}
{/if}
{include file="customer/main/checkout_notes.tpl"}
</td>
</tr>
</table>

<br />
<input type="hidden" name="payment_method" value="{$payment_data.payment_method_orig}" />
<center>
{$lng.txt_terms_and_conditions_note}

<p />

{if $js_enabled}
{assign var="button_href" value="javascript: "}
{if $config.General.check_cc_number eq "Y" and ($payment_cc_data.type eq "C" or $payment_data.paymentid eq 1 or  ($payment_data.processor_file eq "ps_paypal_pro.php" and $payment_cc_data.paymentid ne $payment_data.paymentid)) and $payment_cc_data.disable_ccinfo ne "Y"}
{assign var="button_href" value=$button_href|cat:"if(checkCCNumber(document.checkout_form.card_number,document.checkout_form.card_type) && checkExpirationDate(document.checkout_form.card_expire_Month,document.checkout_form.card_expire_Year)"}
{if $payment_cc_data.disable_ccinfo ne "C"}
{assign var="button_href" value=$button_href|cat:" && checkCVV2(document.checkout_form.card_cvv2,document.checkout_form.card_type)"}
{/if}
{assign var="button_href" value=$button_href|cat:")"}
{/if}
{assign var="button_href" value=$button_href|cat:" if(checkRequired(requiredFields)) document.checkout_form.submit()"}

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
