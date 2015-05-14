{* $Id: recommends.tpl,v 1.9 2006/03/21 07:17:18 svowl Exp $ *}
{if $recommends}
{capture name=recommends}
{$lng.txt_recommends_comment}
<ul class="RPItems">
{section name=num loop=$recommends}
	<li><a href="product.php?productid={$recommends[num].productid}" class="ItemsList">{$recommends[num].product}</a></li>
{/section}
</ul>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_recommends content=$smarty.capture.recommends extra='width="100%"'}
{/if}
