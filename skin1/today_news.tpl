{* $Id: today_news.tpl,v 1.13 2006/03/28 08:21:07 max Exp $ *}
{if $news_message eq ""}
{$lng.txt_no_news_available}
{else}
<b>{$news_message.send_date|date_format:$config.Appearance.date_format}</b><br />
{$news_message.body}
<br /><br />
{if $usertype eq "C"}
<a href="news.php" class="SmallNote">{$lng.lbl_previous_news}</a>
{/if}
{/if}
<hr class="VertMenuHr" size="1" />
