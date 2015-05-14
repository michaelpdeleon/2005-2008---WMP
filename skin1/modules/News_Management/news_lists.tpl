{* $Id: news_lists.tpl,v 1.2.2.2 2006/07/11 08:39:32 svowl Exp $ *}

{capture name=dialog}
<form action="news.php" method="post">
<input type="hidden" name="mode" value="subscribe" />
<input type="hidden" name="newsemail" value="{$newsemail|escape}" />
<table>
{foreach from=$lists item=list key=k}
<tr>
  <td>
     <input type="checkbox" name="lists[{$k}][listid]" id="lists_{$k}" value="{$list.listid}" checked="checked" />
  </td>
  <td>
     <label for="lists_{$k}">
       <b>{$list.name}</b>
     </label>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>
     <label for="lists_{$k}">
       <i>{$list.descr}</i>
     </label>
  </td>
</tr>
{/foreach}
<tr>
  <td colspan="2">
    <br />
    <input type="submit" value="{$lng.lbl_subscribe|strip_tags:false|escape}" />
  </td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_news_subscribe_to_newslists content=$smarty.capture.dialog extra='width="100%"'}
