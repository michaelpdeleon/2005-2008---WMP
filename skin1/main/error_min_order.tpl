{* $Id: error_min_order.tpl,v 1.9 2005/11/17 06:55:39 max Exp $ *}
{include assign="tmp_value" file="currency.tpl" value=$config.General.minimal_order_amount}
<font class="ErrorMessage">
{$lng.err_checkout_not_allowed_msg|substitute:"value":$tmp_value}
<br /><br />
{include file="buttons/go_back.tpl"}
</font>
