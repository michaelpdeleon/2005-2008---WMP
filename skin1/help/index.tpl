{* $Id: index.tpl,v 1.6 2004/03/16 10:43:08 svowl Exp $ *}
<!-- Deleted by Michael de Leon 11.21.06
{* include file="page_title.tpl" title=$lng.lbl_help_zone *}
-->
{if $section eq "Password_Recovery"}
{include file="help/Password_Recovery.tpl"}

{elseif $section eq "Password_Recovery_message"}
{include file="help/Password_Recovery_message.tpl"}

{elseif $section eq "Password_Recovery_error"}
{include file="help/Password_Recovery.tpl"}

{elseif $section eq "FAQ"}
{include file="help/FAQ_HTML.tpl"}

{elseif $section eq "contactus"}
{include file="help/contactus.tpl"}

{elseif $section eq "about"}
{include file="help/about.tpl"}

{elseif $section eq "business"}
{include file="help/business.tpl"}

{elseif $section eq "conditions"}
{include file="help/conditions.tpl"}

{elseif $section eq "publicity"}
{include file="help/publicity.tpl"}

{else}
{include file="help/general.tpl"}
{/if}
