{* $Id: shipping_options.tpl,v 1.36.2.3 2006/07/11 10:57:05 svowl Exp $ *}

{include file="page_title.tpl" title=$lng.lbl_shipping_options}

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

<br />

{$lng.txt_shipping_options_top_text}

<br /><br />

{$lng.lbl_select_service}:
{section name=carrier loop=$carriers}
{if $carriers[carrier].0 eq $carrier}
<b>{$carriers[carrier].1}</b>
{else}
<a href="shipping_options.php?carrier={$carriers[carrier].0}">{$carriers[carrier].1}</a>
{/if}
{if not %carrier.last%}&nbsp;::&nbsp;{/if}
{/section}

<br /><br />

{if $carrier eq "FDX"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=FDX#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"FedEx"}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="FDX" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="30%"><b>{$lng.lbl_company_type}:</b></td>
	<td>
	<select name="company_type">
		<option value="Express"{if $shipping_options.fdx.param00 eq "Express"} selected="selected"{/if}>FedEx Express</option>
		<option value="Ground"{if $shipping_options.fdx.param00 eq "Ground"} selected="selected"{/if}>FedEx Ground</option>
		<option value="Both"{if $shipping_options.fdx.param00 eq "Both"} selected="selected"{/if}>FedEx Ground & FedEx Express</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_packaging}:</b></td>
	<td>
	<select name="packaging">
		<option value="1"{if $shipping_options.fdx.param01 eq "1"} selected="selected"{/if}>My packaging</option>
		<option value="2"{if $shipping_options.fdx.param01 eq "2"} selected="selected"{/if}>FedEx Express Pak</option>
		<option value="3"{if $shipping_options.fdx.param01 eq "3"} selected="selected"{/if}>FedEx Express Box</option>
		<option value="4"{if $shipping_options.fdx.param01 eq "4"} selected="selected"{/if}>FedEx Express Tube</option>
		<option value="6"{if $shipping_options.fdx.param01 eq "6"} selected="selected"{/if}>FedEx Express Envelope</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ship_to_residence}:</b></td>
	<td>
	<select name="dropoff_type">
		<option value="true"{if $shipping_options.fdx.param02 eq "true"} selected="selected"{/if}>{$lng.lbl_yes}</option>
		<option value="false"{if $shipping_options.fdx.param02 eq "false"} selected="selected"{/if}>{$lng.lbl_no}</option>
	</select>
	</td>
</tr>

<tr>
	<td colspan="2"><hr /></td>
</tr>

<tr>
	<td colspan="2"><h3>{$lng.lbl_fuel_surcharges}</h3></td>
</tr>

<tr>
	<td><b>{$lng.lbl_fedex_express_percent}:</b></td>
	<td><input size="20" name="expr_fuel_surch" value="{$shipping_options.fdx.param03}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_fedex_ground_percent}:</b></td>
	<td><input size="20" name="grnd_fuel_surch" value="{$shipping_options.fdx.param04}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<hr />

{********* start of uploading code **********}

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="FDX_IMPORT" />

<a name="fdx_import_rates" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td colspan="2"><h3>{$lng.lbl_import_rates_data}</h3></td>
</tr>

<tr>
	<td><b>{$lng.lbl_origin_zip_code}</b></td>
	<td>{$fdx_import_stat.ozip|default:"&nbsp;"}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_zipcode_import_date}</b></td>
	<td>{$fdx_import_stat.date|date_format:$config.Appearance.datetime_format|default:"&nbsp;"}
{if $fdx_import_updated eq "true" and $fdx_import_stat.updated eq 1}
<b><font color="green"> - {$lng.lbl_updated}</font></b>
{/if}
	</td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

<tr>
	<td><b>{$lng.lbl_shipping_methods_zones}</b></td>
	<td><b>{$lng.lbl_date_of_import}</b></td>
</tr>

{foreach from=$fdx_import_stat.files key=id item=name}

{if $name.date ne ""}
<tr>
	<td>{$id|capitalize}</td>
	<td>{$name.date|date_format:$config.Appearance.datetime_format}
{if $fdx_import_updated eq "true" and $name.updated eq 1}
<b><font color="green"> - {$lng.lbl_updated}</font></b>
{/if}
	</td>
</tr>
{/if}

{/foreach}

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

{if $fdx_import_ok eq "true"}
<tr>
	<td colspan="2"><b><font color="green">{$lng.txt_fdx_files_imported}</font></b></td>
</tr>
{else}
<tr>
	<td colspan="2"><b><font color="red">{$fdx_import_ok}</font></b></td>
</tr>
{/if}

<tr>
	<td><b>{$lng.lbl_server_path_to_files}</b></td>
	<td><input size="40" name="fdx_import_files_path" value="{$fdx_files_path}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_import|strip_tags:false|escape}" /></td>
</tr>

<tr>
	<td colspan="2">
	<a href="javascript: void(0);" onclick="javascript: window.open('popup_info.php?action=FDX','FDX_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"><b>{$lng.lbl_read_more_about_importing}</b></a>
	</td>
</tr>

</table>
</form>

{********* end of uploading code **********}

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"FedEx"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "USPS"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=USPS#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"U.S.P.S."}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="USPS" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td colspan="2"><h3>{$lng.lbl_international_usps}</h3></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_type_of_mail}:</b></td>
	<td>
	<select name="mailtype">
		<option value="Package"{if $shipping_options.usps.param00 eq "Package"} selected="selected"{/if}>Package</option>
		<option value="Postcards or Aerogrammes"{if $shipping_options.usps.param00 eq "Postcards or Aerogrammes"} selected="selected"{/if}>Postcards or Aerogrammes</option>
		<option value="Matter for the Blind"{if $shipping_options.usps.param00 eq "Matter for the Blind"} selected="selected"{/if}>Matter for the Blind</option>
		<option value="Envelope"{if $shipping_options.usps.param00 eq "Envelope"} selected="selected"{/if}>Envelope</option>
	</select>
	</td>
</tr>

<tr>
	<td colspan="2"><hr /></td>
</tr>

<tr>
	<td colspan="2"><h3>{$lng.lbl_domestic_usps}</h3></td>
</tr>

<tr>
	<td><b>{$lng.lbl_package_size} {$lng.lbl_package_size_note}:</b></td>
	<td>
	<select name="package_size">
		<option value="Regular"{if $shipping_options.usps.param01 eq "Regular"} selected="selected"{/if}>Regular (0 &lt; size &lt;= 84)</option>
		<option value="Large"{if $shipping_options.usps.param01 eq "Large"} selected="selected"{/if}>Large (84 &lt; size &lt;= 108)</option>
		<option value="Oversize"{if $shipping_options.usps.param01 eq "Oversize"} selected="selected"{/if}>Oversize (108 &lt; size &lt;= 130)</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_machinable}:</b></td>
	<td>
	<select name="machinable">
		<option value="FALSE"{if $shipping_options.usps.param02 eq "FALSE"} selected="selected"{/if}>{$lng.lbl_no}</option>
		<option value="TRUE"{if $shipping_options.usps.param02 eq "TRUE"} selected="selected"{/if}>{$lng.lbl_yes}</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_usps_container}:</b></td>
	<td>
	<select name="container_express">
		<option>{$lng.lbl_none}</option>
		<option value="Flat Rate Envelope"{if $shipping_options.usps.param03 eq "Flat Rate Envelope"} selected="selected"{/if}>Express Mail Flat Rate Envelope, 12.5 x 9.5</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_usps_container2}:</b></td>
	<td>
	<select name="container_priority">
		<option>{$lng.lbl_none}</option>
		<option value="Flat Rate Envelope"{if $shipping_options.usps.param04 eq "Flat Rate Envelope"} selected="selected"{/if}>Priority Mail Flat Rate Envelope, 12.5 x 9.5</option>
		<option value="Flat Rate Box"{if $shipping_options.usps.param04 eq "Flat Rate Box"} selected="selected"{/if}>Priority Mail Flat Rate Box, 14" x 12" x 3.5", 11.25" x 8.75" x 6"</option>
	</select>
	</td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"U.S.P.S."}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "Intershipper"}

{capture name=dialog}

<div align="right"><a href="shipping.php#rt">{$lng.lbl_manage_shipping_methods}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="Intershipper" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="50%"><b>{$lng.lbl_type_of_delivery}:</b></td>
	<td>
	<select name="delivery">
		<option value="COM"{if $shipping_options.intershipper.param00 eq "COM"} selected="selected"{/if}>Commercial delivery</option>
		<option value="RES"{if $shipping_options.intershipper.param00 eq "RES"} selected="selected"{/if}>Residential delivery</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_type_of_pickup}:</b></td>
	<td>
	<select name="pickup[]" size="3">
		<option value="DRP"{if $shipping_options.intershipper.param01 eq "DRP"} selected="selected" {/if}>Drop of at carrier location</option>
		<option value="SCD"{if $shipping_options.intershipper.param01 eq "SCD"} selected="selected" {/if}>Regularly Scheduled Pickup</option>
		<option value="PCK"{if $shipping_options.intershipper.param01 eq "PCK"} selected="selected" {/if}>Schedule A Special Pickup</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_length}:</b></td>
	<td><input type="text" name="length" size="10" value="{$shipping_options.intershipper.param02}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_width}:</b></td>
	<td><input type="text" name="width" size="10" value="{$shipping_options.intershipper.param03}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_height}:</b></td>
	<td><input type="text" name="height" size="10" value="{$shipping_options.intershipper.param04}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_dimensional_unit}:</b></td>
	<td>
	<select name="dunit">
		<option value="IN"{if $shipping_options.intershipper.param05 eq "IN"} selected="selected"{/if}>Inches</option>
		<option value="CM"{if $shipping_options.intershipper.param05 eq "CM"} selected="selected"{/if}>Centimeters</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_package_type}:</b></td>
	<td>
	<select name="packaging">
		<option value="BOX"{if $shipping_options.intershipper.param06 eq "BOX"} selected="selected"{/if}>Box</option>
		<option value="ENV"{if $shipping_options.intershipper.param06 eq "ENV"} selected="selected"{/if}>Envelope</option>
		<option value="ltr"{if $shipping_options.intershipper.param06 eq "ltr"} selected="selected"{/if}>Letter</option>
		<option value="TUB"{if $shipping_options.intershipper.param06 eq "TUB"} selected="selected"{/if}>Tube</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_nature_of_shipment_contents}:</b></td>
	<td>
	<select name="contents">
		<option value="OTR"{if $shipping_options.intershipper.param07 eq "OTR"} selected="selected"{/if}>Other: Most shipments will use this code</option>
		<option value="LQD"{if $shipping_options.intershipper.param07 eq "LQD"} selected="selected"{/if}>Liquid</option>
		<option value="AHM"{if $shipping_options.intershipper.param07 eq "AHM"} selected="selected"{/if}>Accessible HazMat</option>
		<option value="IHM"{if $shipping_options.intershipper.param07 eq "IHM"} selected="selected"{/if}>Inaccessible HazMat</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_package_cod_value}:</b></td>
	<td><input type="text" name="codvalue" size="10" value="{$shipping_options.intershipper.param08}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_package_insured_value}:</b></td>
	<td><input type="text" name="insvalue" size="10" value="{$shipping_options.intershipper.param09}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"InterShipper"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "CPC"}

{capture name=dialog}

<div align="right"><a href="shipping.php?carrier=CPC#rt">{$lng.lbl_X_shipping_methods|substitute:"service":"Canada Post"}</a></div>

<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="CPC" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="50%"><b>{$lng.lbl_item_description}:</b></td>
	<td><input type="text" name="descr" size="50" value="{$shipping_options.cpc.param00}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_item_length}:</b></td>
	<td><input type="text" name="length" size="10" value="{$shipping_options.cpc.param01}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_item_width}:</b></td>
	<td><input type="text" name="width" size="10" value="{$shipping_options.cpc.param02}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_item_height}:</b></td>
	<td><input type="text" name="height" size="10" value="{$shipping_options.cpc.param03}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_cpc_package_insured_value}:</b></td>
	<td><input type="text" name="insvalue" size="10" value="{$shipping_options.cpc.param04}" /></td>
</tr>

<tr>
	<td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
	<font class="SmallText">{$lng.txt_shipping_cost_convertion_rate}</font>
	</td>
	<td valign="top"><input type="text" name="currency_rate" size="10" value="{$shipping_options.cpc.param05}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Canada Post"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "ARB"}
{capture name=dialog}
<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="ARB" />

<table width="100%">

<tr>
	<td width="50%"><b>{$lng.lbl_arb_pkgtype}:</b></td>
	<td width="50%">
	<select name="param00">
		<option value="P"{if $shipping_options.arb.param00 eq "P"} selected="selected"{/if}>Package</option>
		<option value="L"{if $shipping_options.arb.param00 eq "L"} selected="selected"{/if}>Letter</option>
	</select>
	</td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_shipdays}:</b></td>
	<td><input type="text" name="param01" size="10" value="{$shipping_options.arb.param01}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_pkg_len}:</b></td>
	<td><input type="text" name="param02" size="10" value="{$shipping_options.arb.param02}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_pkg_width}:</b></td>
	<td><input type="text" name="param03" size="10" value="{$shipping_options.arb.param03}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_pkg_height}:</b></td>
	<td><input type="text" name="param04" size="10" value="{$shipping_options.arb.param04}" /></td>
</tr>

<tr valign="top">
	<td width="50%"><b>{$lng.lbl_arb_ap_type}:</b></td>
	<td width="50%">
	<select name="param05">
		<option value="NR" {if $shipping_options.arb.param05 eq "NR"} selected="selected"{/if}>Not required</option>
		<option value="AP" {if $shipping_options.arb.param05 eq "AP"} selected="selected"{/if}>Asset Protection</option>
	</select>
	</td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_ap_value}:</b></td>
	<td><input type="text" name="param06" size="10" value="{$shipping_options.arb.param06}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_haz}:</b></td>
	<td><input type="checkbox" name="opt_haz" value="Y"{if $shipping_options.arb.opt_haz eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_codpmt}:</b></td>
	<td>
	<select name="param08">
		<option value="M"{if $shipping_options.arb.param08 eq "M"} selected="selected"{/if}>Cashier's Check or Money Order</option>
		<option value="P"{if $shipping_options.arb.param08 eq "P"} selected="selected"{/if}>Personal or Company Check</option>
	</select>
	</td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_codval}:</b></td>
	<td><input type="text" name="param09" size="10" value="{$shipping_options.arb.param09}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_arb_opt_own_account}:</b></td>
	<td><input type="checkbox" name="opt_own_account" value="Y"{if $shipping_options.arb.opt_own_account eq "Y"} checked="checked"{/if} /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Airborne"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "APOST"}
{capture name=dialog}
<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="APOST" />

<table width="100%">

<tr>
	<td width="50%"><b>{$lng.lbl_apost_pkg_len}:</b></td>
	<td><input type="text" name="param00" size="10" value="{$shipping_options.apost.param00}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_apost_pkg_width}:</b></td>
	<td><input type="text" name="param01" size="10" value="{$shipping_options.apost.param01}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_apost_pkg_height}:</b></td>
	<td><input type="text" name="param02" size="10" value="{$shipping_options.apost.param02}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"Australia Post"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}

{if $carrier eq "DHL"}
{capture name=dialog}
<form method="post" action="shipping_options.php">
<input type="hidden" name="carrier" value="{$carrier}" />

<table width="100%">

<tr>
	<td width="50%"><b>{$lng.lbl_dhl_pkg_width}:</b></td>
	<td><input type="text" name="param01" size="10" value="{$shipping_options.dhl.param01}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_dhl_pkg_height}:</b></td>
	<td><input type="text" name="param02" size="10" value="{$shipping_options.dhl.param02}" /></td>
</tr>

<tr>
	<td width="50%"><b>{$lng.lbl_dhl_pkg_depth}:</b></td>
	<td><input type="text" name="param03" size="10" value="{$shipping_options.dhl.param03}" /></td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/capture}
{assign var="section_title" value=$lng.lbl_X_shipping_options|substitute:"service":"DHL"}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$section_title extra='width="100%"'}

{/if}
