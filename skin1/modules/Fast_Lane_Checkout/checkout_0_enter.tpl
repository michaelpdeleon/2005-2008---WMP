{* $Id: checkout_0_enter.tpl,v 1.7.2.5 2006/06/29 13:31:46 svowl Exp $ *}
{*
CHECKOUT: STEP 0 (Authorization/Registration)
*}
<!-- Deleted by Michael de Leon 11.13.06
<h3>{* $lng.lbl_my_account *}</h3>
-->
<div align="center">
<table cellpadding="0" cellspacing="5" width="634">
<tr>
<td>
{capture name=dialog}

<!-- Deleted by Michael de Leon 11.13.06
{* $lng.txt_login_incorrect *}
<br />
<br />
-->

{******************** LOGIN FORM: BEGIN ************************}

{include file="main/login_form.tpl" is_flc=true}

{******************** LOGIN FORM: END ************************}
{/capture}
{include file="dialog_personaldetails.tpl" title=$lng.lbl_returning_customer content=$smarty.capture.dialog extra='width="295"' is_flc_dialog=true}
</td>

<td>
{capture name=dialog}

<!-- Deleted by Michael de Leon 11.14.06
<center>
<font class="FLC_Register">{* $lng.lbl_flc_new_customer_text *} <a href="#regdlg" onclick="javascript: document.getElementById('reg_dlg').style.display = (document.getElementById('reg_dlg').style.display == '') ? 'none' : '';">{* $lng.lbl_flc_new_customer_link *}</a></font>
</center>
-->

<div align="center">
<table border="0" cellspacing="0" cellpadding="0" height="200" width="250">
  <tr>
    <td class="wwmp_cartlogin_desc2" align="left">You <strong>do not</strong> need to create an account to make purchases. But we still need some information to complete this order. All information will be kept strictly confidential.</td>
  </tr>
  <tr>
    <td align="center"><a href="#regdlg" onclick="javascript: document.getElementById('reg_dlg').style.display = (document.getElementById('reg_dlg').style.display == '') ? 'none' : '';"><img src="{$ImagesDir}/wwmp_cartlogincontinue11.14.06.jpg"></a></td>
  </tr>
  <tr>
    <td class="wwmp_cartlogin_desc2" align="left">To make future purchases even faster, we recommended you <a class="wwmp_vertmenulink" href="register.php" target="_self">create an account</a>.</td>
  </tr>
</table>
</div>

{/capture}
{include file="dialog_personaldetails.tpl" title=$lng.lbl_new_customer content=$smarty.capture.dialog extra='width="295"' is_flc_dialog=true}

</td>
</tr>
</table>
</div>

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
