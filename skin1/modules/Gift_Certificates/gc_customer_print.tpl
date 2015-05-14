{* $Id: gc_customer_print.tpl,v 1.3 2005/12/28 07:00:57 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
{if $css_file ne ""}
<head>
<link rel="stylesheet" href="{$SkinDir}/modules/Gift_Certificates/{$css_file}" />
</head>
{/if}
<body>
{if $config.Gift_Certificates.print_giftcerts_separated eq "Y"}
{assign var="separator" value="<div style='page-break-after:always'></div>"}
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
