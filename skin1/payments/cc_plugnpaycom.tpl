{* $Id: cc_plugnpaycom.tpl,v 1.6.2.2 2006/07/11 08:39:37 svowl Exp $ *}
<h3>Plug'n'Pay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_plugnpaycom_publisher}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_plugnpaycom_host}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /><br />
{$lng.lbl_cc_plugnpaycom_host_note}
</td>
</tr>

<tr>
<td valign="top">{$lng.lbl_cc_plugnpaycom_avs}:</td>
<td>
<input type="radio" name="param05" value="0"{if $module_data.param05 eq "0"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_0}<br />
<input type="radio" name="param05" value="1"{if $module_data.param05 eq "1"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_1}<br />
<input type="radio" name="param05" value="3"{if $module_data.param05 eq "3"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_3}<br />
<input type="radio" name="param05" value="4"{if $module_data.param05 eq "4"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_4}<br />
<input type="radio" name="param05" value="5"{if $module_data.param05 eq "5"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_5}<br />
<input type="radio" name="param05" value="6"{if $module_data.param05 eq "6"} checked="checked"{/if} />{$lng.lbl_cc_plugnpaycom_avs_6}<br />
</td><br />
</tr>


<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
</table>
<p />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
