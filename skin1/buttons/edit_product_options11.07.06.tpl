{if !$target}{assign var="target" value="cart"}{/if}
{include file="buttons/button.tpl" button_title=$lng.lbl_edit_options href="javascript: openPopupPOptions('`$target`', '`$id`');" target=""}
