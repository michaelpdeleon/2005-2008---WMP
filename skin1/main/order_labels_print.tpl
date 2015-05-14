{* $Id: order_labels_print.tpl,v 1.7 2005/12/05 08:16:18 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
{include file="meta.tpl" }
</head>
<body>
{section name=oi loop=$orders_data}
{assign var=order value=$orders_data[oi].order}
{assign var=customer value=$orders_data[oi].customer}
{assign var=products value=$orders_data[oi].products}
{assign var=giftcerts value=$orders_data[oi].giftcerts}
<pre>{include file="main/order_label_print.tpl"}</pre>
======================================================
{/section}
</body>
</html>
