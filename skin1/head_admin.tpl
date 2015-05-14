{* $Id: head_admin.tpl,v 1.10 2006/03/17 08:50:44 svowl Exp $ *}
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td class="HeadLogo"><a href="{$http_location}/"><img src="{$ImagesDir}/admin_xlogo.gif" width="244" height="67" alt="" /></a></td>
{if $login ne ""}
	<td align="right">{include file="authbox_top.tpl"}</td>
	<td width="10"><img src="{$ImagesDir}/spacer.gif" width="10" height="1" alt="" /></td>
{/if}
</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td colspan="2" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr> 
	<td colspan="2" class="HeadLine" align="right" height="22">
{if ($usertype eq "P" or $usertype eq "A") and $login and $all_languages_cnt gt 1}
<form action="{$smarty.server.REQUEST_URI|amp}" method="post" name="asl_form">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><b>{$lng.lbl_current_language}:</b>&nbsp;</td>
	<td>
<input type="hidden" name="redirect" value="{$smarty.server.QUERY_STRING|amp}" />
<select name="asl" onchange="javascript: document.asl_form.submit()">
{section name=ai loop=$all_languages}
<option value="{$all_languages[ai].code}"{if $current_language eq $all_languages[ai].code} selected="selected"{/if}>{$all_languages[ai].language}</option>
{/section}
</select>
	</td>
</tr>
</table>
</form>
{else}
&nbsp;
{/if}
</td>
</tr>
<tr> 
	<td colspan="2" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<!-- Start addition by Michael de Leon 09.14.06 -->
{if $login ne ""}
<tr>
	<td>{include file="modules/Users_online/menu_users_online.tpl"}</td>
</tr>
{/if}
<!-- End addition by Michael de Leon 09.14.06 -->
{******** Remove this line to display how much products there are online ****
<tr>
{insert name="productsonline" assign="_productsonline"}
	<td colspan="2" class="NumberOfArticles" align="right">
{if $config.Appearance.show_in_stock eq "Y"}
{insert name="itemsonline" assign="_itemsonline"}
{$lng.lbl_products_and_items_online|substitute:"X":$_productsonline:"Y":$_itemsonline}
{else}
{$lng.lbl_products_online|substitute:"X":$_productsonline}
{/if}
&nbsp;
	</td>
</tr>
**** Remove this line to display how much products there are online ********}
<tr>
	<td colspan="2" valign="middle" height="32">
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td height="18"><img src="{$ImagesDir}/spacer.gif" width="1" height="18" alt="" /></td>
</tr>
</table>
	</td>
</tr>
</table>
