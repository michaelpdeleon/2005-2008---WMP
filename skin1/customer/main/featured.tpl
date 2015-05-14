{* $Id: featured.tpl,v 1.16 2005/11/17 06:55:37 max Exp $ *}
{capture name=dialog}
{if $f_products ne ""}
{if $total_pages gt 2}
<br />
{ include file="customer/main/navigation.tpl" }
{/if}
{include file="customer/main/products.tpl" products=$f_products featured="Y"}
{if $total_pages gt 2}
<br />
{ include file="customer/main/navigation.tpl" }
{/if}
{else}
{$lng.txt_no_featured}
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_featured_products content=$smarty.capture.dialog extra='width="100%"'}
