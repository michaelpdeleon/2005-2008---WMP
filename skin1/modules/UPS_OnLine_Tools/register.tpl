{* $Id: register.tpl,v 1.9 2006/02/01 14:26:44 max Exp $ *}
{capture name="dialog"}
{$lng.txt_ups_av_err}

<br /><br />

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING}" method="post" name="registerform">
{foreach key=key item=item from=$get_vars}
<input type="hidden" name="{$key}" value="{$item}" />
{/foreach}
{foreach key=key item=item from=$post_vars}
{if $key eq "additional_values"}
{foreach key=akey item=aitem from=$item}
<input type="hidden" name="additional_values[{$akey}]" value="{$aitem}" />
{/foreach}
{else}
<input type="hidden" name="{$key}" value="{$item}" />
{/if}
{/foreach}
<input type="hidden" name="suggest" value="yes" />
<input type="hidden" name="ups_av" value="1" />

<table cellpadding="1" cellspacing="1" width="100%">

<tr>
	<td width="20%"><b>{$lng.lbl_you_entered}:</b></td>
	<td width="80%">{$userinfo.s_city}, {$userinfo.s_state} {if $userinfo.s_state ne ""}({$s_statename}){/if} {$userinfo.s_zipcode}</td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

{if $av_result ne ""}

<tr>
	<td><b>{$lng.lbl_we_suggest}:</b></td>
	<td>
<select name="rank">
{foreach item=av key=key from=$av_result}
<option value="{$key}">{$av.city}, {$av.state} {$av.zipcode}</option>
{/foreach}
</select>
	</td>
</tr>

<tr>
	<td colspan="2">
<br /><br />
{include file="buttons/button.tpl" button_title=$lng.lbl_reenter_address style="button" href="javascript: document.registerform.suggest.value='no'; document.registerform.submit();"}
<br /><br /><br />
{include file="buttons/button.tpl" button_title=$lng.lbl_use_suggestion style="button" href="javascript: document.registerform.submit();"}
&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;
{include file="buttons/button.tpl" button_title=$lng.lbl_keep_current_address style="button" href="javascript: document.registerform.suggest.value='';document.registerform.submit();"}
	</td>
</tr>

{else}

<tr>
	<td colspan="2"><font class="ErrorMessage">{$lng.txt_ups_av_no_alternative_address}</font></td>
</tr>

<tr>
	<td colspan="2"><br /><br />
{include file="buttons/button.tpl" button_title=$lng.lbl_reenter_address style="button" href="javascript: document.registerform.suggest.value='no'; document.registerform.submit();"}
	</td>
</tr>

{/if}

</table>
</form>

<br /><br />

<hr />

{include file="modules/UPS_OnLine_Tools/ups_av_notice.tpl"}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_ups_av_error content=$smarty.capture.dialog extra='width="100%"'}
