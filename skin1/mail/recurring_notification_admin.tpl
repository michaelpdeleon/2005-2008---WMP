{* $Id: recurring_notification_admin.tpl,v 1.6 2006/03/17 14:50:41 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.txt_billing_notification|strip_tags}

{$str}

{include file="mail/signature.tpl"}
