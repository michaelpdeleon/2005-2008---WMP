{* $Id: user_delete_confirmation.tpl,v 1.27 2005/11/30 13:29:35 max Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_delete_users}

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

<br />

{$lng.txt_delete_users_top_text}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_to_search_results href="users.php?mode=search`$pagestr`"}</div>
<br />

{$lng.txt_delete_users_top_note}

<ul>
{section name=user loop=$users}
{assign var="utype" value=$users[user].usertype}
<li><span class="ProductPriceSmall">{$users[user].login}</span>: {$usertypes.$utype}
<dl>
<dd><b>{$users[user].title} {$users[user].firstname} {$users[user].lastname}</b></dd>
<dd>{$users[user].company}</dd>
<dd><i>{$lng.lbl_phone}:</i> {$users[user].phone} / <i>{$lng.lbl_email}:</i> {$users[user].email}</dd>
<dd><i>{$lng.lbl_web_site}:</i> {$users[user].url|default:$lng.txt_not_available}</dd>
<dd>
<table cellpadding="0" cellspacing="0">
<tr>
	<td>
<i>{$lng.lbl_shipping_address}:</i>
<dd>{$users[user].s_address},</dd>
{if $users[user].s_address_2}<dd>{$users[user].s_address_2},</dd>{/if}
<dd>{$users[user].s_city},</dd>
<dd>{$users[user].s_state} {if $users[user].s_statename ne $users[user].s_state}({$users[user].s_statename}){/if} {$users[user].s_zipcode},</dd>
<dd>{$users[user].s_countryname}</dd>
	</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>
<i>{$lng.lbl_billing_address}:</i>
<dd>{$users[user].b_address},</dd>
{if $users[user].b_address_2}<dd>{$users[user].b_address_2},</dd>{/if}
<dd>{$users[user].b_city},</dd>
<dd>{$users[user].b_state} {if $users[user].b_statename ne $users[user].b_state}({$users[user].b_statename}){/if} {$users[user].b_zipcode},</dd>
<dd>{$users[user].b_countryname}</dd>
	</td>
</tr>
</table>
</dl>
</li>
{/section}
</ul>

{$lng.txt_operation_not_reverted_warning}

<br /><br />

<form action="process_user.php" method="post" name="processform">

<input type="hidden" name="mode" value="delete" />
<input type="hidden" name="confirmed" value="Y" />

<table cellspacing="0" cellpadding="0">
<tr>
	<td>{$lng.txt_are_you_sure_to_proceed}</td>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_yes href="javascript:document.processform.submit()" js_to_href="Y"}</td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_no href="users.php?mode=search`$pagestr`"}</td>
</tr>
</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
