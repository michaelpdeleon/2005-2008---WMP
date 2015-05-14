{* $Id: cc_basia.tpl,v 1.5.2.2 2006/07/11 08:39:36 svowl Exp $ *}
<h3>Bank of Asia</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_basia_return_url}:</td>
<td>{$http_location}/payment/{$module_data.processor}</td>
</tr>

<tr>
<td>{$lng.lbl_cc_basia_id}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_basia_path}:</td>
<td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>


<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param03">
<option {if $module_data.param03 eq "900"}selected{/if} value="900">BAHT</option>
<option {if $module_data.param03 eq "901"}selected{/if} value="901">US Dollar</option>
<option {if $module_data.param03 eq "902"}selected{/if} value="902">YEN</option>
<option {if $module_data.param03 eq "903"}selected{/if} value="903">Singapure Dollar</option>
<option {if $module_data.param03 eq "904"}selected{/if} value="904">HongKong Dollar</option>
<option {if $module_data.param03 eq "905"}selected{/if} value="905">EURO</option>
<option {if $module_data.param03 eq "906"}selected{/if} value="906">Pound Sterling</option>
<option {if $module_data.param03 eq "907"}selected{/if} value="907">Australian Dollar</option>
<option {if $module_data.param03 eq "908"}selected{/if} value="908">Swiss Franc</option>
<option {if $module_data.param03 eq "909"}selected{/if} value="909">New Zealand Dollar</option>
<option {if $module_data.param03 eq "910"}selected{/if} value="910">Canadian Dollar </option>
</select>
</td>
</tr>

<!--
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
-->

</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
