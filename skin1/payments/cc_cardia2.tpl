{* $Id: cc_cardia2.tpl,v 1.9.2.3 2006/07/17 11:15:52 max Exp $ *}
<h3>Cardia Shop</h3>
{$lng.txt_cc_configure_top_text}
{$lng.lbl_cc_cardia_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_c2_token}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_c2_store}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option value="NOK"{if $module_data.param03 eq "NOK"} selected="selected"{/if}>Norwegian kroner
<option value="SEK"{if $module_data.param03 eq "SEK"} selected="selected"{/if}>Swedish kroner
<option value="DKK"{if $module_data.param03 eq "DKK"} selected="selected"{/if}>Danish kroner
<option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro
<option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>British pounds
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_c2_skipFirstPage}:</td>
<td>
<select name="param04">
<option value="false"{if $module_data.param04 eq "false"} selected="selected"{/if}>{$lng.lbl_cc_false}
<option value="true"{if $module_data.param04 eq "true"} selected="selected"{/if}>{$lng.lbl_cc_true}
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_c2_skipLastPage}:</td>
<td>
<select name="param05">
<option value="false"{if $module_data.param05 eq "false"} selected="selected"{/if}>{$lng.lbl_cc_false}
<option value="true"{if $module_data.param05 eq "true"} selected="selected"{/if}>{$lng.lbl_cc_true}
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_c2_isOnHold}:</td>
<td>
<select name="param06">
<option value="false"{if $module_data.param06 eq "false"} selected="selected"{/if}>{$lng.lbl_cc_false}
<option value="true"{if $module_data.param06 eq "true"} selected="selected"{/if}>{$lng.lbl_cc_true}
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_c2_useThirdPartySecurity}:</td>
<td>
<select name="param07">
<option value="false"{if $module_data.param07 eq "false"} selected="selected"{/if}>{$lng.lbl_cc_false}
<option value="true"{if $module_data.param07 eq "true"} selected="selected"{/if}>{$lng.lbl_cc_true}
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param08" size="32" value="{$module_data.param08|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
