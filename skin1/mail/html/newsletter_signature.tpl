{* $Id: newsletter_signature.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
<hr size="1" noshade="noshade" />
{$lng.eml_unsubscribe_information}
<br />
<a href="{$http_location}/mail/unsubscribe.php?email={$email|escape}&listid={$listid}">{$http_location}/mail/unsubscribe.php?email={$email|escape}&amp;listid={$listid}</a>

{include file="mail/html/signature.tpl"}

