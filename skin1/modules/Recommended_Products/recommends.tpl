{* $Id: recommends.tpl,v 1.9 2006/03/21 07:17:18 svowl Exp $ *}
{if $recommends}
{capture name=recommends}
{$lng.txt_recommends_comment}
<br /><br />
<!-- Deleted by Michael de Leon 11.17.06
<ul class="RPItems">
-->
{section name=num loop=$recommends}
	<img src="{$ImagesDir}/wwmp_recommendedproducts_arrow11.17.06.jpg"> <a class="wwmp_vertmenulink" href="product.php?productid={$recommends[num].productid}" class="ItemsList">{$recommends[num].product}</a><br />
{/section}
<!-- Deleted by Michael de Leon 11.17.06
</ul>
-->
{/capture}
{include file="dialog_recommends.tpl" title=$lng.lbl_recommends content=$smarty.capture.recommends extra='width="100%"'}
{/if}
