{* $Id: profile_admin_modified.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_profile_modified_admin}

<p />{$lng.lbl_profile_details}:

{include file="mail/html/profile_data.tpl" show_pwd="Y"}

{include file="mail/html/signature.tpl"}

