{* $Id: menu_users_online.tpl,v 1.7 2006/02/15 07:15:08 svowl Exp $ *}
{if $users_online}
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="DialogBorder"><img src="{$ImagesDir}/spacer.gif" width="1" height="1" alt="" /></td>
</tr>
<tr>
	<td class="BottomDialogBox">
<b>{$lng.lbl_users_online}:</b>&nbsp;
{foreach from=$users_online item=v name="_users"}
<font class="VertMenuItems" style="WHITE-SPACE: nowrap;">{$v.count}
{strip}
{if $v.usertype eq 'A' || ($v.usertype eq 'P' && $active_modules.Simple_Mode)}
{$lng.lbl_admin_s}
{elseif $v.usertype eq 'P'}
{$lng.lbl_provider_s} 
{elseif $v.usertype eq 'B'}
{$lng.lbl_partner_s} 
{elseif $v.usertype eq 'C' && $v.is_registered eq 'Y'}
{$lng.lbl_registered_customer_s} 
{elseif $v.usertype eq 'C' && $v.is_registered eq 'A'}
{$lng.lbl_anonymous_customer_s}
{elseif $v.usertype eq 'C' && $v.is_registered eq ''}
{$lng.lbl_unregistered_customer_s} 
{/if}
{if not $smarty.foreach._users.last}, {/if}
{/strip}
</font>
{/foreach}
	</td>
</tr>
</table>
{/if}
