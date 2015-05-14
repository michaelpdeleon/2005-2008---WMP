{* $Id: secure_login_form.tpl,v 1.9 2005/12/05 15:00:44 max Exp $ *}
{$lng.txt_secure_login_form}
<p />
{capture name=dialog}
<form action="{$https_location}/include/login.php" method="post" name=secureform>

<table>
<tr> 
	<td height="10" width="78" class="FormButton">{$lng.lbl_login}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="text" name="username" size="30" /></td>
</tr>
<tr> 
	<td height="10" width="78" class="FormButton">{$lng.lbl_password}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"> 
<input type="password" name="password" size="30" />
{if $active_modules.Simple_Mode ne "" and $usertype ne "C"}
<input type="hidden" name="usertype" value="P" />
{else}
<input type="hidden" name="usertype" value="{$usertype}" />
{/if}
<input type="hidden" name="redirect" value="{$redirect}" />
<input type="hidden" name="mode" value="login" />
	</td>
</tr>
<tr> 
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10">&nbsp;</td>
	<td width="282" height="10" class="ErrorMessage">
</td>
</tr>
<tr> 
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10" class="FormButton">&nbsp;</td>
	<td width="282" height="10">
{if $js_enabled}
{include file="buttons/submit.tpl" href="javascript:document.secureform.submit()" js_to_href="Y"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_submit}
{/if}
	</td>
</tr>
</table>
</form>
<p />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_secure_login_form content=$smarty.capture.dialog extra='width="100%"'}
