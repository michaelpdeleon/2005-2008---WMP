{* $Id: product_images.tpl,v 1.16.2.1 2006/05/18 08:02:40 max Exp $ *}
{if $images ne ""}
{capture name=dialog}
<center>
{section name=image loop=$images}
{if $images[image].avail eq "Y"}
{if $images[image].tmbn_url}
<img src="{$images[image].tmbn_url}" alt="{$images[image].alt|escape}" style="padding-bottom: 10px;" />
{else}
<img src="{$xcart_web_dir}/image.php?id={$images[image].imageid}&amp;type=D" alt="{$images[image].alt|escape}" style="padding-bottom: 10px;" />
{/if}
<br />
{/if}
{/section}
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_detailed_images content=$smarty.capture.dialog extra='width="100%"'}
{/if}
