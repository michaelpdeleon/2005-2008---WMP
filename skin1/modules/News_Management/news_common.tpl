{* $Id: news_common.tpl,v 1.9 2005/11/17 06:55:51 max Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_news_management}

{$lng.txt_news_management_top_text}

<br /><br />

{if $mode eq ""}
{include file="modules/News_Management/news_lists_select.tpl"}

{else}
{if $mode ne "create"}
{include file="dialog_tools.tpl"}
<br />
{/if}

{if $mode eq "create" or $mode eq "modify" or ($mode eq "messages" and ($action eq "add" or $action eq "modify"))}
{$lng.txt_fields_are_mandatory}
<br />
{/if}

<br />

{capture name=dialog}

{if $mode eq "create"}
{assign var="dialog_title" value=$lng.lbl_news_list_details}
{include file="modules/News_Management/news_details.tpl"}

{elseif $mode eq "modify"}
{assign var="dialog_title" value=$lng.lbl_news_list_details}
{include file="modules/News_Management/news_details.tpl"}

{elseif $mode eq "subscribers"}
{assign var="dialog_title" value=$lng.lbl_news_list_maillist}
{include file="modules/News_Management/news_subscribers.tpl"}

{elseif $mode eq "messages"}
{assign var="dialog_title" value=$lng.lbl_news_list_messages}
{if $action eq "add" or $action eq "modify"}
{assign var="dialog_title" value=$lng.lbl_news_list_message_details}
{/if}
{include file="modules/News_Management/news_messages.tpl"}
{/if}

{/capture}
{include file="dialog.tpl" title=$dialog_title content=$smarty.capture.dialog extra='width="100%"'}
{/if}

