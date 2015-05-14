{* $Id: password_recover.tpl,v 1.7 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_dear_customer},

<p />{$lng.eml_password_recovery_msg}

<p />
<table cellpadding="1" cellspacing="1">
{if $accounts}
{section name=acc_num loop=$accounts}
<tr>
<td colspan="3"><b>{$lng.lbl_account_information}:</b></td>
</tr>
<tr>
<td><tt>{$lng.lbl_usertype}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$accounts[acc_num].usertype}</tt></td>
</tr>
<tr>
<td><tt>{$lng.lbl_username}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$accounts[acc_num].login}</tt></td>
</tr>
<tr>
<td><tt>{$lng.lbl_password}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$accounts[acc_num].password}</tt></td>
</tr>
{/section}
{else}
<tr>
<td colspan="3"><tt>no data was found</tt></td>
</tr>
{/if}
</table>

{include file="mail/html/signature.tpl"}

