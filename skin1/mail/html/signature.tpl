{* $Id: signature.tpl,v 1.5 2005/12/02 07:25:36 max Exp $ *}
<hr size="1" noshade="noshade" />
{$lng.eml_signature}
<p><font size="2">
{$config.Company.company_name}<br />
{$lng.lbl_phone}: {$config.Company.company_phone}<br />
{$lng.lbl_fax}:   {$config.Company.company_fax}<br />
{$lng.lbl_url}:   <a href="{$http_location}/" target=_new>{$config.Company.company_website}</a>
</font></p>
