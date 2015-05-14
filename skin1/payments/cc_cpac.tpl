{* $Id: cc_cpac.tpl,v 1.8.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>LaCaixa</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_cpac_note|substitute:"http_location":$http_location}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">

<tr>
<td>{$lng.lbl_cc_cpac_path}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_cpac_java}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>


<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="XEU"{if $module_data.param02 eq "XEU"} selected="selected"{/if}>Euro
</select>
</tr>
<tr>
<td>{$lng.lbl_cc_cpac_language}:</td>
<td>
<select name="param03">
<option value="esp"{if $module_data.param03 eq "esp"} selected="selected"{/if}>Spanish
<option value="cat"{if $module_data.param03 eq "cat"} selected="selected"{/if}>Catalan
<option value="eng"{if $module_data.param03 eq "eng"} selected="selected"{/if}>English
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /><br />{$lng.lbl_cc_cpac_digits_only}</td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
