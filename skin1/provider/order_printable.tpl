{* $Id: order_printable.tpl,v 1.29 2005/12/15 12:59:15 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
<title>{$lng.txt_site_title}</title>
{ include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
</head>
<body>
<center>
<br />
{capture name=dialog}
{$lng.lbl_date}: {$order.date|date_format:$config.Appearance.datetime_format}
<p />

{include file="main/subheader.tpl" title=$lng.lbl_product_info}

<table cellspacing="0" cellpadding="0" width="400">

{foreach from=$products item=product}
<tr> 
	<td colspan="2" valign="top" class="ProductTitle">{$product.product|escape}</td>
</tr>
<tr> 
	<td valign="top" colspan="2" class="Text">&nbsp;</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_price}</td>
	<td valign="top" class="Text">{include file="currency.tpl" value=$product.price}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_quantity}</td>
	<td valign="top" class="Text">{$product.amount} {$lng.lbl_items}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_delivery}</td>
	<td valign="top" class="Text">{$order.shipping|trademark}</td>
</tr>
<tr>
	<td valign="top" class="Text">{$lng.lbl_shipping_cost}</td>
	<td valign="top" class="Text">{include file="currency.tpl" value=$order.shipping_cost}</td>
</tr>
<tr> 
	<td valign="top" class="ProductDetails" height="14">&nbsp;</td>
	<td valign="top" class="ProductDetails" height="14">&nbsp;</td>
</tr>
{/foreach}
<tr> 
	<td valign="top" class="ProductDetailsTitle">{$lng.lbl_customer_info}</td>
	<td>&nbsp;</td>
</tr>
<tr> 
	<td class="Line" colspan="2" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr> 
	<td valign="top" colspan="2" class="ProductDetails"></td>
</tr>
<tr> 
	<td valign="top" colspan="2" class="ProductDetails" height="10">&nbsp;</td>
</tr>
<tr valign="top"> 
	<td class="Text">{$lng.lbl_title}</td>
	<td class="Text">{$customer.title}</td>
</tr>
<tr valign="top"> 
	<td class="Text">{$lng.lbl_first_name}</td>
	<td class="Text">{$customer.firstname}</td>
</tr>
<tr valign="top"> 
	<td class="Text">{$lng.lbl_last_name}</td>
	<td class="Text">{$customer.lastname}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_address}</td>
	<td valign="top" class="Text">{$customer.b_address}<br />{$customer.b_address_2}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_zip_code}</td>
	<td valign="top" class="Text">{$customer.b_zipcode}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_city}</td>
	<td valign="top" class="Text">{$customer.b_city}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_phone}</td>
	<td valign="top" class="Text">{$customer.phone}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_fax}</td>
	<td valign="top" class="Text">{$customer.fax}</td>
</tr>
<tr> 
	<td valign="top" class="Text">{$lng.lbl_email}</td>
	<td valign="top"><a href="mailto:{$customer.email}"><font class="Text">{$customer.email}</font></a></td>
</tr>
<tr> 
	<td colspan="2" valign="top" class="Text" height="10">&nbsp;</td>
</tr>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_order_details_label content=$smarty.capture.dialog extra="width=300"}
</center>
</body>
</html>
