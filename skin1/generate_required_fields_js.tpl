{* $Id: generate_required_fields_js.tpl,v 1.8 2006/03/17 11:30:43 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var requiredFields = [
{foreach from=$default_fields item=v key=k}
{if $v.required eq 'Y' && $v.avail eq 'Y'}
	["{$k}", "{$v.title|strip|replace:'"':'\"'}"],
{/if}
{/foreach}
{foreach from=$additional_fields item=v key=k}
{if $v.required eq 'Y' && $v.type eq 'T'  && $v.avail eq 'Y'} 
	["additional_values_{$v.fieldid}", "{$v.title|strip|replace:'"':'\"'}"],
{/if} 
{/foreach}
{if $anonymous eq "" or $config.General.disable_anonymous_checkout eq "Y"}
	["uname", "{$lng.lbl_username|strip|replace:'"':'\"'}"],
	["passwd1", "{$lng.lbl_password|strip|replace:'"':'\"'}"],
	["passwd2", "{$lng.lbl_confirm_password|strip|replace:'"':'\"'}"],
{/if}
];
-->
</script>
