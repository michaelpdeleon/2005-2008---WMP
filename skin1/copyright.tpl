{* $Id: copyright.tpl,v 1.12 2005/11/17 06:55:36 max Exp $ *}
<!-- Deleted by Michael de Leon 09.14.06
{* $lng.lbl_copyright *} &copy; {* $config.Company.start_year *}{* if $config.Company.start_year lt $config.Company.end_year *}-{*$smarty.now|date_format:"%Y" *}{*/if*} {*$config.Company.company_name*}
-->
<!-- Start addition by Michael de Leon 09.14.06 -->
<div class="BottomText">{if $config.Company.company_phone_2}{$lng.lbl_phone_2_title}: {$config.Company.company_phone_2}{/if}</div>
<!-- End addition by Michael de Leon 09.14.06 -->
