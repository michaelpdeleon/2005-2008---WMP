{* $Id: signin_notification.tpl,v 1.10 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

{$lng.eml_signin_notification}

{$lng.lbl_your_profile}:
---------------------
{include file="mail/profile_data.tpl" show_pwd="Y"}


{include file="mail/signature.tpl"}
