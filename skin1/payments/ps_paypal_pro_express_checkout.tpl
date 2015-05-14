{* $Id: ps_paypal_pro_express_checkout.tpl,v 1.8.2.1 2006/06/16 10:47:51 max Exp $ *}
{if $paypal_express_link eq "logo"}
<!-- PayPal Logo -->
<a href="javascript:void(0);" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','width=400, height=350, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no');"><img src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" border="0" alt="{$lng.lbl_paypal_alt_text|escape}" /></a>
<!-- PayPal Logo -->
{elseif $paypal_express_link eq "text"}
{$lng.txt_paypal_text2}
{elseif $paypal_express_link eq "return"}
{include file="buttons/button.tpl" button_title=$lng.lbl_modify href="`$current_location`/payment/ps_paypal_pro.php?mode=express&payment_id=`$smarty.get.paymentid`&do_return=1"}
{else}
<p>
{capture name=paypal_express_dialog}
<table border="0">
<form action="{$current_location}/payment/ps_paypal_pro.php" method="get">
<input type="hidden" name="mode" value="express" />
<input type="hidden" name="payment_id" value="{$paypal_express_active}" />
<tr>
  <td>
   <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" />
  </td>
  <td>&nbsp;</td>
  <td>{$lng.txt_paypal_text1}</td>
</tr>
</form>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_checkout_with_paypal_express content=$smarty.capture.paypal_express_dialog extra='width="100%"'}
</p>
{/if}
