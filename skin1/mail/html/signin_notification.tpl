{* $Id: signin_notification.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.eml_signin_notification}

<p />{$lng.lbl_your_profile}:

{include file="mail/html/profile_data.tpl" show_pwd="Y"}

{include file="mail/html/signature.tpl"}

