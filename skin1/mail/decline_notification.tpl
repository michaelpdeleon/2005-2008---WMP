{* $Id: decline_notification.tpl,v 1.17 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":"`$customer.title` `$customer.firstname` `$customer.lastname`"},

{$lng.eml_order_declined}

{$lng.lbl_order_id|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}#{$order.orderid}
{$lng.lbl_order_date|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$order.date|date_format:$config.Appearance.datetime_format}

{include file="mail/order_data.tpl"}

{include file="mail/signature.tpl"}
