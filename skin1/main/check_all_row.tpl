{* $Id: check_all_row.tpl,v 1.1 2006/01/05 14:20:37 max Exp $ *}
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}
<div{if $style ne ''} style="{$style}"{/if}><a href="javascript: checkAll(true, document.{$form}, '{$prefix}');">{$lng.lbl_check_all}</a> / <a href="javascript: checkAll(false, document.{$form}, '{$prefix}');">{$lng.lbl_uncheck_all}</a></div>
