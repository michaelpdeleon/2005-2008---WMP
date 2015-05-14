{if !$target}{assign var="target" value="cart"}{/if}
{include file="buttons/button_shoppingcart_edit.tpl" href="javascript: openPopupPOptions('`$target`', '`$id`');" target=""}
