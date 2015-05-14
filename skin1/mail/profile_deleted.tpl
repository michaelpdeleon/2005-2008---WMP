{* $Id: profile_deleted.tpl,v 1.14 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

{$lng.eml_profile_deleted|substitute:"company":$config.Company.company_name}

{include file="mail/signature.tpl"}
