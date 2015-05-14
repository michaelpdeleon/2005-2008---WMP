{* $Id: giftcert_return.tpl,v 1.3 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":$giftcert.recipient},

{$lng.eml_rma_giftcert_note|substitute:"returnid":$returnid:"amount":$giftcert.amount}


{$lng.lbl_message}:
{$giftcert.message}

+--------------------------------------------+
|                                            |
|   {$lng.lbl_gc_id}: {$giftcert.gcid}    
|                                            |
+--------------------------------------------+

{$lng.eml_gc_body}

{include file="mail/signature.tpl"}
