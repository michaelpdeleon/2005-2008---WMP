{* $Id: login_error.tpl,v 1.6 2004/05/31 10:52:01 max Exp $ *}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_login_error}

{$lng.lbl_remote_addr|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$smarty.server.REMOTE_ADDR}
{$lng.lbl_http_x_forwarded_for|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$smarty.server.HTTP_X_FORWARDED_FOR}

{include file="mail/signature.tpl"}
