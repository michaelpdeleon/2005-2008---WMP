{* ------------------------------------------ *}
{* Deleted the CONFIG SETTINGS.  FOLLOW THE SETTINGS on v4menu.tpl by Michael de Leon 10.31.06 *}
{* ------------------------------------------ *}
{if $config.General.root_categories eq "Y"}
<!-- Start addition by Michael de Leon 10.30.06 -->
<table cellspacing="1" width="100%" class="VertMenuBorder">
<tr>
<td class="VertMenuTitle">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td><img src="{$ImagesDir}/wwmp_od_icon10.26.06.jpg" class="VertMenuTitleIcon" alt="{$menu_title|escape}" />{$link_end}</td>
<td width="100%" align="right"><font class="VertMenuTitle">Other Divisions</font></td>
</tr></table>
</td>
</tr>
</table>
<div id="v4menuODwrapper">
<div class="v4menuOD">
<!-- End addition by Michael de Leon 10.30.06 -->
<ul>
{foreach from=$categories item=c}
	{if $c.categoryid eq "25"}
		<div class="v4menuODFS">
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menuODtable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
		</div>
	{elseif $c.categoryid eq "26"}
		<div class="v4menuODGP">
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menuODtable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
		</div>
	{elseif $c.categoryid eq "27"}
		<div class="v4menuODIC">
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menuODtable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
		</div>
	{elseif $c.categoryid eq "28"}
		<div class="v4menuODJS">
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menuODtable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
		</div>
	{elseif $c.categoryid eq "29"}
		<div class="v4menuODSM">
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menuODtable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
		</div>
	{/if}
{/foreach}
</ul>
</div>
</div>
{/if}
