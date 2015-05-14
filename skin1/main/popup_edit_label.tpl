<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html>
<head>
<title>{$lng.lbl_label_dialog}</title>
<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
{include file="presets_js.tpl"}
{include file="main/include_js.tpl" src="common.js"}
{include file="main/include_js.tpl" src="main/popup_edit_label.js"}
{if $active_modules.HTML_Editor && $tarea}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}
<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
<style>
{literal}
BODY {
	MARGIN: 5px;
	PADDING: 0px;
	BACKGROUND-COLOR: #FFFBD3;
}
IMG.Icon {
	BORDER: 0px;
	VERTICAL-ALIGN: middle;
	WIDTH: 23px;
	HEIGHT: 22px;
}
.Head {
	FONT-SIZE: 12px;
	FONT-WEIGHT: bold;
}
#labelName {
	FONT-SIZE: 12px;
	PADDING-LEFT: 10px;
}
{/literal}
</style>
</head>
<body onload="javascript: getData();" onunload="javascript: rememberXY();">

<form name="lf" action="{$catalogs.admin}/set_label.php" method="post" accept-charset="{$default_charset|default:"iso-8859-1"}" onsubmit="javascript: copyText();">
<input type="hidden" name="lang" value="{$shop_language}" />
<input type="hidden" name="name" value="" />

<table cellspacing="0" cellpadding="0" id="tbl">
<tr>
	<td class="Head">{$lng.lbl_name}:</td>
	<td id="labelName"></td>
</tr>
<tr>
	<td class="Head" valign="top">{$lng.lbl_value}:</td>
	<td style="padding-left: 10px;" valign="top">
{if $tarea}
{if $config.UA.browser eq 'MSIE'}
{include file="main/textarea.tpl" cols=50 rows=10 data="" name="val" width="460px" style="width: 460px;"}
{else}
{include file="main/textarea.tpl" cols=50 rows=5 data="" name="val" width="460px" style="width: 460px;" btn_rows=4}
{/if}
{else}
<input type="text" name="val" size="50" />
{/if}
</td>
</tr>
<tr>
	<td colspan="2" align="center">
<a href="javascript: copyText();"><img class="Icon" src="{$ImagesDir}/preview.gif" alt="" />&nbsp;{$lng.lbl_preview}</a>
&nbsp;&nbsp;&nbsp;
<a href="javascript: copyText(); document.lf.submit();"><img class="Icon" src="{$ImagesDir}/save.gif" alt="" />&nbsp;{$lng.lbl_save}</a>
&nbsp;&nbsp;&nbsp;
<a href="javascript: restoreLabel(); window.close();"><img class="Icon" src="{$ImagesDir}/cancel.gif" alt="" />&nbsp;{$lng.lbl_cancel}</a>
	</td>
</tr>
</table>
</form>

</body>
</html>
