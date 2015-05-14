{* $Id: egoods_download_keys.tpl,v 1.15 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear_customer},

{$lng.eml_egoods}

{$lng.eml_egoods_download}:
--------------------
{section name=prod_num loop=$products}
{if $products[prod_num].download_key}
{$lng.lbl_sku|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].productcode}
{$lng.lbl_product|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].product}
{$lng.lbl_item_price|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{include file="currency.tpl" value=$products[prod_num].price}

{$lng.lbl_filename|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$products[prod_num].distribution_filename}
{$lng.lbl_download_url|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$catalogs.customer}/download.php?id={$products[prod_num].download_key}

{/if}
{/section}

{$lng.eml_egoods_download_note|substitute:"ttl":$config.Egoods.download_key_ttl}

{include file="mail/signature.tpl"}
