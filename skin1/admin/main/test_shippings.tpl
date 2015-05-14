{* $Id: test_shippings.tpl,v 1.25 2005/11/30 13:29:35 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html>
<head>
	<title>{$lng.lbl_test_destination_shipping_address}</title>
	{ include file="meta.tpl" }
	<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
</head>
<body{$reading_direction_tag}>
{ include file="head_admin.tpl" }
<!-- main area -->
{include file="check_zipcode_js.tpl"}

<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
function check_zip_code_local(flag) {
	if (!flag) {
		return check_zip_code_field(document.forms["registerform"].s_country, document.forms["registerform"].s_zipcode); 
	} else {
		return check_zip_code_field(document.getElementById('o_country'), document.getElementById('o_zipcode'));
	}
}
-->
{/literal}
</script>

{if $config.Shipping.realtime_shipping ne "Y"}
<table width="100%" cellpadding="10" cellspacing="10">
<tr>
	<td><h2>{$lng.txt_realtime_cals_is_disabled}</h2></td>
</tr>
</table>
{else}

<table width="100%" cellpadding="0" cellspacing="10">
<tr>
	<td align="left">

{capture name=dialog}
<form action="test_realtime_shipping.php" method="post" name="registerform" onsubmit="javascript: return check_zip_code_local()">

<table cellpadding="1" cellspacing="1" width="400">

<tr valign="middle">
	<td height="20" colspan="3"><b>{$lng.lbl_origin_address}:</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
	<td align="right" width="25%">{$lng.lbl_city}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap" width="75%"><input type="text" name="origin[city]" size="32" maxlength="64" value="{$config.Company.location_city}" /></td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_state}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">{include file="main/states.tpl" states=$states name="origin[state]" default=$config.Company.location_state default_country=$config.Company.location_country}</td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_country}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<select id="o_country" name="origin[country]" size="1" onchange="javascript: check_zip_code_local(true);">
{foreach from=$countries item=c}
		<option value="{$c.country_code}"{if $config.Company.location_country eq $c.country_code} selected="selected"{/if}>{$c.country}</option>
{/foreach}
	</select>
	</td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_zip_code}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<input type="text" id="o_zipcode" name="origin[zipcode]" size="32" maxlength="32" value="{$config.Company.location_zipcode}" onchange="javascript: check_zip_code_local(true);" />
	</td>
</tr>

<tr valign="middle">
	<td height="20" colspan="3">&nbsp;</td>
</tr>

<tr valign="middle">
	<td height="20" colspan="3"><b>{$lng.lbl_destination_address}:</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
	<td align="right" width="25%">{$lng.lbl_city}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap" width="75%">
	<input type="text" name="s_city" size="32" maxlength="64" value="{$userinfo.s_city}" />
	</td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_state}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	{include file="main/states.tpl" states=$states name="s_state" default=$userinfo.s_state default_country=$userinfo.s_country}
	</td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_country}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<select name="s_country" size="1" onchange="check_zip_code_local()">
{section name=country_idx loop=$countries}
		<option value="{$countries[country_idx].country_code}"{if $userinfo.s_country eq $countries[country_idx].country_code || ($countries[country_idx].country_code eq $config.General.default_country and $userinfo.s_country eq "")} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
	</select>
	</td>
</tr>

<tr valign="middle">
	<td align="right">{$lng.lbl_zip_code}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<input type="text" name="s_zipcode" size="32" maxlength="32" value="{$userinfo.s_zipcode}" onchange="check_zip_code_local()" />
	</td>
</tr>

{if $show_arb_account_field}
<tr>
	<td align="right">{$lng.lbl_arb_account}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<input type="text" id="s_arb_account" name="s_arb_account" size="32" maxlength="32" value="{$airborne_account}" />
	</td>
</tr>
{/if}

<tr valign="middle">
	<td height="20" colspan="3"><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
	<td align="right" nowrap="nowrap">{$lng.lbl_weight} {if $config.General.weight_symbol}({$config.General.weight_symbol}){/if}:</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap" width="75%"><input type="text" name="weight" value="{$weight}" /></td>
</tr>

{if $active_modules.UPS_OnLine_Tools and $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y"}
<tr>
	<td align="right" nowrap="nowrap">{$lng.lbl_shipping_carrier}:</td>
	<td>&nbsp;</td>
	<td>
	<select name="selected_carrier">
		<option value="UPS" {if $current_carrier eq "UPS"}selected{/if}>{$lng.lbl_ups_carrier}</option>
		<option value="" {if $current_carrier ne "UPS"}selected{/if}>{$lng.lbl_other_carriers}</option>
	</select>
	</td>
</tr>
{/if}

<tr valign="middle">
	<td colspan="2">&nbsp;</td>
	<td nowrap="nowrap"><br />
{if $js_enabled}
{include file="buttons/submit.tpl" href="javascript: if(check_zip_code_local()) document.registerform.submit()" js_to_href="Y"}
{else}
{include file="submit_wo_js.tpl" value=$lng.lbl_submit}
{/if}
	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_test_destination_shipping_address content=$smarty.capture.dialog}
	</td>
</tr>
<tr>
	<td><hr noshade="noshade" style="COLOR: #FF8600; HEIGHT: 1px;" /></td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="10">
<tr>
	<td>{$content}</td>
</tr>
</table>

{/if}
<!-- main area -->
</body>
</html>
