{* $Id: error_max_order.tpl,v 1.2 2005/11/17 06:55:39 max Exp $ *}
{include assign="tmp_value" file="currency.tpl" value=$config.General.maximum_order_amount}
<font class="ErrorMessage">
{$lng.err_checkout_max_order_msg|substitute:"value":$tmp_value}
<br /><br />
{include file="buttons/go_back.tpl"}
</font>
