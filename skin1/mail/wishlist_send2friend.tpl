{* $Id: wishlist_send2friend.tpl,v 1.10 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}
{$lng.eml_hello}

{$lng.eml_send2friend|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}

{$product.product}
===========================================
{$product.descr}

{$lng.lbl_price}: {include file="currency.tpl" value=$product.price}


{$lng.eml_click_to_view_product}:

 {$catalogs.customer}/product.php?productid={$product.productid}

{include file="mail/signature.tpl"}
