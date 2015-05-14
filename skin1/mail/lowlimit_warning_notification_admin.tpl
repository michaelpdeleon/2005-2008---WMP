{* $Id: lowlimit_warning_notification_admin.tpl,v 1.8.2.1 2006/07/17 07:17:16 max Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_lowlimit_warning_message|substitute:"sender":$config.Company.company_name:"productid":$product.productid}

{$lng.lbl_sku}: {$product.productcode}
{$lng.lbl_product}: {$product.product}
{if $product.product_options ne ""}
{$lng.lbl_selected_options}:
{include file="modules/Product_Options/display_options.tpl" options=$product.product_options options_txt=$product.product_options_txt is_plain="Y"}
{/if}

{$lng.lbl_items_in_stock|substitute:"items":$product.avail}


{include file="mail/signature.tpl"}
