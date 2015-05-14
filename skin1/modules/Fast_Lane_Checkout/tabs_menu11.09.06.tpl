<div align="right">
<table cellpadding="0" cellspacing="0" width="70%">

<tr>
{assign var="columns_counter" value=0}
{assign var="curpos" value="B"}
{foreach item=step from=$checkout_tabs}
{math assign="columns_counter" equation="x+1" x=$columns_counter}
<td>
{if $step.selected eq "Y"}
{assign var="curpos" value="A"}
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="50%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="19"><img src="{$ImagesDir}/cart_checkout.gif" width="19" height="16" alt="" /></td>
<td width="50%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>
{else}&nbsp;{/if}</td>
{/foreach}
</tr>

<tr>
<td colspan="{$columns_counter}"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>

<tr>
{assign var="cnt" value=0}
{assign var="curpos" value="B"}
{assign var="mark" value="B"}
{foreach item=step from=$checkout_tabs}
{math assign="cnt" equation="x+1" x=$cnt}
<td>
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<td{if $cnt gt 1} class="{if $curpos eq "B"}LineBeforeCart{else}LineAfterCart{/if}"{/if} width="50%"><img src="{$ImagesDir}/spacer.gif" width="1" height="2" alt="" /></td>
<td class="{if $curpos eq "B"}LineBeforeCart{else}LineAfterCart{/if}" width="2"><img src="{$ImagesDir}/spacer.gif" width="2" height="2" alt="" /></td>
{if $step.selected eq "Y"}{assign var="curpos" value="A"}{/if}
<td {if $cnt lt $columns_counter}class="{if $curpos eq "B"}LineBeforeCart{else}LineAfterCart{/if}"{/if} width="50%"><img src="{$ImagesDir}/spacer.gif" width="1" height="2" alt="" /></td>
</tr>
<tr>
<td width="50%"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
<td class="{if $mark eq "B"}LineBeforeCart{else}LineAfterCart{/if}" width="2"><img src="{$ImagesDir}/spacer.gif" width="2" height="5" alt="" /></td>
{if $mark ne $curpos}{assign var="mark" value="A"}{/if}
<td width="50%"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>
</table>
</td>
{/foreach}
</tr>

<tr>
<td colspan="{$columns_counter}"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td>
</tr>

<tr>
{assign var="hide_link" value=0}
{foreach item=step from=$checkout_tabs}
<td align="center">{if $step.link ne "" and $step.selected ne "Y" and $hide_link eq 0}<a href="{$step.link|amp}" class="CheckoutTab">{$step.title}</a>{else}<font class="CheckoutTabSel">{$step.title}</font>{/if}{if $step.selected eq "Y"}{assign var="hide_link" value=1}{/if}</td>
{/foreach}
</tr>

</table>
</div>
<br />

