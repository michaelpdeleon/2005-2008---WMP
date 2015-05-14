{* $Id: customer_details.tpl,v 1.12.2.1 2006/06/13 11:18:42 max Exp $ *}
{if $userinfo.field_sections.P}
{$lng.lbl_personal_information}:
---------------------
{if $userinfo.default_fields.firstname}
  {$lng.lbl_first_name}:   {$userinfo.firstname}
{/if}
{if $userinfo.default_fields.lastname}
  {$lng.lbl_last_name}:    {$userinfo.lastname}
{/if}
{if $userinfo.default_fields.phone}
  {$lng.lbl_phone}:        {$userinfo.phone}
{/if}
{if $userinfo.default_fields.fax}
  {$lng.lbl_fax}:          {$userinfo.fax}
{/if}
{if $userinfo.default_fields.email}
  {$lng.lbl_email}:       {$userinfo.email}
{/if}
{if $userinfo.default_fields.url}
  {$lng.lbl_web_site}:     {$userinfo.url}
{/if}
{if $userinfo.default_fields.tax_number}
  {$lng.lbl_tax_number}:   {$userinfo.tax_number}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'C' || $v.section eq 'P'}
  {$v.title}:              {$v.value}
{/if}{/foreach}

{/if}{if $userinfo.field_sections.B}
{$lng.lbl_billing_address}:
----------------
{if $userinfo.default_fields.b_firstname}
  {$lng.lbl_first_name}:   {$userinfo.b_firstname}
{/if}
{if $userinfo.default_fields.b_lastname}
  {$lng.lbl_last_name}:    {$userinfo.b_lastname}
{/if}
{if $userinfo.default_fields.b_address}
  {$lng.lbl_address}:      {$userinfo.b_address}
{if $userinfo.b_address_2}
		{$userinfo.b_address_2}
{/if}
{/if}
{if $userinfo.default_fields.b_city}
  {$lng.lbl_city}:         {$userinfo.b_city}
{/if}
{if $userinfo.default_fields.b_state}
  {$lng.lbl_state}:        {$userinfo.b_statename}
{/if}
{if $userinfo.default_fields.b_country}
  {$lng.lbl_country}:      {$userinfo.b_countryname}
{/if}
{if $userinfo.default_fields.b_zipcode}
  {$lng.lbl_zip_code}:     {$userinfo.b_zipcode}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'B'}
  {$v.title}:              {$v.value}
{/if}{/foreach}

{/if}{if $userinfo.field_sections.S}
{$lng.lbl_shipping_address}:
-----------------
{if $userinfo.default_fields.s_firstname}
  {$lng.lbl_first_name}:   {$userinfo.s_firstname}
{/if}
{if $userinfo.default_fields.s_lastname}
  {$lng.lbl_last_name}:    {$userinfo.s_lastname}
{/if}
{if $userinfo.default_fields.s_address}
  {$lng.lbl_address}:      {$userinfo.s_address}
{if $userinfo.s_address_2}
		{$userinfo.s_address_2}
{/if}
{/if}
{if $userinfo.default_fields.s_city}
  {$lng.lbl_city}:         {$userinfo.s_city}
{/if}
{if $userinfo.default_fields.s_state}
  {$lng.lbl_state}:        {$userinfo.s_statename}
{/if}
{if $userinfo.default_fields.s_country}
  {$lng.lbl_country}:      {$userinfo.s_countryname}
{/if}
{if $userinfo.default_fields.s_zipcode}
  {$lng.lbl_zip_code}:     {$userinfo.s_zipcode}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'S'}
  {$v.title}:              {$v.value}
{/if}{/foreach}{/if}{assign var="is_header" value=""}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'A'}
{if $is_header ne 'Y'}
 
{$lng.lbl_additional_information}:
-----------------
{assign var="is_header" value="Y"}{/if}
 {$v.title}:               {$v.value}
{/if}{/foreach} 
