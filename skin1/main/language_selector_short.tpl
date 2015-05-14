{* $Id: language_selector_short.tpl,v 1.5 2006/03/29 13:55:19 max Exp $ *}
{if $all_languages_cnt > 1}
<select{if $selector_disabled} disabled="disabled"{else} id="edit_lng" name="edit_lng" onchange="javascript: self.location='{$script|amp}edit_lng='+this.value+'&amp;old_lng={$shop_language}';"{/if}>
{if $shop_language eq $config.default_admin_language && $is_no_default eq 'Y'}
<option value="">{$lng.lbl_please_select_language}</option>
{/if}
{foreach from=$all_languages item=v}
{if ($v.code ne $config.default_admin_language || $is_no_default ne 'Y') && $v.language ne ''}
<option value="{$v.code}"{if $v.code eq $shop_language} selected="selected"{/if}>{$v.language}</option>
{/if}
{/foreach}
</select>
{/if}
