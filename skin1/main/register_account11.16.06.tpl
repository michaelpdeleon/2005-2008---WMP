{* $Id: register_account.tpl,v 1.19.2.2 2006/08/12 06:21:37 max Exp $ *}
{if $hide_account_section ne "Y"}

{if $hide_header eq ""}
<tr>
<td colspan="3" class="RegSectionTitle">{$lng.lbl_username_n_password}<hr size="1" noshade="noshade" /></td>
</tr>
{/if}

{if $anonymous ne "" and $config.General.disable_anonymous_checkout ne "Y"}

{* Anonymous account *}

<tr>
<td colspan="3">{$lng.txt_anonymous_account_msg}</td>
</tr>

{/if}

{if $userinfo.login eq $login and $login and $userinfo.usertype ne "C"}

{* Display membership level *}

<tr style="display: none;">
<td>
<input type="hidden" name="membershipid" value="{$userinfo.membershipid}" />
<input type="hidden" name="pending_membershipid" value="{$userinfo.pending_membershipid}" />
</td>
</tr>

{else}

{if $config.General.membership_signup eq "Y" and ($usertype eq "C" or ($active_modules.Simple_Mode ne "" and $usertype eq "P") or $usertype eq "A" or $usertype eq "B")}
{include file="admin/main/membership_signup.tpl"}
{/if}

{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode ne "")}
{include file="admin/main/membership.tpl"}
{/if}

{* /Display membership level *}

{/if}

{if $anonymous ne "" and $config.General.disable_anonymous_checkout ne "Y"}

{* Anonymous account *}

<tr>
<td align="right">{$lng.lbl_username}</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" id="uname" name="uname" size="32" maxlength="32" value="{if $userinfo.uname}{$userinfo.uname}{else}{$userinfo.login}{/if}" />
{if ($reg_error ne "" and $userinfo.uname eq "" and $userinfo.login eq "") or $reg_error eq "U"}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_password}</td>
<td>&nbsp;</td>
<td nowrap="nowrap"><input type="password" name="passwd1" size="32" maxlength="32" value="{$userinfo.passwd1}" />
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_confirm_password}</td>
<td>&nbsp;</td>
<td nowrap="nowrap"><input type="password" name="passwd2" size="32" maxlength="32" value="{$userinfo.passwd2}" />
</td>
</tr>

{* /Anonymous account *}

{else}

{* NOT anonymous account *}

<tr>
<td align="right">{$lng.lbl_username}</td>
<td class="Star">*</td>
<td nowrap="nowrap">
{if $userinfo.login ne "" || ($login eq $userinfo.uname && $login ne '')}
<b>{$userinfo.login|default:$userinfo.uname}</b>
<input type="hidden" name="uname" value="{$userinfo.login|default:$userinfo.uname}" />
{else}
<input type="text" id="uname" name="uname" size="32" maxlength="32" value="{if $userinfo.uname}{$userinfo.uname}{else}{$userinfo.login}{/if}" />
{if ($reg_error ne "" and $userinfo.uname eq "" and $userinfo.login eq "") or $reg_error eq "U"}<font class="Star">&lt;&lt;</font>{/if}
{/if}
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_password}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap"><input type="password" id="passwd1" name="passwd1" size="32" maxlength="32" value="{$userinfo.passwd1}" />
{if $reg_error ne "" and $userinfo.passwd1 eq ""}<font class="Star">&lt;&lt;</font>{/if} 
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_confirm_password}</td>
<td class="Star">*</td>
<td nowrap="nowrap"><input type="password" id="passwd2" name="passwd2" size="32" maxlength="32" value="{$userinfo.passwd2}" />
{if $reg_error ne "" and $userinfo.passwd2 eq ""}<font class="Star">&lt;&lt;</font>{/if} 
</td>
</tr>

{* / NOT anonymous account *}

{/if}

{if (($active_modules.Simple_Mode ne "" and $usertype eq "P") or $usertype eq "A") and ($userinfo.uname && $userinfo.uname ne $login or !$userinfo.uname and $userinfo.login ne $login)}

{if $userinfo.status ne "A"}{* only for non-anonymous users *}
<tr valign="middle">
<td align="right">{$lng.lbl_account_status}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<select name="status">
<option value="N"{if $userinfo.status eq "N"} selected="selected"{/if}>{$lng.lbl_account_status_suspended}</option>
<option value="Y"{if $userinfo.status eq "Y"} selected="selected"{/if}>{$lng.lbl_account_status_enabled}</option>
{if $active_modules.XAffiliate ne "" and ($userinfo.usertype eq "B" or $smarty.get.usertype eq "B")}
<option value="Q"{if $userinfo.status eq "Q"} selected="selected"{/if}>{$lng.lbl_account_status_not_approved}</option>
<option value="D"{if $userinfo.status eq "D"} selected="selected"{/if}>{$lng.lbl_account_status_declined}</option>
{/if}
</select>
</td>
</tr>

{if $display_activity_box eq "Y"}
<tr valign="middle">
<td align="right">{$lng.lbl_account_activity}:</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<select name="activity">
<option value="Y"{if $userinfo.activity eq "Y"} selected="selected"{/if}>{$lng.lbl_account_activity_enabled}</option>
<option value="N"{if $userinfo.activity eq "N"} selected="selected"{/if}>{$lng.lbl_account_activity_disabled}</option>
</select>
</td>
</tr>
{/if}

{/if}{* $userinfo.status ne "A" *}

<tr valign="middle">
	<td colspan="2">&nbsp;</td>
	<td nowrap="nowrap">

<table>
<tr>
	<td><input type="checkbox" id="change_password" name="change_password" value="Y"{if $userinfo.change_password eq "Y"} checked="checked"{/if} /></td>
	<td><label for="change_password">{$lng.lbl_reg_chpass}</label></td>
</tr>
</table>

	</td>
</tr>

{/if}

{else}
<tr style="display: none;">
<td>
<input type="hidden" name="uname" value="{$userinfo.login|default:$userinfo.uname}" />
<input type="hidden" name="passwd1" value="{$userinfo.passwd1}" />
<input type="hidden" name="passwd2" value="{$userinfo.passwd2}" />
</td>
</tr>
{/if}
