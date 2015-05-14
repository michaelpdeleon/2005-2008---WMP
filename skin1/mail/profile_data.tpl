{* $Id: profile_data.tpl,v 1.11 2006/01/31 14:24:04 svowl Exp $ *}
{$lng.lbl_personal_information}:
---------------------
{$lng.lbl_username|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.login}
{if $show_pwd and $config.Email.show_passwords_in_notifications eq "Y"}{$lng.lbl_password|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.password}
{/if}
{if $userinfo.default_fields.firstname}{$lng.lbl_first_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.firstname}
{/if}
{if $userinfo.default_fields.lastname}{$lng.lbl_last_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.lastname}
{/if}
{if $userinfo.default_fields.company}{$lng.lbl_company|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.company}
{/if}
{if $userinfo.default_fields.tax_number}{$lng.lbl_tax_number|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.tax_number}
{/if}
{if $userinfo.tax_exempt eq 'Y'}{$lng.lbl_tax_exempt|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$lng.txt_tax_exemption_assigned}
{/if}
{if $userinfo.membership}{$lng.lbl_membership|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.membership}
{/if}
{if $userinfo.pending_membershipid ne $userinfo.membershipid}{$lng.lbl_signup_for_membership|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.pending_membership}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'P'}
{$v.title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$v.value}
{/if}{/foreach}

{if $userinfo.field_sections.B}
{$lng.lbl_billing_address}:
----------------
{if $userinfo.default_fields.b_firstname}{$lng.lbl_first_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_firstname}
{/if}
{if $userinfo.default_fields.b_lastname}{$lng.lbl_last_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_lastname}
{/if}
{if $userinfo.default_fields.b_address}{$lng.lbl_address|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_address}
{/if}
{if $userinfo.default_fields.b_address_2}{$lng.lbl_address_2|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_address_2}
{/if}
{if $userinfo.default_fields.b_city}{$lng.lbl_city|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_city}
{/if}
{if $userinfo.default_fields.b_county && $config.General.use_counties eq "Y"}{$lng.lbl_counties|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_countyname}
{/if}
{if $userinfo.default_fields.b_state}{$lng.lbl_state|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_statename}
{/if}
{if $userinfo.default_fields.b_country}{$lng.lbl_country|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_countryname}
{/if}
{if $userinfo.default_fields.b_zipcode}{$lng.lbl_zip_code|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.b_zipcode}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'B'}
{$v.title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$v.value}
{/if}{/foreach}
{/if}

{if $userinfo.field_sections.S}
{$lng.lbl_shipping_address}:
-----------------
{if $userinfo.default_fields.s_firstname}{$lng.lbl_first_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_firstname}
{/if}
{if $userinfo.default_fields.s_lastname}{$lng.lbl_last_name|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_lastname}
{/if}
{if $userinfo.default_fields.s_address}{$lng.lbl_address|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_address}
{/if}
{if $userinfo.default_fields.s_address_2}{$lng.lbl_address_2|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_address_2}
{/if}
{if $userinfo.default_fields.s_city}{$lng.lbl_city|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_city}
{/if}
{if $userinfo.default_fields.s_county && $config.General.use_counties eq "Y"}{$lng.lbl_counties|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_countyname}
{/if}
{if $userinfo.default_fields.s_state}{$lng.lbl_state|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_statename}
{/if}
{if $userinfo.default_fields.s_country}{$lng.lbl_country|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_countryname}
{/if}
{if $userinfo.default_fields.s_zipcode}{$lng.lbl_zip_code|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.s_zipcode}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'S'}
{$v.title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$v.value}
{/if}{/foreach}
{/if}

{if $userinfo.default_fields.phone}{$lng.lbl_phone|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.phone}
{/if}
{if $userinfo.default_fields.fax}{$lng.lbl_fax|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.fax}
{/if}
{if $userinfo.default_fields.email}{$lng.lbl_email|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.email}
{/if}
{if $userinfo.default_fields.url}{$lng.lbl_url|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$userinfo.url}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'C' || $v.section eq 'A'}
{$v.title|truncate:$max_truncate:"...":true|cat:":"|string_format:$max_space}{$v.value}
{/if}{/foreach}

