{* $Id: cc_psclient.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>PaySystems Client</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_psclient_binarypath}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /><br />
{$lng.lbl_cc_psclient_binarypath_note}
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="CAD"{if $module_data.param02 eq "CAD"} selected="selected"{/if}>Canadian Dollar (Canada)
<option value="EUR"{if $module_data.param02 eq "EUR"} selected="selected"{/if}>Euro (Europe)
<option value="FRF"{if $module_data.param02 eq "FRF"} selected="selected"{/if}>French Franc (France)
<option value="GBP"{if $module_data.param02 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)
<option value="USD"{if $module_data.param02 eq "USD"} selected="selected"{/if}>US Dollar (United States)
</select>
</td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
