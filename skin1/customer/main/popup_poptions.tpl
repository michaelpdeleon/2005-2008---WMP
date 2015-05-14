{* $Id: popup_poptions.tpl,v 1.16.2.2 2006/07/11 08:39:27 svowl Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
<title>{$lng.txt_site_title}</title>
{ include file="meta.tpl" }
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
<script type="text/javascript" language="JavaScript 1.2">
<!--
var min_avail = {$min_avail|default:0};
var avail = {math equation="x+1" x=$min_avail};
var product_avail = avail;
var txt_out_of_stock = "{$lng.txt_out_of_stock|replace:"\n":"<br />"|replace:"\r":" "|replace:'"':'\"'}";

{literal}
function FormValidation() {

    if(!check_exceptions()) {
        alert(exception_msg);
        return false;
    } else if (min_avail > avail) {
		alert(txt_out_of_stock);
		return false;
	}
{/literal}
	{if $product_options_js ne ''}
	{$product_options_js}
	{/if}
{literal}

    return true;
}
{/literal}
-->
</script>
</head>
<body{$reading_direction_tag}>
<table width="100%" cellpadding="0" cellspacing="0" align="center" class="Container">
<tr>
	<td class="PopupTitle">{$lng.lbl_edit_options}</td>
</tr>
<tr>
	<td height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="PopupBG" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{if $err ne ''}
<tr> 
    <td align="center"><br /><font class="Star">{if $err eq 'exception'}{$lng.txt_exception_warning}{elseif $err eq 'avail'}{$lng.txt_out_of_stock}{/if}</font><br /></td>
</tr> 
{/if}
<tr>
	<td class="Container">

<form action="popup_poptions.php" method="post" name="orderform" onsubmit="return FormValidation();">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="target" value="{$target}" />
<input type="hidden" name="eventid" value="{$eventid}" />

	<table cellspacing="20" cellpadding="0">
	<tr>
		<td>

	<table cellspacing="3" cellpadding="0">
{ include file="modules/Product_Options/customer_options.tpl"}
	<tr>
		<td>&nbsp;</td>
		<td class="SubmitBox"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
	</tr>
	</table>

		</td>
	</tr>
	</table>

</form>

	</td>
</tr>

<tr>
	<td valign="bottom">{include file="popup_bottom.tpl"}</td>
</tr>
</table>
</body>
</html>
