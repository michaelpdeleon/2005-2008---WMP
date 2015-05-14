{* $Id: order_customer.tpl,v 1.19 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

{$lng.eml_thankyou_for_order}

{$lng.lbl_invoice}:

{include file="mail/order_invoice.tpl"}

{include file="mail/signature.tpl"}
