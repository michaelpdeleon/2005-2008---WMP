{* $Id: cc_gestpay.tpl,v 1.6.2.2 2006/07/11 08:39:36 svowl Exp $ *}
{php}
	global $sql_tbl;

	$result = func_query_first ("SELECT COUNT(*) AS numba FROM ".$sql_tbl["cc_gestpay_data"]." WHERE type='C'");
	$this->assign ("ric_number", $result["numba"]);
	
	$result = func_query_first ("SELECT COUNT(*) AS numba FROM ".$sql_tbl["cc_gestpay_data"]." WHERE type='S'");
	$this->assign ("ris_number", $result["numba"]);
{/php}
<h3>GestPay</h3>
{$lng.txt_cc_configure_top_text}
<p />
{$lng.txt_cc_gestpay_note|substitute:"http_location":$http_location}
<p />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<center>
<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_gestpay_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option value="242"{if $module_data.param02 eq "242"} selected="selected"{/if}>Euro</option>
<option value="18"{if $module_data.param02 eq "18"} selected="selected"{/if}>Italian Lira</option>
</select>
</td>
</tr>
<tr>
<td>{$lng.lbl_cc_gestpay_terminalid}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
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
<br />
{capture name=dialog}
<form method="post" action="{$xcart_web_dir}/payment/cc_gestpay.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="import_passwords" />
<table width="100%" border="0">
<tr>
	<td>{$lng.lbl_cc_gestpay_ricris}</td>
	<td>{$ric_number} / {$ris_number}</td>
</tr>
<tr>
	<td colspan="2">{$lng.lbl_cc_gestpay_importpasswords}:</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;{$lng.lbl_cc_gestpay_ricfile}</td>
	<td><input type="file" name="ric" /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;{$lng.lbl_cc_gestpay_risfile}</td>
	<td><input type="file" name="ris" /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;{$lng.lbl_cc_gestpay_delete_all}</td>
	<td><input type="checkbox" name="delete_all" value='Y' /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="{$lng.lbl_cc_gestpay_import|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_gestpay_passwords content=$smarty.capture.dialog extra='width="100%"'}
