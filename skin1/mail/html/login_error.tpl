{* $Id: login_error.tpl,v 1.5 2005/11/28 14:19:29 max Exp $ *}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_login_error}

<p /> 
<table cellpadding="2" cellspacing="1">
<tr>
<td width="20%"><b>{$lng.lbl_remote_addr}:</b></td> 
<td width="10">&nbsp;</td> 
<td>{$smarty.server.REMOTE_ADDR}</td>
</tr>
<tr>
<td><b>{$lng.lbl_http_x_forwarded_for}:</b></td>
<td>&nbsp;</td>
<td>{$smarty.server.HTTP_X_FORWARDED_FOR}</td>
</tr>
</table>

{include file="mail/html/signature.tpl"}
