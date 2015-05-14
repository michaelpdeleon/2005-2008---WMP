{* $Id: error_max_items.tpl,v 1.2 2005/11/17 06:55:39 max Exp $ *}
<font class="ErrorMessage">
{$lng.err_checkout_max_items_msg|substitute:"quantity":$config.General.maximum_order_items}
<br /><br />
{include file="buttons/go_back.tpl"}
</font>
