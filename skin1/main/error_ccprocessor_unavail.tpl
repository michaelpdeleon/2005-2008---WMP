{* $Id: error_ccprocessor_unavail.tpl,v 1.16 2005/11/17 06:55:39 max Exp $ *}
<font class="ErrorMessage">
{$lng.err_payment_cc_not_available}<br />
{$smarty.get.bill_message}
{include file="buttons/go_back.tpl" href="`$catalogs.customer`/cart.php?mode=checkout"}
</font>
