{* $Id: product.tpl,v 1.9 2005/11/21 12:42:06 max Exp $ *}
<!--
{* php *}
include_once $xcart_dir."/home/wwmpon2/public_html/cart/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{* /php *}
-->
<!-- Start addition by Michael de Leon 09.21.06 -->
{if $active_modules.Extra_Fields ne ""}
	{section name=field loop=$extra_fields}
		{assign var=extrafield_value1 value=$extra_fields[field].field_value}
		{if $extra_fields[field].active eq "Y" && $extra_fields[field].field}
		<tr>
		<td class="wwmp_product_labels">{$extra_fields[field].field}:</td>
		{/if}
	{/section}
		{if $product.extra_field1 ne ""}
		<td><SPAN id="product_extrafield1">{$product.extra_field1}</SPAN></td>
		{else}
		<td><SPAN id="product_extrafield1">{$extrafield_value1}</SPAN></td>
		{/if}
		</tr>
{/if}
<!-- End addition by Michael de Leon 09.21.06 -->
<!-- Deleted by Michael de Leon 09.21.06
{* section name=field loop=$extra_fields *}
{* if $extra_fields[field].active eq "Y" && $extra_fields[field].field_value *}
<tr>
	<td width="30%">{* $extra_fields[field].field *}</td>
	<td>{* $extra_fields[field].field_value *}</td>
</tr>
{* /if *}
{* /section *}
-->