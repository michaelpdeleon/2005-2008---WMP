{* $Id: check_options.tpl,v 1.61.2.3 2006/09/19 07:31:06 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--

/*
variants array:
	0 - array:
		0 - taxed price
		1 - quantity
		2 - variantid if variant have thumbnail
		3 - weight
		4 - original price (without taxes)
		5 - productcode
		6 - discount price
	1 - array: variant options as classid => optionid
	2 - array: taxes as taxid => tax amount
	3 - wholesale prices array:
		0 - quantity
		1 - next quantity
		2 - taxed price
		3 - taxes array: as taxid => tax amount
		4 - original price (without taxes)
*/
var variants = [];
{if $variants ne ''}
{foreach from=$variants item=v key=k}
variants[{$k}] = [{strip}
/* Start addition by Michael de Leon 09.21.06 */
	[{$v.taxed_price|default:$v.price|default:$product.taxed_price|default:$product.price}, {$v.avail|default:0}, new Image(), '{$v.weight|default:0}', {$v.price|default:$product.price|default:'0'}, "{$v.productcode|escape:javascript}", '{section name=field loop=$extra_fields}{if $extra_fields[field].active eq "Y"}{$v.extra_field1|default:$extra_fields[field].field_value|default:"Call for shipping schedule"}{/if}{/section}',{$v.discount_price|default:$product.discount_price}], 
/* End addition by Michael de Leon 09.21.06 */
/* Deleted by Michael de Leon 09.21.06
	[{$v.taxed_price|default:$v.price|default:$product.taxed_price|default:$product.price}, {$v.avail|default:0}, new Image(), '{$v.weight|default:0}', {$v.price|default:$product.price|default:'0'}, "{$v.productcode|escape:javascript}"],
*/
	{ldelim}{foreach from=$v.options item=o name=opts}{if $o ne ''}{if not $smarty.foreach.opts.first}, {/if}{$o.classid|default:0}: {$o.optionid|default:0}{/if}{/foreach}{rdelim},
	{ldelim}{foreach from=$v.taxes item=t key=id name=taxes}{if not $smarty.foreach.taxes.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim},
	[{foreach from=$v.wholesale item=w key=p name=whl}

		[
			{$w.quantity|default:0},
			{if $w.next_quantity}{math equation="x-1" x=$w.next_quantity}{else}0{/if},
			{$w.taxed_price|default:$product.taxed_price},
			{ldelim}{foreach from=$w.taxes item=t key=kt name=whlt}{if not $smarty.foreach.whlt.first}, {/if}{$kt}: {$t|default:0}{/foreach}{rdelim},
			{$w.price|default:$product.price}
		]{if not $smarty.foreach.whl.last},{/if}
{/foreach}]
{/strip}];
{if $v.is_image}
variants[{$k}][0][2].src = "{if $v.image_url ne ''}{$v.image_url}{else}{if $full_url}{$http_location}{else}{$xcart_web_dir}{/if}/image.php?id={$k}&type=W{/if}"; 
{/if}
{/foreach}
{/if}

/*
modifiers array: as clasid => array: as optionid => array:
	0 - price_modifier
	1 - modifier_type
	2 - taxes array: as taxid => tax amount
*/
var modifiers = [];
// names array: as classid => class name
var names = [];
{foreach from=$product_options item=v key=k}
names[{$v.classid}] = {ldelim}class_name: "{$v.class_orig|default:$v.class|escape:javascript}", options: []{rdelim};
{foreach from=$v.options item=o name=opts}
names[{$v.classid}]['options'][{$o.optionid}] = "{$o.option_name_orig|default:$o.option_name|escape:javascript}";
{/foreach}
{if $v.is_modifier eq 'Y'}
modifiers[{$v.classid}] = {ldelim}{strip}
{foreach from=$v.options item=o name=opts}
	{$o.optionid}: [
		{$o.price_modifier|default:"0.00"}, 
		'{$o.modifier_type|default:"$"}',
		{ldelim}{foreach from=$o.taxes item=t key=id name=optt}{if not $smarty.foreach.optt.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim}
	]{if not $smarty.foreach.opts.last},{/if}

{/foreach}
{/strip}{rdelim};
{/if}
{/foreach}

/*
taxes array: as taxid => array()
	0 - calculated tax value for default product price
	1 - tax name
	2 - tax type ($ or %)
	3 - tax value
*/
var taxes = [];
{if $product.taxes}
{foreach from=$product.taxes key=tax_name item=tax}
{if $tax.display_including_tax eq "Y" && ($tax.display_info eq 'A' || $tax.display_info eq 'V')}
taxes[{$tax.taxid}] = [{$tax.tax_value|default:0}, "{$tax.tax_display_name}", "{$tax.rate_type}", "{$tax.rate_value}"];
{/if}
{/foreach}
{/if}

// exceptions array: as exctionid => array: as clasid => optionid
var exceptions = [];
{if $product_options_ex ne ''}
{foreach from=$product_options_ex item=v key=k}
exceptions[{$k}] = [];
{foreach from=$v item=o}
exceptions[{$k}][{$o.classid}] = {$o.optionid};
{/foreach} 
{/foreach} 
{/if}

/*
_product_wholesale array: as id => array:
	0 - quantity
	1 - next quantity
	2 - taxed price
	3 - taxes array: as taxid => tax amount
	4 - original price (without taxes)
*/
var product_wholesale = [];
var _product_wholesale = [];
{if $product_wholesale ne ''}
{foreach from=$product_wholesale item=v key=k}
_product_wholesale[{$k}] = [{$v.quantity|default:0},{$v.next_quantity|default:0},{$v.taxed_price|default:$product.taxed_price}, [], {$v.price|default:$product.price}];
{if $v.taxes ne ''}
{foreach from=$v.taxes item=t key=kt}
_product_wholesale[{$k}][3][{$kt}] = {$t|default:0};
{/foreach}
{/if}
{/foreach}
{/if}

var product_image = new Image();
product_image.src = "{if $product.tmbn_url}{$product.tmbn_url}{else}{if $full_url}{$http_location}{else}{$xcart_web_dir}{/if}/image.php?id={$product.productid}&type={if $product.is_image}P{else}T{/if}{/if}";
var exception_msg = "{$lng.txt_exception_warning|strip_tags|escape:javascript}!";
var exception_msg_html = "{$lng.txt_exception_warning|escape:javascript}!";
var txt_out_of_stock = "{$lng.txt_out_of_stock|strip_tags|escape:javascript}";
var pconf_price = {$taxed_total_cost|default:0}
var default_price = {$product.taxed_price|default:"0"};
var currency_symbol = "{$config.General.currency_symbol|escape:"javascript"}";
var alter_currency_symbol = "{$config.General.alter_currency_symbol|escape:"javascript"}";
var alter_currency_rate = {$config.General.alter_currency_rate|default:"0"};
var lbl_no_items_available = "{$lng.lbl_no_items_available|escape:javascript}";
var txt_items_available = "{$lng.txt_items_available|escape:javascript}";
var list_price = {$product.list_price|default:0};
var price = {$product.taxed_price|default:"0"};
var orig_price = {$product.price|default:$product.taxed_price|default:"0"};
var mq = {$config.Appearance.max_select_quantity|default:0};
var dynamic_save_money_enabled = {if $config.Product_Options.dynamic_save_money_enabled eq 'Y'}true{else}false{/if};
var is_unlimit = {if $config.General.unlimited_products eq 'Y'}true{else}false{/if};

var lbl_item = "{$lng.lbl_item|escape:javascript}";
var lbl_items = "{$lng.lbl_items|escape:javascript}";
var lbl_quantity = "{$lng.lbl_quantity|escape:javascript}";
var lbl_price = "{$lng.lbl_price_per_item|escape:javascript}";
var txt_note = "{$lng.txt_note|escape:javascript}";
var lbl_including_tax = "{$lng.lbl_including_tax|escape:javascript}";

-->
</script>

{include file="main/include_js.tpl" src="modules/Product_Options/func.js"}
