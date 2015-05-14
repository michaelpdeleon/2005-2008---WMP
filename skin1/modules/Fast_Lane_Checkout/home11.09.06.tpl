{* $Id: home.tpl,v 1.4.2.1 2006/07/05 09:36:32 svowl Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html>
<head>
<title>
{if $config.SEO.page_title_format eq "A"}
{section name=position loop=$location}
{$location[position].0|escape}
{if not %position.last%} :: {/if}
{/section}
{else}
{section name=position loop=$location step=-1}
{$location[position].0|escape}
{if not %position.last%} :: {/if}
{/section}
{/if}
</title>
{ include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
<link rel="stylesheet" href="{$SkinDir}/modules/Fast_Lane_Checkout/{#CSSFile#}" />
</head>
<body>
{ include file="rectangle_top.tpl" }
{ include file="head.tpl" }
{if $active_modules.SnS_connector}
{include file="modules/SnS_connector/header.tpl"}
{/if}
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" width="150"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="6">&nbsp;</td>
<td valign="top" align="center">
<table cellpadding="0" cellspacing="0" width="700">
<tr>
<td align="left">
<!-- central space -->

{if $checkout_step ge 0}

{include file="modules/Fast_Lane_Checkout/tabs_menu.tpl"}

{include file="dialog_message.tpl"}

{include file="modules/Fast_Lane_Checkout/home_main.tpl"}

{else}

{include file="dialog_message.tpl"}

{include file="modules/Fast_Lane_Checkout/home_main.tpl"}

{/if}

<!-- /central space -->
&nbsp;
</td>
</tr>
</table>
</td>
<td width="6">&nbsp;</td>
<td valign="top" width="150"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /> </td>
</tr>
</table>
{ include file="rectangle_bottom.tpl" }
</body>
</html>
