{* $Id: popup_info.tpl,v 1.23 2006/01/30 14:40:33 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
<title>{$lng.txt_site_title}</title>
{ include file="meta.tpl" }
{if $usertype ne "C"}
<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
{else}
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
{/if}
</head>
{if $force_height eq 0}{assign var="force_height" value=460}{/if}
<body{$reading_direction_tag}>
{include file="presets_js.tpl"}
{include file="main/include_js.tpl" src="common.js"}
<table width="100%" cellpadding="0" cellspacing="0" align="center" height="{math equation="x-2" x=$force_height}">
<tr>
	<td class="PopupTitle">{$popup_title|default:"&nbsp;"}</td>
</tr>
<tr>
	<td height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="PopupBG" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
	<td height="{math equation="x-82" x=$force_height}" valign="top">
<table width="100%" cellpadding="15" cellspacing="0">
<tr>
	<td>

{include file="dialog_message.tpl"}

{if $template_name ne ""}
{include file=$template_name}

{elseif $pre ne ""}
{$pre}

{else}
{include file="main/error_page_not_found.tpl"}
{/if}

	</td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td>{include file="popup_bottom.tpl"}</td>
</tr>
</table>
</body>
</html>
