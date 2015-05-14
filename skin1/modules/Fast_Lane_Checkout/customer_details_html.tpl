{* $Id: customer_details_html.tpl,v 1.4 2006/02/02 10:13:16 svowl Exp $ *}

<table cellspacing="0" cellpadding="10" width="100%">

<tr>
<td valign="top" width="50%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_contact_information class="grey"}
<table cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="40%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="60%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{if $userinfo.default_fields.firstname}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_first_name}:</td>
<td>{$userinfo.firstname}</td>
</tr>
{/if}
{if $userinfo.default_fields.lastname}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_last_name}:</td>
<td>{$userinfo.lastname}</td>
</tr>
{/if}
{if $userinfo.default_fields.company}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_company}:</td>
<td>{$userinfo.company}</td>
</tr>
{/if}
{if $userinfo.default_fields.tax_number}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_tax_number}:</td>
<td>{$userinfo.tax_number}</td>
</tr>
{/if}
{if $userinfo.default_fields.phone}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_phone}:</td>
<td>{$userinfo.phone}</td>
</tr>
{/if}
{if $userinfo.default_fields.fax}
<tr>  
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_fax}:</td>
<td>{$userinfo.fax}</td>
</tr>
{/if}
{if $userinfo.default_fields.email}
<tr>   
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_email}:</td>
<td>{$userinfo.email}</td>
</tr>
{/if}
{if $userinfo.default_fields.url}
<tr>   
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_web_site}:</td>
<td>{$userinfo.url}</td>
</tr>
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'C' || $v.section eq 'P'}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$v.title}:</td>
<td>{$v.value}</td>
</tr>
{/if}{/foreach}
</table>
</td>
<td> </td>
</tr>

<tr>

<td valign="top" width="50%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_billing_address class="grey"}
<table cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="40%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="60%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{if $userinfo.default_fields.b_firstname}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_first_name}:</td>
<td>{$userinfo.b_firstname}</td>
</tr>
{/if}
{if $userinfo.default_fields.b_lastname}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_last_name}:</td>
<td>{$userinfo.b_lastname}</td>
</tr>
{/if}
{if $userinfo.default_fields.b_address}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_address}:</td>
<td>{$userinfo.b_address}
{if $userinfo.b_address_2}
<br />{$userinfo.b_address_2}
{/if}
</td>
</tr>
{/if}
{if $userinfo.default_fields.b_city}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_city}:</td>
<td>{$userinfo.b_city}</td>
</tr>
{/if}
{if $userinfo.default_fields.b_state}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_state}:</td>
<td>{$userinfo.b_statename}</td>
</tr>
{/if}
{if $userinfo.default_fields.b_zipcode}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_zip_code}:</td>
<td>{$userinfo.b_zipcode}</td>
</tr>
{/if}
{if $userinfo.default_fields.b_country}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_country}:</td>
<td>{$userinfo.b_countryname}</td>
</tr>
{/if}
{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'B'}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$v.title}:</td>
<td>{$v.value}</td>
</tr>
{/if}{/foreach}
</table>
</td>

<td valign="top" width="50%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_shipping_address class="grey"}
<table cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="40%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="60%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{if $userinfo.default_fields.s_firstname}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_first_name}:</td>
<td>{$userinfo.s_firstname}</td>
</tr>
{/if}
{if $userinfo.default_fields.s_lastname}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_last_name}:</td>
<td>{$userinfo.s_lastname}</td>
</tr>
{/if}
{if $userinfo.default_fields.s_address}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_address}:</td>
<td>{$userinfo.s_address}
{if $userinfo.s_address_2}
<br />{$userinfo.s_address_2}
{/if}
</td>
</tr>
{/if}
{if $userinfo.default_fields.s_city}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_city}:</td>
<td>{$userinfo.s_city}</td>
</tr>
{/if}
{if $userinfo.default_fields.s_state}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_state}:</td>
<td>{$userinfo.s_statename}</td>
</tr>
{/if}
{if $userinfo.default_fields.s_zipcode}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_zip_code}:</td>
<td>{$userinfo.s_zipcode}</td>
</tr>
{/if}
{if $userinfo.default_fields.s_country}
<tr> 
<td class="wwmp_cartplaceorder_personalinfo_labels">{$lng.lbl_country}:</td>
<td>{$userinfo.s_countryname}</td>
</tr>
{/if}
{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'S'}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$v.title}:</td>
<td>{$v.value}</td>
</tr>
{/if}
{/foreach}
</table>
</td>

</tr>

{capture name=addfields}
{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'A'}
<tr>
<td class="wwmp_cartplaceorder_personalinfo_labels">{$v.title}:</td>
<td>{$v.value}</td>
</tr>
{/if}
{/foreach}
{/capture}

{if $smarty.capture.addfields ne ""}
<tr>
<td valign="top" width="50%">
{include file="customer/main/subheader.tpl" title=$lng.lbl_additional_information class="grey"}
<table cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="40%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
<td width="60%"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{$smarty.capture.addfields}
</table>
</td>
<td> </td>
</tr>
{/if}

<tr>
	<td><a href="register.php?mode=update&amp;action=cart&amp;paymentid=`$smarty.get.paymentid`"><img src="{$ImagesDir}/wwmp_editbtn11.07.06.jpg" border="0"></a></td>
</tr>
</table>
