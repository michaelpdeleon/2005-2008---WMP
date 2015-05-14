{* $Id: Password_Recovery.tpl,v 1.16 2005/11/21 12:41:58 max Exp $ *}
<!-- Start addition by Michael de Leon 12.04.06 -->
<img class="wwmp_catalog_banner" src="{$ImagesDir}/wwmp_banner.jpg">
<!-- End addition by Michael de Leon 12.04.06 -->

<!-- Start addition by Michael de Leon 12.05.06 -->
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr>
	<td class="wwmp_error_lostpw_desc" align="left">
	{$lng.txt_password_recover} If you have any questions, <a class="wwmp_aboutus_textlink" href="help.php?section=contactus&mode=update">contact us</a>.
	</td>
</tr>
</table>
</div>
<!-- End addition by Michael de Leon 12.05.06 -->

<div class="wwmp_error_lostpw_box" align="center">
{capture name=dialog}
<div align="center">
<form action="help.php" method="post" name="processform">
<table border="0" cellpadding="2" cellspacing="2" height="100">
<tr>
<td class="wwmp_error_lostpw" align="right"><strong>{$lng.lbl_email}</strong></td>
<td class="wwmp_error_lostpw" align="left"><font class="Star">*</font></td>
<td class="wwmp_error_lostpw" align="left"> 
  <input type="text" name="email" size="30" value="{$smarty.get.email|escape:"html"}" />
</td>
</tr>
{if $smarty.get.section eq "Password_Recovery_error"}
<tr>
<td align="left">&nbsp;</td>
<td align="left">&nbsp;</td>
<td class="ErrorMessage" align="left"><div class="wwmp_error_login">{$lng.txt_email_invalid}</div></td>
</tr>
{/if}
<tr> 
<td align="left">&nbsp;</td>
<td align="left">&nbsp;</td>
<td class="wwmp_cartlogin_contents" align="left"><a href="javascript: document.processform.submit()"><input src="{$ImagesDir}/wwmp_logingobtn10.31.06.jpg" type="image"></a>
<!-- Deleted by Michael de Leon 12.05.06 {* include file="buttons/submit.tpl" href="javascript: document.processform.submit()" js_to_href="Y" *}-->
</td>
</tr>
</table>
<input type="hidden" name="action" value="recover_password" />
</form>
</div>
{/capture}
{include file="dialog_personaldetails.tpl" title=$lng.lbl_forgot_password content=$smarty.capture.dialog extra='width="295"'}
<!-- Deleted by Michael de Leon 12.05.06
{* include file="dialog.tpl" title=$lng.lbl_forgot_password content=$smarty.capture.dialog extra='width="100%"' *}
-->
</div>