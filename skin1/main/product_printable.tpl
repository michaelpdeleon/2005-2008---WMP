{* $Id: product_printable.tpl,v 1.15 2005/11/30 13:29:35 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
	<title>{$lng.txt_site_title}</title>
	{ include file="meta.tpl" }
	<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
</head>
<body>
<table cellpadding="10" cellspacing="10">
<tr>
	<td>
{if $active_modules.Product_Configurator and $main eq "product_configurator"}
{include file="modules/Product_Configurator/product.tpl"}
{else}
{include file="main/product.tpl"}
{/if}
	</td>
</tr>
</table>
</body>
</html>
