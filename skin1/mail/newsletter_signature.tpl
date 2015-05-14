{* $Id: newsletter_signature.tpl,v 1.6 2006/03/31 05:51:43 svowl Exp $ *}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}
-----------------------------------------------------------
{$lng.eml_unsubscribe_information}
{$http_location}/mail/unsubscribe.php?email={$email|escape}&listid={$listid}

--
{$lng.eml_signature}

{$config.Company.company_name}
{$lng.lbl_phone|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_phone}
{$lng.lbl_fax|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_fax}
{$lng.lbl_url|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_website}

