{* $Id: register.tpl,v 1.51.2.3 2006/07/11 08:39:27 svowl Exp $ *}
{if $av_error eq 1}

{include file="modules/UPS_OnLine_Tools/register.tpl"}

{else}

{if $js_enabled eq 'Y'}
{include file="check_email_script.tpl"}
{include file="check_zipcode_js.tpl"}
{include file="generate_required_fields_js.tpl"} 
{include file="check_required_fields_js.tpl"}
{if $config.General.use_js_states eq 'Y'}
{include file="change_states_js.tpl"}
{/if}
{/if}

{if $action ne "cart"}

<!-- Deleted by Michael de Leon 11.29.06
{* if $newbie eq "Y" *}
{* if $login ne "" *}
{* assign var="title" value=$lng.lbl_modify_profile *}
{* else *}
{* assign var="title" value=$lng.lbl_create_profile *}
{* /if *}
{* else *}
{* if $main eq "user_add" *}
{* assign var="title" value=$lng.lbl_create_customer_profile *}
{* else *} 
{* assign var="title" value=$lng.lbl_modify_customer_profile *}
{* /if *}
{* /if *}

{* include file="page_title.tpl" title=$title *}
-->

<!-- IN THIS SECTION -->

{if $newbie ne "Y"}
{include file="dialog_tools.tpl"}
{/if}

<!-- IN THIS SECTION -->

{if $usertype ne "C"}
<br />
{if $main eq "user_add"}
{$lng.txt_create_customer_profile}
{else}
{$lng.txt_modify_customer_profile}
{/if}
<br /><br />
{/if}

{/if}
<font class="Text">

<!-- Deleted by Michael de Leon 11.16.06
{* if $newbie eq "Y" *}
	{* if $registered eq "" *}
		{* if $smarty.get.mode eq "update" *}
			{* $lng.txt_modify_profile_msg *}
		{* else *}
			{* $lng.txt_create_profile_msg *}
		{* /if *}
	{* /if *}
	<br /><br />
{* /if *}

{* $lng.txt_fields_are_mandatory *}
-->

</font>

<!-- Deleted by Michael de Leon 11.29.06
<br /><br />
-->
<div align="center">
{capture name=dialog}

{if $newbie ne "Y" and $main ne "user_add" and ($usertype eq "P" and $active_modules.Simple_Mode eq "Y" or $usertype eq "A")}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_to_search_results href="users.php?mode=search"}</div>
{/if}

{assign var="reg_error" value=$top_message.reg_error}
{assign var="error" value=$top_message.error}
{assign var="emailerror" value=$top_message.emailerror}

{if $registered eq ""}
{if $reg_error}
<font class="Star">
{if $reg_error eq "F" }
{$lng.txt_registration_error}
{elseif $reg_error eq "E" }
{$lng.txt_email_already_exists}
{elseif $reg_error eq "U" }
{$lng.txt_user_already_exists}
{/if}
</font>
<br />
{/if}

{if $error ne ""}
<font class="Star">
{if $error eq "b_statecode"}
{$lng.err_billing_state}
{elseif $error eq "s_statecode"}
{$lng.err_shipping_state}
{elseif $error eq "b_county"}
{$lng.err_billing_county}
{elseif $error eq "s_county"}
{$lng.err_shipping_county}
{elseif $error eq "email"}
{$lng.txt_email_invalid}
{/if}
</font>
<br />
{/if}

<script type="text/javascript" language="JavaScript 1.2">
<!--
var is_run = false;
function check_registerform_fields() {ldelim}
	if(is_run)
		return false;
	is_run = true;
	if (check_zip_code(){if $default_fields.email.avail eq 'Y'} && checkEmailAddress(document.registerform.email, '{$default_fields.email.required}'){/if} {if $config.General.check_cc_number eq "Y" AND $config.General.disable_cc ne "Y"}&& checkCCNumber(document.registerform.card_number,document.registerform.card_type) {/if}&& checkRequired(requiredFields)) {ldelim}
		document.registerform.submit();
		return true;
	{rdelim}
	is_run = false;
	return false;
{rdelim}
-->
</script>

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING|amp}" method="post" name="registerform" onsubmit="javascript: check_registerform_fields(); return false;">
{if $config.Security.use_https_login eq "Y"}
<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
{/if}
<table cellspacing="2" cellpadding="2" width="100%">
<tbody>
{include file="main/register_personal_info.tpl" userinfo=$userinfo}

{include file="main/register_billing_address.tpl" userinfo=$userinfo}

{include file="main/register_shipping_address.tpl" userinfo=$userinfo}

{include file="main/register_contact_info.tpl" userinfo=$userinfo}

{include file="main/register_additional_info.tpl" section='A'}

{if $config.General.disable_cc ne "Y"}
{include file="main/register_ccinfo.tpl"}
{/if}

{include file="main/register_account.tpl" userinfo=$userinfo}

{if $active_modules.Special_Offers and $usertype ne "C"}
{include file="modules/Special_Offers/customer/register_bonuses.tpl"}
{/if}

{if $active_modules.News_Management and $newslists}
{include file="modules/News_Management/register_newslists.tpl" userinfo=$userinfo}
{/if}

<!-- Deleted by Michael de Leon 11.16.06
<tr>
<td colspan="3" align="center">
<br /><br />
{* if $newbie eq "Y" *}
{* $lng.txt_terms_and_conditions_newbie_note *}
{* /if *}
</td>
</tr>
-->

<tr>
<td colspan="2">&nbsp;</td>
<td align="left">

{if $smarty.get.mode eq "update"}
<input type="hidden" name="mode" value="update" />
{/if}

<input type="hidden" name="anonymous" value="{$anonymous}" />

<br /><br />
{if $js_enabled and $usertype eq "C"}
<!-- Deleted by Michael de Leon 11.16.06
{* include file="buttons/submit.tpl" type="input" style="button" href="javascript: return check_registerform_fields();" *}
-->
{include file="buttons/submit_yourinfo_continuebtn.tpl" type="input" style="image" href="javascript: return check_registerform_fields();"}
{else}
<!-- Deleted by Michael de Leon 11.16.06
<input type="submit" value=" {* $lng.lbl_save|strip_tags:false|escape *} " />
-->
<input class="wwmp_logingobtn" src="{$ImagesDir}/wwmp_continuebtnsmall11.08.06.jpg" type="image" value=" {$lng.lbl_save|strip_tags:false|escape} ">
{/if}

</td>
</tr>

</tbody>
</table>
<input type="hidden" name="usertype" value="{if $smarty.get.usertype ne ""}{$smarty.get.usertype|escape:"html"}{else}{$usertype}{/if}" />
</form>

<!-- Deleted by Michael de Leon 11.16.06
<br /><br />

{* if $newbie eq "Y" *}
{* $lng.txt_newbie_registration_bottom *}
<br /><a href="help.php?section=conditions" target="_blank"><font class="Text" style="white-space: nowrap;"><b>{* $lng.lbl_terms_n_conditions *}</b>&nbsp;</font>{* include file="buttons/go.tpl" *}</a>
{* else *}
{* $lng.txt_user_registration_bottom *}
{* /if *}

<br />
-->
{if $is_areas.S eq 'Y' or $is_areas.B eq 'Y'}
{if $active_modules.UPS_OnLine_Tools and $av_enabled eq "Y"}
<br />
<br />
<br />
<table cellpadding="1" cellspacing="1" width="100%">
<tbody>
<tr>
<td colspan="3">
{include file="modules/UPS_OnLine_Tools/ups_av_notice.tpl" postoffice=1}
{include file="modules/UPS_OnLine_Tools/ups_av_notice.tpl"}
<br /><br />
</td>
</tr>
</tbody>
</table>
{/if}
{/if}


{else}

{if $smarty.post.mode eq "update" or $smarty.get.mode eq "update"}
{$lng.txt_profile_modified}
{elseif $smarty.get.usertype eq "B"  or $usertype eq "B"}
{$lng.txt_partner_created}
{else}
{$lng.txt_profile_created}
{/if}
{/if}

{/capture}
{include file="dialog_shoppingcart_placeorder.tpl" title=$lng.lbl_profile_details content=$smarty.capture.dialog extra='width="634"'}
</div>
<!-- Deleted by Michael de Leon 11.16.06
{* include file="dialog.tpl" title=$lng.lbl_profile_details content=$smarty.capture.dialog extra='width="100%"' *}
-->

{/if}
