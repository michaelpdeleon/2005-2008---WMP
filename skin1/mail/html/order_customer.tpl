{* $Id: order_customer.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

<p />{$lng.eml_thankyou_for_order}

<p /><b>{$lng.lbl_invoice}:</b>

<p />{include file="mail/html/order_invoice.tpl"}

{include file="mail/html/signature.tpl"}
