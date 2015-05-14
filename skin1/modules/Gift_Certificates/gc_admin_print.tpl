{* $Id: gc_admin_print.tpl,v 1.5 2005/11/17 06:55:46 max Exp $ *}
<html>
{if $css_file ne ""}
<head>
<link rel="stylesheet" href="{$SkinDir}/modules/Gift_Certificates/{$css_file}" />
</head>
{/if}
<body>
{if $config.Gift_Certificates.print_giftcerts_separated eq "Y"}
{assign var="separator" value="<div style='page-break-after: always;'></div>"}
{else}
{assign var="separator" value="<br /><hr size='1' noshade="noshade" /><br />"}
{/if}
{foreach name=giftcerts from=$giftcerts key=key item=giftcert}
{include file="modules/Gift_Certificates/`$giftcert.tpl_file`"}
{if not $smarty.foreach.giftcerts.last}
{$separator}
{/if}
{/foreach}
</body>
</html>
