{* $Id: anonymous_checkout.tpl,v 1.23 2006/03/16 15:28:19 svowl Exp $ *}
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
<p />
{if $js_enabled}
{include file="buttons/update.tpl" href="javascript: document.cartform.submit()" js_to_href="Y"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_update}
{/if}
</form>
{/capture}
{assign var="_dlg_title" value=$lng.lbl_checkout_step_x_of_y|substitute:"X":$checkout_step:"Y":$total_checkout_steps}
{include file="dialog.tpl" title=$_dlg_title content=$smarty.capture.checkout_dialog extra='width="100%"'}
{if $paypal_express_active}
{include file="payments/ps_paypal_pro_express_checkout.tpl"}
{/if}
<p class="Message">{$lng.txt_register_have_account}</p>
<p />
{include file="customer/main/register.tpl"}
