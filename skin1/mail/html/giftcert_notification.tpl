{* $Id: giftcert_notification.tpl,v 1.7 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_gc_notification|substitute:"recipient":$giftcert.recipient}

<p />{$lng.eml_gc_copy_sent|substitute:"email":$giftcert.recipient_email}:

<p />=========================| start |=========================

<table cellpadding="15" cellspacing="0" width="100%"><tr><td bgcolor="#EEEEEE">
{include file="mail/html/giftcert.tpl"}
</td></tr></table>

<p />=========================| end |=========================

{include file="mail/html/signature.tpl"}
