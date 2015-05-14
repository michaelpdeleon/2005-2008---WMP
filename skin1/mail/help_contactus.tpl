{* $Id: help_contactus.tpl,v 1.13.2.1 2006/05/03 13:37:03 max Exp $ *}
{include file="mail/mail_header.tpl"}
{assign var=max_truncate value=$config.Email.max_truncate}{math assign="max_space" equation="x+5" x=$max_truncate}{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_customers_need_help}

{if $is_areas.C}
{$lng.lbl_customer_info}:
---------------------
{if $default_fields.title.avail}{$lng.lbl_title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.title}
{/if}
{if $default_fields.firstname.avail}{$lng.lbl_first_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.firstname}
{/if}
{if $default_fields.lastname.avail}{$lng.lbl_last_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.lastname}
{/if}
{if $default_fields.company.avail}{$lng.lbl_company|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.company}
{/if}

{/if}
{if $is_areas.A}
{$lng.lbl_address}:
----------------
{if $default_fields.b_address.avail}{$lng.lbl_address|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_address}
{/if}
{if $default_fields.b_address_2.avail}{$lng.lbl_address_2|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_address_2}
{/if}
{if $default_fields.b_city.avail}{$lng.lbl_city|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_city}
{/if}
{if $default_fields.b_county.avail && $config.General.use_counties eq "Y"}
{$lng.lbl_county|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_countyname}
{/if}
{if $default_fields.b_state.avail}{$lng.lbl_state|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_statename}
{/if}
{if $default_fields.b_country.avail}{$lng.lbl_country|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_countryname}
{/if}
{if $default_fields.b_zipcode.avail}{$lng.lbl_zip_code|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.b_zipcode}
{/if}

{if $default_fields.phone.avail}{$lng.lbl_phone|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.phone}
{/if}
{if $default_fields.fax.avail}{$lng.lbl_fax|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.fax}
{/if}
{if $default_fields.email.avail}{$lng.lbl_email|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.email}
{/if}
{if $default_fields.url.avail}{$lng.lbl_web_site|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.url}
{/if}
{/if}
{if $additional_fields ne ''}

{foreach from=$additional_fields item=v}
{$v.title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$v.value}
{/foreach}
{/if}

{if $default_fields.department.avail}{$lng.lbl_department|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.department}
{/if}
{$lng.lbl_subject|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$contact.subject}
{$lng.lbl_message}:
{$contact.body}

{include file="mail/signature.tpl"}
