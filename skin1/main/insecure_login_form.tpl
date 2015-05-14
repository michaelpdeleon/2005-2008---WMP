{* $Id: insecure_login_form.tpl,v 1.12 2005/11/30 13:29:35 max Exp $ *}
{$lng.txt_insecure_login_form}
<p />
{capture name=dialog}
<form action="{$http_location}/include/login.php" method="post" name="insecureform">

<table>
<tr> 
	<td height="10" width="78" class="FormButton">{$lng.lbl_username}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"> 
	<input type="text" name="username" size="30" value="{#default_login#}" />
	</td>
</tr>
<tr> 
	<td height="10" width="78" class="FormButton">{$lng.lbl_password}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"> 
<input type="password" name="password" size="30" value="{#default_password#}" />
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
{include file="buttons/submit.tpl" href="javascript: document.insecureform.submit()" js_to_href="Y"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_submit}
{/if}
	</td>
</tr>

</table>
</form>

<p />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_insecure_login_form content=$smarty.capture.dialog extra='width="100%"'}
