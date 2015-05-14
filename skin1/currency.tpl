{* $Id: currency.tpl,v 1.13.2.1 2006/04/29 06:36:56 max Exp $ *}
{if $plain_text_message eq ""}<span style="WHITE-SPACE: nowrap">{/if}{if $display_sign}{if $value gte 0}+{else}-{/if}{/if}{$config.General.currency_symbol}{$value|abs_value|formatprice}{if $plain_text_message eq ""}</span>{/if}
