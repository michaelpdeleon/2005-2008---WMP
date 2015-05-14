{* $Id: init_order_customer.tpl,v 1.8 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

<p />{$lng.eml_init_order_customer}

<p />{$lng.lbl_order_details_label}:

<p />
<table cellpadding="2" cellspacing="1">
<tr>
<td width="20%"><b>{$lng.lbl_order_id}:</b></td>
<td width="10">&nbsp;</td>
<td>#{$order.orderid}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_date}:</b></td>
<td>&nbsp;</td>
<td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_status}:</b></td>
<td>&nbsp;</td>
<td>{include file="main/order_status.tpl" mode="static" status=$order.status}</td>
</tr>
</table>

{include file="mail/html/signature.tpl"}
