{strip}
{*** Google Analytics conversion tracking ***}
{* by cart-lab.com for X-cart 4.0.x *}
{* Make sure carriage return/line feeds stay intact! *}
{* https://www.google.com/support/analytics/bin/answer.py?answer=27203&hl=en *}
<form style="display:none;" name="utmform"><textarea id="utmtrans">
{literal}UTM:T|{/literal}{$order.orderid}{literal}||{/literal}{$order.subtotal}{literal}|{/literal}{$order.applied_taxes.tax_cost}{literal}||{/literal}{$order.b_city|escape}{literal}|{/literal}{$order.b_state|escape}{literal}|{/literal}{$order.b_countryname|escape}
{/strip}
{if $products}{foreach from="$products" item="product"}
{strip}{literal}UTM:I|{/literal}{$orders.orderid}{literal}|{/literal}{$product.productcode|escape}{literal}|{/literal}{$product.product|escape}{literal}||{/literal}{$product.display_price}{literal}|{/literal}{$product.amount}{/strip}
{/foreach}{/if}</textarea></form>
