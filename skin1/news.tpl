{* $Id: news.tpl,v 1.30 2005/12/22 08:06:20 svowl Exp $ *}
{if $active_modules.News_Management}
{insert name="gate" func="news_exist" assign="is_news_exist" lngcode=$shop_language}
{/if}
{if $active_modules.News_Management and $is_news_exist}
<br />
{capture name=menu}
<div class="VertMenuItems">
<div style="font-size: 9px">
{include file="today_news.tpl"}
</div>
{insert name="gate" func="news_subscription_allowed" assign="is_subscription_allowed" lngcode=$shop_language}
{if $is_subscription_allowed}
<img src="{$ImagesDir}/spacer.gif" width="1" height="8" alt="" /><br />

<form action="news.php" name="subscribeform" method="post">
<input type="hidden" name="subscribe_lng" value="{$store_language}" />

<table>
<tr>
	<td>
{$lng.lbl_your_email}
<br />
<input type="text" name="newsemail" size="16" />
<br />
{include file="buttons/subscribe_menu.tpl"}
	</td>
</tr>
</table>
</form>
{/if}{* $is_subscription_allowed *}
</div>
{/capture}
{ include file="menu.tpl" dingbats="dingbats_news.gif" menu_title=$lng.lbl_news menu_content=$smarty.capture.menu }
{/if}{* $active_modules.News_Management and $is_news_exist *}
