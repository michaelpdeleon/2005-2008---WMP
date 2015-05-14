{* $Id: signature.tpl,v 1.11 2005/10/05 11:06:09 max Exp $ *}
--
{$lng.eml_signature}

{$config.Company.company_name}
{if $config.Company.company_phone}{$lng.lbl_phone|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_phone}
{/if}
{if $config.Company.company_fax}{$lng.lbl_fax|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_fax}
{/if}
{if $config.Company.company_website}{$lng.lbl_url|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$config.Company.company_website}
{/if}
