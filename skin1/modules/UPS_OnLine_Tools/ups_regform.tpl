{* $Id: ups_regform.tpl,v 1.5 2006/02/01 14:26:44 max Exp $ *}
{include file="check_email_script.tpl"}

<table width="100%" cellspacing="3" cellpadding="2">

<tr valign="middle">
	<td>{$lng.lbl_contact_name}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[contact_name]" size="32" maxlength="30" value="{$userinfo.contact_name}" style="width:250;" />
{if $reg_error ne "" and $userinfo.contact_name eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_title}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[title_name]" size="32" maxlength="35" value="{$userinfo.title_name}" style="width:250;" />
{if $reg_error ne "" and $userinfo.title_name eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_company_name}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[company]" size="32" maxlength="35" value="{$userinfo.company}" style="width:250;" />
{if $reg_error ne "" and $userinfo.company eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_street_address}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[address]" size="32" maxlength="50" value="{$userinfo.address}" style="width:250;" />
{if $reg_error ne "" and $userinfo.address eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_city}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[city]" size="32" maxlength="50" value="{$userinfo.city}" style="width:250;" />
{if $reg_error ne "" and $userinfo.city eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_state}:</td>
	<td nowrap="nowrap">
<select name="posted_data[state]" style="width:250;">
<option value="">{$lng.lbl_non_us_canada_address}</option>
{foreach key=code item=state from=$ups_states}
<option value="{$code}"{if $userinfo.state eq $code} selected="selected"{/if}>{$state}</option>
{/foreach}
</select>
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_country}:</td>
	<td nowrap="nowrap">
<select name="posted_data[country]" style="width:250;">
<option value="">{$lng.lbl_please_select_one}</option>
{foreach key=code item=country from=$ups_countries}
<option value="{$code}"{if $userinfo.country eq $code} selected="selected"{/if}>{$country}</option>
{/foreach}
</select>
{if $reg_error ne "" and $userinfo.country eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_zip_code}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[postal_code]" size="32" maxlength="11" value="{$userinfo.postal_code}" style="width:250;" />
{if $reg_error ne "" and $userinfo.postal_code eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_phone_number}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[phone]" size="32" maxlength="25" value="{$userinfo.phone}" style="width:250;" />
{if $reg_error ne "" and $userinfo.phone eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr>
	<td colspan="2"><b>{$lng.txt_note}:</b> {$lng.txt_ups_phone_number}</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_web_site_url}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[url]" size="32" maxlength="254" value="{$userinfo.url}" style="width:250;" />
{if $reg_error ne "" and $userinfo.url eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_email_address}:</td>
	<td nowrap="nowrap">
<input type="text" name="email" size="32" maxlength="50" value="{$userinfo.email}" style="width:250;" />
{if $reg_error ne "" and $userinfo.email eq ""}<font class="Star">&lt;&lt;</font>{/if}
	</td>
</tr>

<tr valign="middle">
	<td>{$lng.lbl_ups_account_number}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[shipper_number]" size="32" maxlength="10" value="{$userinfo.shipper_number}" style="width:250;" />
	</td>
</tr>

<tr>
	<td colspan="2">
{$lng.txt_ups_account_number_note}

<br /><br /><br />

{$lng.lbl_ups_reg_contact_me}
<table>

<tr>
	<td><input type="radio" name="posted_data[software_installer]" value="yes"{if $userinfo.software_installer eq "yes"} checked="checked"{/if} /></td>
	<td>{$lng.lbl_yes}</td>
	<td>&nbsp;&nbsp;</td>
	<td><input type="radio" name="posted_data[software_installer]" value="no"{if $userinfo.software_installer eq "no"} checked="checked"{/if} /></td>
	<td>{$lng.lbl_no}</td>
{if $reg_error ne "" and $userinfo.software_installer ne "yes" and $userinfo.software_installer ne "no"}
	<td><font class="Star">&lt;&lt;</font></td>
{/if}
</tr>

</table>

	</td>
</tr>

</table>
