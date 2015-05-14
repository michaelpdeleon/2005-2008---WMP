{* $Id: error_ccprocessor_error.tpl,v 1.18 2005/11/28 08:15:02 max Exp $ *}
<font class="ErrorMessage">
{$lng.err_payment_declined_order}<br />
{if $smarty.get.bill_message ne ""}<span class="NumberOfArticles">{$lng.err_payment_reason}:</span> <font class="TableCenterErrorMessageOrange">{$smarty.get.bill_message|escape:"html"}<br /><br />{/if}
<p />
{include file="buttons/go_back.tpl" href="`$catalogs.customer`/cart.php?mode=checkout"}
</font>
