{* $Id: states.tpl,v 1.13 2005/11/30 13:29:35 max Exp $ *}
{if $states ne ""}
<select name="{$name}" id="{$name|replace:"[":"_"|replace:"]":""}" {$style}>
{if $required eq "N"}
	<option value="">[{$lng.lbl_please_select_one}]</option>
{/if}
	<option value="{if $value_for_other ne "no"}Other{/if}"{if $default eq "Other"} selected="selected"{/if}>{$lng.lbl_other}</option>
{section name=state_idx loop=$states}
{if $config.General.default_country eq $states[state_idx].country_code || $country_name eq '' || $default_fields.$country_name.avail eq 'Y'}
	<option value="{$states[state_idx].state_code}"{if $default eq $states[state_idx].state_code and $default_country eq $states[state_idx].country_code} selected="selected"{/if}>{$states[state_idx].country_code}: {$states[state_idx].state}</option>
{/if}
{/section}
</select>
{else}
<input type="text"{if $name ne ''} id="{$name|replace:"[":"_"|replace:"]":""}"{/if} size="32" maxlength="65" name="{$name}" value="{$default}" />
{/if}
