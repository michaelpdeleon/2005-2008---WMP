{* $Id: register_states.tpl,v 1.13 2006/02/10 14:15:19 max Exp $ *}
{if $country_id eq ''}{assign var="country_id" value=$country_name}{/if}
<script type="text/javascript" language="JavaScript 1.2">
<!--
init_js_states(document.getElementById('{$country_id}'), '{$state_name}', '{$county_name}', '{$state_value|escape:"javascript"}', '{$county_value|escape:"javascript"}');
-->
</script>

