<table cellspacing="1" width="100%" class="VertMenuBorder">
<tr>
<td class="VertMenuTitle">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td>{$link_begin}<img src="{$ImagesDir}/{if $dingbats ne ''}{$dingbats}{else}spacer.gif{/if}" class="VertMenuTitleIcon" alt="{$menu_title|escape}" />{$link_end}</td>
<td width="100%" align="right">{if $link_href}<a href="{$link_href}">{/if}<font class="VertMenuTitle">{$menu_title}</font>{if $link_href}</a>{/if}</td>
</tr></table>
</td>
</tr>
<tr> 
<td class="VertMenuBox">
<table cellpadding="0" cellspacing="0" width="100%">
<tr><td class="VertMenuContent">{$menu_content}<!-- Deleted by Michael de Leon 10.27.06 <br /> --></td></tr>
</table>
</td></tr>
</table>
