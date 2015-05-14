{* $Id: membership.tpl,v 1.8 2005/11/17 06:55:37 max Exp $ *}
<tr valign="middle">
<td align="right">{$lng.lbl_membership}</td>
<td></td>
<td nowrap="nowrap">
<select name="membershipid">
<option value="">{$lng.lbl_not_member}</option>
{foreach from=$membership_levels item=v}
<option value="{$v.membershipid}"{if $userinfo.membershipid eq $v.membershipid} selected="selected"{/if}>{$v.membership}</option>
{/foreach}
</select>
</td>
</tr>
