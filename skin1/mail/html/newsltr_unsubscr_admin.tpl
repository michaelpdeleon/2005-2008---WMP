{* $Id: newsltr_unsubscr_admin.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_unsubscribe_admin_msg|substitute:"email":"<b>`$email`</b>"}

{include file="mail/html/signature.tpl"}
