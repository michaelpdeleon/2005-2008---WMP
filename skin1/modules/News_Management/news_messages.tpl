{* $Id: news_messages.tpl,v 1.2 2004/04/16 13:31:08 svowl Exp $ *}

{if $action eq "add" or $action eq "modify"}
{include file="modules/News_Management/news_messages_modify.tpl}
{else}
{include file="modules/News_Management/news_messages_list.tpl}
{/if}
