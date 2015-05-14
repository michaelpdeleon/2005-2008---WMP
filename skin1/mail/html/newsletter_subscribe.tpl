{* $Id: newsletter_subscribe.tpl,v 1.5 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<p />{$lng.eml_subscribed}

<p />{$lng.eml_unsubscribe_information}
<br />
<a href="{$http_location}/mail/unsubscribe.php?email={$email|escape}">{$http_location}/mail/unsubscribe.php?email={$email|escape}</a>

{include file="mail/html/signature.tpl"}
