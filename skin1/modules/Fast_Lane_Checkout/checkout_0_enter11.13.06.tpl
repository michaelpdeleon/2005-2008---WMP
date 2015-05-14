{* $Id: checkout_0_enter.tpl,v 1.7.2.5 2006/06/29 13:31:46 svowl Exp $ *}
{*
CHECKOUT: STEP 0 (Authorization/Registration)
*}

<h3>{$lng.lbl_my_account}</h3>

<table cellpadding="0" cellspacing="5" width="100%">
<tr>
<td class="FLCDialogCell">
{capture name=dialog}

{$lng.txt_login_incorrect}

<br />
<br />

{******************** LOGIN FORM: BEGIN ************************}

{include file="main/login_form.tpl" is_flc=true}

{******************** LOGIN FORM: END ************************}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_returning_customer content=$smarty.capture.dialog extra='class="FLCDialog"' is_flc_dialog=true}
</td>

<td class="FLCDialogCell">

{capture name=dialog}

<center>
<font class="FLC_Register">{$lng.lbl_flc_new_customer_text} <a href="#regdlg" onclick="javascript: document.getElementById('reg_dlg').style.display = (document.getElementById('reg_dlg').style.display == '') ? 'none' : '';">{$lng.lbl_flc_new_customer_link}</a></font>
</center>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_new_customer content=$smarty.capture.dialog extra='class="FLCDialog"' valign="middle" is_flc_dialog=true}

</td>
</tr>
</table>

<br />
{if $paypal_express_active}
{include file="payments/ps_paypal_pro_express_checkout.tpl"}
{/if}

<br />
<br />

<div id="reg_dlg"{if $av_error ne 1 && $js_enabled && $top_message.reg_error eq ''} style="display: none;"{/if}>

<a name="regdlg" />

{******************** REGISTER FORM: BEGIN ************************}

{include file="customer/main/register.tpl"}

{******************** REGISTER FORM: END ************************}
</div>
{if $top_message.reg_error ne '' or $av_error eq 1}
<script type="text/javascript">
<!--
self.location.hash = 'regdlg';
-->
</script>
{/if}
