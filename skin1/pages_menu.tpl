{* $Id: pages_menu.tpl,v 1.5 2005/11/17 06:55:36 max Exp $ *}
{section name=pg loop=$pages_menu}
<a href="pages.php?pageid={$pages_menu[pg].pageid}" class="VertMenuItems">{$pages_menu[pg].title}</a><br />
{/section}
