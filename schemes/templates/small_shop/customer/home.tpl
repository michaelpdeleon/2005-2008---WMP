{* $Id: home.tpl,v 1.9 2005/12/13 15:01:06 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{if $printable ne ''}
{include file="customer/home_printable.tpl"}
{else}
{config_load file="$skin_config"}
<html>
<head>
<title>
{if $config.SEO.page_title_format eq "A"}
{section name=position loop=$location}
{$location[position].0|strip_tags|escape}
{if not %position.last%} :: {/if}
{/section}
{else}
{section name=position loop=$location step=-1}
{$location[position].0|strip_tags|escape}
{if not %position.last%} :: {/if}
{/section}
{/if}
</title>
{include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
</head>
<body{$reading_direction_tag}{if $body_onload ne ''} onload="javascript: {$body_onload}"{/if}>
{include file="rectangle_top.tpl" }
{include file="head.tpl" }
{if $active_modules.SnS_connector}
{include file="modules/SnS_connector/header.tpl"}
{/if}
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="20">&nbsp;</td>
<td valign="top">
<!-- central space -->
{include file="location.tpl"}

{include file="dialog_message.tpl"}

{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/customer/new_offers_message.tpl"}
{/if}

{include file="customer/home_main.tpl"}
<!-- /central space -->
&nbsp;
</td>
<td class="VertMenuRightColumn">
{if $active_modules.SnS_connector && $config.SnS_connector.sns_display_button eq 'Y'}
{include file="modules/SnS_connector/button.tpl"}<br />
<br />
{/if}
{ include file="customer/menu_cart.tpl" }
<br />
{if $login eq "" }
{ include file="auth.tpl" }
{else}
{ include file="authbox.tpl" }
{/if}
<br />
{ include file="help.tpl" }
<br />
<br />
{ include file="poweredby.tpl" }
<br />
<img src="{$ImagesDir}/spacer.gif" width="150" height="1" alt="" />
</td>
</tr>
</table>
{include file="rectangle_bottom.tpl" }
</body>
</html>
{/if}
