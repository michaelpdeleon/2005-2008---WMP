{* $Id: password_recover.tpl,v 1.10 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear_customer},

{$lng.eml_password_recovery_msg}

{section name=acc_num loop=$accounts}
{$lng.lbl_account_information}:
--------------------
{$lng.lbl_usertype|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$accounts[acc_num].usertype}
{$lng.lbl_username|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$accounts[acc_num].login}
{$lng.lbl_password|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$accounts[acc_num].password}

{/section}

{include file="mail/signature.tpl"}
