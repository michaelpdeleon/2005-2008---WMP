{* $Id: membership_signup.tpl,v 1.7 2005/11/17 06:55:37 max Exp $ *}
<tr valign="middle">
<td class="wwmp_yourinfo_label" align="right">{$lng.lbl_signup_for_membership}</td>
<td align="left">&nbsp;</td>
<td align="left">
<select name="pending_membershipid">
<option value="">{$lng.lbl_not_member}</option>
{foreach from=$membership_levels item=v}
<option value="{$v.membershipid}"{if $userinfo.pending_membershipid eq $v.membershipid} selected="selected"{/if}>{$v.membership}</option>
{/foreach}
</select>
</td>
</tr>
