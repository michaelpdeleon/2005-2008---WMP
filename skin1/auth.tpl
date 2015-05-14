{* $Id: auth.tpl,v 1.43 2005/11/17 15:08:15 max Exp $ *}
{capture name=menu}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$https_location}
{else}
{assign var="form_url" value=$current_location}
{/if}
<form action="{$form_url}/include/login.php" method="post" name="authform">
<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
<table cellpadding="0" cellspacing="0" width="100%">
{if $config.Security.use_secure_login_page eq "Y"} {* use_secure_login_page *}
<tr>
<td>
{assign var="slogin_url_add" value=""}
{if $usertype eq "C"}
{assign var="slogin_url" value=$catalogs_secure.customer}
{if $catalogs_secure.customer ne $catalogs.customer}
{assign var="slogin_url_add" value="?`$XCARTSESSNAME`=`$XCARTSESSID`"}
{/if}
{elseif $usertype eq "P" and $active_modules.Simple_Mode eq "Y" or $usertype eq "A"}
{assign var="slogin_url" value=$catalogs_secure.admin}
{elseif $usertype eq "P"}
{assign var="slogin_url" value=$catalogs_secure.provider}
{elseif $usertype eq "B"}
{assign var="slogin_url" value=$catalogs_secure.partner}
{/if}
{include file="buttons/secure_login.tpl"}
</td>
</tr>
{else} {* use_secure_login_page *}
<tr>
<td class="VertMenuItems">
<!-- Deleted by Michael de Leon 10.31.06
<font class="VertMenuItems">{* $lng.lbl_username *}</font><br />
-->
<!-- Start addition by Michael de Leon 10.31.06 -->
<font class="wwmp_loginlabel">{$lng.lbl_username}</font><br />
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="text" name="username" size="16" value="{#default_login#}" /><br />
<!-- Deleted by Michael de Leon 10.31.06
<font class="VertMenuItems">{* $lng.lbl_password *}</font><br />
-->
<!-- Start addition by Michael de Leon 10.31.06 -->
<font class="wwmp_loginlabel">{$lng.lbl_password}</font><br />
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="password" name="password" size="16" value="{#default_password#}" />
<!-- Start addition by Michael de Leon 10.31.06 -->
<td valign="bottom" align="left">
<a href="javascript: document.authform.submit();"><input class="wwmp_logingobtn" src="{$ImagesDir}/wwmp_logingobtn10.31.06.jpg" type="image"></a><br />
</td>
<!-- End addition by Michael de Leon 10.31.06 -->
<input type="hidden" name="mode" value="login" />
{if $active_modules.Simple_Mode ne "" and $usertype ne "C" and $usertype ne "B"}
<input type="hidden" name="usertype" value="P" />
{else}
<input type="hidden" name="usertype" value="{$usertype}" />
{/if}
<input type="hidden" name="redirect" value="{$redirect}" />
</td></tr>
<!-- Deleted by Michael de Leon 10.31.06
<tr>
<td height="24" class="VertMenuItems">{* include file="buttons/login_menu.tpl" *}</td>
</tr>
-->
{/if} {* use_secure_login_page *}
{if $usertype eq "C" or ($usertype eq "B" and $config.XAffiliate.partner_register eq "Y")}
<tr>
<!-- Start addition by Michael de Leon 10.31.06 -->
<td colspan="2" height="24" class="VertMenuItems"><a href="register.php" class="wwmp_vertmenulink">Create a new account?</a></td>
<!-- End addition by Michael de Leon 10.31.06 -->
<!-- Deleted by Michael de Leon 10.31.06
<td height="24" nowrap="nowrap" class="VertMenuItems">{* include file="buttons/create_profile_menu.tpl" *}</td>
-->
</tr>
{/if}
{if $login eq ""}
<tr>
<!-- Start addition by Michael de Leon 10.31.06 -->
<td colspan="2" height="24" class="VertMenuItems"><a href="help.php?section=Password_Recovery" class="wwmp_vertmenulink">Forgot your username or password?</a></td>
<!-- End addition by Michael de Leon 10.31.06 -->
<!-- Deleted by Michael de Leon 10.31.06
<td height="24" nowrap="nowrap" class="VertMenuItems"><a href="help.php?section=Password_Recovery" class="VertMenuItems">{* $lng.lbl_recover_password *}</a></td>
-->
</tr>
{/if}

{if $usertype eq "P" and $active_modules.Simple_Mode eq "Y" or $usertype eq "A"}
<!-- insecure login form link -->
<tr>
<td class="VertMenuItems">
<br />
<div align="left"><a href="insecure_login.php" class="SmallNote">{$lng.lbl_insecure_login}</a></div>
</td>
</tr>
<!-- insecure login form link -->
{/if}
<!-- Deleted by Michael de Leon 10.31.06
{* if $usertype eq "C" *}
<tr>
<td class="VertMenuItems" align="right">
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
</form>
{/capture}
<!-- Start addition by Michael de Leon 10.26.06 -->
{ include file="menu.tpl" dingbats="wwmp_login_icon10.26.06.jpg" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu }
<!-- End addition by Michael de Leon 10.26.06 -->
<!-- Deleted by Michael de Leon 10.26.06
{* include file="menu.tpl" dingbats="dingbats_authentification.gif" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu *}
-->
