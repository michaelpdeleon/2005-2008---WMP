{* $Id: menu_bestsellers.tpl,v 1.12 2005/11/17 06:55:40 max Exp $ *}
{if $bestsellers}
{capture name=menu}
{section name=num loop=$bestsellers}
<font class="VertMenuItems"><b>{math equation="value+1" value=$smarty.section.num.index}.</b></font> 
<a href="product.php?productid={$bestsellers[num].productid}&amp;cat={$cat}&amp;bestseller=Y" class="VertMenuItems">{$bestsellers[num].product}</a><br />
{/section}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_bestsellers menu_content=$smarty.capture.menu }
<br />
{/if}
