{* $Id: button.tpl,v 1.25.2.2 2006/06/16 10:47:41 max Exp $ *}
{if $config.Adaptives.platform eq 'MacPPC' && $config.Adaptives.browser eq 'NN'}
  {assign var="js_to_href" value="Y"}
{/if}
{if $type eq 'input'}
  {assign var="img_type" value='input type="image"'}
{else}
  {assign var="img_type" value='img'}
{/if}
{assign var="js_link" value=$href|regex_replace:"/^\s*javascript\s*:/Si":""}
{if $js_link eq $href}
  {assign var="js_link" value="javascript: self.location='"|cat:$href|amp|cat:"';"}
{else}
  {assign var="js_link" value=$href}
  {if $js_to_href ne 'Y'}
    {assign var="onclick" value=$href}
    {assign var="href" value="javascript: void(0);"}
  {/if}
{/if}

{if $style eq 'button' && ($config.Adaptives.platform ne 'MacPPC' || $config.Adaptives.browser ne 'NN')}
<table cellspacing="0" cellpadding="0" onclick="{$js_link}" class="ButtonTable"{if $title ne ''} title="{$title|escape}"{/if}>
{strip}
<tr>
	<td>
		<{$img_type} src="{$ImagesDir}/but1.gif" class="ButtonSide" alt="{$title|escape}" />
	</td>
	<td class="Button"{$reading_direction_tag}>
		<font class="Button">{$button_title}</font>
	</td>
	<td>
		<img src="{$ImagesDir}/but2.gif" class="ButtonSide" alt="{$title|escape}" />
	</td>
</tr>
{/strip}
</table>
{elseif $image_menu}
{strip}
<table cellspacing="0" class="SimpleButton">
<tr>
{if $button_title ne ''}
	<td>
<a class="VertMenuItems" href="{$href|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}>
	<font class="VertMenuItems">{$button_title}&nbsp;</font>
</a>
	</td>
{/if}
	<td>
{if $img_type eq 'img'}
<a class="VertMenuItems" href="{$href|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}>
{/if}
	<{$img_type} {include file="buttons/go_image_menu.tpl"} />
{if $img_type eq 'img'}
</a>
{/if}
</td>
</tr>
</table>
{/strip}
{else}
{strip}
<table cellspacing="0">
<tr>
{if $button_title ne ''}
	<td>
<a class="Button" href="{$href|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}>
	{$button_title}&nbsp;
</a>
	</td>
{/if}
	<td class="wwmp_cart_btns">
{if $img_type eq 'img'}
<a href="{$href|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}>
{/if}
	<{$img_type} {include file="buttons/editbtn_image.tpl" full_url='Y'} />
{if $img_type eq 'img'}
</a>
{/if}
</td>
</tr>
</table>
{/strip}
{/if}
