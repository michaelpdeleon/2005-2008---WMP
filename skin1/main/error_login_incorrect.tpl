{* $Id: error_login_incorrect.tpl,v 1.35 2005/11/30 17:02:36 max Exp $ *}
<!-- Start addition by Michael de Leon 12.04.06 -->
<img class="wwmp_catalog_banner" src="{$ImagesDir}/wwmp_banner.jpg">
<!-- End addition by Michael de Leon 12.04.06 -->

<!-- Deleted by Michael de Leon 11.14.06
{* $lng.txt_login_incorrect *}
<p />
-->

<div class="wwmp_error_login" align="center">
{ capture name=dialog }
{include file="main/login_form.tpl}
{ /capture }
{include file="dialog_personaldetails.tpl" title=$lng.lbl_authentication content=$smarty.capture.dialog extra='width="295"'}
</div>

<!-- Deleted by Michael de Leon 12.05.06
{* include file="dialog.tpl" title=$lng.lbl_authentication content=$smarty.capture.dialog extra='width="100%"' *}
-->
