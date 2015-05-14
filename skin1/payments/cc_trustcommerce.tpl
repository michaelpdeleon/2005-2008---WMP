{* $Id: cc_trustcommerce.tpl,v 1.7.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>TrustCommerce: </h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_trustcommerce_customerid}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_trustcommerce_password}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param05">
<option value="eur"{if $module_data.param05 eq "eur"} selected="selected"{/if}>Euro (Europe)
<option value="usd"{if $module_data.param05 eq "usd"} selected="selected"{/if}>US Dollar (United States)
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}
</select>
</td>
</tr>


<tr>
<td>{$lng.lbl_cc_trustcommerce_avs}:</td>
<td>
<select name="param06">
<option value="Y"{if $module_data.param06 eq "Y"} selected="selected"{/if}>Y
<option value="N"{if $module_data.param06 eq "N"} selected="selected"{/if}>N
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_trustcommerce_operator}:</td>
<td><input type="text" name="param07" size="36" value="{$module_data.param07|escape}" /></td>
</tr>

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
