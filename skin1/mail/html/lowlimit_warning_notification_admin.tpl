{* $Id: lowlimit_warning_notification_admin.tpl,v 1.6.2.1 2006/07/17 07:17:16 max Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p />{$lng.eml_lowlimit_warning_message|substitute:"sender":$config.Company.company_name:"productid":$product.productid}

<table cellspacing="1" cellpadding="2">
<tr>
	<td>{$lng.lbl_sku}:</td>
	<td><b>{$product.productcode}</b></td>
</tr>
<tr>
	<td>{$lng.lbl_product}:</td>
	<td><b>{$product.product}</b></td>
</tr>
{if $product.product_options ne ""}
<tr>
	<td>{$lng.lbl_selected_options}:</td>
	<td>{include file="modules/Product_Options/display_options.tpl" options=$product.product_options options_txt=$product.product_options_txt}</td>
</tr>
{/if}
</table>

<p />{$lng.lbl_items_in_stock|substitute:"items":$product.avail}

{include file="mail/html/signature.tpl"}
