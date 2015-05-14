{* $Id: giftcert_notification.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}

{$lng.eml_gc_notification|substitute:"recipient":$giftcert.recipient}

{$lng.eml_gc_copy_sent|substitute:"email":$giftcert.recipient_email}:

=== /start/ ==============================================================

{include file="mail/giftcert.tpl"}


=== /end/ ================================================================

{include file="mail/signature.tpl"}
