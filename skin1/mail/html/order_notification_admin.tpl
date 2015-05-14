{* $Id: order_notification_admin.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

{assign var=where value="A"}

<p />{$lng.eml_order_notification|substitute:"orderid":$order.orderid}

{include file="mail/html/order_invoice.tpl" to_admin="Y"}

{include file="mail/html/signature.tpl"}
