{* $Id: submit_wo_js.tpl,v 1.6.2.2 2006/07/11 08:39:26 svowl Exp $ *}
<input type="submit" value="{$value|strip_tags:false|escape}" /><br />
{if $note ne "off"}
<br />{$lng.txt_js_disabled_msg}
{/if}
