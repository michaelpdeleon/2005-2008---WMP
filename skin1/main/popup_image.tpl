{* $Id: popup_image.tpl,v 1.19.2.4 2006/07/24 06:56:39 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html>
<head>
	<title>{$title|default:$lng.lbl_images|escape}</title>
	<link rel="stylesheet" href="{if $usertype eq 'C'}{$SkinDir}/{#CSSFile#}{else}{$SkinDir}/skin1_admin.css{/if}" />
{include file="presets_js.tpl"}
{include file="main/include_js.tpl" src="common.js"}
{include file="main/include_js.tpl" src="main/popup_image_js.js"}
<script type="text/javascript">
<!--
var images = [];
{if $js_selector}
{foreach from=$images key=k item=v}
images[{$k}] = [new Image(),'{$v.alt|escape:"javascript"}', '{if $v.url ne ''}{$v.url}{else}image.php?type={$type}&id={$v.id}{/if}', false];
{/foreach}
{/if}

var added_h = {if $images_count > 1}30{else}0{/if};
var larrow_grey = false;
var rarrow_grey = false;
-->
</script>
</head>
<body{if $js_selector} onload="javascript: changeImg(0);"{/if}{$reading_direction_tag} class="PImage">
{math assign="idx" equation="x-1" x=$page}
<table cellpadding="0" cellspacing="0" width="100%" class="Container">
<tr>
	<td class="Container">
<table cellpadding="0" cellspacing="0" width="100%">
{if $images_count > 1}
<tr>
	<td class="PImagePageRow">

{if $js_selector}
<script type="text/javascript">
<!--
larrow = new Image();
larrow.src = '{$ImagesDir}/larrow.gif';
rarrow = new Image();
rarrow.src = '{$ImagesDir}/rarrow.gif';

larrow_grey = new Image();
larrow_grey.src = '{$ImagesDir}/larrow_grey.gif';
rarrow_grey = new Image();
rarrow_grey.src = '{$ImagesDir}/rarrow_grey.gif';

larrow2 = new Image();
larrow2.src = '{$ImagesDir}/larrow_2.gif';
rarrow2 = new Image();
rarrow2.src = '{$ImagesDir}/rarrow_2.gif';

larrow2_grey = new Image();
larrow2_grey.src = '{$ImagesDir}/larrow_2_grey.gif';
rarrow2_grey = new Image();
rarrow2_grey.src = '{$ImagesDir}/rarrow_2_grey.gif';

var max_nav_pages = {$config.Appearance.max_nav_pages|default:0};
var lbl_page = "{$lng.lbl_page}";
var spc = new Image();
spc = '{$ImagesDir}/spacer.gif';
--></script>

<table cellpadding="0">
<tr>
{if $config.Appearance.max_nav_pages > 0 && $images_count > $config.Appearance.max_nav_pages}
	<td><a href="javascript: void(0);" onclick="javascript: changeImg(current_id-max_nav_pages);"><img id="larr2" src="{$ImagesDir}/larrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_prev_group_pages|escape}" /></a></td>
{/if}
	<td valign="middle"><a href="javascript: void(0);" onclick="javascript: changeImg(current_id-1);"><img id="larr" src="{$ImagesDir}/larrow.gif" class="NavigationArrow" alt="{$lng.lbl_prev_page|escape}" /></a>&nbsp;</td>
	<td id="prow"></td>
	<td valign="middle">&nbsp;<a href="javascript: void(0);" onclick="javascript: changeImg(current_id+1);"><img id="rarr" src="{$ImagesDir}/rarrow.gif" class="NavigationArrow" alt="{$lng.lbl_next_page|escape}" /></a></td>
{if $config.Appearance.max_nav_pages > 0 && $images_count > $config.Appearance.max_nav_pages}
	<td><a href="javascript: void(0);" onclick="javascript: changeImg(current_id+max_nav_pages);"><img id="rarr2" src="{$ImagesDir}/rarrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_next_group_pages|escape}" /></a></td>
{/if}
</tr>
</table>
{else}
<table cellpadding="0">
<tr>
{if $current_super_page gt 1}
	<td><a href="{$navigation_script}&amp;page={math equation="page-1" page=$start_page}"><img src="{$ImagesDir}/larrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_prev_group_pages|escape}" /></a></td>
{/if}
{section name=page loop=$total_pages start=$start_page}
{if %page.first%}
{if $navigation_page gt 1}
	<td valign="middle"><a href="{$navigation_script}&amp;page={math equation="page-1" page=$navigation_page}"><img src="{$ImagesDir}/larrow.gif" class="NavigationArrow" alt="{$lng.lbl_prev_page|escape}" /></a>&nbsp;</td>
{/if}
{/if}
{if %page.index% eq $navigation_page}
	<td class="NavigationCellSel" title="{$lng.lbl_current_page|escape}: #{%page.index%}">{%page.index%}</td>
{else}
{if %page.index% ge 100}
{assign var="suffix" value="Wide"}
{else}
{assign var="suffix" value=""}
{/if}
	<td class="NavigationCell{$suffix}"><a href="{$navigation_script}&amp;page={%page.index%}" title="{$lng.lbl_page|escape} #{%page.index%}">{%page.index%}</a><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
{/if}
{if %page.last%}
{math equation="pages-1" pages=$total_pages assign="total_pages_minus"}
{if $navigation_page lt $total_super_pages*$config.Appearance.max_nav_pages}
	<td valign="middle">&nbsp;<a href="{$navigation_script}&amp;page={math equation="page+1" page=$navigation_page}"><img src="{$ImagesDir}/rarrow.gif" class="NavigationArrow" alt="{$lng.lbl_next_page|escape}" /></a></td>
{/if}
{/if}
{/section}
{if $current_super_page lt $total_super_pages}
	<td><a href="{$navigation_script}&amp;page={math equation="page+1" page=$total_pages_minus}"><img src="{$ImagesDir}/rarrow_2.gif" class="NavigationArrow" alt="{$lng.lbl_next_group_pages|escape}" /></a></td>
{/if}
</tr>
</table>
{/if}

	</td>
</tr>
<tr>
	<td class="PImageLine"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>
{/if}
<tr>
	<td class="PImageImageCell" align="center">
	{if $js_selector}
	<img id="img" alt="" src="{$ImagesDir}/spacer.gif" />
	{else}
	<img id="img" alt="{$images[$idx].alt|escape}" src="{if $images[$idx].url ne ''}{$images[$idx].url}{else}image.php?type={$type}&amp;id={$images[$idx].id}{/if}" onload="javascript: imgOnLoad(this);" />
	{/if}
	</td>
</tr>
</table>

	</td>
</tr>
<tr>
	<td height="20" class="BottomPopup"><a href="javascript: void(0);" onclick="javascript: window.close();">{$lng.lbl_close_window}</a>&nbsp;&nbsp;</td>
</tr>
</table>
</body>
</html>
