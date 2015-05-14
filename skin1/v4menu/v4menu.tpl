{* ------------------------------------------ *}
{* Display icons? y/n *}
{assign var='showicons' value='n'}
{* Define icon *}
{assign var='iconsymbol' value='<font color="#F4F88E">&#8226;</font>&nbsp;'}
{* Display arrows? y/n *}
{* Start addition by Michael de Leon 10.27.06 *}
{assign var='showarrows' value='n'}
{* End addition by Michael de Leon 10.27.06 *}
{* Deleted by Michael de Leon 10.27.06 
{* assign var='showarrows' value='y' *}
{* Deleted by Michael de Leon 10.27.06*}
{* Define arrow *}
{assign var='arrowsymbol' value='<font color="#8FD2A5">&raquo;</font>'}
{* ------------------------------------------ *}
{if $config.General.root_categories eq "Y"}
<!-- Start addition by Michael de Leon 10.30.06 -->
<table cellspacing="1" width="100%" class="VertMenuBorder">
<tr>
<td class="VertMenuTitle">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td><img src="{$ImagesDir}/wwmp_lb_icon10.26.06.jpg" class="VertMenuTitleIcon" alt="{$menu_title|escape}" />{$link_end}</td>
<td width="100%" align="right"><font class="VertMenuTitle">Lab Products</font></td>
</tr></table>
</td>
</tr>
</table>
<div id="v4menuwrapper">
<div class="v4menu">
<!-- End addition by Michael de Leon 10.30.06 -->
<ul>
{foreach from=$categories item=c}
<!-- Start addition by Michael de Leon 10.30.06 -->
	{if $c.categoryid ne "25" && $c.categoryid ne "26" && $c.categoryid ne "27" && $c.categoryid ne "28" && $c.categoryid ne "29"}
<!-- End addition by Michael de Leon 10.30.06 -->
		<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
		{if $c.subcats ne ""}
			<table class="v4menutable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		{/if}
		</li>
<!-- Start addition by Michael de Leon 10.30.06 -->
	{/if}
<!-- End addition by Michael de Leon 10.30.06 -->
{/foreach}
</ul>
</div>
</div>
{/if}
