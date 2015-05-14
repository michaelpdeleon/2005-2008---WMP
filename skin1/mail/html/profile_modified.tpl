{* $Id: profile_modified.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.txt_profile_modified}

<p />{$lng.lbl_your_profile}:

{include file="mail/html/profile_data.tpl"}

{include file="mail/html/signature.tpl"}
