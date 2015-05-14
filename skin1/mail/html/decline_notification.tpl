{* $Id: decline_notification.tpl,v 1.9 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":"`$customer.title` `$customer.firstname` `$customer.lastname`"},

<p />{$lng.eml_order_declined}

<hr size="1" noshade="noshade" />
<p />
<table cellpadding="2" cellspacing="1" width="100%">
<tr>
<td width="20%"><b>{$lng.lbl_order_id}:</b></td>
<td width="10">&nbsp;</td>
<td width="80%"><tt><b>#{$order.orderid}</b></tt></td>
</tr>
<tr>
<td><b>{$lng.lbl_order_date}:</b></td>
<td width="10">&nbsp;</td>
<td><tt><b>{$order.date|date_format:$config.Appearance.datetime_format}</b></tt></td>
</tr>

<tr>
	<td colspan="3">{include file="mail/html/order_data.tpl"}</td>
</tr>
</table>

{include file="mail//html/signature.tpl"}
