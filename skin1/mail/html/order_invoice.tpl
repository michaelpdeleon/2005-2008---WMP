{* $Id: order_invoice.tpl,v 1.37 2006/01/31 14:24:05 svowl Exp $ *}
{if $customer ne ''}{assign var="_userinfo" value=$customer}{else}{assign var="_userinfo" value=$userinfo}{/if}
{config_load file="$skin_config"}
{if $is_nomail ne 'Y'}
<p />
{/if}
<table cellspacing="0" cellpadding="0" width="{if $is_nomail eq 'Y'}100%{else}600{/if}" bgcolor="#ffffff">
<tr>
	<td>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td valign="top"><img src="{$ImagesDir}/companyname_small.jpg" alt="" /></td>
		<td width="100%">
		<table cellspacing="0" cellpadding="2" width="100%">
		<tr>
			<td width="30">&nbsp;</td>
			<td valign="top">
<font style="FONT-SIZE: 28px"><b style="text-transform: uppercase;">{$lng.lbl_invoice}</b></font>
<br /><br />
<b>{$lng.lbl_date}:</b> {$order.date|date_format:$config.Appearance.datetime_format} 
<!-- Start addition by Michael de Leon 09.18.06 -->
EST
<!-- End addition by Michael de Leon 09.18.06 -->
<br /><b>{$lng.lbl_order_id}:</b> #{$order.orderid}<br /><b>{$lng.lbl_order_status}:</b> {include file="main/order_status.tpl" status=$order.status mode="static"}<br />
<b>{$lng.lbl_payment_method}:</b><br />{$order.payment_method}<br /><b>{$lng.lbl_delivery}:</b><br />{$order.shipping|trademark|default:$lng.txt_not_available}
			</td>
			<td valign="bottom" align="right">
<b>{$config.Company.company_name}</b><br />
{$config.Company.location_address}<br />
{$config.Company.location_city}, {$config.Company.location_state_name}<br />
{$config.Company.location_zipcode}<br />
{$config.Company.location_country_name}<br />
{if $config.Company.company_phone}<b>{$lng.lbl_phone_1_title}:</b> {$config.Company.company_phone}<br />{/if}
{if $config.Company.company_phone_2}<b>{$lng.lbl_phone_2_title}:</b> {$config.Company.company_phone_2}<br />{/if}
{if $config.Company.company_fax}<b>{$lng.lbl_fax}:</b> {$config.Company.company_fax}<br />{/if}
{if $config.Company.orders_department}<b>{$lng.lbl_email}:</b> {$config.Company.orders_department}<br />{/if}
{if $order.applied_taxes}
<br />
{foreach from=$order.applied_taxes key=tax_name item=tax}
{$tax.regnumber}<br />
{/foreach}
{/if}
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" alt="" /></td>
	</tr>
	<tr>
		<td bgcolor="#000000"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
	</tr>
	<tr>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	</table>
	<br />
	<table cellspacing="0" cellpadding="0" width="45%" border="0">
{if $_userinfo.default_fields.firstname}
	<tr>
		<td nowrap="nowrap"><b>{$lng.lbl_first_name}:</b></td>
		<td>{$order.firstname}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.lastname}
	<tr>
		<td nowrap="nowrap"><b>{$lng.lbl_last_name}:</b></td>
		<td>{$order.lastname}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.company}
	<tr>
		<td><b>{$lng.lbl_company}:</b></td>
		<td>{$order.company}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.tax_number}
	<tr>
		<td><b>{$lng.lbl_tax_number}:</b></td>
		<td>{$order.tax_number}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.phone}
	<tr>
		<td><b>{$lng.lbl_phone}:</b></td>
		<td>{$order.phone}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.fax}
	<tr>
		<td><b>{$lng.lbl_fax}:</b></td>
		<td>{$order.fax}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.email}
	<tr>
		<td><b>{$lng.lbl_email}:</b></td>
		<td>{$order.email}</td>
	</tr>
{/if}
{if $_userinfo.default_fields.url}
	<tr>
		<td><b>{$lng.lbl_url}:</b></td>
		<td>{$order.url}</td>
	</tr>
{/if}
{foreach from=$_userinfo.additional_fields item=v}
{if $v.section eq 'C' || $v.section eq 'P'}
	<tr>
		<td><b>{$v.title}:</b></td>
        <td>{$v.value}</td>
	</tr>
	{/if}
{/foreach}
	</table>
	<br />
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td width="45%" height="25"><b>{$lng.lbl_shipping_address}</b></td>
		<td width="10%">&nbsp;</td>
		<td width="45%" height="25"><b>{$lng.lbl_billing_address}</b></td>
	</tr>
	<tr>
		<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
		<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
	</tr>
	<tr>
		<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	<tr>
		<td>
		<table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $_userinfo.default_fields.s_firstname}
		<tr>
			<td><b>{$lng.lbl_first_name}:</b> </td>
			<td>{$order.s_firstname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_lastname}
		<tr>
			<td><b>{$lng.lbl_last_name}:</b> </td>
			<td>{$order.s_lastname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_address}
		<tr>
			<td><b>{$lng.lbl_address}:</b> </td>
			<td>{$order.s_address}<br />{$order.s_address_2}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_city}
		<tr>
			<td><b>{$lng.lbl_city}:</b> </td>
			<td>{$order.s_city}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_county && $config.General.use_counties eq 'Y'}
		<tr>
			<td><b>{$lng.lbl_county}:</b> </td>
			<td>{$order.s_countyname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_state}
		<tr>
			<td><b>{$lng.lbl_state}:</b> </td>
			<td>{$order.s_statename}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_zipcode}
		<tr>
			<td><b>{$lng.lbl_zip_code}:</b> </td>
			<td>{$order.s_zipcode}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.s_country}
		<tr>
			<td><b>{$lng.lbl_country}:</b> </td>
			<td>{$order.s_countryname}</td>
		</tr>
{/if}
{foreach from=$_userinfo.additional_fields item=v}
{if $v.section eq 'S'}
		<tr>
			<td><b>{$v.title}:</b></td>
        	<td>{$v.value}</td>
		</tr>
	{/if}
{/foreach}
		</table>
		</td>
		<td>&nbsp;</td>
		<td>
		<table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $_userinfo.default_fields.b_firstname}
		<tr>
			<td><b>{$lng.lbl_first_name}:</b> </td>
			<td>{$order.b_firstname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_lastname}
		<tr>
			<td><b>{$lng.lbl_last_name}:</b> </td>
			<td>{$order.b_lastname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_address}
		<tr>
			<td><b>{$lng.lbl_address}:</b> </td>
			<td>{$order.b_address}<br />{$order.b_address_2}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_city}
		<tr>
			<td><b>{$lng.lbl_city}:</b> </td>
			<td>{$order.b_city}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_county && $config.General.use_counties eq 'Y'}
		<tr>
			<td><b>{$lng.lbl_county}:</b> </td>
			<td>{$order.b_countyname}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_state}
		<tr>
			<td><b>{$lng.lbl_state}:</b> </td>
			<td>{$order.b_statename}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_zipcode}
		<tr>
			<td><b>{$lng.lbl_zip_code}:</b> </td>
			<td>{$order.b_zipcode}</td>
		</tr>
{/if}
{if $_userinfo.default_fields.b_country}
		<tr>
			<td><b>{$lng.lbl_country}:</b> </td>
			<td>{$order.b_countryname}</td>
		</tr>
{/if}
{foreach from=$_userinfo.additional_fields item=v}
{if $v.section eq 'B'}
		<tr>
			<td><b>{$v.title}:</b></td>
        	<td>{$v.value}</td>
		</tr>
	{/if}
{/foreach}
		</table>
        </td>
	</tr>

{assign var="is_header" value=""}
{foreach from=$_userinfo.additional_fields item=v}
{if $v.section eq 'A'}
{if $is_header eq ''}
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td width="45%" height="25"><b>{$lng.lbl_additional_information}</b></td>
	<td colspan="2" width="55%">&nbsp;</td>
</tr>
<tr>
	<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
	<td colspan="2" width="55%"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
</tr>
<tr>
	<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
</tr>
<tr>
	<td><table cellspacing="0" cellpadding="0" width="100%" border="0">
{assign var="is_header" value="E"}
{/if}
<tr valign="top">
	<td><b>{$v.title}</b></td>
   	<td>{$v.value}</td>
</tr>
{/if}
{/foreach}
{if $is_header eq 'E'}
</table></td>
<td colspan="2" width="55%">&nbsp;</td>
</tr>
{/if}


{if $config.Email.show_cc_info eq "Y" and $show_order_details eq "Y"}

	<tr>
	<td colspan="3">&nbsp;</td>
	</tr>

	<tr>
	<td width="45%" height="25"><b>{$lng.lbl_order_payment_details}</b></td>
	<td colspan="2" width="55%">&nbsp;</td>
	</tr>
	
	<tr>
	<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
	<td colspan="2"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	<tr>
	<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>

	<tr>
	<td colspan="3">{$order.details|replace:"\n":"<br />"}</td>
	</tr>

{/if}

{if $order.netbanx_reference}
<tr>
	<td colspan="3">NetBanx Reference: {$order.netbanx_reference}</td>
</tr>
{/if}

	</table>
	<br />
	<br />

{include file="mail/html/order_data.tpl"}

	</td>
</tr>

{if $order.customer_notes ne ""}

<tr>
	<td colspan="3">
	<br />
	<br />
	<table cellspacing="0" cellpadding="0" width="100%" border="0">

	<tr>
		<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_customer_notes}</font></td>
	</tr>

	</table>
	<table cellspacing="0" cellpadding="10" width="100%" border="1">
	<tr>
		<td style="height:50px;">{$order.customer_notes}</td>
	</tr>
	</table>
	</td>
</tr>

{/if}

<tr>
<td align="center"><br /><br /><font style="FONT-SIZE:12px">{$lng.txt_thank_you_for_purchase}</font></td>
</tr>

</table>

