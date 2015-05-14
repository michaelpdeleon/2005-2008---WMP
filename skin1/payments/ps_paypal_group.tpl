{* $Id: ps_paypal_group.tpl,v 1.10.2.2 2006/07/11 08:39:37 svowl Exp $ *}

<h3>PayPal</h3>

{$lng.txt_cc_configure_top_text}

{* declare js *}
{literal}
<script type="text/javascript" language="JavaScript 1.2">
<!--
function view_solution(solution) {
	if (!document.getElementById('sol_ipn') || !document.getElementById('sol_pro'))
		return false;

	if (solution == "ipn") {
		document.getElementById('sol_ipn').style.display = '';
		document.getElementById('sol_pro').style.display = 'none';

	} else {
		document.getElementById('sol_ipn').style.display = 'none';
		document.getElementById('sol_pro').style.display = '';
	}
}
-->
</script>
{/literal}
<p />
{capture name=dialog}

<br />

{$lng.txt_paypal_solution_title}
<br /><br />

<table cellpadding="5" cellspacing="0" width="100%">

<form action="cc_processing.php" method="post">
<input type="hidden" name="cc_processor" value="{$smarty.get.cc_processor|escape:"url"}" />

{* main switch *}
<tr valign="top">
<td width="20"><input id="r_sol_ipn" type="radio" name="paypal_solution" onclick="view_solution('ipn');" value="ipn"{if $config.paypal_solution eq "ipn"} CHECKED{/if} /></td>
<td width="100%"><label for="r_sol_ipn"><b>{$lng.lbl_paypal_sol_std}</b><br />
{$lng.txt_paypal_sol_std_note}
</label>
</tr>

<tr valign="top">
<td><input id="r_sol_pro" type="radio" name="paypal_solution" onclick="view_solution('pro');" value="pro"{if $config.paypal_solution eq "pro"} CHECKED{/if} /></td>
<td><label for="r_sol_pro"><b>{$lng.lbl_paypal_sol_pro}</b> &nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:void(0);" onclick="javascript:window.open('http://www.x-cart.com/xcart_manual/online/paypal_pro_notes.htm','PPEC_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');">{$lng.lbl_paypal_guidelines_click}</a><br />
{$lng.txt_paypal_sol_pro_note}
</label>
</tr>

<tr valign="top">
<td><input id="r_sol_express" type="radio" name="paypal_solution" onclick="view_solution('express');" value="express"{if $config.paypal_solution eq "express"} CHECKED{/if} /></td>
<td><label for="r_sol_express"><b>{$lng.lbl_paypal_sol_express}</b> &nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:void(0);" onclick="javascript:window.open('http://www.x-cart.com/xcart_manual/online/paypal_pro_notes.htm','PPEC_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');">{$lng.lbl_paypal_guidelines_click}</a><br />
{$lng.txt_paypal_sol_express_note}
</label>
</tr>

<tr>
<td colspan="2"><hr size="1" noshade="noshade" /></td>
</tr>

{* configuration boxes *}
<tr id="sol_pro" style="display: {if $config.paypal_solution ne "ipn"}''{else}none{/if}">
<td>&nbsp;</td>
<td>
{include file="payments/ps_paypal_pro.tpl" conf_prefix="conf_data[pro]" module_data=$conf_data.pro}
</td>
</tr>

<tr id="sol_ipn" style="display: {if $config.paypal_solution eq "ipn"}''{else}none{/if}">
<td>&nbsp;</td>
<td>
{include file="payments/ps_paypal.tpl" conf_prefix="conf_data[ipn]" module_data=$conf_data.ipn}
</td>
</tr>

<tr>
<td colspan="2" align="center">
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</td>
</tr>

</form>

</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
