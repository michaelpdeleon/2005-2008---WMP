{* $Id: change_states_js.tpl,v 1.17.2.1 2006/04/19 12:59:33 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var txt_no_states = "{$lng.lbl_country_doesnt_require_state|escape:javascript}";
var txt_no_counties = "{$lng.lbl_country_doesnt_require_county|escape:javascript}";
var use_counties = {if $config.General.use_counties eq 'Y'}true{else}false{/if};
var states_sort_override = {if $config.UA.browser eq 'Opera'}true{else}false{/if};

var countries = {ldelim}{rdelim};
{assign var="cnt" value=0}
{foreach from=$countries item=v}
countries.{$v.country_code} = {ldelim}states: {if $v.display_states eq 'Y'}[]{else}false{/if}{rdelim};
{/foreach}

{if $states ne ''}
var i = 0;
{foreach from=$states item=v key=k}
countries.{$v.country_code}.states[{$v.stateid}] = {ldelim}code: "{$v.state_code|escape:"javascript"|replace:"\n":" "}", name: "{$v.state|escape:"javascript"|replace:"\n":" "}", counties: []{if $config.UA.browser eq 'Opera'}, order: i++{/if}{rdelim};
{/foreach}
{/if}

{if $config.General.use_counties eq 'Y' && $counties ne ''}
i = 0;
{foreach from=$counties item=v}
countries.{$v.country_code}.states[{$v.stateid}].counties[{$v.countyid}] = {ldelim}name: "{$v.county|escape:"javascript"|replace:"\n":" "}"{if $config.UA.browser eq 'Opera'}, order: i++{/if}{rdelim};
{/foreach}
{/if}

-->
</script>
{include file="main/include_js.tpl" src="change_states.js"}

