{* $Id: subheader.tpl,v 1.4 2005/12/07 14:07:21 max Exp $ *}
{if $class eq 'grey'}
<table cellspacing="0" class="SubHeaderGrey">
<tr>
	<td class="SubHeaderGrey">{$title}</td>
</tr>
<tr>
	<td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>
{elseif $class eq "black"}
<table cellspacing="0" class="SubHeaderBlack">
<tr>
	<td class="SubHeaderBlack">{$title}</td>
</tr>
<tr>
	<td class="SubHeaderBlackLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>
</table>
{else}
<table cellspacing="0" class="SubHeader">
<tr>
	<td class="SubHeader">{$title}</td>
</tr>
<tr>
	<td class="SubHeaderLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>
</table>
{/if}
