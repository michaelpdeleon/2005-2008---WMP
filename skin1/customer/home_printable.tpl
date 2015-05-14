{* $Id: home_printable.tpl,v 1.8 2006/01/12 14:23:10 max Exp $ *}
{ config_load file="$skin_config" }
<html>
<head>
<title>
{if $main eq "catalog"}
	{if $location eq ""}  {$lng.txt_subtitle_home}
	{else}
	{strip}
	{section name=position loop=$location start=0 }
	{if %position.last% eq "true"}{$location[position].0|escape} - {/if}
	{/section}
	{/strip}
	{/if}
{elseif $main eq "product"}
	{if $product.product ne ''}{$product.product} - {/if}
{elseif $main eq "help"}
{$lng.txt_subtitle_help}
{elseif $main eq "cart"}
{$lng.txt_subtitle_cart}
{elseif $main eq "checkout"}
{$lng.txt_subtitle_checkout}
{elseif $main eq "order_message"}
{$lng.txt_subtitle_thankyou}
{elseif $main eq "wishlist"}
{$lng.txt_subtitle_wishlist}
{elseif $main eq "giftcert"}
{$lng.txt_subtitle_giftcerts}
{/if}
{$lng.txt_site_title}
</title>
{ include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/skin1_printable.css" />
</head>
<body{$reading_direction_tag}>
{ include file="rectangle_top.tpl" width="700" }
{ include file="head_printable.tpl" }
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="Central">
<!-- central space -->
{include file="location.tpl"}

{include file="dialog_message.tpl"}

{include file="customer/home_main.tpl"}
<!-- /central space -->
&nbsp;
</td>
</tr>
</table>
{ include file="rectangle_bottom.tpl" }
</body>
</html>
