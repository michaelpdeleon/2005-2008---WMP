{* $Id: authbox_top.tpl,v 1.1 2006/02/10 14:27:31 svowl Exp $ *}
<form action="{$xcart_web_dir}/include/login.php" method="post" name="loginform">
<div class="AuthText">{$login} {$lng.txt_logged_in}</div>
{include file="buttons/button.tpl" button_title=$lng.lbl_logoff href="javascript: document.loginform.submit();" js_to_href="Y" type="input"}
<input type="hidden" name="mode" value="logout" />
<input type="hidden" name="redirect" value="{$redirect}" />
</form>
