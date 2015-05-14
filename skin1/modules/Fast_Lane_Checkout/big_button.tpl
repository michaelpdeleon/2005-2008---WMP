{* $Id: big_button.tpl,v 1.3.2.3 2006/06/16 10:47:44 max Exp $ *}
{if $config.Adaptives.platform eq 'MacPPC' && $config.Adaptives.browser eq 'NN'}{assign var="js_to_href" value="Y"}{/if}
{if $type eq 'input'}{assign var="img_type" value='INPUT type="image"'}{else}{assign var="img_type" value='IMG'}{/if}
{assign var="js_link" value=$href|regex_replace:"/^\s*javascript\s*:/Si":""}
{if $js_link eq $href}{assign var="js_link" value="javascript: self.location='`$href`'"}
{else}{assign var="js_link" value=$href}{if $js_to_href ne 'Y'}{assign var="onclick" value=$href}{assign var="href" value="javascript: void(0);"}{/if}{/if}
{if $config.Adaptives.platform ne 'MacPPC' || $config.Adaptives.browser ne 'NN'}
{if $color eq "red"}
{assign var="bg_title_class" value="RedBackground"}
{assign var="sfx" value="_r"}
{else}
{assign var="bg_title_class" value="YellowBackground"}
{assign var="sfx" value=""}
{/if}
<table cellspacing="0" cellpadding="0" onclick="{$js_link}" style="cursor: pointer;" dir="ltr">
<tr>
<td width="9" style="background-repeat: no-repeat; background-image: url({$ImagesDir}/top_cl{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
<td height="9" style="background-repeat: repeat-x; background-image: url({$ImagesDir}/top_b{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
<td width="9" style="background-repeat: no-repeat; background-image: url({$ImagesDir}/top_cr{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
</tr>

<tr>
<td width="9" style="background-repeat: repeat-y; background-image: url({$ImagesDir}/tab_left{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
<td class="{$bg_title_class}"{$reading_direction_tag}>&nbsp;{$button_title}&nbsp;{if $arrow eq "Y"}<img src="{$ImagesDir}/rarrow_flc.gif" class="BBCorner" alt="" />{/if}</td>
<td width="9" style="background-repeat: repeat-y; background-image: url({$ImagesDir}/tab_right{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
</tr>

<tr>
<td width="9" style="background-repeat: no-repeat; background-image: url({$ImagesDir}/tab_cl{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
<td height="9" style="background-repeat: repeat-x; background-image: url({$ImagesDir}/tab_bt{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
<td width="9" style="background-repeat: no-repeat; background-image: url({$ImagesDir}/tab_cr{$sfx}.gif);"><img src="{$ImagesDir}/spacer.gif" class="BBCorner" alt="" /></td>
</tr>

</table>
{else}
<a href="{$href}"{if $onclick ne ''} onclick="{$onclick}"{/if}{if $title ne ''} title="{$title|escape}"{/if}{if $target ne ''} target="{$target}"{/if}><font class="FormButton">{$button_title} <{$img_type} {include file="buttons/go_image.tpl" full_url='Y'}></font></a>
{/if}
