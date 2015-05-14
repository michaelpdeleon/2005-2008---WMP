{* $Id: cc_processing.tpl,v 1.36 2006/03/03 09:02:21 max Exp $ *}

{$lng.txt_cc_ach_processing_top_text}

<br /><br />

{capture name=dialog}
<form action="cc_processing.php" method="get" name="myform">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="subscribe" value="" />

<table cellpadding="2" cellspacing="1" width="100%">

<tr><td colspan="3">{$lng.txt_credit_card_processor_note}</td></tr>

<tr>
	<td nowrap="nowrap"><b>{$lng.lbl_payment_gateways}</b></td>
	<td align="center" width="100%">
	<select name="processor" style="width: 100%;">
{assign var=type value=""}
		<option value="">{$lng.lbl_select}...</option>
{section name=module loop=$cc_modules}
{if $cc_modules[module].type eq "C" and $cc_modules[module].type ne $type}
{if $type ne ""}</optgroup>{/if}{assign var=type value="C"}
		<optgroup label="--- {$lng.lbl_credit_card_processor} ---">
{elseif $cc_modules[module].type eq "H" and $cc_modules[module].type ne $type}
{if $type ne ""}</optgroup>{/if}{assign var=type value="H"}
		<optgroup label="--- {$lng.lbl_check_processor} ---">
{elseif $cc_modules[module].type eq "D" and $cc_modules[module].type ne $type}
{if $type ne ""}</optgroup>{/if}{assign var=type value="D"}
		<optgroup label="--- {$lng.lbl_direct_debit_processor} ---">
{elseif $cc_modules[module].type eq "P" and $cc_modules[module].type ne $type}
{if $type ne ""}</optgroup>{/if}{assign var=type value="P"}
		<optgroup label="--- {$lng.lbl_ps_processor} ---">
{/if}
		<option value="{$cc_modules[module].processor}">{$cc_modules[module].module_name}</option>
{/section}
		</optgroup>
	</select>
</td>
	<td nowrap="nowrap">{include file="buttons/button.tpl" button_title=$lng.lbl_add href="javascript: if (document.myform.processor.selectedIndex > 0) document.myform.submit();" js_to_href="Y"}</td>
</tr>

{if $active_modules.Subscriptions ne ""}

<tr><td colspan="3"><br /><br />{$lng.txt_subscription_processor_note}</td></tr>

<tr>
	<td nowrap="nowrap"><b>{$lng.lbl_subscription_processor}</b></td>
	<td align="center">
	<select name="cc_processor" style="width:100%">
		<option value=""{if $config.active_subscriptions_processor eq ""} selected="selected"{/if}>{$lng.lbl_manual_processing}</option>
{section name=module loop=$sb_modules}
		<option value="{$sb_modules[module].processor}"{if $config.active_subscriptions_processor eq $sb_modules[module].processor} selected="selected"{assign var=active_subscriptions_processor value=$sb_modules[module].module_name}{/if}>{$sb_modules[module].module_name}</option>
{/section}
	</select>
	</td>
	<td nowrap="nowrap">{include file="buttons/continue.tpl" href="javascript: document.myform.mode.value='update'; document.myform.subscribe.value='yes'; document.myform.submit();" js_to_href="Y"}</td>
</tr>
{if $active_sb.status ne "1" or $active_sb.in_testmode}
<tr><td colspan="3">
{if $active_sb.status ne "1"}<font class="AdminSmallMessage">{$lng.txt_cc_processor_requirements_failed|substitute:"processor":$active_subscriptions_processor}</font>{/if}
{if $active_sb.in_testmode}<font class="AdminSmallMessage">{$lng.txt_cc_processor_in_text_mode|substitute:"processor":$active_subscriptions_processor}</font>{/if}
</td></tr>
{/if}

{/if}

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_payment_gateways content=$smarty.capture.dialog extra='width="100%"'}
