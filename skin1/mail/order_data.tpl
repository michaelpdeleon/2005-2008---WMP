{* $Id: order_data.tpl,v 1.42 2006/03/30 05:46:32 max Exp $ *}
{$lng.lbl_products_ordered}:
-----------------

{section name=prod_num loop=$products}
{$lng.lbl_sku|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].productcode}
{$lng.lbl_product|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].product}
{$lng.lbl_quantity|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].amount}
{if $products[prod_num].product_options ne ""}
{$lng.lbl_selected_options}:
{include file="modules/Product_Options/display_options.tpl" options=$products[prod_num].product_options options_txt=$products[prod_num].product_options_txt is_plain="Y"}
{/if}
{$lng.lbl_item_price|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$products[prod_num].display_price}
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}

{foreach from=$products[prod_num].extra_data.taxes key=tax_name item=tax}
{if $tax.tax_value gt 0}{$tax.tax_display_name} {if $tax.rate_type eq "%"}{$tax.rate_value|formatprice}%{else}{include file="currency.tpl" value=$tax.rate_value}{/if}{/if}

{/foreach}
{/if}


{/section}
{section name=giftcert loop=$giftcerts}
{$lng.lbl_gift_certificate|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$giftcerts[giftcert].gcid}
{$lng.lbl_amount|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$giftcerts[giftcert].amount}

{$lng.lbl_recipient|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$giftcerts[giftcert].recipient}
{if $giftcerts[giftcert].send_via eq "P"}
{$lng.lbl_gc_send_via_postal_mail}
{$lng.lbl_mail_address|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$giftcerts[giftcert].recipient_firstname} {$giftcerts[giftcert].recipient_lastname}
		{$giftcerts[giftcert].recipient_address}, {$giftcerts[giftcert].recipient_city},
		{if $giftcerts[giftcert].recipient_countyname ne ''}{$giftcerts[giftcert].recipient_countyname} {/if}{$giftcerts[giftcert].recipient_state} {$giftcerts[giftcert].recipient_country}, {$giftcerts[giftcert].recipient_zipcode}
{$lng.lbl_phone|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$giftcerts[giftcert].recipient_phone}
{else}
{$lng.lbl_recipient_email|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$giftcerts[giftcert].recipient_email}
{/if}

{/section}

{$lng.lbl_total}:
-------
{$lng.lbl_payment_method|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$order.payment_method}
{$lng.lbl_delivery|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$order.shipping|trademark}
{$lng.lbl_subtotal|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.display_subtotal}

{if $order.discount gt 0}{$lng.lbl_discount|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.discount}{/if}

{if $order.coupon and $order.coupon_type ne "free_ship"}
{$lng.lbl_coupon_saving|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.coupon_discount} ({$order.coupon})
{/if}
{if $order.discounted_subtotal ne $order.subtotal}
{$lng.lbl_discounted_subtotal|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.display_discounted_subtotal}

{/if}
{$lng.lbl_shipping_cost|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.display_shipping_cost}

{if $order.coupon and $order.coupon_type eq "free_ship"}
{$lng.lbl_coupon_saving|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.coupon_discount} ({$order.coupon})

{/if}
{if $order.applied_taxes and $order.extra.tax_info.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$order.applied_taxes}
{assign var="rate_value" value=$tax.rate_value|formatprice}{if $tax.rate_type eq "%"}{assign var="tax_display_name" value="`$tax.tax_display_name` `$rate_value`%"}{else}{assign var="tax_display_name" value=$tax.tax_display_name}{/if}{$tax_display_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$tax.tax_cost}

{/foreach}
{/if}
{if $order.payment_surcharge ne 0}
{if $order.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{else}{$lng.lbl_payment_method_discount|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{/if}{include file="currency.tpl" value=$order.payment_surcharge}
{/if}
{if $order.giftcert_discount gt 0}
{$lng.lbl_giftcert_discount|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.giftcert_discount}
{/if}

{$lng.lbl_total|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.total}

{if $_userinfo.tax_exempt ne "Y"}
{if $order.applied_taxes and $order.extra.tax_info.display_taxed_order_totals eq "Y"}
{$lng.lbl_including}:
{foreach key=tax_name item=tax from=$order.applied_taxes}
{assign var="rate_value" value=$tax.rate_value|formatprice}{if $tax.rate_type eq "%"}{assign var="tax_display_name" value="`$tax.tax_display_name` `$rate_value`%"}{else}{assign var="tax_display_name" value=$tax.tax_display_name}{/if}{$tax_display_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$tax.tax_cost}

{/foreach}
{/if}
{else}
{$lng.txt_tax_exemption_applied|strip_tags}
{/if}

{if $order.applied_giftcerts}
{$lng.lbl_applied_giftcerts}:
{section name=gc loop=$order.applied_giftcerts}
    {$order.applied_giftcerts[gc].giftcert_id|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$order.applied_giftcerts[gc].giftcert_cost}

{/section}
{/if}

{if $order.extra.special_bonuses ne ""}
{include file="mail/special_offers_order_bonuses.tpl" bonuses=$order.extra.special_bonuses}
{/if}

