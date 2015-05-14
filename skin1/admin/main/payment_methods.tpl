{* $Id: payment_methods.tpl,v 1.43.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_payment_methods}

{$lng.txt_payment_methods_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="payment_methods.php?"}

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="pmform" prefix="posted_data.+active"}

<form action="payment_methods.php" method="post" name="pmform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="5" cellspacing="1" width="100%">

<tr class="TableHead">
	<td>&nbsp;</td>
	<td width="40%">{$lng.lbl_methods}</td>
	<td width="20%" nowrap="nowrap">{$lng.lbl_special_instructions}</td>
	<td width="20%">{$lng.lbl_protocol}</td>
	<td width="10%">{$lng.lbl_membership}</td>
{if $active_modules.Anti_Fraud}
	<td width="10%" nowrap="nowrap">{$lng.lbl_check}*</td>
{/if}
	<td width="10%">{$lng.lbl_pos}</td>
</tr>

{section name=method loop=$payment_methods}
{cycle values=', class="TableSubHead"' assign=trcolor}

{if $payment_methods[method].disable_checkbox eq "Y"}<input type="hidden" name="posted_data[{$payment_methods[method].paymentid}][active]" value="Y" />{/if}

<tr{$trcolor}>
	<td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
	<input type="checkbox" name="posted_data[{$payment_methods[method].paymentid}][active]" value="Y"{if $payment_methods[method].active eq "Y"} checked="checked"{/if}{if $payment_methods[method].disable_checkbox eq "Y"} disabled="disabled"{/if} />
	</td>
	<td valign="top">
	<input type="text" size="30" name="posted_data[{$payment_methods[method].paymentid}][payment_method]" value="{$payment_methods[method].payment_method|escape:"html"}" />
<br />
<table cellpadding="1" cellspacing="0">
<tr>
	<td class="FormButton">{$lng.lbl_cod_extra_charge}:</td>
	<td><input type="text" size="8" name="posted_data[{$payment_methods[method].paymentid}][surcharge]" value="{$payment_methods[method].surcharge|default:"0"|formatprice}" /></td>
	<td>
	<select name="posted_data[{$payment_methods[method].paymentid}][surcharge_type]">
		<option value="%"{if $payment_methods[method].surcharge_type eq "%"} selected="selected"{/if}>%</option>
		<option value="$"{if $payment_methods[method].surcharge_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
	</select>
	</td>
</tr>
</table>
{if $payment_methods[method].processor_file eq ""}
<table cellpadding="1" cellspacing="0">
<tr>
	<td><input type="checkbox" id="is_cod_{$payment_methods[method].paymentid}" name="posted_data[{$payment_methods[method].paymentid}][is_cod]" value="Y"{if $payment_methods[method].is_cod eq 'Y'} checked="checked"{/if} /></td>
	<td class="FormButton"><label for="is_cod_{$payment_methods[method].paymentid}">{$lng.lbl_cash_on_delivery_method}</label></td>
</tr>
</table>
{/if}
	</td>
	<td valign="top" nowrap="nowrap">
	<textarea name="posted_data[{$payment_methods[method].paymentid}][payment_details]" cols="40" rows="3">{$payment_methods[method].payment_details|escape:"html"}</textarea>
	</td>
	<td valign="top">
	<select name="posted_data[{$payment_methods[method].paymentid}][protocol]" style="width:100%">
		<option value="http"{if $payment_methods[method].protocol eq "http"} selected="selected"{/if}>HTTP</option>
		<option value="https"{if $payment_methods[method].protocol eq "https"} selected="selected"{/if}>HTTPS</option>
	</select>
	</td>
	<td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
	{include file="main/membership_selector.tpl" field="posted_data[`$payment_methods[method].paymentid`][membershipids][]" data=$payment_methods[method] is_short="Y"}
	</td>
{if $active_modules.Anti_Fraud}
	<td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
	<input type="checkbox" name="posted_data[{$payment_methods[method].paymentid}][af_check]" value="Y"{if $payment_methods[method].af_check eq 'Y'} checked="checked"{/if} />
	</td>
{/if}
	<td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
	<input type="text" size="5" maxlength="5" name="posted_data[{$payment_methods[method].paymentid}][orderby]" value="{$payment_methods[method].orderby}" />
	</td>
</tr>

{if $payment_methods[method].module_name ne ""}
<tr{$trcolor}>
	<td colspan="3" valign="bottom">
{if $payment_methods[method].type eq "C"}{$lng.lbl_credit_card_processor}{elseif $payment_methods[method].type eq "H"}{$lng.lbl_check_processor}{else}{assign var=type value="ps"}{$lng.lbl_ps_processor}{/if} <b>{$payment_methods[method].module_name}</b>:
<a href="cc_processing.php?mode=update&amp;cc_processor={$payment_methods[method].processor}">{$lng.lbl_configure}</a> | <a href="cc_processing.php?mode=delete&amp;paymentid={$payment_methods[method].paymentid}">{$lng.lbl_delete}</a>
{if $payment_methods[method].is_down or $payment_methods[method].in_testmode}
<table cellpadding="2">
{if $payment_methods[method].is_down}
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td><font class="AdminSmallMessage">{$lng.txt_cc_processor_requirements_failed|substitute:"processor":$payment_methods[method].module_name}</font></td>
</tr>
{/if}
{if $payment_methods[method].in_testmode}
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td><font class="AdminSmallMessage">{$lng.txt_cc_processor_in_text_mode|substitute:"processor":$payment_methods[method].module_name}</font></td>
</tr>
{/if}
</table>
{/if}{* $payment_methods[method].is_down or $payment_methods[method].in_testmode *}
	</td>
</tr>
{/if}

{/section}

<tr>
	<td align="center" colspan="{if $active_modules.Anti_Fraud}7{else}6{/if}" class="SubmitBox"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>

{if $active_modules.Anti_Fraud}
<tr>
	<td colspan="7">&nbsp;</td>
</tr>
<tr>
	<td colspan="7">*) {$lng.txt_af_payment_method_note}</td>
</tr>
{/if}

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_payment_methods content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />{include file="admin/main/cc_processing.tpl"}
