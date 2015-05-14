{* ------------------------------------------ *}
{* Display icons? y/n *}
{assign var='showicons' value='n'}
{* Define icon *}
{assign var='iconsymbol' value='<font color="#F4F88E">&#8226;</font>&nbsp;'}
{* Display arrows? y/n *}
{assign var='showarrows' value='y'}
{* Define arrow *}
{assign var='arrowsymbol' value='<font color="#8FD2A5">&raquo;</font>'}
{* ------------------------------------------ *}
<div id="v4menuwrapper">
{if $config.General.root_categories eq "Y"}
<div class="v4menu">
<ul>
{foreach from=$categories item=c}
<li><a href="home.php?cat={$c.categoryid}" title="{$c.category}">{if $showicons eq "y"}{$iconsymbol}{/if}{$c.category}{if $c.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
{if $c.subcats ne ""}
<table class="v4menutable"><tr><td>
<ul>
{foreach from=$c.subcats item=sc}
<li><a href="home.php?cat={$sc.categoryid}" title="{$sc.category}">{$sc.category}{if $sc.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
{if $sc.subcats ne ""}
<table class="v4menutable"><tr><td>
<ul>
{foreach from=$sc.subcats item=ssc}
<li><a href="home.php?cat={$ssc.categoryid}" title="{$ssc.category}">{$ssc.category}{if $ssc.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}<!--[if IE 7]><!--></a><!--<![endif]-->
{if $ssc.subcats ne ""}
<table class="v4menutable"><tr><td>
<ul>
{foreach from=$ssc.subcats item=sssc}
<li><a href="home.php?cat={$sssc.categoryid}" title="{$sssc.category}">{$sssc.category}{if $sssc.subcats && $showarrows eq "y"}&nbsp;{$arrowsymbol}{/if}</a></li>
{/foreach}
</ul>
</td></tr></table>
<!--[if lte IE 6]></a><![endif]-->
{/if}
</li>
{/foreach}
</ul>
</td></tr></table>
<!--[if lte IE 6]></a><![endif]-->
{/if}
</li>
{/foreach}
</ul>
</td></tr></table><!--[if lte IE 6]></a><![endif]-->
{/if}
</li>
{/foreach}
</ul>
</div>
{/if}
</div>