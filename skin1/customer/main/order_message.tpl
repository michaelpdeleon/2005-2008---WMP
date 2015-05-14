{* $Id: order_message.tpl,v 1.35 2005/11/28 14:19:29 max Exp $ *}
<!-- Start addition by Michael de Leon 11.20.06 -->
<img src="{$ImagesDir}/wwmp_temp_banner.jpg">
<!-- End addition by Michael de Leon 11.20.06 -->
{if $this_is_printable_version eq ""}
{capture name=dialog}
<font class="ProductDetails">{$lng.txt_order_placed}</font>
<br /><br />
<font class="ProductDetails">{$lng.txt_order_placed_msg}</font>
<br />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
{/if}
<br />
{capture name=dialog}
{section name=oi loop=$orders}
{include file="mail/html/order_invoice.tpl" is_nomail='Y' products=$orders[oi].products giftcerts=$orders[oi].giftcerts userinfo=$orders[oi].userinfo order=$orders[oi].order}
<br /><br /><br /><br />
{if $active_modules.Interneka ne ""}
{ include file="modules/Interneka/interneka_tags.tpl" } 
{/if}
<!-- Start addition by Michael de Leon 12.11.06 for Google Analytics -->
{*** Google Analytics conversion tracking ***}
{include file="g_analytics.tpl" products=$orders[oi].products order=$orders[oi].order}
<!-- End addition by Michael de Leon 12.11.06 for Google Analytics -->
{/section}
{if $this_is_printable_version eq ""}
<table width="100%">
<tr>
<td align="left">
<!-- Deleted by Michael de Leon 11.20.06
{* include file="buttons/button.tpl" button_title=$lng.lbl_print_invoice href="order.php?mode=invoice&orderid=`$orderids`" target="preview_invoice" *}
-->
<a href="order.php?mode=invoice&orderid={$orderids}" target="preview_invoice"><img src="{$ImagesDir}/wwmp_printinvoicebtn11.20.06.jpg" border="0"></a>
</td>
<td align="right">
<!-- Deleted by Michael de Leon 11.20.06
{include file="buttons/button.tpl" button_title=$lng.lbl_continue_shopping style="button" href="home.php"}
-->
<a href="home.php"><img src="{$ImagesDir}/wwmp_continueshoppingbtnsmall11.20.06.jpg" border="0"></a>
</td>
</tr>
</table>
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_invoice content=$smarty.capture.dialog extra='width="100%"'}
