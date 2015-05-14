{* $Id: init_order_customer.tpl,v 1.10 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

{$lng.eml_init_order_customer}

{$lng.lbl_order_details_label}:

{$lng.lbl_order_id|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}#{$order.orderid}
{$lng.lbl_order_date|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$order.date|date_format:$config.Appearance.datetime_format}
{$lng.lbl_order_status|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="main/order_status.tpl" mode="static" status=$order.status}

{include file="mail/signature.tpl"}
