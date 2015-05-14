{* $Id: multimedia_links.tpl,v 1.8 2005/11/28 08:15:02 max Exp $ *}
<p />
{if $product.param01 eq "" and $product.param02 eq "" and $product.param03 eq ""}
{$lng.txt_no_samples}
{/if}
{if $product.param01 ne ""}
<a href="{$product.param01}"><img src="{$ImagesDir}/real-player.gif" width="32" height="28" border=0 /><br />Sample#1</a>
{/if}
<p />
{if $product.param02 ne ""}
<a href="{$product.param02}"><img src="{$ImagesDir}/real-player.gif" width="32" height="28" border=0 /><br />Sample#2</a>
{/if}
<p />
{if $product.param03 ne ""}
<a href="{$product.param03}"><img src="{$ImagesDir}/real-player.gif" width="32" height="28" border=0 /><br />Sample#3</a>
{/if}
