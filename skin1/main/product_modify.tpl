{* $Id: product_modify.tpl,v 1.106.2.2 2006/07/25 14:52:01 max Exp $ *}
<a name="main" />
{if $product}
{assign var="product_title" value=$product.product|truncate:30:"...":false}
{assign var="page_title" value="`$page_title`<br /><span class='ProductTitle'>`$product_title`</span>"}
{/if}

{include file="page_title.tpl" title=$page_title}

<script type="text/javascript" language="JavaScript 1.2">
<!--
window.name="prodmodwin";
-->
</script>

{include file="main/include_js.tpl" src="main/popup_image_selection.js"}
{include file="main/multirow.tpl"}
{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

{if $products && $geid}
<br />
{capture name=dialog}
{ include file="customer/main/navigation.tpl" }

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	<td>{$lng.lbl_sku}</td>
	<td>{$lng.lbl_product}</td>
</tr>

{foreach from=$products item=v}

<tr{cycle name="ge" values=', class="TableSubHead"'}>
	<td>{if $productid eq $v.productid}<b>{else}<a href="product_modify.php?productid={$v.productid}{if $section ne 'main'}&amp;section={$section}{/if}&amp;geid={$geid}">{/if}
{$v.productcode|escape}
{if $productid eq $v.productid}</b>{else}</a>{/if}
</td>
	<td>{if $productid eq $v.productid}<b>{else}<a href="product_modify.php?productid={$v.productid}{if $section ne 'main'}&amp;section={$section}{/if}&amp;geid={$geid}">{/if}
{$v.product|escape}
{if $productid eq $v.productid}</b>{else}</a>{/if}
</td>
</tr>

{/foreach}

</table>
{ include file="customer/main/navigation.tpl" }

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_product_list extra="width='100%'"}
{/if}

<br />

{if $section eq "main" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_main" />
{include file="main/product_details.tpl"}
<br />
{/if}

{if $section eq "lng" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_lng" />
{include file="main/products_lng.tpl"}
<br />
{/if}

{if $section eq "subscr" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_subscr" />
{include file="modules/Subscriptions/subscription_plans.tpl"}
<br />
{/if}

{if $section eq "options" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_options" />
{$lng.txt_add_product_options_note}<br />
<br />
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_product_options_help href="javascript:window.open('popup_info.php?action=OPT','OPT_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"}</div>
<br />
{if ($submode eq 'product_options_add' || $product_options eq '' || $product_option ne '')}

{if $config.General.display_all_products_on_1_page eq 'Y'}
{include file="modules/Product_Options/product_options.tpl"}
<br />
{/if}

{include file="modules/Product_Options/add_product_options.tpl"}
{else}
{include file="modules/Product_Options/product_options.tpl"}
{/if}
<br />
{/if}

{if $product.is_variants eq 'Y'}
{if $section eq "variants" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_variants" />
{include file="modules/Product_Options/product_variants.tpl"}
<br />
{/if}
{/if}

{if $section eq "pclass" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_pclass" />
{include file="modules/Product_Configurator/pconf_classification.tpl"}
<br />
{/if}

{if $product.is_variants ne 'Y' && ($section eq "wholesale" || $config.General.display_all_products_on_1_page eq 'Y')}
<a name="section_wholesale" />
{include file="modules/Wholesale_Trading/product_wholesale.tpl"}
<br />
{/if}

{if $section eq "upselling" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_upselling" />
{include file="modules/Upselling_Products/product_links.tpl"}
<br />
{/if}

{if $section eq "images" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_images" />
{include file="modules/Detailed_Product_Images/product_images_modify.tpl"}
<br />
{/if}

{if $section eq "zoomer" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_zoomer" />
{include file="modules/Magnifier/product_magnifier_modify.tpl"}
<br />
{/if}

{if $section eq "reviews" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_reviews" />
{include file="modules/Customer_Reviews/admin_reviews.tpl"}
<br />
{/if}

{if $section eq "feature_class" || $config.General.display_all_products_on_1_page eq 'Y'}
<a name="section_feature_class" />
{include file="modules/Feature_Comparison/product_class.tpl"}
<br />
{/if}

{if $section eq "error"}
{capture name=dialog}
<br />
{$lng.txt_cant_create_product_warning}
<br /><br />
{include file="buttons/button.tpl" button_title=$lng.lbl_register_provider href="user_add.php?usertype=P"}
<br />
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_warning extra="width='100%'"}

{/if}
