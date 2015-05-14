{* $Id: title_selector.tpl,v 1.3 2005/11/30 13:29:35 max Exp $ *}
{foreach from=$titles item=v}
	<option value="{$v.title_orig|escape}"{if $field eq $v.titleid} selected="selected"{/if}>{$v.title}</option>
{/foreach}

