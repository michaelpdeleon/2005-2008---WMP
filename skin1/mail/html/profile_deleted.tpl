{* $Id: profile_deleted.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.eml_profile_deleted|substitute:"company":$config.Company.company_name}

{include file="mail/html/signature.tpl"}

