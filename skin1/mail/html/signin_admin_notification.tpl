{* $Id: signin_admin_notification.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_signin_admin_notification}

<p />{$lng.lbl_profile_details}:

{include file="mail/html/profile_data.tpl" show_pwd="Y"}

{include file="mail/html/signature.tpl"}

