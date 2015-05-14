{* $Id: news_archive.tpl,v 1.5 2005/11/17 06:55:51 max Exp $ *}
{if $news_messages eq ""}
{$lng.txt_no_news_available}
{else}
{section name=idx loop=$news_messages}
{capture name=dialog}
<b>{$news_messages[idx].subject}</b>
<br /><br />
{if $news_messages[idx].allow_html eq "N"}
{$news_messages[idx].body|replace:"\n":"<br />"}
{else}
{$news_messages[idx].body}
{/if}
{/capture}
{include file="dialog.tpl" title=$news_messages[idx].send_date|date_format:$config.Appearance.date_format content=$smarty.capture.dialog extra='width="100%"'}
<br />
{/section}
{/if}
