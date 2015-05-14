{* $Id: preview.tpl,v 1.5 2005/11/17 06:55:37 max Exp $ *}
{ config_load file="$skin_config" }
<html>
<head>
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
</head>
<body>
<table cellpadding="10" cellspacing="10" width="100%">
<tr><td>
{assign var="this_is_printable_version" value="Y"}
{include file=$template}
</td></tr>
<tr>
<td align="right">
{include file="buttons/button.tpl" button_title=$lng.lbl_close_window href="javascript:window.close()"}
</td>
</tr>
</table>
</body>
