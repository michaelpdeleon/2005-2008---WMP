{* $Id: preview.tpl,v 1.5 2005/11/17 06:55:36 max Exp $ *}
{ config_load file="$skin_config" }
{if $use_default_css}
<link rel="stylesheet" href="{$SkinDir}/{#CSSFile#}" />
{/if}
{include file=$template}
