{* $Id: service_header.tpl,v 1.6 2005/12/23 13:49:15 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
	<title>{$lng.txt_site_title}</title>
	{ include file="meta.tpl" }
	<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
</head>
<body>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="HeadLogo"><a href="{$http_location}"><img src="{$ImagesDir}/admin_xlogo.gif" width="244" height="67" alt="" /></a></td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="HeadLine" height="22"><img src="{$ImagesDir}/spacer.gif" width="1" height="22" alt="" /></td>
</td>
</tr>
<tr>
	<td class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>
<br />
