{* $Id: image_property.tpl,v 1.1.2.1 2006/06/16 10:47:41 max Exp $ *}
{if $image && $image.image_type ne '' && $image.image_size > 0}
{$lng.lbl_image_size|escape}: {$image.image_x}x{$image.image_y}, {math equation="round(x/1024, 3)" x=$image.image_size}Kb
{$lng.lbl_image_type|escape}: {$image.image_type|replace:"image/":""|upper}
{/if}
