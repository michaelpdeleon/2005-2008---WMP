{* $Id: send2friend.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
{$lng.eml_hello}<br />
<br />
{$lng.eml_send2friend|substitute:"sender":$name}<br />
<br />
{$product.product}<br />
<hr />
{$product.descr}<br />
<br />
{$lng.lbl_price}: {include file="currency.tpl" value=$product.taxed_price}<br />
<br />
<br />
{$lng.eml_click_to_view_product}:<br />
<a href="{$catalogs.customer}/product.php?productid={$product.productid}">{$catalogs.customer}/product.php?productid={$product.productid}</a><br />
<br />
{include file="mail/html/signature.tpl"}
