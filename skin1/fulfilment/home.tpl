{* $Id: home.tpl,v 1.28 2006/02/10 14:27:32 svowl Exp $ *}
{ config_load file="$skin_config" }
<html>
<head>
<title>{$lng.txt_site_title}</title>
{ include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0"{$reading_direction_tag}>
{ include file="rectangle_top.tpl" }
{ include file="head_admin.tpl" }
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
<td width="3">&nbsp;</td>
<td width="150" valign="top">
{if $login eq "" }
{ include file="auth.tpl" }
{else}
{ include file="fulfilment/menu.tpl" }
<br />
{if $active_modules.Simple_Mode ne ""}
{ include file="single/menu_profile.tpl" }
{else}
{ include file="menu_profile.tpl" }
{/if}
{/if}
<br />
{ include file="help.tpl" }
<img src="{$ImagesDir}/spacer.gif" width="150" height="1" border="0" />
</td>
<td width="20">&nbsp;</td>
<td valign="top">
<!-- central space -->
{include file="location.tpl"}

{include file="dialog_message.tpl"}

{if $smarty.get.mode eq "subscribed"}
{include file="main/subscribe_confirmation.tpl"}

{elseif $main eq "order_edit"}
{include file="modules/Advanced_Order_Management/order_edit.tpl"}

{elseif $main eq "statistics"}
{include file="admin/main/statistics.tpl"}

{elseif $smarty.get.mode eq "unsubscribed"}
{include file="main/unsubscribe_confirmation.tpl"}

{elseif $main eq "home" and $login ne ""}
{include file="main/orders.tpl"}

{elseif $main eq "home"}
{include file="admin/main/welcome.tpl"}

{elseif $main eq "register"}
{include file="admin/main/register.tpl"}

{elseif $main eq "change_mpassword"}
{include file="admin/main/change_mpassword.tpl"}

{elseif $main eq "change_password"}
{include file="main/change_password.tpl"}

{else}
{include file="common_templates.tpl"}
{/if}

<!-- /central space -->
&nbsp;
</td>
<td>
<img src="{$ImagesDir}/spacer.gif" width="20" height="1" border="0" />
</td>
</tr>
</table>
{ include file="rectangle_bottom.tpl" }
</body>
</html>
