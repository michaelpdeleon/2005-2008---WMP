{* $Id: products_export.tpl,v 1.9 2004/07/23 07:32:25 max Exp $ *}{if $show_column_headers eq "Y"}{assign var="cntr" value=1}{foreach key=key item=item from=$products.0}{if $key ne 'categories' && $key ne 'prices'}{$key|upper}{if $key eq 'category'}{section name=i loop=$max_categories start=1}{$delimiter}CATEGORY{/section}{elseif $key eq 'price'}{section name=i loop=$max_prices start=1}{$delimiter}PRICE{/section}{/if}{if $cntr lt $total_columns}{$delimiter}{math assign="cntr" equation="x+1" x=$cntr}{/if}{/if}{/foreach}

{/if}
{section name=prod_num loop=$products}
{assign var="cntr" value=1}{foreach key=key item=item from=$products[prod_num]}{if $key ne 'categories' && $key ne 'prices'}{$item}{if $key eq 'category' && $max_categories > 1}{foreach from=$products[prod_num].categories item=c}{$delimiter}{$c}{/foreach}{elseif $key eq 'price' && $max_prices > 1}{foreach from=$products[prod_num].prices item=p}{$delimiter}{$p|default:"0.00"}{/foreach}{/if}{if $cntr lt $total_columns}{$delimiter}{math assign="cntr" equation="x+1" x=$cntr}{/if}{/if}{/foreach}

{/section}
