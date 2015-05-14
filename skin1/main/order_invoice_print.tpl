{* $Id: order_invoice_print.tpl,v 1.13 2006/04/07 14:02:00 svowl Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
{include file="meta.tpl" }
<style type="text/css">
<!--
BODY {ldelim}
    FONT-FAMILY: Verdana, Arial, Helvetica, Sans-serif;
    FONT-SIZE: 11px;
    MARGIN: 10px;
    PADDING: 10px;
{rdelim}
-->
</style>
</head>
<body>
{if $config.Appearance.print_orders_separated eq "Y"}
{assign var="separator" value="<div style='page-break-after: always;'></div>"}
{else}
{assign var="separator" value="<br /><hr size='1' noshade="noshade" /><br />"}
{/if}
{section name=oi loop=$orders_data}
{ include file="mail/html/order_invoice.tpl" order=$orders_data[oi].order customer=$orders_data[oi].customer products=$orders_data[oi].products giftcerts=$orders_data[oi].giftcerts}

{if not %oi.last%}
{$separator}
{/if}

{/section}
</body>
</html>
