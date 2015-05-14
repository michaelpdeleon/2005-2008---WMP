{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}
{$lng.eml_hello}

{$lng.eml_wish_list_send_msg|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}

{section name=num loop=$wl_products}
===========================================
{$wl_products[num].product}

{$wl_products[num].descr|truncate:200:"..."}

{$lng.lbl_price}: {include file="currency.tpl" value=$wl_products[num].price}

{/section}
===========================================

{$lng.eml_click_to_view_wishlist}:

 {$catalogs.customer}/cart.php?mode=friend_wl&wlid={$wlid}

{include file="mail/signature.tpl"}
