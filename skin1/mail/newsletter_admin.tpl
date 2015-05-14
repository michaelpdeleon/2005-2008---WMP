{* $Id: newsletter_admin.tpl,v 1.7 2006/03/17 14:50:41 svowl Exp $ *}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.txt_new_subscriber|strip_tags}

{$email}

{include file="mail/signature.tpl"}
