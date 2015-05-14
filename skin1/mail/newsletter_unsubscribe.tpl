{* $Id: newsletter_unsubscribe.tpl,v 1.8 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_unsubscribed}

{include file="mail/signature.tpl"}
