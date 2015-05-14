<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>{$lng.lbl_images_transferring_log}</title>
<style>
<!--
{literal}
BODY {
	FONT-FAMILY: Verdana, Arial, Helvetica, Sans-serif;
	COLOR: #550000;
	FONT-SIZE: 12px;
	MARGIN: 0px;
	PADDING: 0px;
	BACKGROUND-COLOR: #FFFBD3;
}
TABLE,IMG {
	BORDER: 0px;
}
TD.Line {
	BACKGROUND-COLOR: #FF8600;
}
TD.Header {
	HEIGHT: 40px;
	VERTICAL-ALIGN: middle;
	FONT-WEIGHT: bold;
}
{/literal}
-->
</style>
</head>
<body{$reading_direction_tag}>
<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td class="Line">&nbsp;</td>
</tr>
<tr>
	<td height="1"><table height="1" cellspacing="0" cellpadding="0"><td></td></table></td>
</tr>
<tr>
	<td class="Line" height="1"><table height="1" cellspacing="0" cellpadding="0"><td></td></table></td>
</tr>
<tr>
	<td class="Header">{$lng.lbl_images_transferring_log}</td>
</tr>
<tr>
<td>
<table cellpadding="5" cellspacing="0" width="0"><tr><td>
<!-- begin -->
<pre>
{if $incfile}
{$incfile}
{else}
{$lng.lbl_log_file_empty}
{/if}
</pre>
<!-- end -->
</td></tr></table>

</td>
</tr>
</table>
</body>
</html>
