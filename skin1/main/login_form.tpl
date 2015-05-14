{* $Id: login_form.tpl,v 1.6.2.4 2006/12/07 08:28:06 svowl Exp $ *}
{if $config.Security.use_https_login eq "Y" and $usertype eq "C"}
{assign var="form_url" value=$https_location}
{else}
{assign var="form_url" value=$current_location}
{/if}
<form action="{$form_url}/include/login.php" method="post" name="errorform">
<input type="hidden" name="is_remember" value="{$is_remember}" />

<!-- Deleted by Michael de Leon 02.06.07
<table>
<tr> 
	<td height="10" width="78" class="FormButton">{* $lng.lbl_login *}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="text" name="username" size="30" value="{* #default_login#|default:$username *}" /></td>
</tr>
<tr> 
	<td height="10" width="78" class="FormButton">{* $lng.lbl_password *}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="password" name="password" size="30" maxlength="64" value="{* #default_password# *}" />
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<div align="center">
<table cellpadding="0" cellspacing="0" border="0" height="230" width="250" align="center">
<tr>
	<td class="wwmp_cartlogin_desc" align="left" width="100%">Log on to your account to place orders.</td>
</tr>
<tr> 
	<td class="wwmp_loginlabel" align="left" width="100%">{$lng.lbl_login}</td>
</tr>
<tr>
	<td class="wwmp_cartlogin_contents" align="left" width="100%"><input type="text" name="username" size="30" value="{#default_login#|default:$remember_login}" /></td>
</tr>
<tr> 
	<td class="wwmp_loginlabel" align="left" width="100%">{$lng.lbl_password}</td>
</tr>
<tr>
	<td class="wwmp_cartlogin_contents" align="left" width="100%"><input type="password" name="password" size="30" value="{#default_password#}" />
<!-- End addition by Michael de Leon 02.06.07 -->
{if $active_modules.Simple_Mode ne "" and $usertype ne "C" and $usertype ne "B"}
<input type="hidden" name="usertype" value="P" />
{else}
<input type="hidden" name="usertype" value="{$usertype}" />
{/if}
<input type="hidden" name="redirect" value="{$redirect}" />
<input type="hidden" name="mode" value="login" />
</td>
</tr>
<!-- Deleted by Michael de Leon 02.06.07
<tr> 
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10">&nbsp;</td>
	<td width="282" height="10" class="ErrorMessage">{* if $main eq "login_incorrect" *}{* $lng.err_invalid_login *}{* /if *}</td>
</tr>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr> 
	<td class="ErrorMessage" align="left" width="100%">{ if $main eq "login_incorrect"}<div class="wwmp_error_invalidlogin">{$lng.err_invalid_login}</div>{/if}</td>
</tr>
<tr> 
<!-- End addition by Michael de Leon 02.06.07 -->
{if $active_modules.Image_Verification and $show_antibot.on_login eq 'Y' and $login_antibot_on}
{include file="modules/Image_Verification/spambot_arrest.tpl" mode="advanced" id=$antibot_sections.on_login}
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
	<td align="left" height="5"></td>
</tr>
<tr>
<td class="ErrorMessage" align="left" width="100%">{if $antibot_err}<div class="wwmp_error_invalidlogin">{$lng.msg_err_antibot}</div>{/if}</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
{/if}
<!-- Deleted by Michael de Leon 02.06.07
<tr>
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10">&nbsp;</td>
	<td width="282" height="10" class="ErrorMessage">{* if $antibot_err *}{* $lng.msg_err_antibot *}{* /if *}</td>
</tr>
<tr> 
<td height="10" width="78" class="FormButton"></td>
<td width="10" height="10" class="FormButton">&nbsp;</td>
<td width="282" height="10">
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<td class="wwmp_cartlogin_contents" align="left" width="100%">
<!-- End addition by Michael de Leon 02.06.07 -->
{if $js_enabled}
<!-- Start addition by Michael de Leon 02.06.07 -->
<a href="javascript:document.errorform.submit()"><input src="{$ImagesDir}/wwmp_logingobtn10.31.06.jpg" type="image"></a>
<!-- End addition by Michael de Leon 02.06.07 -->
<!-- Deleted by Michael de Leon 02.06.07
{* include file="buttons/submit.tpl" href="javascript:document.errorform.submit()" js_to_href="Y" type="input" style="button" *}
-->
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_submit}
{/if}
</td>
</tr>

<!-- Deleted by Michael de Leon 02.06.07
</table>

</form>

<div align="right">{* include file="buttons/button.tpl" href="help.php?section=Password_Recovery" button_title=$lng.lbl_recover_password *}</div>
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
	<td align="left" height="20" width="100%">
	<a class="wwmp_vertmenulink" href="help.php?section=Password_Recovery" target="_self">Forgot your username or password?</a>
	</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
{if $usertype eq "C" && !$is_flc}
<!-- Deleted by Michael de Leon 02.06.07
<br />{* $lng.txt_new_account_msg *}
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
	<td align="left" height="5"></td>
</tr>
<tr>
	<td align="left" height="15" width="100%"><a href="register.php" class="wwmp_vertmenulink">Create a new account?</a></td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
{elseif $usertype eq "P"}
<!-- Deleted by Michael de Leon 02.06.07
<br />{* $lng.txt_create_account_msg *}
-->
<!-- Start addition by Michael de Leon 02.06.07 -->
<tr>
	<td align="left" height="10" width="100%">{$lng.txt_create_account_msg}</td>
</tr>
<!-- End addition by Michael de Leon 02.06.07 -->
{/if}

<!-- Start addition by Michael de Leon 02.06.07 -->
</table>
</div>

</form>
<!-- End addition by Michael de Leon 02.06.07 -->