{* $Id: pages.tpl,v 1.5 2005/11/17 06:55:37 max Exp $ *}
{capture name=dialog}
{if $page_content ne ''}
{if $config.General.parse_smarty_tags eq "Y"}
{eval var=$page_content}
{else}
{$page_content}
{/if}
{/if}
{/capture}
{include file="dialog.tpl" title=$page_data.title content=$smarty.capture.dialog extra='width="100%"'}
