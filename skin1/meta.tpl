{* $Id: meta.tpl,v 1.26 2006/04/10 07:36:17 max Exp $ *}
<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
{include file="presets_js.tpl"}
{include file="main/include_js.tpl" src="common.js"}
{if $config.Adaptives.isJS eq '' && $config.Adaptives.is_first_start eq 'Y'}
<script type="text/javascript">
<!--
var usertype = "{$usertype}";
-->
</script>
<script id="adaptives_script" type="text/javascript" language="JavaScript 1.2"></script>
{include file="main/include_js.tpl" src="browser_identificator.js"}
{/if}
{if $usertype eq "P" or $usertype eq "A"}
<meta name="ROBOTS" content="NOINDEX" />
<meta name="ROBOTS" content="NOFOLLOW" />
{else}
{assign var="_meta_descr" value=""}
{assign var="_meta_keywords" value=""}
{if $product.meta_descr ne "" and $config.SEO.include_meta_products eq "Y"}
{assign var="_meta_descr" value="`$product.meta_descr` "}
{assign var="_meta_keywords" value="`$product.meta_keywords` "}
{/if}
{if $current_category.meta_descr ne "" and $config.SEO.include_meta_categories eq "Y"}
{assign var="_meta_descr" value="$_meta_descr`$current_category.meta_descr` "}
{assign var="_meta_keywords" value="$_meta_keywords`$current_category.meta_keywords` "}
{/if}
{assign var="_meta_descr" value="$_meta_descr`$config.SEO.meta_descr`"}
{assign var="_meta_keywords" value="$_meta_keywords`$config.SEO.meta_keywords`"}
<meta name="description" content="{$_meta_descr|truncate:"500":"...":false|escape}" />
<meta name="keywords" content="{$_meta_keywords|truncate:"500":"":false|escape}" />
{/if}
{if $webmaster_mode eq "editor"}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var store_language = "{if ($usertype eq "P" or $usertype eq "A") and $current_language ne ""}{$current_language}{else}{$store_language}{/if}";
var catalogs = new Object();
catalogs.admin = "{$catalogs.admin}";
catalogs.provider = "{$catalogs.provider}";
catalogs.customer = "{$catalogs.customer}";
catalogs.partner = "{$catalogs.partner}";
catalogs.images = "{$ImagesDir}";
catalogs.skin = "{$SkinDir}";
var lng_labels = [];
{foreach key=lbl_name item=lbl_val from=$webmaster_lng}
lng_labels['{$lbl_name}'] = '{$lbl_val}';
{/foreach}
var page_charset = "{$default_charset|default:"iso-8859-1"}";
-->
</script>
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/editor_common.js"></script>
{if $user_agent eq "ns"}
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/editorns.js"></script>
{else}
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/editor.js"></script>
{/if}
{/if}
