{* $Id: category_selector.tpl,v 1.3 2006/04/10 06:42:21 max Exp $ *}
{if $display_field eq ''}{assign var="display_field" value="category_path"}{/if}
<select name="{$field|default:"categoryid"}"{$extra}>
{if $display_empty eq 'P'}
<option value="">{$lng.lbl_please_select_category}</option>
{elseif $display_empty eq 'E'}
<option value=""></option>
{/if}
{foreach from=$allcategories item=c key=catid}
<option value="{$catid}"{if $categoryid eq $catid} selected="selected"{/if}>{$c.$display_field}</option>
{/foreach}
</select>
