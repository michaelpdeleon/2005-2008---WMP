{* $Id: taxed_price.tpl,v 1.10.2.1 2006/06/29 06:34:20 max Exp $ *}
{if $taxes}
{foreach key=tax_name item=tax from=$taxes}
{if $tax.tax_value gt 0}
{if $tax.display_including_tax eq "Y"}
{if $display_info eq ""}{assign var="display_info" value=$tax.display_info}{/if}
{$lng.lbl_including_tax|substitute:"tax":$tax.tax_display_name}{if $display_info eq "V" or ($display_info eq "A" and $tax.rate_type eq "$")} {if !$is_subtax}<span id="tax_{$tax.taxid}">{/if}{include file="currency.tpl" value=$tax.tax_value}{if !$is_subtax}</span>{/if}{elseif $display_info eq "R"} {if $tax.rate_type eq "$"}{include file="currency.tpl" value=$tax.rate_value}{else}{$tax.rate_value|formatprice}%{/if}{elseif $display_info eq "A"} {if $tax.rate_type eq "%"}{$tax.rate_value|formatprice}% ({if !$is_subtax}<span id="tax_{$tax.taxid}">{/if}{include file="currency.tpl" value=$tax.tax_value}{if !$is_subtax}</span>{/if}){/if}{/if}
<br />
{/if}
{/if}
{/foreach}
{/if}

