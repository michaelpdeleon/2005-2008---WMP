{* $Id: authbox.tpl,v 1.25 2005/11/17 15:08:15 max Exp $ *}
{capture name=menu}
<form action="{$xcart_web_dir}/include/login.php" method="post" name="loginform">
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
<td>&nbsp;&nbsp;&nbsp;</td>
<td class="VertMenuItems" valign="top">
{$login}<br />{$lng.txt_logged_in}<br />
<br />
{include file="buttons/logout_menu.tpl"}
<br />
</td>
</tr>
{if $usertype eq "C"}
<tr>
<td class="VertMenuItems" colspan="2" align="right">
<br />
{if $js_enabled}
<a href="{$js_update_link|amp}" class="SmallNote">{$lng.txt_javascript_disabled}</a>
{else}
<a href="{$js_update_link|amp}" class="SmallNote">{$lng.txt_javascript_enabled}</a>
{/if}
</td>
</tr>
{/if}
</table>
<input type="hidden" name="mode" value="logout" />
<input type="hidden" name="redirect" value="{$redirect}" />
</form>
{/capture}
{ include file="menu.tpl" dingbats="dingbats_authentification.gif" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu }
