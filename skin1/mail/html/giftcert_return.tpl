{* $Id: giftcert_return.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":$giftcert.recipient},

<p />
{$lng.eml_rma_giftcert_note|substitute:"returnid":$returnid:"amount":$giftcert.amount}

<p />{$lng.lbl_message}:
<br />
{$giftcert.message}

<p />
<table border="1" cellpadding="20" cellspacing="0">
<tr><td>{$lng.lbl_gc_id}: {$giftcert.gcid}</td></tr>
</table>

<p /><pre>{$lng.eml_gc_body}</pre>

{include file="mail/html/signature.tpl"}
