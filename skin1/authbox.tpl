{* $Id: authbox.tpl,v 1.25 2005/11/17 15:08:15 max Exp $ *}
{capture name=menu}
<form action="{$xcart_web_dir}/include/login.php" method="post" name="loginform">
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
<!-- Deleted by Michael de Leon 11.01.06
<td>&nbsp;&nbsp;&nbsp;</td>
-->
<td class="VertMenuItems" valign="top">
<font class="wwmp_cartlogin_status">Hello <strong>{$login}!</strong></font><br />
<a href="javascript: document.loginform.submit();"><img class="wwmp_logoutbtn" src="{$ImagesDir}/wwmp_logoutbtn11.01.06.jpg" border="0"></a>
<br />
<a href="register.php?mode=update" class="wwmp_vertmenulink">{$lng.lbl_modify_profile}</a><br />
<a href="orders.php" class="wwmp_vertmenulink">{$lng.lbl_orders_history}</a><br />
</td>
</tr>
<!-- Deleted by Michael de Leon 11.01.06
{* if $usertype eq "C" *}
<tr>
<td class="VertMenuItems" colspan="2" align="right">
<br />
{* if $js_enabled *}
<a href="{* $js_update_link|amp *}" class="SmallNote">{* $lng.txt_javascript_disabled *}</a>
{* else *}
<a href="{* $js_update_link|amp *}" class="SmallNote">{* $lng.txt_javascript_enabled *}</a>
{* /if *}
</td>
</tr>
{* /if *}
-->
</table>
<input type="hidden" name="mode" value="logout" />
<input type="hidden" name="redirect" value="{$redirect}" />
</form>
{/capture}
<!-- Start addition by Michael de Leon 10.26.06 -->
{ include file="menu.tpl" dingbats="wwmp_login_icon10.26.06.jpg" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu }
<!-- End addition by Michael de Leon 10.26.06 -->
<!-- Deleted by Michael de Leon 10.26.06
{* include file="menu.tpl" dingbats="dingbats_authentification.gif" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu *}
-->